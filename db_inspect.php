<?php
// Quick database inspection for login troubleshooting
header('Content-Type: text/plain');

require_once('tools.php');

try {
    $dbHelper = new DatabaseHelper();
    echo "=== DATABASE CONNECTION TEST ===\n";
    echo "✓ Connected successfully\n\n";
    
    echo "=== CHECKING TABLES ===\n";
    
    // Check if authentication tables exist
    $tables_sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_NAME IN ('clave_acceso', 'entidad')";
    $tables = $dbHelper->query($tables_sql);
    
    echo "Found " . count($tables) . " authentication tables:\n";
    foreach ($tables as $table) {
        echo "- " . $table['TABLE_NAME'] . "\n";
    }
    echo "\n";
    
    // Check clave_acceso table structure and sample data
    echo "=== CLAVE_ACCESO TABLE ===\n";
    try {
        $columns_sql = "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'clave_acceso' ORDER BY ORDINAL_POSITION";
        $columns = $dbHelper->query($columns_sql);
        
        echo "Columns:\n";
        foreach ($columns as $col) {
            echo "- " . $col['COLUMN_NAME'] . " (" . $col['DATA_TYPE'] . ")" . ($col['IS_NULLABLE'] == 'YES' ? ' NULL' : ' NOT NULL') . "\n";
        }
        echo "\n";
        
        // Get count
        $count_sql = "SELECT COUNT(*) as total FROM clave_acceso";
        $count = $dbHelper->query($count_sql);
        echo "Total records: " . $count[0]['total'] . "\n";
        
        // Get sample data (without passwords)
        $sample_sql = "SELECT TOP 10 id_entidad, nombre_cuenta FROM clave_acceso ORDER BY nombre_cuenta";
        $samples = $dbHelper->query($sample_sql);
        
        echo "Sample accounts:\n";
        foreach ($samples as $sample) {
            echo "- Account: '" . $sample['nombre_cuenta'] . "', Entity ID: " . $sample['id_entidad'] . "\n";
        }
        echo "\n";
        
    } catch (Exception $e) {
        echo "Error accessing clave_acceso: " . $e->getMessage() . "\n\n";
    }
    
    // Check entidad table structure and sample data
    echo "=== ENTIDAD TABLE ===\n";
    try {
        $columns_sql = "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'entidad' ORDER BY ORDINAL_POSITION";
        $columns = $dbHelper->query($columns_sql);
        
        echo "Columns:\n";
        foreach ($columns as $col) {
            echo "- " . $col['COLUMN_NAME'] . " (" . $col['DATA_TYPE'] . ")" . ($col['IS_NULLABLE'] == 'YES' ? ' NULL' : ' NOT NULL') . "\n";
        }
        echo "\n";
        
        // Get count
        $count_sql = "SELECT COUNT(*) as total FROM entidad";
        $count = $dbHelper->query($count_sql);
        echo "Total records: " . $count[0]['total'] . "\n";
        
        // Get sample data
        $sample_sql = "SELECT TOP 10 id_entidad, descripcion FROM entidad ORDER BY descripcion";
        $samples = $dbHelper->query($sample_sql);
        
        echo "Sample entities:\n";
        foreach ($samples as $sample) {
            echo "- ID: " . $sample['id_entidad'] . ", Description: '" . $sample['descripcion'] . "'\n";
        }
        echo "\n";
        
    } catch (Exception $e) {
        echo "Error accessing entidad: " . $e->getMessage() . "\n\n";
    }
    
    // Test the JOIN query
    echo "=== TESTING JOIN QUERY ===\n";
    try {
        $join_sql = "SELECT TOP 5 a.nombre_cuenta, a.id_entidad, b.descripcion 
                     FROM clave_acceso a 
                     INNER JOIN entidad b ON a.id_entidad = b.id_entidad 
                     ORDER BY a.nombre_cuenta";
        $joins = $dbHelper->query($join_sql);
        
        echo "Sample joined data:\n";
        foreach ($joins as $join) {
            echo "- Account: '" . $join['nombre_cuenta'] . "', Entity: '" . $join['descripcion'] . "'\n";
        }
        echo "\n";
        
    } catch (Exception $e) {
        echo "Error testing JOIN: " . $e->getMessage() . "\n\n";
    }
    
    echo "=== TESTING LOGIN QUERY ===\n";
    try {
        // Test the exact login query with a dummy user
        $login_sql = "SELECT a.id_entidad, b.descripcion 
                      FROM clave_acceso a 
                      INNER JOIN entidad b ON a.id_entidad = b.id_entidad 
                      WHERE a.nombre_cuenta = ? AND a.clave_acceso = ?";
        
        // Use a test account (this should return no results, but shouldn't error)
        $result = $dbHelper->query($login_sql, ['nonexistent_user', 'wrong_password']);
        echo "Login query test: ✓ Executed successfully\n";
        echo "Results found: " . count($result) . "\n";
        
    } catch (Exception $e) {
        echo "Login query test: ✗ Failed - " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "=== DATABASE CONNECTION FAILED ===\n";
    echo "Error: " . $e->getMessage() . "\n";
}
?>
