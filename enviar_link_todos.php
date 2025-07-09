<?php
   session_start();
    if ( !isset($_SESSION['id_usuario']))
    {
        ?><script>alert("Su sesión vencio");self.location ="logout.php";</script><?php
        return;
    }
   include('tools.php');
   $enlace =  mssql_connect($host , $usuario, $contrasena); 
   mssql_select_db($database,$enlace );

   $str_sql = "";
   $str_sql .= "SET DATEFORMAT DMY; ";   
   $str_sql .= "SELECT a.* ";   
   $str_sql .= "FROM dbo.VW_PAQUETERIA_MIAMIV2 a WITH(NOLOCK) ";
   $str_sql .= "WHERE a.codigo_barra = '".$_REQUEST['codigo_barra']."' ";
   $rs =  mssql_query($str_sql,$enlace); 
   $registro = mssql_fetch_array($rs);
   


   $str_sql = "";
   $str_sql .= "SET DATEFORMAT DMY; ";   
   $str_sql .= "SELECT a.* ";   
   $str_sql .= "FROM dbo.PAQUETERIA_MIAMI_EMAIL a WITH(NOLOCK) ";      
   $str_sql .= "WHERE a.codigo_barra = '".$_REQUEST['codigo_barra']."' ";
   $rs2 =  mssql_query($str_sql,$enlace); 


?>

<script language="javascript">
<!--
	function volver()
	{
        window.location = "index2.php?component=paquetes_todos";
	}
    function enviar_link()
	{
		if ( document.getElementById("txtEmail").value == "" )
		{
			alert("Email en blanco");
			document.getElementById("txtEmail").focus();
			return;
		}
        if ( document.getElementById("txtFechaMaxima").value == "" )
        {
            alert("Fecha Maxima en blanco");
            document.getElementById("txtFechaMaxima").focus();
            return;
        }
        if ( document.getElementById("txtHoraMaxima").value == "" )
        {
            alert("Hora Maxima en blanco");
            document.getElementById("txtHoraMaxima").focus();
            return;
        }

	$.post("db/enviar_link_todos.php",
			   { 
			       codigo_barra: <?php echo $_REQUEST['codigo_barra'] ?>,
				   email: document.getElementById("txtEmail").value,
				   tipo_correo: document.getElementById("slTipoCorreo").value,
                   fecha_maxima: document.getElementById("txtFechaMaxima").value,
                   hora_maxima: document.getElementById("txtHoraMaxima").value,
                   tipo: document.getElementById("slTipoCorreo").value
			   },
			   function (respuesta)
			   {
     	    		alert(respuesta);
                   window.location = "index2.php?component=paquetes_todos";
			   }
			  )		
		
	}		
	
//-->
</script>
<form action="" method="post"name="frm_data" id="frm_data">
<table width="50%" border="0" align="center" class="table table-sm" id="tblFiltros" style="width:50%">
     <tr>
      <th colspan="2" class="msgconfig">ENVIAR LINK ENVIO DOCUMENTO</th>
     </tr>
     <tr>
       <td width="20%"><div align="left">Código Barra:</div></td>
       <td width="80%"><div align="left"><?php echo $registro['codigo_barra'] ?></div></td>
     </tr>
     <tr>
     <td width="20%"><div align="left">Fecha:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['fecha_importacion'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Remitente:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['Remitente'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Consignatario:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['Consignatario'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Tracking:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['Tracking'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Piezas:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['piezas'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Peso:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['peso'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Valor Declarado:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['valordeclarado'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Seccion:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['seccion'] ?>
       <input type="hidden" name="codigo_barra" id="codigo_barra" value="<?php echo $registro['codigo_barra'] ?>"/>
     </div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Email:</div></td>
     <td width="80%"><div align="left">            <input name="txtEmail" type="text" id="txtEmail" size="75" maxlength="150" /></div></td>
     </tr>
    <tr>
        <td width="20%"><div align="left">Fecha M&aacute;xima recepci&oacute;n:</div></td>
        <td width="80%"><div align="left"><input name="txtFechaMaxima" type="text" id="txtFechaMaxima" size="10" maxlength="10" /></div></td>
    </tr>
    <tr>
        <td width="20%"><div align="left">Hora M&aacute;xima recepci&oacute;n:</div></td>
        <td width="80%"><div align="left"><input name="txtHoraMaxima" type="text" id="txtHoraMaxima" size="5" maxlength="5" /></div></td>
    </tr>
     <tr>
     <td width="20%"><div align="left">Tipo Correo:</div></td>
     <td width="80%"><div align="left">
       <select name="slTipoCorreo" id="slTipoCorreo">
         <option value="0" selected="selected">Solicitud Contenido/Valor/Documento</option>
         <option value="1">Confirmación Contenido/Valor</option>
       </select>
     </div></td>
     </tr>

     
     <tr>
       <td colspan="2"><div align="center">
         <input type="button" name="cmdEnviar" id="cmdEnviar" value="Enviar Link" onClick="javascript:enviar_link();" style="width:350px"> &nbsp;&nbsp;&nbsp;
         <input type="button" name="cmdSalir" id="cmdSalir" value="Salir sin cambios" onclick="javascript:volver();" style="width:200px"/>
       </div></td>
     </tr>
     </table>
<p>&nbsp;</p>
</form>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-sm table-striped">
    <thead class="thead-dark">
      <tr>
         <th width="6%" ><div align="center">No.</div></th>
         <th width="25%"><div align="center">Código Barra</div></th>
         <th width="16%"><div align="center">Fecha Envi&oacute;</div></th>
         <th width="20%"><div align="center">Usuario Envi&oacute;</div></th>
         <th width="33%"><div align="center">Email</div></th>
      </tr>
    </thead>
    <tbody>
  <?php
   $contador = 0;
   while ( $registro = mssql_fetch_array($rs2) )
   {
	 $contador++;
	echo '<tr>';
	echo '<td><div align="center"><span class="FontTexto">'.$contador.'</div></td>   ';
    echo '<td><div align="center"><span class="FontTexto">'. mb_convert_encoding($registro['codigo_barra'],"UTF-8").'</div></td>   '; 
    echo '<td><div align="center"><span class="FontTexto">'. mb_convert_encoding($registro['fecha_email'],"UTF-8").'</div></td>   '; 
    echo '<td><div align="center"><span class="FontTexto">'. mb_convert_encoding($registro['usuario_email'],"UTF-8").'</div></td>   '; 
    echo '<td><div align="center"><span class="FontTexto">'. mb_convert_encoding($registro['email_envio'],"UTF-8").'</div></td>   '; 
	echo '</tr>';
  }
  ?>
    </tbody>
</table>


<?php
    mssql_close($enlace);
?>
