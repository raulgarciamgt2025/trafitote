<?php
// Include security configuration first to get session settings function
require_once('security.php');

// Configure session settings before starting session
configure_session_settings();

// Start session
session_start();

// Include tools after session is started
require_once('tools.php');

// Validate session
SecurityManager::validateSession();

// Initialize database helper
$db = new DatabaseHelper();

// Sanitize and validate input
$codigo_barra = isset($_REQUEST['codigo_barra']) ? SecurityManager::sanitizeInput($_REQUEST['codigo_barra'], 'codigo_barra') : '';

if (empty($codigo_barra)) {
    echo '<div class="alert alert-danger">Código de barra requerido</div>';
    return;
}

try {
    // Get package information with prepared statement
    $consulta = "SELECT a.* 
                 FROM dbo.VW_PAQUETERIA_MIAMIV2 a WITH(NOLOCK) 
                 WHERE a.codigo_barra = ?";
    
    $registros = $db->getRecords($consulta, [$codigo_barra]);
    
    if (empty($registros)) {
        echo '<div class="alert alert-warning">No se encontró el paquete con el código de barra especificado</div>';
        return;
    }
    
    $registro = $registros[0];
    
} catch (Exception $e) {
    error_log("Error in cargar_documento_todos.php: " . $e->getMessage());
    echo '<div class="alert alert-danger">Error al cargar los datos del paquete</div>';
    return;
}

?>

<script language="javascript">
<!--
	function volver()
	{
        // Close the modal using the global function
        if (typeof window.closeActionModal === 'function') {
            window.closeActionModal();
        } else {
            // Fallback: close modal directly
            const modal = document.getElementById('actionModal');
            if (modal) {
                const bootstrapModal = bootstrap.Modal.getInstance(modal);
                if (bootstrapModal) {
                    bootstrapModal.hide();
                }
            }
        }

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
     <td width="80%"><div align="left"><?php echo htmlspecialchars($registro['codigo_barra'], ENT_QUOTES, 'UTF-8'); ?></div></td>
     </tr>
     <tr>
     <td width="20%"><div align="left">Fecha:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($registro['fecha_importacion'], ENT_QUOTES, 'UTF-8'); ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Remitente:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($registro['Remitente'], ENT_QUOTES, 'UTF-8'); ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Consignatario:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($registro['Consignatario'], ENT_QUOTES, 'UTF-8'); ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Tracking:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($registro['Tracking'], ENT_QUOTES, 'UTF-8'); ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Piezas:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($registro['piezas'], ENT_QUOTES, 'UTF-8'); ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Peso:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($registro['peso'], ENT_QUOTES, 'UTF-8'); ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Valor Declarado:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($registro['valordeclarado'], ENT_QUOTES, 'UTF-8'); ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Seccion:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($registro['seccion'], ENT_QUOTES, 'UTF-8'); ?>
       <input type="hidden" name="codigo_barra" id="codigo_barra" value="<?php echo htmlspecialchars($registro['codigo_barra'], ENT_QUOTES, 'UTF-8'); ?>"/>
       <input type="hidden" name="csrf_token" value="<?php echo SecurityManager::generateCSRFToken(); ?>"/>
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
         <input type="button" name="cmdCargar" id="cmdCargar" value="Cargar documento" onClick="javascript:cargar_documento();" class="btn btn-primary" style="width:350px"> &nbsp;&nbsp;&nbsp;
         <input type="button" name="cmdSalir" id="cmdSalir" value="Salir sin cambios" onclick="javascript:volver();" class="btn btn-secondary" style="width:200px"/>
       </div></td>
     </tr>
     </table>
</form>

<?php
    // Connection is automatically closed when $db goes out of scope
?>

