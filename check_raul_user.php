<?php
// Check if the specific user exists in the database
require_once('tools.php');

try {
    $dbHelper = new DatabaseHelper();
    
    echo "<h2>Checking User: raul.garcia</h2>";
    
    // First, let's check if this user exists
    $sql = "SELECT a.nombre_cuenta, a.clave_acceso, a.id_entidad, b.descripcion 
            FROM clave_acceso a 
            LEFT JOIN entidad b ON a.id_entidad = b.id_entidad 
            WHERE a.nombre_cuenta = ?";
    
    $user_check = $dbHelper->query($sql, ['raul.garcia']);
    
    if (!empty($user_check)) {
        echo "<div style='color: green;'>✓ User 'raul.garcia' found in database!</div>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Username</th><th>Password</th><th>Entity ID</th><th>Description</th></tr>";
        foreach ($user_check as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['nombre_cuenta']) . "</td>";
            echo "<td>" . htmlspecialchars($user['clave_acceso']) . "</td>";
            echo "<td>" . htmlspecialchars($user['id_entidad']) . "</td>";
            echo "<td>" . htmlspecialchars($user['descripcion'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check if password matches
        $stored_password = $user_check[0]['clave_acceso'];
        $input_password = 'Dell2011Vesda';
        
        echo "<br><strong>Password comparison:</strong><br>";
        echo "Stored password: '" . htmlspecialchars($stored_password) . "'<br>";
        echo "Input password: '" . htmlspecialchars($input_password) . "'<br>";
        echo "Passwords match: " . ($stored_password === $input_password ? "YES" : "NO") . "<br>";
        
    } else {
        echo "<div style='color: red;'>✗ User 'raul.garcia' NOT found in database!</div>";
        
        // Let's check for similar usernames
        echo "<br><h3>Checking for similar usernames:</h3>";
        $similar_sql = "SELECT nombre_cuenta, id_entidad 
                       FROM clave_acceso 
                       WHERE nombre_cuenta LIKE '%raul%' OR nombre_cuenta LIKE '%garcia%'";
        $similar_users = $dbHelper->query($similar_sql);
        
        if (!empty($similar_users)) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Similar Username</th><th>Entity ID</th></tr>";
            foreach ($similar_users as $user) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['nombre_cuenta']) . "</td>";
                echo "<td>" . htmlspecialchars($user['id_entidad']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No similar usernames found.<br>";
        }
        
        // Show all users for reference
        echo "<br><h3>All available users (first 20):</h3>";
        $all_users_sql = "SELECT TOP 20 nombre_cuenta, id_entidad 
                         FROM clave_acceso 
                         WHERE nombre_cuenta IS NOT NULL AND nombre_cuenta != '' 
                         ORDER BY nombre_cuenta";
        $all_users = $dbHelper->query($all_users_sql);
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Username</th><th>Entity ID</th></tr>";
        foreach ($all_users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['nombre_cuenta']) . "</td>";
            echo "<td>" . htmlspecialchars($user['id_entidad']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
