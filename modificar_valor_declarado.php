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
   $str_sql .= "WHERE a.codigo_barra = '".$_REQUEST['codigo_barra']."' AND a.awb <> 'd' ";
   $rs =  mssql_query($str_sql,$enlace); 
   $registro = mssql_fetch_array($rs);
?>

<script language="javascript">
<!--
	function volver()
	{

        window.location = "index2.php?component=paquetes_todos";

    }
    function enlazar_documento()
	{
		if ( document.getElementById("txtValorDeclarado").value == "" )
		{
			alert("Valor declarado en blanco");
			document.getElementById("txtValorDeclarado").focus();
			return;
		}

        if ( document.getElementById("txtMotivo").value == "" )
        {
            alert("Motivo en blanco");
            document.getElementById("txtMotivo").focus();
            return;
        }

		$.post("db/modificar_valor_documento.php",
			   { codigo_barra: <?php echo $_REQUEST['codigo_barra'] ?>,
				 valor_declarado: document.getElementById("txtValorDeclarado").value,
                 motivo: document.getElementById("txtMotivo").value,
				 usuario_grabo: '<?php echo $_SESSION['nombre_usuario'] ?>'
			   },
			   function (respuesta)
			   {
					if ( respuesta != "Ok" )
						alert(respuesta);
					else
					{
						alert("Documento Enlazado");
                        window.location = "index2.php?component=paquetes_todos";
                    }
			   }
			  )
	}
	
//-->
</script>
<table width="50%" border="0" align="center" class="adminform" id="tblFiltros" style="width:50%">
     <tr>
      <th colspan="2" class="msgconfig">MODIFICAR VALOR</th>
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
        <td width="20%"><div align="left">Contenido(Miami):</div></td>
        <td width="80%"><div align="left"><?php echo $registro['contenido'] ?></div></td>
    </tr>
    <tr>
        <td width="20%"><div align="left">Valor Declarado(Miami):</div></td>
        <td width="80%"><div align="left"><?php echo $registro['valordeclarado'] ?></div></td>
    </tr>
    <tr>
        <td width="20%"><div align="left">Contenido(Cliente):</div></td>
        <td width="80%"><div align="left"><?php echo $registro['contenido_cliente'] ?></div></td>
    </tr>
    <tr>
     <td width="20%"><div align="left">Valor Declarado(Cliente):</div></td>
     <td width="80%"><div align="left"><?php echo $registro['valor_declarado'] ?></div></td>
    </tr>

     <tr>
     <td width="20%"><div align="left">Seccion:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['seccion'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Valor declarado:</div></td>
     <td width="80%"><div align="left">
       <input name="txtValorDeclarado" type="text" id="txtValorDeclarado" value="" size="15" />
     </div></td>
     </tr>
    <tr>
        <td width="20%"><div align="left">Motivo del cambio:</div></td>
        <td width="80%"><div align="left">
                <input name="txtMotivo" type="text" id="txtMotivo" value="" size="75" maxlength="200" />
            </div></td>
    </tr>
     
     <tr>
       <td colspan="2"><div align="center">
         <input type="button" name="cmdEnlazar" id="cmdEnlazar" value="Modificar Información" onClick="javascript:enlazar_documento();" style="width:350px"> &nbsp;&nbsp;&nbsp;
         <input type="button" name="cmdSalir" id="cmdSalir" value="Salir sin cambios" onclick="javascript:volver();" style="width:200px"/>
       </div></td>
     </tr>
     </table>
<p>&nbsp;</p>


<?php
    mssql_close($enlace);
?>
