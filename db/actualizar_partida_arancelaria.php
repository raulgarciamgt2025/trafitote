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
if (!isset($_POST['csrf_token']) || !SecurityManager::validateCSRFToken($_POST['csrf_token'])) {
    http_response_code(403);
    echo "Token de seguridad inválido";
    exit();
}

try {
    // Initialize database helper
    $db = new DatabaseHelper();
    
    // Sanitize and validate inputs
    $partida_arancelaria = SecurityManager::sanitizeInput($_REQUEST['partida_arancelaria'], 'int');
    $codigo_barra = SecurityManager::sanitizeInput($_REQUEST['codigo_barra'], 'codigo_barra');
    
    if (empty($codigo_barra) || $partida_arancelaria <= 0) {
        echo "Parámetros inválidos";
        exit();
    }
    
    // Update partida arancelaria with prepared statement
    $sql = "UPDATE dbo.PAQUETERIA_MIAMI 
            SET partida_arancelaria = ?,
                fecha_partida = GETDATE(),
                usuario_partida = ?
            WHERE nrogui = ?";
    
    $params = [
        $partida_arancelaria,
        $_SESSION['id_usuario'],
        $codigo_barra
    ];
    
    $db->executeQuery($sql, $params);
    
    // Update last action
    $sql2 = "UPDATE PAQUETERIA_MIAMI 
             SET ultima_accion = 12,
                 fecha_ultima_accion = GETDATE(),
                 usuario_ultima_accion = ?
             WHERE nrogui = ?";
    
    $db->executeQuery($sql2, [$_SESSION['id_usuario'], $codigo_barra]);
    
    echo "Partida arancelaria actualizada correctamente";
    
} catch (Exception $e) {
    error_log("Error in actualizar_partida_arancelaria.php: " . $e->getMessage());
    http_response_code(500);
    echo "Error del sistema: " . $e->getMessage();
}
?>

    $str_sql = "";
    $str_sql .= "SET DATEFORMAT DMY; ";
    $str_sql .= "INSERT INTO PAQUETERIA_MIAMI_ACCIONES(accion,fecha_accion,usuario_grabo,codigo_barra) ";
    $str_sql .= "VALUES(12,GETDATE(),'".$_SESSION['id_usuario']."','".$_REQUEST['codigo_barra']."') ";
    $resultado = mssql_query($str_sql,$enlace);
    if (!$resultado)
    {
        echo  mssql_get_last_message();
        return;
    }
   
   mssql_close($enlace);
   
   echo "Ok";
   
   
?>