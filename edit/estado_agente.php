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

$id_agente = strip_tags($_REQUEST['id_agente']);
$estado = strip_tags($_REQUEST['estado']);

if ( $estado == "ACTIVO")
    $conectado = '0';
else
    $conectado = '1';

$consulta = "";
$consulta .= "UPDATE dbo.FACTURA_AGENTE ";
$consulta .= "SET conectado = $conectado ";
$consulta .= "WHERE id_agente = $id_agente ";
$rs =  mssql_query($consulta,$enlace);
mssql_close($enlace);