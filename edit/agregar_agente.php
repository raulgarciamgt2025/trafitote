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
    $orden = strip_tags($_REQUEST['orden']);
    $perfil = strip_tags($_REQUEST['perfil']);
    $modalidad = strip_tags($_REQUEST['modalidad']);
    $conectado = 1;

    $parametros = array($id_agente,$orden,$conectado);

    $consulta = "";
    $consulta .= "INSERT INTO dbo.FACTURA_AGENTE";
    $consulta .= "(";
    $consulta .= "    id_entidad,";
    $consulta .= "    orden,";
    $consulta .= "    conectado,";
    $consulta .= "    id_perfil,";
    $consulta .= "    id_modalidad";
    $consulta .= ")";
    $consulta .= "VALUES";
    $consulta .= "($id_agente,";
    $consulta .= "$orden,";
    $consulta .= "$conectado,";
    if ( $perfil == "0")
        $consulta .= "NULL,";
    else
        $consulta .= "$perfil,";
    if ( $modalidad == "0")
        $consulta .= "NULL";
    else
        $consulta .= "$modalidad";
    $consulta .= ")";
    $rs =  mssql_query($consulta,$enlace);
    mssql_close($enlace);
