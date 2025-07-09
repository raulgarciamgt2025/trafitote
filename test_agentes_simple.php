<?php
// Simple test to show agentes data without complex formatting
require_once('security.php');
configure_session_settings();
session_start();
require_once('tools.php');

try {
    $db = new DatabaseHelper();
    $agentes = $db->getRecords("SELECT a.* FROM VW_AGENTE_TRAFICO a ORDER BY a.ORDEN ASC");
    
    echo "<h2>Simple Agentes Test (Raw Data)</h2>";
    echo "<p>Total records found: " . count($agentes) . "</p>";
    
    if (count($agentes) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #ccc;'>";
        echo "<th>No.</th><th>ID</th><th>Entidad</th><th>Agente</th><th>Perfil</th><th>Modalidad</th><th>Orden</th><th>Estado</th>";
        echo "</tr>";
        
        $contador = 0;
        foreach ($agentes as $registro) {
            $contador++;
            echo "<tr>";
            echo "<td>$contador</td>";
            echo "<td>" . ($registro['id_agente'] ?? 'N/A') . "</td>";
            echo "<td>" . ($registro['id_entidad'] ?? 'N/A') . "</td>";
            echo "<td>" . ($registro['agente'] ?? 'N/A') . "</td>";
            echo "<td>" . ($registro['perfil_servicio'] ?? 'N/A') . "</td>";
            echo "<td>" . ($registro['modalidad'] ?? 'N/A') . "</td>";
            echo "<td>" . ($registro['orden'] ?? 'N/A') . "</td>";
            echo "<td>" . ($registro['estado'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>No agentes found!</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
