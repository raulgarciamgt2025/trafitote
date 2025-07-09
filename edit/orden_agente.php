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

    $serverName = $host;
    $connectionOptions = array(
        "Database" => $db ,
        "Uid" => $usuario,
        "PWD" => $contrasena
    );


    $id_agente = strip_tags($_REQUEST['id_agente']);
    $orden = strip_tags($_REQUEST['orden']);



    $parametros = array($orden,$id_agente);

    $consulta = "";
    $consulta .= "UPDATE dbo.FACTURA_AGENTE ";
    $consulta .= "SET orden = $orden ";
    $consulta .= "WHERE id_agente = $id_agente ";
    $rs =  mssql_query($consulta,$enlace);
    mssql_close($enlace);