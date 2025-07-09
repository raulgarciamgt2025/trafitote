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
    
    // Update document validation with prepared statement
    $sql = "UPDATE dbo.PAQUETERIA_MIAMI 
            SET documento_validado = '1',
                fecha_valido = GETDATE(),
                observaciones_validacion = 'PAQUETE VALIDADO NUEVO PROCESO',
                contenido_cliente = CASE 
                                      WHEN ISNULL(contenido_cliente,'') = '' THEN contenido
                                      ELSE contenido_cliente
                                   END,
                valor_declarado = CASE 
                                    WHEN ISNULL(valor_declarado,'') = '' THEN valordeclarado
                                    ELSE valor_declarado
                                 END,
                usuario_valido = ?,
                factura_cargada = 1,
                partida_arancelaria = ?,
                fecha_partida = GETDATE(),
                usuario_partida = ?
            WHERE nrogui = ?";
    
    $params = [
        $_SESSION['id_usuario'],
        $partida_arancelaria,
        $_SESSION['id_usuario'],
        $codigo_barra
    ];
    
    $db->executeQuery($sql, $params);
    
    // Update last action
    $sql2 = "UPDATE PAQUETERIA_MIAMI 
             SET ultima_accion = 11,
                 fecha_ultima_accion = GETDATE(),
                 usuario_ultima_accion = ?
             WHERE nrogui = ?";
    
    $db->executeQuery($sql2, [$_SESSION['id_usuario'], $codigo_barra]);
    
    echo "Ok";
    
} catch (Exception $e) {
    error_log("Error in validar_documento_todos.php: " . $e->getMessage());
    http_response_code(500);
    echo "Error del sistema";
}
?>

    $str_sql = "";
    $str_sql .= "SET DATEFORMAT DMY; ";
    $str_sql .= "INSERT INTO PAQUETERIA_MIAMI_ACCIONES(accion,fecha_accion,usuario_grabo,codigo_barra) ";
    $str_sql .= "VALUES(11,GETDATE(),'".$_SESSION['id_usuario']."','".$_REQUEST['codigo_barra']."') ";
    $resultado = mssql_query($str_sql,$enlace);
    if (!$resultado)
    {
        echo  mssql_get_last_message();
        return;
    }
   
   mssql_close($enlace);
   
   echo "Ok";
   
   
?>