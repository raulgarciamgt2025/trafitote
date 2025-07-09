<?php
  session_start();
  if ( !isset($_SESSION['id_usuario']))
  {
    ?><script>alert("Su sesión vencio");self.location ="../logout.php";</script><?php
    return;
  }
  include('../tools.php');
  $str_sql  = "";
  $str_sql .= "UPDATE dbo.PAQUETERIA_MIAMI ";
  $str_sql .= "SET persona_grabo_factura = '".$_REQUEST['usuario_grabo']."',";
  $str_sql .= "    fecha_factura = GETDATE(),";
  $str_sql .= "    valor_declarado = '".$_REQUEST['valor_declarado']."',";
  $str_sql .= "    archivo_alerta= '".$_REQUEST['url']."',";
  $str_sql .= "    contenido_cliente= '".$_REQUEST['contenido']."',";  
  $str_sql .= "    factura_cargada= 1 ";  
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
   
   
    $str_sql  = "";
	 $str_sql .= "INSERT INTO dbo.PAQUETERIA_MIAMI_DOCUMENTOS(codigo_barra,url,fecha_cargo,usuario_cargo,valor_declarado,contenido) ";
	 $str_sql .= "VALUES ('".$_REQUEST['codigo_barra']."','".$_REQUEST['url']."',GETDATE(),'".$_REQUEST['usuario_grabo']."','".$_REQUEST['valor_declarado']."','".$_REQUEST['contenido']."')";	
	  $resultado = mssql_query($str_sql,$enlace);
   if (!$resultado)
   {
   	 echo  mssql_get_last_message();
	 return;
   }

    $str_sql = "";
    $str_sql .= "SET DATEFORMAT DMY; ";
    $str_sql .= "UPDATE PAQUETERIA_MIAMI ";
    $str_sql .= "SET ultima_accion = 3, ";
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
    $str_sql .= "VALUES(3,GETDATE(),'".$_SESSION['id_usuario']."','".$_REQUEST['codigo_barra']."') ";
    $resultado = mssql_query($str_sql,$enlace);
    if (!$resultado)
    {
        echo  mssql_get_last_message();
        return;
    }

   mssql_close($enlace);
   
   echo "Ok";
   
   
?>