<?php
  session_start();
  include('../tools.php');
  if ( !isset($_SESSION['id_usuario']))
  {
        ?><script>alert("Su sesión vencio");self.location ="../logout.php";</script><?php
        return;
  }
  $str_sql  = "";
  $str_sql .= "UPDATE dbo.PAQUETERIA_MIAMI ";
  $str_sql .= "SET usuario_cancelo = '".$_REQUEST['usuario_cancelo']."',";
  $str_sql .= "    fecha_cancelo = GETDATE(),";
  $str_sql .= "    motivo_cancelo= '".$_REQUEST['motivo_cancelacion']."',";
  $str_sql .= "    factura_cargada= 1, ";  
  $str_sql .= "    documento_validado = '2', ";      
  $str_sql .= "    fecha_valido = GETDATE(), ";
  $str_sql .= "    observaciones_validacion = 'Documento fue cancelado desde la opcion cancelar paquete', ";
  $str_sql .= "    usuario_valido = '".$_REQUEST['usuario_cancelo']."' ";  
  $str_sql .= "WHERE nrogui  = '".$_REQUEST['codigo_barra']."'";
   $enlace =  mssql_connect($host, $usuario, $contrasena);
   if (!$enlace)
   {
   	echo  mssql_get_last_message();
	return;
	}
   mssql_select_db($database,$enlace );

   $resultado = mssql_query($str_sql,$enlace);
   if (!$resultado)
   {
   	 echo  mssql_get_last_message();
	 return;
   }

    $str_sql = "";
    $str_sql .= "SET DATEFORMAT DMY; ";
    $str_sql .= "UPDATE PAQUETERIA_MIAMI ";
    $str_sql .= "SET ultima_accion = 1, ";
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
    $str_sql .= "VALUES(1,GETDATE(),'".$_SESSION['id_usuario']."','".$_REQUEST['codigo_barra']."') ";
    $resultado = mssql_query($str_sql,$enlace);
    if (!$resultado)
    {
        echo  mssql_get_last_message();
        return;
    }

    mssql_close($enlace);
   
   echo "Ok";
   
   
?>