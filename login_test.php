<?php
// Simple login test
require_once('security.php');
configure_session_settings();
session_start();

require_once('tools.php');

// Enable error display for testing
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Login Test</h2>";

try {
    echo "1. Testing SecurityManager...<br>";
    $securityManager = SecurityManager::getInstance();
    echo "✓ SecurityManager instance created<br>";
    
    echo "2. Testing input sanitization...<br>";
    $testUser = $securityManager->sanitizeInput("test123", 'alphanumeric');
    echo "✓ Input sanitization works: '$testUser'<br>";
    
    echo "3. Testing DatabaseHelper...<br>";
    $dbHelper = new DatabaseHelper();
    echo "✓ DatabaseHelper instance created<br>";
    
    echo "4. Testing login query with test user...<br>";
    $sql = "SELECT a.id_entidad, b.descripcion 
            FROM clave_acceso a INNER JOIN entidad b ON a.id_entidad = b.id_entidad 
            WHERE a.nombre_cuenta = ? AND a.clave_acceso = ?";
    
    $user_data = $dbHelper->query($sql, ["test", "test"]);
    echo "✓ Login query executed successfully<br>";
    echo "Found " . count($user_data) . " records<br>";
    
    if (!empty($user_data)) {
        echo "User data: <pre>" . print_r($user_data[0], true) . "</pre>";
    }
    
    echo "<br><strong>All login components working correctly!</strong>";
    
} catch (Exception $e) {
    echo "<div style='color: red;'>";
    echo "<strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<strong>Stack trace:</strong><br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
?>
