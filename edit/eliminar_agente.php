<?php
    session_start();
    if ( !isset($_SESSION['id_usuario']))
    {
        ?><script>alert("Su sesión vencio");self.location ="../logout.php";</script><?php
        return;
    }
    include('../tools.php');
    $enlace =  mssql_connect($host , $usuario, $contrasena);
    mssql_select_db($database,$enlace );
    $mensaje = "";
    $id_agente = strip_tags($_REQUEST['id_agente']);




    $consulta = "";
    $consulta .= "DELETE FROM dbo.FACTURA_AGENTE ";
    $consulta .= "WHERE id_agente = $id_agente";
    $rs =  mssql_query($consulta,$enlace);
    mssql_close($enlace);