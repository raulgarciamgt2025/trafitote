<?php
   require_once('security.php');
   configure_session_settings();
   session_start();
   
   if (!isset($_SESSION['id_usuario'])) {
       SecurityManager::getInstance()->redirectToLogout();
       exit;
   }
   
   require_once('tools.php');
   
   try {
       $db = DatabaseHelper::getInstance();
       $codigoBarra = SecurityManager::getInstance()->sanitizeInput($_REQUEST['codigo_barra'] ?? '');
       
       if (empty($codigoBarra)) {
           throw new Exception('Código de barra requerido');
       }
       
       // Get package information
       $str_sql = "SET DATEFORMAT DMY; ";   
       $str_sql .= "SELECT a.* ";   
       $str_sql .= "FROM dbo.VW_PAQUETERIA_MIAMI a WITH(NOLOCK) ";      
       $str_sql .= "WHERE a.codigo_barra = ? ";
       
       $stmt = $db->prepare($str_sql);
       $stmt->execute([$codigoBarra]);
       $registro = $stmt->fetch(PDO::FETCH_ASSOC);
       
       // Get call history
       $str_sql = "SET DATEFORMAT DMY; ";   
       $str_sql .= "SELECT a.* ";   
       $str_sql .= "FROM dbo.PAQUETERIA_MIAMI_LLAMADA a WITH(NOLOCK) ";      
       $str_sql .= "WHERE a.codigo_barra = ? ";
       
       $stmt2 = $db->prepare($str_sql);
       $stmt2->execute([$codigoBarra]); 
   



?>

<script language="javascript">
<!--
	function volver()
	{

        window.location = "index2.php?component=paquetes_todos";

    }
    function grabar_llamada()
	{
		if ( document.getElementById("txtTelefono").value == "" )
		{
			alert("Telefono en blanco");
			document.getElementById("txtTelefono").focus();
			return;
		}
		
		if ( document.getElementById("txtObservaciones").value == "" )
		{
			alert("Observaciones en blanco");
			document.getElementById("txtObservaciones").focus();
			return;
		}
		
	$.post("db/grabar_llamada.php",
			   { 
			     codigo_barra: <?php echo $_REQUEST['codigo_barra'] ?>,
				 telefono: document.getElementById("txtTelefono").value,
				 usuario: '<?php echo $_SESSION['nombre_usuario'] ?>',
				 observaciones: document.getElementById("txtObservaciones").value
			   },
			   function (respuesta)
			   {
					if ( respuesta != "Ok" )
						alert(respuesta);
					else
					{
						alert("Llamada grabada");
                        window.location = "index2.php?component=paquetes_todos";

					}
			   }
			  )		
		
	}		
	
//-->
</script>
<form action="" method="post"name="frm_data" id="frm_data">
<table width="50%" border="0" align="center" class="table table-sm" id="tblFiltros" style="width:50%">
     <tr>
      <th colspan="2" class="msgconfig">GRABAR LLAMADA AL CLIENTE</th>
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
     <td width="80%"><div align="left"><?php echo $registro['valor_declaro'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Seccion:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['seccion'] ?>
       <input type="hidden" name="codigo_barra" id="codigo_barra" value="<?php echo $registro['codigo_barra'] ?>"/>
     </div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Teléfono:</div></td>
     <td width="80%"><div align="left">            <input name="txtTelefono" type="text" id="txtTelefono" size="25" maxlength="25" /></div></td>
     </tr>
 <tr>
     <td width="20%"><div align="left">Observaciones:</div></td>
     <td width="80%"><div align="left">
       <textarea name="txtObservaciones" cols="75" rows="5" id="txtObservaciones"></textarea>
     </div></td>
    </tr>
     
     <tr>
       <td colspan="2"><div align="center">
         <input type="button" name="cmdGrabar" id="cmdGrabar" value="Grabar llamada" onClick="javascript:grabar_llamada();" style="width:350px"> &nbsp;&nbsp;&nbsp;
         <input type="button" name="cmdSalir" id="cmdSalir" value="Salir sin cambios" onclick="javascript:volver();" style="width:200px"/>
       </div></td>
     </tr>
     
  </table>
<p>&nbsp;</p>
</form>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-sm table-striped">
    <thead class="thead-dark">
      <tr>
         <th width="6%"><div align="center">No.</div></th>
         <th width="15%"><div align="center">Código Barra</div></th>
         <th width="15%"><div align="center">Fecha Llamada</div></th>
         <th width="14%"><div align="center">Usuario Llamo</div></th>
         <th width="15%"><div align="center">Teléfono</div></th>
         <th width="35%"><div align="center">Observaciones</div></th>
      </tr>
    </thead>
    <tbody>
  <?php
   $contador = 0;
   while ($registro_llamada = $stmt2->fetch(PDO::FETCH_ASSOC))
   {
	 $contador++;
	echo '<tr>';
	echo '<td><div align="center">'.$contador.'</div></td>   ';
    echo '<td><div align="center">'. htmlspecialchars($registro_llamada['codigo_barra'] ?? '', ENT_QUOTES, 'UTF-8').'</div></td>   ';
    echo '<td><div align="center">'. htmlspecialchars($registro_llamada['fecha_llamada'] ?? '', ENT_QUOTES, 'UTF-8').'</div></td>   ';
    echo '<td><div align="center">'. htmlspecialchars($registro_llamada['usuario_llamada'] ?? '', ENT_QUOTES, 'UTF-8').'</div></td>   ';
    echo '<td><div align="center">'. htmlspecialchars($registro_llamada['telefono_llamada'] ?? '', ENT_QUOTES, 'UTF-8').'</div></td>   ';
    echo '<td><div align="center">'. htmlspecialchars($registro_llamada['observaciones'] ?? '', ENT_QUOTES, 'UTF-8').'</div></td>   ';
	echo '</tr>';
  }
  ?>
    </tbody>
</table>

<?php
   } catch (Exception $e) {
       error_log("Error in paquete_llamada_todos.php: " . $e->getMessage());
       echo '<div class="alert alert-danger">Error al cargar los datos: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</div>';
   }
    // Connection is automatically closed when $db goes out of scope
?>
