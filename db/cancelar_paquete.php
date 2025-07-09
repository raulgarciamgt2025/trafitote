<?php
// Include security configuration first to get session settings function
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
SecurityManager::validateCSRFToken();

// Initialize database helper
$db = new DatabaseHelper();

// Sanitize and validate inputs
$codigo_barra = isset($_REQUEST['codigo_barra']) ? SecurityManager::sanitizeInput($_REQUEST['codigo_barra'], 'codigo_barra') : '';
$motivo_cancelacion = isset($_REQUEST['motivo_cancelacion']) ? SecurityManager::sanitizeInput($_REQUEST['motivo_cancelacion'], 'text') : '';
$usuario_cancelo = isset($_REQUEST['usuario_cancelo']) ? SecurityManager::sanitizeInput($_REQUEST['usuario_cancelo'], 'text') : '';

if (empty($codigo_barra) || empty($motivo_cancelacion) || empty($usuario_cancelo)) {
    echo "Datos incompletos";
    return;
}

try {
    // Start transaction
    $db->beginTransaction();
    
    // Update package cancellation
    $sql1 = "UPDATE dbo.PAQUETERIA_MIAMI 
             SET usuario_cancelo = ?,
                 fecha_cancelo = GETDATE(),
                 motivo_cancelo = ?,
                 factura_cargada = 1,
                 documento_validado = '2',
                 fecha_valido = GETDATE(),
                 observaciones_validacion = 'Documento fue cancelado desde la opcion cancelar paquete',
                 usuario_valido = ?
             WHERE nrogui = ?";
    
    $params1 = [$usuario_cancelo, $motivo_cancelacion, $usuario_cancelo, $codigo_barra];
    $result1 = $db->executeQuery($sql1, $params1);
    
    if (!$result1) {
        throw new Exception("Error al cancelar paquete");
    }
    
    // Update last action
    $sql2 = "UPDATE PAQUETERIA_MIAMI 
             SET ultima_accion = 1,
                 fecha_ultima_accion = GETDATE(),
                 usuario_ultima_accion = ?
             WHERE nrogui = ?";
    
    $params2 = [$_SESSION['id_usuario'], $codigo_barra];
    $result2 = $db->executeQuery($sql2, $params2);
    
    if (!$result2) {
        throw new Exception("Error al actualizar última acción");
    }
    
    // Insert action log
    $sql3 = "INSERT INTO PAQUETERIA_MIAMI_ACCIONES(accion,fecha_accion,usuario_grabo,codigo_barra) 
             VALUES(1,GETDATE(),?,?)";
    
    $params3 = [$_SESSION['id_usuario'], $codigo_barra];
    $result3 = $db->executeQuery($sql3, $params3);
    
    if (!$result3) {
        throw new Exception("Error al registrar acción");
    }
    
    // Commit transaction
    $db->commitTransaction();
    
    echo "Ok";
    
} catch (Exception $e) {
    // Rollback transaction on error
    $db->rollbackTransaction();
    error_log("Error in cancelar_paquete.php: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
}
?>