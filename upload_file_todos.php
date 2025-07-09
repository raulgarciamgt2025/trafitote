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

try {
    // Initialize database helper
    $dbHelper = new DatabaseHelper();
    
    // Sanitize inputs
    $codigo_barra = SecurityManager::sanitizeInput($_REQUEST['codigo_barra'] ?? '');
    $valor_declarado = SecurityManager::sanitizeInput($_REQUEST['txtValorDeclarado'] ?? '', 'numeric');
    $contenido = SecurityManager::sanitizeInput($_REQUEST['txtContenido'] ?? '');
    
    if (empty($codigo_barra)) {
        throw new Exception("Código de barra requerido");
    }

	$pdf_target_path_folder = "/var/www/html/documentos/";
	$year = date("Y");
	$month = date("M");
	$pdf_target_path_part1 = "/var/www/html/documentos/".$year."/";

	if (!is_dir($pdf_target_path_part1))
	{
	 mkdir($pdf_target_path_part1, 0777, true);
	 chmod($pdf_target_path_part1, 0777);
	}

	$pdf_target_path2 = "/var/www/html/documentos/".$year."/".$month."/";

	if (!is_dir($pdf_target_path2))
	{
	 mkdir($pdf_target_path2, 0777, true);
	 chmod($pdf_target_path2, 0777);
	}

    $emailbody_or_url = "";
	$fecha = date("YmdHis");

	if ( $_FILES["txtArchivo"]['name'] != "" )
	{
	  // Validate file type for security
	  $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
	  if (!in_array($_FILES["txtArchivo"]['type'], $allowed_types)) {
	      throw new Exception("Tipo de archivo no permitido");
	  }
	  
	  // Sanitize filename
	  $filename = preg_replace('/[^a-zA-Z0-9.-]/', '_', $_FILES["txtArchivo"]['name']);
	  
	  $archivo_tmp =  "/var/www/html/documentos/" . $filename;
	  $archivo =  "documentos/" . $filename;
	  
	  if (!move_uploaded_file($_FILES['txtArchivo']['tmp_name'], $archivo_tmp)) {
	      throw new Exception("El archivo no fue cargado correctamente");
	  }
	  
 	  $archivo_original =  "/var/www/html/".$archivo;
	  $archivo_destino =  $pdf_target_path2  . 'CB_'.$codigo_barra.'_'.$fecha.'_'.'1.pdf';
	  $emailbody_or_url = "http://205.211.224.182/documentos/".$year."/".$month."/". 'CB_'.$codigo_barra.'_'.$fecha.'_'.'1.pdf';
	  
	  copy($archivo_original, $archivo_destino);
	  unlink($archivo_original);
	}

	 // Update main package record
	 $str_sql = "UPDATE dbo.PAQUETERIA_MIAMI 
	             SET persona_grabo_factura = ?,
	                 fecha_factura = GETDATE(),
	                 archivo_alerta = ?,
	                 valor_declarado = ?,
	                 contenido_cliente = ?,
	                 factura_cargada = 1 
	             WHERE nrogui = ?";
	             
	 $params = [$_SESSION['nombre_usuario'], $emailbody_or_url, $valor_declarado, $contenido, $codigo_barra];
	 $dbHelper->executeQuery($str_sql, $params);

	 // Insert document record
	 $str_sql = "INSERT INTO dbo.PAQUETERIA_MIAMI_DOCUMENTOS(codigo_barra,url,fecha_cargo,usuario_cargo,valor_declarado,contenido) 
	             VALUES (?,?,GETDATE(),?,?,?)";
	             
	 $params = [$codigo_barra, $emailbody_or_url, $_SESSION['nombre_usuario'], $valor_declarado, $contenido];
	 $dbHelper->executeQuery($str_sql, $params);

    // Update last action
    $str_sql = "SET DATEFORMAT DMY; 
                UPDATE PAQUETERIA_MIAMI 
                SET ultima_accion = 2, 
                    fecha_ultima_accion = GETDATE(), 
                    usuario_ultima_accion = ?  
                WHERE nrogui = ?";
                
    $params = [$_SESSION['id_usuario'], $codigo_barra];
    $dbHelper->executeQuery($str_sql, $params);
    
} catch (Exception $e) {
    error_log("Error in upload_file_todos.php: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
    exit;
}





?>

<script language="javascript">
<!--
	function volver()
	{
        window.location = "index2.php?component=paquetes_todos";
	}


//-->
</script>
<form  method="post" enctype="multipart/form-data" name="frm_data" id="frm_data">
<table width="50%" border="0" align="center" class="adminform" id="tblFiltros" style="width:50%">
     <tr>
      <th class="msgconfig">DOCUMENTO CARGADO</th>
     </tr>

     <tr>
       <td><div align="center">
         <p>&nbsp;</p>
         <p>DOCUMENTO CARGADO CORRECTAMENTE.</p>
         <p>&nbsp;</p>
       </div>         <div align="left"></div></td>
     </tr>


     <tr>
       <td><div align="center">
         <input type="button" name="cmdRetornar" id="cmdRetornar" value="Volver" onClick="javascript:volver();" style="width:350px"> &nbsp;&nbsp;&nbsp;</div></td>
     </tr>
     </table>
</form>

<?php
    // Connection is automatically closed when $db goes out of scope
?>
