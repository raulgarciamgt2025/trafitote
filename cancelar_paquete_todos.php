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
    function cancelar_paquete()
	{
		if ( document.getElementById("txt_criterio").value == "" )
		{
			alert("Razon de cancelacion en blanco");
			document.getElementById("txt_criterio").focus();
			return;
		}
		
		$.post("db/cancelar_paquete.php",
			   { codigo_barra: <?php echo $_REQUEST['codigo_barra'] ?>,
				 motivo_cancelacion: document.getElementById("txt_criterio").value,
				 usuario_cancelo: '<?php echo $_SESSION['nombre_usuario'] ?>'
			   },
			   function (respuesta)
			   {
					if ( respuesta != "Ok" )
						alert(respuesta);
					else
					{
						alert("Paquete cancelado");
                        window.location = "index2.php?component=paquetes_todos";
                    }
			   }
			  )
		
	}		
	
//-->
</script>
<table width="50%" border="0" align="center" class="table table-sm" id="tblFiltros" style="width:50%">
     <tr>
      <th colspan="2" class="msgconfig">CANCELACIÓN DE PAQUETE</th>
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
     <td width="80%"><div align="left"><?php echo $registro['valor_declaro'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Seccion:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['seccion'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Motivo Cancelación:</div></td>
     <td width="80%"><div align="left">
       <textarea name="txt_criterio" cols="75" rows="5" id="txt_criterio"></textarea>
     </div></td>
     </tr>

     
     <tr>
       <td colspan="2"><div align="center">
         <input type="button" name="cmdCancelar" id="cmdCancelar" value="Grabar Cancelación" onClick="javascript:cancelar_paquete();" style="width:350px"> &nbsp;&nbsp;&nbsp;
         <input type="button" name="cmdSalir" id="cmdSalir" value="Salir sin cambios" onclick="javascript:volver();" style="width:200px"/>
       </div></td>
     </tr>
     </table>


<?php
    mssql_close($enlace);
?>
