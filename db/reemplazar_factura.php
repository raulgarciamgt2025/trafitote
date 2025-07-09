<?php
  session_start();
  if ( !isset($_SESSION['id_usuario']))
  {
    ?><script>alert("Su sesión vencio");self.location ="../logout.php";</script><?php
    return;
  }
   include('../tools.php');
  $codigo_barra = $_REQUEST['codigo_barra'];
  $usuario_reemplazo = $_REQUEST['usuario_reemplazo'];

   $enlace =  mssql_connect($host , $usuario, $contrasena);
   mssql_select_db($database,$enlace );
   
   $str_sql = "";
   $str_sql .= "SET DATEFORMAT DMY; ";   
   $str_sql .= "UPDATE PAQUETERIA_MIAMI ";   
   $str_sql .= "SET ";      
   $str_sql .= "    fecha_reemplazo = GETDATE(), ";
   $str_sql .= "    reemplazar_factura = 1, ";
   $str_sql .= "    usuario_reemplazo = '".$usuario_reemplazo."' ";
   $str_sql .= "WHERE nrogui = '".$codigo_barra."' ";
   $rs =  mssql_query($str_sql,$enlace);
    if (!$rs)
    {
        echo  mssql_get_last_message();
        return;
    }

    $str_sql = "";
    $str_sql .= "SET DATEFORMAT DMY; ";
    $str_sql .= "UPDATE PAQUETERIA_MIAMI ";
    $str_sql .= "SET ultima_accion = 10, ";
    $str_sql .= "    fecha_ultima_accion = GETDATE(), ";
    $str_sql .= "    usuario_ultima_accion = '".$_SESSION['id_usuario']."'  ";
    $str_sql .= "WHERE nrogui = '".$_REQUEST['codigo_barra']."'";
    $resultado = mssql_query($str_sql,$enlace);
    if (!$resultado)
    {
        echo  mssql_get_last_message();
        return;
    }

    $str_sql = "";
    $str_sql .= "SET DATEFORMAT DMY; ";
    $str_sql .= "INSERT INTO PAQUETERIA_MIAMI_ACCIONES(accion,fecha_accion,usuario_grabo,codigo_barra) ";
    $str_sql .= "VALUES(10,GETDATE(),'".$_SESSION['id_usuario']."','".$_REQUEST['codigo_barra']."') ";
    $resultado = mssql_query($str_sql,$enlace);
    if (!$resultado)
    {
        echo  mssql_get_last_message();
        return;
    }

   mssql_close($enlace);
   
 

?>

