<?php
// Test login with actual user
require_once('security.php');
configure_session_settings();
session_start();

require_once('tools.php');

echo "<h2>Login Test with Actual User</h2>";

try {
    $securityManager = SecurityManager::getInstance();
    $dbHelper = new DatabaseHelper();
    
    // Test with actual user credentials
    $usuario = "usuario";
    $password = "usuario";
    
    echo "Testing login with: $usuario / $password<br>";
    
    $sql = "SELECT a.id_entidad, b.descripcion 
            FROM clave_acceso a INNER JOIN entidad b ON a.id_entidad = b.id_entidad 
            WHERE a.nombre_cuenta = ? AND a.clave_acceso = ?";
    $params = [$usuario, $password];
    
    $user_data = $dbHelper->query($sql, $params);
    
    if (!empty($user_data)) {
        echo "<div style='color: green;'>";
        echo "✓ Login successful!<br>";
        echo "User data: <pre>" . print_r($user_data[0], true) . "</pre>";
        echo "</div>";
    } else {
        echo "<div style='color: red;'>✗ Login failed - no matching user found</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>";
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "</div>";
}

echo "<h3>Available test users:</h3>";
echo "<ul>";
echo "<li>usuario / usuario</li>";
echo "<li>luis.andrade / luis</li>";
echo "<li>ronald.bone / 53syste</li>";
echo "<li>angel.torres / hilkian</li>";
echo "</ul>";
?>
