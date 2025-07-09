<?php
// Simple login debug script
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug.log');

echo "<h3>Login Debug Test</h3>";

// Include required files
require_once('security.php');
require_once('tools.php');

// Simulate login attempt with a test user
$test_user = 'test_user';
$test_password = 'test_password';

echo "<h4>Step 1: Test Database Connection</h4>";
try {
    $dbHelper = new DatabaseHelper();
    echo "<span style='color: green;'>✓ DatabaseHelper created successfully</span><br>";
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ DatabaseHelper failed: " . $e->getMessage() . "</span><br>";
    die();
}

echo "<h4>Step 2: Test Query Method</h4>";
try {
    $test_sql = "SELECT @@VERSION as version";
    $result = $dbHelper->query($test_sql);
    echo "<span style='color: green;'>✓ Query method works</span><br>";
    echo "SQL Server Version: " . $result[0]['version'] . "<br>";
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ Query method failed: " . $e->getMessage() . "</span><br>";
    die();
}

echo "<h4>Step 3: Test Table Structure</h4>";
try {
    // Check if tables exist and get their structure
    $tables_sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND (TABLE_NAME = 'clave_acceso' OR TABLE_NAME = 'entidad')";
    $tables = $dbHelper->query($tables_sql);
    
    echo "Found tables: " . count($tables) . "<br>";
    foreach ($tables as $table) {
        echo "- " . $table['TABLE_NAME'] . "<br>";
        
        // Get column info for each table
        $columns_sql = "SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?";
        $columns = $dbHelper->query($columns_sql, [$table['TABLE_NAME']]);
        
        echo "&nbsp;&nbsp;Columns:<br>";
        foreach ($columns as $column) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;- " . $column['COLUMN_NAME'] . " (" . $column['DATA_TYPE'] . ")<br>";
        }
    }
    
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ Table structure check failed: " . $e->getMessage() . "</span><br>";
}

echo "<h4>Step 4: Test Login Query Structure</h4>";
try {
    $login_sql = "SELECT a.id_entidad, b.descripcion 
                  FROM clave_acceso a 
                  INNER JOIN entidad b ON a.id_entidad = b.id_entidad 
                  WHERE a.nombre_cuenta = ? AND a.clave_acceso = ?";
    
    // Test with dummy parameters
    $result = $dbHelper->query($login_sql, [$test_user, $test_password]);
    echo "<span style='color: green;'>✓ Login query executed successfully</span><br>";
    echo "Results found: " . count($result) . "<br>";
    
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ Login query failed: " . $e->getMessage() . "</span><br>";
}

echo "<h4>Step 5: Show Sample Data</h4>";
try {
    // Get some sample data from clave_acceso (without passwords)
    $sample_sql = "SELECT TOP 5 id_entidad, nombre_cuenta FROM clave_acceso";
    $samples = $dbHelper->query($sample_sql);
    
    echo "Sample users in clave_acceso:<br>";
    foreach ($samples as $sample) {
        echo "- ID: " . $sample['id_entidad'] . ", Account: " . $sample['nombre_cuenta'] . "<br>";
    }
    
    // Get some sample data from entidad
    $entity_sql = "SELECT TOP 5 id_entidad, descripcion FROM entidad";
    $entities = $dbHelper->query($entity_sql);
    
    echo "<br>Sample entities:<br>";
    foreach ($entities as $entity) {
        echo "- ID: " . $entity['id_entidad'] . ", Description: " . $entity['descripcion'] . "<br>";
    }
    
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ Sample data query failed: " . $e->getMessage() . "</span><br>";
}

echo "<h4>Step 6: Check Error Log</h4>";
$log_file = __DIR__ . '/debug.log';
if (file_exists($log_file)) {
    echo "Recent error log entries:<br>";
    echo "<pre>" . htmlspecialchars(file_get_contents($log_file)) . "</pre>";
} else {
    echo "No debug.log file found<br>";
}

// Check PHP error log
$php_error_log = ini_get('error_log');
if ($php_error_log && file_exists($php_error_log)) {
    echo "<br>Recent PHP error log entries (last 20 lines):<br>";
    $lines = file($php_error_log);
    $recent_lines = array_slice($lines, -20);
    echo "<pre>" . htmlspecialchars(implode('', $recent_lines)) . "</pre>";
} else {
    echo "<br>PHP error log not accessible or not found<br>";
}

?>
