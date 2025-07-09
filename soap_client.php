<?php
/**
 * Modern SOAP Client Wrapper for Trans-Express Authentication
 * Provides a clean interface for the legacy SOAP webservice while maintaining compatibility
 */

class TransExpressSOAPClient {
    private $client;
    private $wsdl_url;
    private $timeout;
    private $connection_timeout;
    
    public function __construct($wsdl_url = null, $timeout = 30, $connection_timeout = 10) {
        $this->wsdl_url = $wsdl_url ?: ($_ENV['WS_URL'] ?? 'http://192.168.0.251/webservice/wsTransExpressExtranet.php?wsdl');
        $this->timeout = $timeout;
        $this->connection_timeout = $connection_timeout;
    }
    
    /**
     * Initialize SOAP client with proper error handling
     */
    private function initializeClient() {
        try {
            // Try modern SoapClient first (PHP 8+ compatible)
            if (class_exists('SoapClient') && $this->isWsdlAccessible()) {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => $this->connection_timeout,
                        'user_agent' => 'Trans-Express PHP Client'
                    ]
                ]);
                
                $this->client = new SoapClient($this->wsdl_url, [
                    'trace' => 1,
                    'exceptions' => true,
                    'connection_timeout' => $this->connection_timeout,
                    'stream_context' => $context,
                    'cache_wsdl' => WSDL_CACHE_NONE, // Disable for development
                    'soap_version' => SOAP_1_1
                ]);
                
                return 'modern';
            }
        } catch (Exception $e) {
            error_log("Modern SoapClient failed: " . $e->getMessage());
        }
        
        // Fallback to NuSOAP for compatibility
        try {
            if (!class_exists('nusoap_client')) {
                require_once(__DIR__ . '/nusoap/nusoap.php');
            }
            
            $this->client = new nusoap_client($this->wsdl_url, true, '', '', '', '');
            $this->client->soap_defencoding = 'UTF-8';
            $this->client->timeout = $this->timeout;
            $this->client->response_timeout = $this->timeout;
            
            $error = $this->client->getError();
            if ($error) {
                throw new Exception("NuSOAP Client Error: " . $error);
            }
            
            return 'nusoap';
        } catch (Exception $e) {
            error_log("NuSOAP Client failed: " . $e->getMessage());
            throw new Exception("Failed to initialize SOAP client: " . $e->getMessage());
        }
    }
    
    /**
     * Check if WSDL is accessible
     */
    private function isWsdlAccessible() {
        $context = stream_context_create([
            'http' => [
                'timeout' => $this->connection_timeout,
                'method' => 'GET'
            ]
        ]);
        
        $headers = @get_headers($this->wsdl_url, 1, $context);
        return $headers && strpos($headers[0], '200') !== false;
    }
    
    /**
     * Validate user credentials via SOAP webservice
     * 
     * @param string $username User login name
     * @param string $password User password
     * @return array|false User data array on success, false on failure
     */
    public function validateUser($username, $password) {
        // Input validation
        if (empty($username) || empty($password)) {
            throw new InvalidArgumentException("Username and password are required");
        }
        
        if (strlen($username) > 50 || strlen($password) > 100) {
            throw new InvalidArgumentException("Username or password too long");
        }
        
        $client_type = $this->initializeClient();
        
        try {
            $parameters = [
                'usuario' => $username,
                'contrasena' => $password
            ];
            
            if ($client_type === 'modern') {
                // Use modern SoapClient
                $result = $this->client->ValidarUsuario($parameters);
                
                // Handle different response formats
                if (is_object($result)) {
                    $result = json_decode(json_encode($result), true);
                }
                
                // Log successful webservice call
                error_log("SOAP Auth Success (Modern): User $username validated");
                
            } else {
                // Use NuSOAP
                $result = $this->client->call("ValidarUsuario", $parameters);
                
                // Check for SOAP faults
                if ($this->client->fault) {
                    error_log("SOAP Fault: " . print_r($result, true));
                    throw new Exception("Authentication service fault");
                }
                
                $error = $this->client->getError();
                if ($error) {
                    error_log("SOAP Error: " . $error);
                    throw new Exception("Authentication service error: " . $error);
                }
                
                // Log successful webservice call
                error_log("SOAP Auth Success (NuSOAP): User $username validated");
            }
            
            // Validate response format
            if (!is_array($result) || empty($result)) {
                error_log("SOAP Auth Failed: Invalid response format for user $username");
                return false;
            }
            
            // Return user data if validation successful
            if (count($result) > 0 && isset($result[0])) {
                $user_data = $result[0];
                
                // Ensure required fields exist
                $required_fields = ['descripcion', 'id_entidad'];
                foreach ($required_fields as $field) {
                    if (!isset($user_data[$field])) {
                        error_log("SOAP Auth Warning: Missing field '$field' for user $username");
                        $user_data[$field] = '';
                    }
                }
                
                return $user_data;
            }
            
            return false;
            
        } catch (SoapFault $e) {
            error_log("SOAP Fault in validateUser: " . $e->getMessage());
            throw new Exception("Authentication service is currently unavailable");
        } catch (Exception $e) {
            error_log("Exception in validateUser: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Test SOAP service connectivity
     * 
     * @return array Status information
     */
    public function testConnection() {
        try {
            $start_time = microtime(true);
            $client_type = $this->initializeClient();
            $connection_time = round((microtime(true) - $start_time) * 1000, 2);
            
            return [
                'status' => 'success',
                'client_type' => $client_type,
                'wsdl_url' => $this->wsdl_url,
                'connection_time_ms' => $connection_time,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'wsdl_url' => $this->wsdl_url,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
    }
    
    /**
     * Get service information
     */
    public function getServiceInfo() {
        try {
            $this->initializeClient();
            
            if (method_exists($this->client, 'getFunctions')) {
                $functions = $this->client->getFunctions();
                $types = method_exists($this->client, 'getTypes') ? $this->client->getTypes() : [];
                
                return [
                    'functions' => $functions,
                    'types' => $types,
                    'wsdl_url' => $this->wsdl_url
                ];
            }
            
            return ['message' => 'Service info not available with current client'];
            
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
