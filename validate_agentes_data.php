<?php
/**
 * Direct test of agentes_data.php functionality
 */

// Start session and set up environment
session_start();

// Mock session data
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'test@test.com';
$_SESSION['user_name'] = 'Test User';
$_SESSION['is_authenticated'] = true;
$_SESSION['login_time'] = time();

// Mock POST parameters for DataTables
$_POST = [
    'draw' => 1,
    'start' => 0,
    'length' => 10,
    'search' => ['value' => ''],
    'order' => [
        ['column' => 7, 'dir' => 'asc']
    ],
    'columns' => array_fill(0, 9, ['search' => ['value' => '']]),
    'csrf_token' => 'test_token'
];

echo "=== Testing agentes_data.php directly ===\n";

// Include required files
require_once 'tools.php';

// Override CSRF validation for testing
class TestSecurityManager extends SecurityManager {
    public static function verifyCSRFToken($token) {
        return true; // Always return true for testing
    }
    
    public static function validateSession() {
        // Override to not redirect for testing
        if (!isset($_SESSION['is_authenticated']) || !$_SESSION['is_authenticated']) {
            throw new Exception('User not authenticated');
        }
    }
}

// Test database connection
try {
    $db = new DatabaseHelper();
    echo "✓ Database connection successful\n";
    
    // Test the main query
    $agentes = $db->getRecords("SELECT a.* FROM VW_AGENTE_TRAFICO a ORDER BY a.ORDEN ASC");
    echo "✓ Found " . count($agentes) . " agents\n";
    
    if (count($agentes) > 0) {
        echo "✓ Sample agent data:\n";
        $sample = $agentes[0];
        foreach ($sample as $key => $value) {
            echo "  - $key: " . substr($value, 0, 50) . (strlen($value) > 50 ? '...' : '') . "\n";
        }
    }
    
    // Test the data formatting
    echo "\n=== Testing data formatting ===\n";
    
    $data = [];
    $rowNumber = 1;
    
    foreach (array_slice($agentes, 0, 3) as $row) { // Test first 3 records
        // Sanitize data
        $id = intval($row['id_agente'] ?? $row['ID'] ?? 0);
        $idEntidad = intval($row['id_entidad'] ?? $row['ID_ENTIDAD'] ?? 0);
        $agente = htmlspecialchars($row['agente'] ?? $row['AGENTE'] ?? '', ENT_QUOTES, 'UTF-8');
        $perfil = htmlspecialchars($row['perfil_servicio'] ?? $row['PERFIL'] ?? '', ENT_QUOTES, 'UTF-8');
        $modalidad = htmlspecialchars($row['modalidad'] ?? $row['MODALIDAD'] ?? '', ENT_QUOTES, 'UTF-8');
        $orden = intval($row['orden'] ?? $row['ORDEN'] ?? 0);
        $estado = $row['estado'] ?? $row['ACTIVO'] ?? $row['activo'] ?? '';
        
        echo "Row $rowNumber:\n";
        echo "  ID: $id\n";
        echo "  Entity ID: $idEntidad\n";
        echo "  Agent: $agente\n";
        echo "  Profile: $perfil\n";
        echo "  Modality: $modalidad\n";
        echo "  Order: $orden\n";
        echo "  Status: $estado\n";
        
        $rowNumber++;
    }
    
    echo "\n✓ Data formatting test completed\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
