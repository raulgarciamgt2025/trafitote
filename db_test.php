<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Database Connection Test</h3>";

// Test environment variables
echo "<h4>Environment Variables:</h4>";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'not set') . "<br>";
echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? 'not set') . "<br>";
echo "DB_USER: " . ($_ENV['DB_USER'] ?? 'not set') . "<br>";
echo "DB_PASSWORD: " . (isset($_ENV['DB_PASSWORD']) ? '***' : 'not set') . "<br>";
echo "DB_PORT: " . ($_ENV['DB_PORT'] ?? 'not set') . "<br>";

// Test direct connection
$host = $_ENV['DB_HOST'] ?? "192.168.0.2";
$database = $_ENV['DB_NAME'] ?? "db_trans";
$usuario = $_ENV['DB_USER'] ?? "web";
$contrasena = $_ENV['DB_PASSWORD'] ?? "!nf0rm4t!k";
$db_port = $_ENV['DB_PORT'] ?? 1433;

echo "<h4>Database Configuration:</h4>";
echo "Host: $host<br>";
echo "Database: $database<br>";
echo "User: $usuario<br>";
echo "Password: ***<br>";
echo "Port: $db_port<br>";

echo "<h4>Testing Direct PDO Connection:</h4>";

try {
    $dsn = "sqlsrv:Server=$host,$db_port;Database=$database";
    echo "DSN: $dsn<br>";
    
    $pdo = new PDO($dsn, $usuario, $contrasena, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    echo "<span style='color: green;'>✓ PDO Connection successful!</span><br>";
    
    // Test a simple query
    echo "<h4>Testing Simple Query:</h4>";
    $stmt = $pdo->query("SELECT @@VERSION as version");
    $result = $stmt->fetch();
    echo "SQL Server Version: " . $result['version'] . "<br>";
    
    // Test the authentication tables
    echo "<h4>Testing Authentication Tables:</h4>";
    
    // Check if tables exist
    $tables_query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND (TABLE_NAME = 'clave_acceso' OR TABLE_NAME = 'entidad')";
    $stmt = $pdo->query($tables_query);
    $tables = $stmt->fetchAll();
    
    echo "Found tables:<br>";
    foreach ($tables as $table) {
        echo "- " . $table['TABLE_NAME'] . "<br>";
    }
    
    // Test the actual login query structure
    echo "<h4>Testing Login Query Structure:</h4>";
    $login_sql = "SELECT a.id_entidad, b.descripcion 
                  FROM clave_acceso a INNER JOIN entidad b ON a.id_entidad = b.id_entidad 
                  WHERE a.nombre_cuenta = ? AND a.clave_acceso = ?";
    
    try {
        $stmt = $pdo->prepare($login_sql);
        echo "<span style='color: green;'>✓ Login query prepared successfully!</span><br>";
        
        // Test with dummy parameters
        $stmt->execute(['test_user', 'test_pass']);
        echo "<span style='color: green;'>✓ Login query executed successfully (no results expected)!</span><br>";
        
    } catch (Exception $e) {
        echo "<span style='color: red;'>✗ Login query failed: " . $e->getMessage() . "</span><br>";
    }
    
} catch (PDOException $e) {
    echo "<span style='color: red;'>✗ PDO Connection failed: " . $e->getMessage() . "</span><br>";
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ General error: " . $e->getMessage() . "</span><br>";
}

echo "<h4>Testing DatabaseHelper Class:</h4>";

try {
    require_once('tools.php');
    
    $dbHelper = new DatabaseHelper();
    echo "<span style='color: green;'>✓ DatabaseHelper instantiated successfully!</span><br>";
    
    // Test the query method
    $test_sql = "SELECT @@VERSION as version";
    $result = $dbHelper->query($test_sql);
    echo "<span style='color: green;'>✓ DatabaseHelper query method works!</span><br>";
    echo "Result: " . print_r($result, true) . "<br>";
    
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ DatabaseHelper failed: " . $e->getMessage() . "</span><br>";
}

echo "<h4>Testing SecurityManager:</h4>";

try {
    require_once('security.php');
    
    // Test CSRF token generation
    $token = SecurityManager::generateCSRFToken();
    echo "<span style='color: green;'>✓ CSRF token generated: " . substr($token, 0, 10) . "...</span><br>";
    
    // Test input sanitization
    $sanitized = SecurityManager::sanitizeInput("test123", 'alphanumeric');
    echo "<span style='color: green;'>✓ Input sanitization works: '$sanitized'</span><br>";
    
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ SecurityManager failed: " . $e->getMessage() . "</span><br>";
}

echo "<h4>Checking PHP Extensions:</h4>";
echo "PDO: " . (extension_loaded('pdo') ? '<span style="color: green;">✓</span>' : '<span style="color: red;">✗</span>') . "<br>";
echo "PDO_SQLSRV: " . (extension_loaded('pdo_sqlsrv') ? '<span style="color: green;">✓</span>' : '<span style="color: red;">✗</span>') . "<br>";
echo "SQLSRV: " . (extension_loaded('sqlsrv') ? '<span style="color: green;">✓</span>' : '<span style="color: red;">✗</span>') . "<br>";

?>
