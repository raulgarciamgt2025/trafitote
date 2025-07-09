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
    
    function enlazar_documento()
	{
		// Reset validation classes
		document.getElementById("txtValorDeclarado").classList.remove("is-invalid");
		document.getElementById("txtUrl").classList.remove("is-invalid");
		document.getElementById("txtContenido").classList.remove("is-invalid");
		
		let hasErrors = false;
		
		if ( document.getElementById("txtValorDeclarado").value == "" )
		{
			document.getElementById("txtValorDeclarado").classList.add("is-invalid");
			document.getElementById("txtValorDeclarado").focus();
			showNotification("Error", "El valor declarado es obligatorio", "error");
			hasErrors = true;
		}
		
		if ( document.getElementById("txtUrl").value == "" )
		{
			document.getElementById("txtUrl").classList.add("is-invalid");
			if (!hasErrors) document.getElementById("txtUrl").focus();
			showNotification("Error", "La URL del documento es obligatoria", "error");
			hasErrors = true;
		}	
		
		if ( document.getElementById("txtContenido").value == "" )
		{
			document.getElementById("txtContenido").classList.add("is-invalid");
			if (!hasErrors) document.getElementById("txtContenido").focus();
			showNotification("Error", "El contenido es obligatorio", "error");
			hasErrors = true;
		}
		
		if (hasErrors) return;
		
		// Show loading state
		const btn = document.getElementById("cmdEnlazar");
		const originalText = btn.value;
		btn.value = "Procesando...";
		btn.disabled = true;
		
		$.post("db/enlazar_documento.php",
			   { codigo_barra: <?php echo $codigoBarra ?>,
				 valor_declarado: document.getElementById("txtValorDeclarado").value,
				 url: document.getElementById("txtUrl").value,
				 contenido: document.getElementById("txtContenido").value,				 
				 usuario_grabo: '<?php echo htmlspecialchars($_SESSION['nombre_usuario'], ENT_QUOTES, 'UTF-8') ?>',
				 csrf_token: '<?php echo SecurityManager::generateCSRFToken(); ?>'
			   },
			   function (respuesta)
			   {
				   // Reset button
				   btn.value = originalText;
				   btn.disabled = false;
				   
				   if ( respuesta != "Ok" )
				   {
					   showNotification("Error", respuesta, "error");
				   }
				   else
				   {
					   showNotification("Éxito", "Documento enlazado correctamente", "success");
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
				  btn.value = originalText;
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
                <div class="card-header text-white rounded-top-3" style="background: linear-gradient(135deg, #007bff 0%, #6f42c1 100%);">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-search me-2"></i>
                        Revisión de Prealertas
                    </h5>
                </div>
                <div class="card-body p-3">
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

                    <!-- Document Link Section -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="txtValorDeclarado" class="form-label fw-bold text-primary mb-1">
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
                            <label for="txtContenido" class="form-label fw-bold text-primary mb-1">
                                <i class="fas fa-edit me-2"></i>
                                Contenido: *
                            </label>
                            <input name="txtContenido" type="text" id="txtContenido" 
                                   class="form-control" maxlength="200" 
                                   placeholder="Describa el contenido del paquete" required/>
                            <div class="invalid-feedback">
                                Por favor, describa el contenido del paquete.
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-12">
                            <label for="txtUrl" class="form-label fw-bold text-primary mb-1">
                                <i class="fas fa-link me-2"></i>
                                URL del Documento: *
                            </label>
                            <textarea name="txtUrl" id="txtUrl" class="form-control" rows="3" 
                                      placeholder="Ingrese la URL completa del documento" required></textarea>
                            <div class="invalid-feedback">
                                Por favor, ingrese la URL del documento.
                            </div>
                            <div class="form-text small">
                                <i class="fas fa-info-circle me-1"></i>
                                Ingrese la URL completa del documento a enlazar
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="volver()">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Cancelar
                                </button>
                                <button type="button" class="btn btn-primary px-3" id="cmdEnlazar" onclick="enlazar_documento()">
                                    <i class="fas fa-link me-2"></i>
                                    Enlazar Documento
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Alerts Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-secondary mb-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Alertas Encontradas:
                                </h6>
                                <div id="grid_prealertas" class="border rounded p-2 bg-light"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
   } catch (Exception $e) {
       error_log("Error in revisar_prealertas_todos.php: " . $e->getMessage());
       echo '<div class="alert alert-danger">Error al cargar los datos: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</div>';
   }
    // Connection is automatically closed when $db goes out of scope
?>
<script language="javascript">
$(document).ready(function() {
    // Load prealerts data
    $.post("http://www.transexpress.com.gt/prealertasV2.php",
        { tracking: '<?php echo htmlspecialchars($registro['Tracking'], ENT_QUOTES, 'UTF-8'); ?>' },
        function (respuesta) {
            document.getElementById("grid_prealertas").innerHTML = respuesta;
        }
    ).fail(function(xhr, status, error) {
        document.getElementById("grid_prealertas").innerHTML = 
            '<div class="alert alert-warning small"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar las prealertas: ' + error + '</div>';
    });
});
</script>