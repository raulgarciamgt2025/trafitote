<?php
session_start();
include('../tools.php');

$enlace =  mssql_connect($host , $usuario, $contrasena);
mssql_select_db($database,$enlace );


$consulta  = "";
$consulta .= "SELECT a.* ";
$consulta .= "FROM VW_PARTIDA_ARANCELARIA_MAS_USADA  a ";
$consulta .= "WHERE a.partida_arancelaria LIKE '%".strip_tags($_REQUEST["filtro"])."%' ";

$rs =  mssql_query($consulta,$enlace);

$arrData = array();;
while ( $registro = mssql_fetch_array($rs) )
{
    $arrData[] = array_map('utf8_encode', $registro);
}
echo json_encode($arrData,JSON_UNESCAPED_UNICODE);


