<?php
// Check table structure
require_once('tools.php');

try {
    $dbHelper = new DatabaseHelper();
    
    echo "<h2>Table Structure Analysis</h2>";
    
    // Check clave_acceso table structure
    echo "<h3>clave_acceso table structure:</h3>";
    $sql = "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, CHARACTER_MAXIMUM_LENGTH 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = 'clave_acceso' 
            ORDER BY ORDINAL_POSITION";
    
    $columns = $dbHelper->query($sql);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Nullable</th><th>Max Length</th></tr>";
    
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($col['COLUMN_NAME']) . "</td>";
        echo "<td>" . htmlspecialchars($col['DATA_TYPE']) . "</td>";
        echo "<td>" . htmlspecialchars($col['IS_NULLABLE']) . "</td>";
        echo "<td>" . htmlspecialchars($col['CHARACTER_MAXIMUM_LENGTH'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check sample data with all columns
    echo "<h3>Sample data from clave_acceso:</h3>";
    $sql = "SELECT TOP 5 * FROM clave_acceso WHERE nombre_cuenta IS NOT NULL AND nombre_cuenta != ''";
    $sample = $dbHelper->query($sql);
    
    if (!empty($sample)) {
        echo "<pre>" . print_r($sample, true) . "</pre>";
    } else {
        echo "<p>No records with non-empty nombre_cuenta found. Checking all records:</p>";
        $sql = "SELECT TOP 5 * FROM clave_acceso";
        $sample = $dbHelper->query($sql);
        echo "<pre>" . print_r($sample, true) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
