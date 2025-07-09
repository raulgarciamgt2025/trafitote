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
                 WHERE a.codigo_barra = ? AND a.awb <> 'd'";
    
    $registros = $db->getRecords($consulta, [$codigo_barra]);
    
    if (empty($registros)) {
        echo '<div class="alert alert-warning">No se encontró el paquete con el código de barra especificado</div>';
        return;
    }
    
    $registro = $registros[0];
    
} catch (Exception $e) {
    error_log("Error in cancelar_paquete_todos.php: " . $e->getMessage());
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
    
    function cancelar_paquete()
	{
		if ( document.getElementById("txt_criterio").value == "" )
		{
			// Show Bootstrap validation
			document.getElementById("txt_criterio").classList.add("is-invalid");
			document.getElementById("txt_criterio").focus();
			
			// Show toast notification
			showNotification("Error", "El motivo de cancelación es obligatorio", "error");
			return;
		}
		
		// Remove validation class if present
		document.getElementById("txt_criterio").classList.remove("is-invalid");
		
		// Show loading state
		const btn = document.querySelector('[onclick="cancelar_paquete()"]');
		const originalText = btn.innerHTML;
		btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
		btn.disabled = true;
		
		$.post("db/cancelar_paquete.php",
			   { codigo_barra: "<?php echo htmlspecialchars($codigo_barra, ENT_QUOTES, 'UTF-8'); ?>",
				 motivo_cancelacion: document.getElementById("txt_criterio").value,
				 usuario_cancelo: '<?php echo htmlspecialchars($_SESSION['nombre_usuario'], ENT_QUOTES, 'UTF-8'); ?>',
				 csrf_token: '<?php echo SecurityManager::generateCSRFToken(); ?>'
			   },
			   function (respuesta)
			   {
					// Reset button
					btn.innerHTML = originalText;
					btn.disabled = false;
					
					if ( respuesta != "Ok" )
					{
						showNotification("Error", respuesta, "error");
					}
					else
					{
						showNotification("Éxito", "Paquete cancelado correctamente", "success");
						setTimeout(() => {
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
						}, 1500);
					}
			   }
			  ).fail(function(xhr, status, error) {
				// Reset button
				btn.innerHTML = originalText;
				btn.disabled = false;
				
				showNotification("Error", "Error de conexión: " + error, "error");
			  });
		
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
                <div class="card-header text-white rounded-top-3" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-ban me-2"></i>
                        Cancelación de Paquete
                    </h5>
                </div>
                <div class="card-body p-3">
                    <form id="frmCancelar">
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
                                <label class="form-label fw-semibold small text-secondary mb-1">Valor Declarado:</label>
                                <div class="form-control-plaintext border rounded p-2 bg-light small">
                                    <?php echo htmlspecialchars($registro['valor_declaro'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">Sección:</label>
                                <div class="form-control-plaintext border rounded p-2 bg-light small">
                                    <?php echo htmlspecialchars($registro['seccion'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Cancellation Reason Section -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="txt_criterio" class="form-label fw-bold text-danger mb-1">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Motivo de Cancelación: *
                                </label>
                                <textarea name="txt_criterio" id="txt_criterio" class="form-control" rows="3" 
                                          placeholder="Ingrese el motivo de la cancelación del paquete..." 
                                          required></textarea>
                                <div class="invalid-feedback">
                                    Por favor, ingrese el motivo de la cancelación.
                                </div>
                                <div class="form-text small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Este campo es obligatorio y se registrará en el historial del paquete.
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
                                    <button type="button" class="btn btn-danger px-3" onclick="cancelar_paquete()">
                                        <i class="fas fa-times-circle me-2"></i>
                                        Confirmar Cancelación
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
