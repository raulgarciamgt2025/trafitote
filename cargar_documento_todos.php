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
		// Reset validation classes
		document.getElementById("txtValorDeclarado").classList.remove("is-invalid");
		document.getElementById("txtContenido").classList.remove("is-invalid");
		document.getElementById("txtArchivo").classList.remove("is-invalid");
		
		let hasErrors = false;
		
		if ( document.getElementById("txtValorDeclarado").value == "" )
		{
			document.getElementById("txtValorDeclarado").classList.add("is-invalid");
			document.getElementById("txtValorDeclarado").focus();
			showNotification("Error", "El valor declarado es obligatorio", "error");
			hasErrors = true;
		}
		if ( document.getElementById("txtContenido").value == "" )
		{
			document.getElementById("txtContenido").classList.add("is-invalid");
			if (!hasErrors) document.getElementById("txtContenido").focus();
			showNotification("Error", "El contenido es obligatorio", "error");
			hasErrors = true;
		}		
		if ( document.getElementById("txtArchivo").value == "" )
		{
			document.getElementById("txtArchivo").classList.add("is-invalid");
			if (!hasErrors) document.getElementById("txtArchivo").focus();
			showNotification("Error", "Debe seleccionar un archivo", "error");
			hasErrors = true;
		}
		
		if (hasErrors) return;
		
		// Show loading state
		const btn = document.getElementById("cmdCargar");
		const originalText = btn.value;
		btn.value = "Cargando...";
		btn.disabled = true;
		
		// Add loading spinner to button
		btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Cargando documento...';
		
		document.getElementById("frm_data").submit();
	}
	
	// Function to show notifications
	function showNotification(title, message, type) {
		const toastContainer = document.getElementById('toastContainer') || createToastContainer();
		const toastId = 'toast-' + Date.now();
		
		const toastHTML = `
			<div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
				<div class="d-flex">
					<div class="toast-body">
						<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
						<strong>${title}:</strong> ${message}
					</div>
					<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
				</div>
			</div>
		`;
		
		toastContainer.insertAdjacentHTML('beforeend', toastHTML);
		const toast = new bootstrap.Toast(document.getElementById(toastId));
		toast.show();
		
		// Remove toast after it's hidden
		document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
			this.remove();
		});
	}
	
	// Function to create toast container
	function createToastContainer() {
		const container = document.createElement('div');
		container.id = 'toastContainer';
		container.className = 'toast-container position-fixed top-0 end-0 p-3';
		container.style.zIndex = '9999';
		document.body.appendChild(container);
		return container;
	}
	
//-->
</script>
<div class="container-fluid p-0">
    <div class="row m-0">
        <div class="col-12">
            <div class="card shadow-sm rounded-3 border-0 h-100">
                <div class="card-header text-white rounded-top-3" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-upload me-2"></i>
                        Cargar Documento
                    </h5>
                </div>
                <div class="card-body p-3">
                    <form action="index2.php?component=upload_file_todos" method="post" enctype="multipart/form-data" name="frm_data" id="frm_data">
                        <input type="hidden" name="codigo_barra" id="codigo_barra" value="<?php echo htmlspecialchars($registro['codigo_barra'], ENT_QUOTES, 'UTF-8'); ?>"/>
                        <input type="hidden" name="csrf_token" value="<?php echo SecurityManager::generateCSRFToken(); ?>"/>
                        
                        <!-- Package Information Section -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">Código Barra:</label>
                                <div class="form-control-plaintext border rounded p-2 bg-light small">
                                    <?php echo htmlspecialchars($registro['codigo_barra'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">Fecha:</label>
                                <div class="form-control-plaintext border rounded p-2 bg-light small">
                                    <?php echo htmlspecialchars($registro['fecha_importacion'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">Remitente:</label>
                                <div class="form-control-plaintext border rounded p-2 bg-light small">
                                    <?php echo htmlspecialchars($registro['Remitente'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">Consignatario:</label>
                                <div class="form-control-plaintext border rounded p-2 bg-light small">
                                    <?php echo htmlspecialchars($registro['Consignatario'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary mb-1">Tracking:</label>
                                <div class="form-control-plaintext border rounded p-2 bg-light small">
                                    <?php echo htmlspecialchars($registro['Tracking'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary mb-1">Piezas:</label>
                                <div class="form-control-plaintext border rounded p-2 bg-light small">
                                    <?php echo htmlspecialchars($registro['piezas'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary mb-1">Peso:</label>
                                <div class="form-control-plaintext border rounded p-2 bg-light small">
                                    <?php echo htmlspecialchars($registro['peso'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">Valor Declarado Actual:</label>
                                <div class="form-control-plaintext border rounded p-2 bg-light small">
                                    <?php echo htmlspecialchars($registro['valordeclarado'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">Sección:</label>
                                <div class="form-control-plaintext border rounded p-2 bg-light small">
                                    <?php echo htmlspecialchars($registro['seccion'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Document Upload Section -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="txtValorDeclarado" class="form-label fw-bold text-success mb-1">
                                    <i class="fas fa-dollar-sign me-2"></i>
                                    Nuevo Valor Declarado: *
                                </label>
                                <input name="txtValorDeclarado" type="number" id="txtValorDeclarado" 
                                       class="form-control" step="0.01" min="0" 
                                       placeholder="Ingrese el valor declarado" required/>
                                <div class="invalid-feedback">
                                    Por favor, ingrese el valor declarado.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="txtArchivo" class="form-label fw-bold text-success mb-1">
                                    <i class="fas fa-file-upload me-2"></i>
                                    Documento: *
                                </label>
                                <input type="file" id="txtArchivo" name="txtArchivo" class="form-control" 
                                       accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx" required/>
                                <div class="invalid-feedback">
                                    Por favor, seleccione un archivo.
                                </div>
                                <div class="form-text small">
                                    Formatos permitidos: PDF, JPG, PNG, GIF, DOC, DOCX
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <label for="txtContenido" class="form-label fw-bold text-success mb-1">
                                    <i class="fas fa-edit me-2"></i>
                                    Contenido: *
                                </label>
                                <input name="txtContenido" type="text" id="txtContenido" 
                                       class="form-control" maxlength="200" 
                                       placeholder="Describa el contenido del paquete" required/>
                                <div class="invalid-feedback">
                                    Por favor, describa el contenido del paquete.
                                </div>
                                <div class="form-text small">
                                    Máximo 200 caracteres
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                                    <button type="button" class="btn btn-outline-secondary px-3" onclick="volver()">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Cancelar
                                    </button>
                                    <button type="button" class="btn btn-success px-3" id="cmdCargar" onclick="cargar_documento()">
                                        <i class="fas fa-upload me-2"></i>
                                        Cargar Documento
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    // Connection is automatically closed when $db goes out of scope
?>

