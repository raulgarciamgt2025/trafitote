<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing database connection...\n";

require_once 'tools.php';

try {
    echo "Creating DatabaseHelper...\n";
    $db = new DatabaseHelper();
    echo "Database connection successful\n";
    
    // Test simple query first - check table structure
    echo "Checking entidad table structure...\n";
    $result = $db->query('SELECT TOP 5 * FROM dbo.entidad', []);
    echo "Found " . count($result) . " entities\n";
    
    if (!empty($result)) {
        echo "First entity columns: ";
        print_r(array_keys($result[0]));
        echo "First entity data: ";
        print_r($result[0]);
    }
    
    // Test the actual query from panel_avances_data.php
    echo "\nTesting panel query...\n";
    $sql = "SELECT TOP 10
                a.id_entidad_asignado,
                e.descripcion as agente,
                SUM(CASE WHEN a.documento_validado = '1' THEN 1 ELSE 0 END) as validados,
                SUM(CASE WHEN a.documento_validado = '0' OR a.documento_validado IS NULL THEN 1 ELSE 0 END) as no_validados,
                COUNT(*) as paquetes
            FROM dbo.PAQUETERIA_MIAMI a WITH (NOLOCK)
            INNER JOIN dbo.entidad e WITH (NOLOCK) ON a.id_entidad_asignado = e.id_entidad
            WHERE a.id_entidad_asignado IS NOT NULL 
                AND (e.inactivo = 0 OR e.inactivo IS NULL)
            GROUP BY a.id_entidad_asignado, e.descripcion
            HAVING COUNT(*) > 0
            ORDER BY COUNT(*) DESC, e.descripcion";
    
    $packages = $db->query($sql, []);
    echo "Panel query found " . count($packages) . " packages\n";
    
    if (!empty($packages)) {
        echo "First package: ";
        print_r($packages[0]);
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
