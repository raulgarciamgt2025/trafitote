<?php
// Check the exact structure of VW_AGENTE_TRAFICO
require_once('tools.php');

try {
    $db = new DatabaseHelper();
    
    echo "<h2>VW_AGENTE_TRAFICO Structure Analysis</h2>";
    
    // Get one record to see all available columns
    $sample = $db->getRecords("SELECT TOP 1 * FROM VW_AGENTE_TRAFICO");
    
    if (!empty($sample)) {
        echo "<h3>Available columns in VW_AGENTE_TRAFICO:</h3>";
        $record = $sample[0];
        foreach ($record as $column => $value) {
            echo "<strong>$column:</strong> " . htmlspecialchars($value ?? 'NULL') . "<br>";
        }
        
        echo "<h3>Checking for missing columns that agentes.php expects:</h3>";
        $expectedColumns = ['id_agente', 'id_entidad', 'agente', 'perfil_servicio', 'modalidad', 'orden', 'estado', 'fecha_descarga'];
        
        foreach ($expectedColumns as $col) {
            if (array_key_exists($col, $record)) {
                echo "✓ <strong>$col:</strong> " . htmlspecialchars($record[$col] ?? 'NULL') . "<br>";
            } else {
                echo "✗ <strong>$col:</strong> MISSING<br>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
