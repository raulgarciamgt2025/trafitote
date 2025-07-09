<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Loading Test ===<br>";

echo "1. Before loading security.php:<br>";
echo "SecurityManager exists: " . (class_exists('SecurityManager') ? "YES" : "NO") . "<br>";

require_once('security.php');
echo "2. After loading security.php:<br>";
echo "SecurityManager exists: " . (class_exists('SecurityManager') ? "YES" : "NO") . "<br>";

configure_session_settings();
session_start();

require_once('tools.php');
echo "3. After loading tools.php:<br>";
echo "SecurityManager exists: " . (class_exists('SecurityManager') ? "YES" : "NO") . "<br>";

if (class_exists('SecurityManager')) {
    echo "4. Testing SecurityManager methods:<br>";
    try {
        $token = SecurityManager::generateCSRFToken();
        echo "generateCSRFToken: " . substr($token, 0, 10) . "...<br>";
        
        $sanitized = SecurityManager::sanitizeInput('agentes', 'component');
        echo "sanitizeInput('agentes', 'component'): '$sanitized'<br>";
        
        $allowed = SecurityManager::includeComponent('agentes');
        echo "includeComponent('agentes'): " . ($allowed ? "YES" : "NO") . "<br>";
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "SecurityManager not available<br>";
}

?>
