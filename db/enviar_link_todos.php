<?php
  session_start();
  if ( !isset($_SESSION['id_usuario']))
  {
    ?><script>alert("Su sesión vencio");self.location ="../logout.php";</script><?php
    return;
  }
  include('../tools.php');

  $str_sql  = "";
  $str_sql .= "INSERT INTO dbo.PAQUETERIA_MIAMI_EMAIL(codigo_barra,usuario_email,fecha_email,email_envio) ";
  $str_sql .= "VALUES ('".$_REQUEST['codigo_barra']."','".$_SESSION['nombre_usuario']."',GETDATE(),'".$_REQUEST['email']."')";

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

    $str_sql  = "";
    $str_sql .= "EXECUTE [CORREO_SIN_FACTURA_EMERGENCIA_MANUAL_INTRANET] '".$_REQUEST['codigo_barra']."','".$_REQUEST['fecha_maxima']."','".$_REQUEST['hora_maxima']."','".$_REQUEST['tipo']."','".$_REQUEST['email']."' ";

    $resultado = mssql_query($str_sql,$enlace);
    if (!$resultado)
    {
        echo  mssql_get_last_message();
        return;
    }

    $str_sql = "";
    $str_sql .= "SET DATEFORMAT DMY; ";
    $str_sql .= "UPDATE PAQUETERIA_MIAMI ";
    $str_sql .= "SET ultima_accion = 6, ";
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
    $str_sql .= "VALUES(6,GETDATE(),'".$_SESSION['id_usuario']."','".$_REQUEST['codigo_barra']."') ";
    $resultado = mssql_query($str_sql,$enlace);
    if (!$resultado)
    {
        echo  mssql_get_last_message();
        return;
    }
   mssql_close($enlace);
   echo "Mensaje enviado";
