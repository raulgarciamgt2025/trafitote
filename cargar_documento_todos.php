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


   

?>

<script language="javascript">
<!--
	function volver()
	{

        window.location = "index2.php?component=paquetes_todos";

    }
    function cargar_documento()
	{
		if ( document.getElementById("txtValorDeclarado").value == "" )
		{
			alert("Valor declarado en blanco");
			document.getElementById("txtValorDeclarado").focus();
			return;
		}
		if ( document.getElementById("txtContenido").value == "" )
		{
			alert("Contenido en blanco");
			document.getElementById("txtContenido").focus();
			return;
		}		
		if ( document.getElementById("txtArchivo").value == "" )
		{
			alert("Seleccione archivo");
			document.getElementById("txtArchivo").focus();
			return;
		}		
		document.getElementById("frm_data").submit();
		
	}		
	
//-->
</script>
<form action="index2.php?component=upload_file_todos" method="post" enctype="multipart/form-data" name="frm_data" id="frm_data">
<table width="50%" border="0" align="center" class="table table-sm" id="tblFiltros" style="width:50%">
     <tr>
      <th colspan="2" class="msgconfig">CARGAR DOCUMENTO</th>
     </tr>
     <tr>     </tr>
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
     <td width="20%"><div align="left">Valor Declarado:</div></td>
     <td width="80%"><div align="left">            <input name="txtValorDeclarado" type="text" id="txtValorDeclarado" size="15" maxlength="15" /></div></td>
     </tr>
     <tr>
     <td width="20%"><div align="left">Contenido:</div></td>
     <td width="80%"><div align="left">            <input name="txtContenido" type="text" id="txtContenido" size="75" maxlength="200" /></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Documento:</div></td>
     <td width="80%"><div align="left"><input type="file" value="test" id="txtArchivo" name="txtArchivo"></div></td>
     </tr>

     
     <tr>
       <td colspan="2"><div align="center">
         <input type="button" name="cmdCargar" id="cmdCargar" value="Cargar documento" onClick="javascript:cargar_documento();" style="width:350px"> &nbsp;&nbsp;&nbsp;
         <input type="button" name="cmdSalir" id="cmdSalir" value="Salir sin cambios" onclick="javascript:volver();" style="width:200px"/>
       </div></td>
     </tr>
     </table>
</form>

<?php
    mssql_close($enlace);

