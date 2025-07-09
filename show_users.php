<?php
// Check users in database
require_once('tools.php');

try {
    $dbHelper = new DatabaseHelper();
    
    echo "<h2>Users in Database</h2>";
    
    $sql = "SELECT TOP 10 a.nombre_cuenta, a.id_entidad, b.descripcion 
            FROM clave_acceso a 
            LEFT JOIN entidad b ON a.id_entidad = b.id_entidad 
            ORDER BY a.nombre_cuenta";
    
    $users = $dbHelper->query($sql);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Username</th><th>Entity ID</th><th>Entity Description</th></tr>";
    
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['nombre_cuenta'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($user['id_entidad'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($user['descripcion'] ?? '') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<p>Total users found: " . count($users) . "</p>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
