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
$usuario_reemplazo = isset($_REQUEST['usuario_reemplazo']) ? SecurityManager::sanitizeInput($_REQUEST['usuario_reemplazo'], 'text') : '';

if (empty($codigo_barra) || empty($usuario_reemplazo)) {
    echo "Datos incompletos";
    return;
}

try {
    // Start transaction
    $db->beginTransaction();
    
    // Update package with replacement flag
    $sql1 = "UPDATE PAQUETERIA_MIAMI 
             SET fecha_reemplazo = GETDATE(),
                 reemplazar_factura = 1,
                 usuario_reemplazo = ?
             WHERE nrogui = ?";
    
    $params1 = [$usuario_reemplazo, $codigo_barra];
    $result1 = $db->executeQuery($sql1, $params1);
    
    if (!$result1) {
        throw new Exception("Error al reemplazar factura");
    }
    
    // Update last action
    $sql2 = "UPDATE PAQUETERIA_MIAMI 
             SET ultima_accion = 10,
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
             VALUES(10,GETDATE(),?,?)";
    
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
    error_log("Error in reemplazar_factura.php: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
}
?>

