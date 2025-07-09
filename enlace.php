<?php
// Include security configuration first
require_once('security.php');

// Configure session settings before starting session
configure_session_settings();

// Start session
session_start();

// Include tools after session is started
require_once('tools.php');

// Validate session
SecurityManager::validateSession();

try {
    // Validate CSRF token if provided
    if (isset($_REQUEST['csrf_token'])) {
        if (!SecurityManager::validateCSRFToken($_REQUEST['csrf_token'])) {
            echo '<script>alert("Token de seguridad inválido"); window.close();</script>';
            exit;
        }
    }
    
    // Initialize database helper
    $dbHelper = new DatabaseHelper();
    
    // Sanitize input
    $cb = SecurityManager::sanitizeInput($_REQUEST['cb'] ?? '');
    
    if (empty($cb)) {
        echo '<script>alert("Código de barra requerido"); window.close();</script>';
        exit;
    }

    $str_sql = "SET DATEFORMAT DMY; ";   
    $str_sql .= "SELECT ISNULL(a.url_compra,'') as url_compra ";
    $str_sql .= "FROM dbo.paqueteria_miami a  ";
    $str_sql .= "WHERE a.nrogui = ? ";
    
    $params = [$cb];
    $result = $dbHelper->query($str_sql, $params);
    
    if ($result && count($result) > 0 && !empty($result[0]["url_compra"])) {
        $url = urldecode($result[0]["url_compra"]);
        // Validate URL before redirecting
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            header("Location: " . $url);
            exit;
        } else {
            echo '<script>alert("URL no válida"); window.close();</script>';
        }
    } else {
        echo '<script>alert("No se encontró enlace para este código de barra"); window.close();</script>';
    }
    
} catch (Exception $e) {
    error_log("Error in enlace.php: " . $e->getMessage());
    echo '<script>alert("Error interno del servidor"); window.close();</script>';
}
?>

