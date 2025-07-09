<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Component Loading Test ===<br>";

// Load necessary files
require_once('security.php');
configure_session_settings();
session_start();
require_once('tools.php');

// Test 1: Check if agentes.php file exists and is readable
echo "<h2>1. File Check</h2>";
$agentesFile = "agentes.php";
echo "File exists: " . (file_exists($agentesFile) ? "YES" : "NO") . "<br>";
echo "File readable: " . (is_readable($agentesFile) ? "YES" : "NO") . "<br>";
echo "File size: " . filesize($agentesFile) . " bytes<br>";

// Test 2: Check SecurityManager methods
echo "<h2>2. SecurityManager Test</h2>";
try {
    $token = SecurityManager::generateCSRFToken();
    echo "✓ CSRF Token generated: " . substr($token, 0, 10) . "...<br>";
    
    $validToken = SecurityManager::validateCSRFToken($token);
    echo "✓ CSRF Token valid: " . ($validToken ? "YES" : "NO") . "<br>";
    
    $sanitized = SecurityManager::sanitizeInput('agentes', 'component');
    echo "✓ Sanitized component: '$sanitized'<br>";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

// Test 3: Simulate the exact flow from index2.php
echo "<h2>3. Simulating index2.php Flow</h2>";
$_REQUEST['component'] = 'agentes';
$_REQUEST['token'] = SecurityManager::generateCSRFToken();

echo "Setting component: " . $_REQUEST['component'] . "<br>";
echo "Setting token: " . substr($_REQUEST['token'], 0, 10) . "...<br>";

if (isset($_REQUEST['component']) && isset($_REQUEST['token'])) {
    echo "✓ Parameters set<br>";
    
    if (SecurityManager::validateCSRFToken($_REQUEST['token'])) {
        echo "✓ Token validation passed<br>";
        
        $component = SecurityManager::sanitizeInput($_REQUEST['component'], 'component');
        echo "✓ Component sanitized: '$component'<br>";
        
        echo "<h3>Including component...</h3>";
        
        // Set $_REQUEST['tokenid'] for compatibility
        $_REQUEST['tokenid'] = $_REQUEST['token'];
        
        ob_start();
        $result = SecurityManager::includeComponent($component);
        $output = ob_get_clean();
        
        echo "Include result: " . ($result ? "SUCCESS" : "FAILED") . "<br>";
        echo "Output captured: " . strlen($output) . " bytes<br>";
        
        if (!empty($output)) {
            echo "<h3>Output Preview (first 1000 chars):</h3>";
            echo "<pre style='border:1px solid #ccc; padding:10px; max-height:300px; overflow:auto;'>";
            echo htmlspecialchars(substr($output, 0, 1000));
            echo "</pre>";
        } else {
            echo "<h3 style='color:red;'>No output captured!</h3>";
        }
        
    } else {
        echo "✗ Token validation failed<br>";
    }
} else {
    echo "✗ Parameters not set properly<br>";
}

?>
