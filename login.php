<?php
// Include security configuration first to get session settings function
require_once('security.php');

// Configure session settings before starting session
configure_session_settings();

// Start session
session_start();

require_once('tools.php');

// Enable detailed error reporting for debugging
if (!defined('APP_ENV')) {
    define('APP_ENV', 'development'); // Change to 'production' when ready
}

if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Validate CSRF token
$securityManager = SecurityManager::getInstance();
if (!isset($_POST['csrf_token']) || !SecurityManager::validateCSRFToken($_POST['csrf_token'])) {
    header("Location: index.php?error=invalid_token");
    exit();
}

// Rate limiting - simple implementation
$max_attempts = 5;
$lockout_time = 300; // 5 minutes

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt'] = 0;
}

if ($_SESSION['login_attempts'] >= $max_attempts) {
    $time_passed = time() - $_SESSION['last_attempt'];
    if ($time_passed < $lockout_time) {
        $remaining = $lockout_time - $time_passed;
        header("Location: index.php?error=locked&remaining=" . $remaining);
        exit();
    } else {
        // Reset attempts after lockout period
        $_SESSION['login_attempts'] = 0;
    }
}

// Sanitize input
$usuario = $securityManager->sanitizeInput($_POST['usuarioLogin'], 'username');
$password = $_POST['passwordLogin']; // Don't sanitize passwords, just validate length

// Input validation
if (empty($usuario) || empty($password)) {
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt'] = time();
    header("Location: index.php?error=empty_fields");
    exit();
}

if (strlen($password) > 100) { // Prevent buffer overflow attacks
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt'] = time();
    header("Location: index.php?error=invalid_credentials");
    exit();
}

try {
    // Initialize database helper
    error_log("Login: About to create DatabaseHelper instance");
    $dbHelper = new DatabaseHelper();
    error_log("Login: DatabaseHelper instance created successfully");
    
    // Log the database connection attempt
    error_log("Login attempt: Initializing database helper for user: " . $usuario);
    
    // Validate user credentials via database with entity join
    $sql = "SELECT a.id_entidad, b.descripcion 
            FROM clave_acceso a INNER JOIN entidad b ON a.id_entidad = b.id_entidad 
            WHERE a.nombre_cuenta = ? AND a.clave_acceso = ?";
    $params = [$usuario, $password];
    
    error_log("Login attempt: Executing query for user: " . $usuario);
    $user_data = $dbHelper->query($sql, $params);
    
	
    error_log("Login attempt: Query executed, result count: " . (is_array($user_data) ? count($user_data) : 'not array'));
    
    if (!empty($user_data)) {
        // Successful login
        $_SESSION['login_attempts'] = 0; // Reset attempts
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        $user_info = $user_data[0]; // Get first record
        
        $_SESSION['id_usuario'] = $usuario;
        $_SESSION['nombre_usuario'] = $user_info['descripcion'] ?? $usuario;
        $_SESSION['id_entidad'] = $user_info['id_entidad'] ?? '';
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        
        // Initialize other session variables
        $_SESSION['fac_nombre_cliente'] = "";
        $_SESSION['fac_direccion'] = "";
        $_SESSION['fac_telefono'] = "";
        $_SESSION['fac_nit'] = "";
        $_SESSION['forma_pago'] = "";
        $_SESSION['emisor'] = "";
        $_SESSION['moneda'] = "";
        
        // Log successful login
        error_log("Successful database login for user: " . $usuario . " from IP: " . $_SERVER['REMOTE_ADDR']);
        
        header("Location: index2.php");
        exit();
    } else {
        // Failed login
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt'] = time();
        
        // Log failed login attempt
        error_log("Failed database login attempt for user: " . $usuario . " from IP: " . $_SERVER['REMOTE_ADDR']);
        
        header("Location: index.php?error=invalid_credentials");
        exit();
    }
    
} catch (Exception $e) {
    error_log("Login Exception: " . $e->getMessage());
    error_log("Login Exception Stack Trace: " . $e->getTraceAsString());
    
    // Show more detailed error in development
    if (defined('APP_ENV') && APP_ENV === 'development') {
        echo '<div class="alert alert-danger">Login Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</div>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
    } else {
        header("Location: index.php?error=system_error");
    }
    exit();
}
?>