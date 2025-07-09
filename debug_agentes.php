<?php
// Debug the agentes query
require_once('tools.php');

try {
    $db = new DatabaseHelper();
    
    echo "<h2>Debugging Agentes Query</h2>";
    
    // Test the original query
    echo "<h3>1. Testing VW_AGENTE_TRAFICO view:</h3>";
    $agentes = $db->getRecords("SELECT a.* FROM VW_AGENTE_TRAFICO a ORDER BY a.ORDEN ASC");
    echo "Records found: " . count($agentes) . "<br>";
    
    if (count($agentes) > 0) {
        echo "Sample record:<br>";
        echo "<pre>" . print_r($agentes[0], true) . "</pre>";
    } else {
        echo "No records found. Let's check if the view exists:<br>";
        
        // Check if the view exists
        $viewCheck = $db->getRecords("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_NAME = 'VW_AGENTE_TRAFICO'");
        if (count($viewCheck) > 0) {
            echo "✓ View VW_AGENTE_TRAFICO exists<br>";
        } else {
            echo "✗ View VW_AGENTE_TRAFICO does not exist<br>";
            
            // Check for similar views
            echo "<h4>Looking for similar views:</h4>";
            $similarViews = $db->getRecords("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_NAME LIKE '%AGENTE%' OR TABLE_NAME LIKE '%TRAFICO%'");
            foreach ($similarViews as $view) {
                echo "- " . $view['TABLE_NAME'] . "<br>";
            }
            
            // Check for tables instead
            echo "<h4>Looking for similar tables:</h4>";
            $similarTables = $db->getRecords("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND (TABLE_NAME LIKE '%AGENTE%' OR TABLE_NAME LIKE '%TRAFICO%')");
            foreach ($similarTables as $table) {
                echo "- " . $table['TABLE_NAME'] . "<br>";
            }
        }
    }
    
    // Test other queries
    echo "<h3>2. Testing entidad query:</h3>";
    $entidades = $db->getRecords("SELECT a.* FROM entidad a WHERE a.inactivo = 0 ORDER BY a.descripcion ASC");
    echo "Entidades found: " . count($entidades) . "<br>";
    
    echo "<h3>3. Testing PERFIL_SERVICIO query:</h3>";
    $perfiles = $db->getRecords("SELECT a.* FROM dbo.PERFIL_SERVICIO a WHERE a.id_perfil_servicio <> 0 ORDER BY a.descripcion ASC");
    echo "Perfiles found: " . count($perfiles) . "<br>";
    
    echo "<h3>4. Testing CLI_MODALIDAD_SERVICIO query:</h3>";
    $modalidades = $db->getRecords("SELECT a.* FROM dbo.CLI_MODALIDAD_SERVICIO a ORDER BY a.descripcion ASC");
    echo "Modalidades found: " . count($modalidades) . "<br>";
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
