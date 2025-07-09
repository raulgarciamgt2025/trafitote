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

       $str_sql = "SET DATEFORMAT DMY; ";   
       $str_sql .= "SELECT a.* ";   
       $str_sql .= "FROM dbo.VW_PAQUETERIA_MIAMIV2 a WITH(NOLOCK) ";
       $str_sql .= "WHERE a.codigo_barra = ? AND a.awb <> 'd' ";
       
       $stmt = $db->prepare($str_sql);
       $stmt->execute([$codigoBarra]);
       $registro = $stmt->fetch(PDO::FETCH_ASSOC);
       
       $todos = SecurityManager::getInstance()->sanitizeInput($_REQUEST['todos'] ?? '');

       if (isset($_REQUEST['agente'])) {
           $agente = 1;
       } else {
           $agente = 0;
       }


?>

<script language="javascript">
<!--
	function cargar(valor_declarado,url,contenido)
	{
		document.getElementById("txtValorDeclarado").value = valor_declarado;
		document.getElementById("txtUrl").value = url;
		document.getElementById("txtContenido").value = contenido;		
		}
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
		
		if ( document.getElementById("txtUrl").value == "" )
		{
			alert("URL del documento en blanco");
			document.getElementById("txtUrl").focus();
			return;
		}	
		
		if ( document.getElementById("txtContenido").value == "" )
		{
			alert("Contenido en blanco");
			document.getElementById("txtContenido").focus();
			return;
		}			
		$.post("db/enlazar_documento.php",
			   { codigo_barra: <?php echo $_REQUEST['codigo_barra'] ?>,
				 valor_declarado: document.getElementById("txtValorDeclarado").value,
				 url: document.getElementById("txtUrl").value,
				 contenido: document.getElementById("txtContenido").value,				 
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
<table width="50%" border="0" align="center" class="table table-sm" id="tblFiltros" style="width:50%">
     <tr>
      <th colspan="2" class="msgconfig">REVISION DE PREALERTAS</th>
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
     <td width="20%"><div align="left">Valor declarado</div></td>
     <td width="80%"><div align="left">
       <input name="txtValorDeclarado" type="text" id="txtValorDeclarado" value="" size="15" />
     </div></td>
     </tr>
     <tr>
     <td width="20%"><div align="left">Contenido:</div></td>
     <td width="80%"><div align="left">
       <input name="txtContenido" type="text" id="txtContenido" value="" size="75" maxlength="200" />
     </div></td>
     </tr>
     <tr>
     <td width="20%"><div align="left">URL documento:</div></td>
     <td width="80%"><div align="left">
       <textarea name="txtUrl" cols="75" rows="5" id="txtUrl"></textarea>
     </div></td>
     </tr>

     
     <tr>
       <td colspan="2"><div align="center">
         <input type="button" name="cmdEnlazar" id="cmdEnlazar" value="Enlazar documento" onClick="javascript:enlazar_documento();" style="width:350px"> &nbsp;&nbsp;&nbsp;
         <input type="button" name="cmdSalir" id="cmdSalir" value="Salir sin cambios" onclick="javascript:volver();" style="width:200px"/>
       </div></td>
     </tr>
     </table>
<p>&nbsp;</p>
<p>ALERTAS ENCONTRADAS:</p>
<div id="grid_prealertas"></div>

<?php
   } catch (Exception $e) {
       error_log("Error in revisar_prealertas_todos.php: " . $e->getMessage());
       echo '<div class="alert alert-danger">Error al cargar los datos: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</div>';
   }
    // Connection is automatically closed when $db goes out of scope
?>
<script language="javascript">

$.post("http://www.transexpress.com.gt/prealertasV2.php",
			   { tracking: '<?php echo $registro['Tracking'] ?>'
			   },
			   function (respuesta)
			   {
				   document.getElementById("grid_prealertas").innerHTML = respuesta;
			   }
			  )
</script>