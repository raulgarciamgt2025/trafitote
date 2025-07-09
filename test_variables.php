<?php
// Simple test to verify the database connection fix
require_once('tools.php');

echo "<h2>Database Connection Variable Test</h2>";

// Test the variable names
echo "DB Host: " . $host . "<br>";
echo "DB Name: " . $database . "<br>";
echo "DB User: " . $db_usuario . "<br>";
echo "DB Password: " . (isset($db_contrasena) ? "***" : "NOT SET") . "<br>";
echo "DB Port: " . $db_port . "<br>";

// Set a test login variable to ensure no conflict
$usuario = "testuser123";
echo "<br>Test login variable \$usuario: " . $usuario . "<br>";

try {
    echo "<br>Testing database connection...<br>";
    $pdo = getPDOConnection();
    echo "✓ Database connection successful!<br>";
    echo "✓ No variable name conflicts detected!<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}
?>
