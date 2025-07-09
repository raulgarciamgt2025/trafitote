<?php
// Test the complete login process with raul.garcia
require_once('tools.php');

echo "<h2>Testing Complete Login Process</h2>";

try {
    $securityManager = SecurityManager::getInstance();
    $dbHelper = new DatabaseHelper();
    
    // Simulate the login process exactly as it happens in login.php
    $original_username = "raul.garcia";
    $password = "Dell2011Vesda";
    
    echo "Original input:<br>";
    echo "Username: '" . htmlspecialchars($original_username) . "'<br>";
    echo "Password: '" . str_repeat('*', strlen($password)) . "'<br><br>";
    
    // Apply the same sanitization as login.php
    $usuario = $securityManager->sanitizeInput($original_username, 'username');
    
    echo "After sanitization:<br>";
    echo "Username: '" . htmlspecialchars($usuario) . "'<br><br>";
    
    // Execute the same query as login.php
    $sql = "SELECT a.id_entidad, b.descripcion 
            FROM clave_acceso a INNER JOIN entidad b ON a.id_entidad = b.id_entidad 
            WHERE a.nombre_cuenta = ? AND a.clave_acceso = ?";
    $params = [$usuario, $password];
    
    echo "Executing login query...<br>";
    $user_data = $dbHelper->query($sql, $params);
    
    if (!empty($user_data)) {
        echo "<div style='color: green; background: #e8f5e8; padding: 10px; border: 1px solid green;'>";
        echo "🎉 <strong>LOGIN SUCCESSFUL!</strong><br>";
        echo "User found: " . htmlspecialchars($user_data[0]['descripcion']) . "<br>";
        echo "Entity ID: " . htmlspecialchars($user_data[0]['id_entidad']) . "<br>";
        echo "</div>";
    } else {
        echo "<div style='color: red; background: #ffe8e8; padding: 10px; border: 1px solid red;'>";
        echo "❌ <strong>LOGIN FAILED</strong><br>";
        echo "No matching user found with these credentials.<br>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>";
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "</div>";
}
?>
