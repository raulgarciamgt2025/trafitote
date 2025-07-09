<?php
// Include security configuration first
require_once('../security.php');

// Configure session settings before starting session
configure_session_settings();

// Start session
session_start();

// Include tools after session is started
require_once('../tools.php');

// Validate session
SecurityManager::validateSession();

// Validate CSRF token
if (!SecurityManager::validateCSRFToken($_GET['csrf_token'] ?? '')) {
    http_response_code(403);
    echo 'CSRF token inválido';
    exit;
}

// Validate and sanitize input
$codigoBarra = SecurityManager::sanitizeInput($_GET['codigoBarra'] ?? '', 'alphanumeric');

if (empty($codigoBarra)) {
    http_response_code(400);
    echo 'Código de barra requerido';
    exit;
}

try {
    // Initialize database helper
    $db = new DatabaseHelper();
    
    // Prepare SQL statement to prevent SQL injection
    $sql = "UPDATE dbo.PAQUETERIA_MIAMI 
            SET factura_cargada = 0, 
                envio_correo = 0, 
                documento_validado = '0' 
            WHERE nrogui = ?";
    
    // Execute the update
    $result = $db->executeStatement($sql, [$codigoBarra]);
    
    if ($result !== false) {
        echo "Ok";
    } else {
        http_response_code(500);
        echo "Error al activar el código de barra";
    }
    
} catch (Exception $e) {
    error_log("Error in db/activar.php: " . $e->getMessage());
    http_response_code(500);
    echo "Error interno del servidor";
}
?>
