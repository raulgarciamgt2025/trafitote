<?php
   session_start();
   if ( !isset($_SESSION['id_usuario']))
  {
    ?><script>alert("Su sesión vencio");self.location ="../logout.php";</script><?php
    return;
   }
   include('../tools.php');

   $str_sql  = "";
   $str_sql .= "EXECUTE SP_VALIDAR_AUTOMATICO_PARTIDA  ".$_SESSION['id_entidad'];

  

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

   mssql_close($enlace);

?>
<script>
    self.location='../index2.php?component=paquetes_todos';
</script>
