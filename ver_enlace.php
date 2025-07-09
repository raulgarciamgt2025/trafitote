<?php
// Include security configuration first to get session settings function
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
            http_response_code(403);
            echo '';
            exit;
        }
    }
    
    // Initialize database helper
    $dbHelper = new DatabaseHelper();
    
    // Sanitize input
    $cb = SecurityManager::sanitizeInput($_REQUEST['cb'] ?? '');
    
    if (empty($cb)) {
        echo '';
        exit;
    }

    $str_sql = "SET DATEFORMAT DMY; ";   
    $str_sql .= "SELECT ISNULL(a.url_compra,'') as url_compra ";
    $str_sql .= "FROM dbo.paqueteria_miami a  ";
    $str_sql .= "WHERE a.nrogui = ? ";
    
    $params = [$cb];
    $result = $dbHelper->query($str_sql, $params);
    
    if ($result && count($result) > 0) {
        echo urldecode($result[0]["url_compra"]);
    } else {
        echo '';
    }
    
} catch (Exception $e) {
    error_log("Error in ver_enlace.php: " . $e->getMessage());
    echo '';
}


?>

