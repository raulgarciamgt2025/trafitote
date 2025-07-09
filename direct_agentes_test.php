<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Direct Agentes Component Test ===<br>";

// Load required files
require_once('security.php');
configure_session_settings();
session_start();
require_once('tools.php');

// Set up authentication - simulate logged in user
$_SESSION['id_usuario'] = 1;
$_SESSION['last_activity'] = time();

// Set up request parameters
$_REQUEST['component'] = 'agentes';
$_REQUEST['token'] = SecurityManager::generateCSRFToken();
$_REQUEST['tokenid'] = $_REQUEST['token']; // For backward compatibility

echo "Testing direct inclusion of agentes.php:<br>";

try {
    ob_start();
    include('agentes.php');
    $output = ob_get_clean();
    
    echo "Output length: " . strlen($output) . " bytes<br>";
    
    if (!empty($output)) {
        // Check if output contains expected elements
        $hasTable = strpos($output, '<table') !== false;
        $hasBootstrap = strpos($output, 'table-striped') !== false;
        $hasData = strpos($output, 'TD_DATOS') !== false;
        
        echo "Contains table: " . ($hasTable ? "YES" : "NO") . "<br>";
        echo "Contains Bootstrap classes: " . ($hasBootstrap ? "YES" : "NO") . "<br>";
        echo "Contains data rows: " . ($hasData ? "YES" : "NO") . "<br>";
        
        // Show preview
        echo "<h3>Output Preview:</h3>";
        echo "<div style='border:1px solid #ccc; padding:10px; max-height:400px; overflow:auto;'>";
        echo $output;
        echo "</div>";
    } else {
        echo "<div style='color:red; font-weight:bold;'>NO OUTPUT GENERATED!</div>";
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "Fatal Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}

?>
