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

?>

<!-- Activar Código Barra component content -->
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #3d4a2d 0%, #2a3d1e 100%); border-bottom: 2px solid #243a1a;">
                    <h5 class="mb-0">
                        <i class="fas fa-barcode me-2"></i>
                        Activar Código de Barra
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <form id="frmActivar" name="frmActivar" method="post" action="">
                                <div class="mb-4">
                                    <label for="codigoBarra" class="form-label fw-bold">
                                        <i class="fas fa-barcode me-2"></i>Código de Barra:
                                    </label>
                                    <input 
                                        type="text" 
                                        class="form-control form-control-lg barcode-input" 
                                        id="codigoBarra" 
                                        name="codigoBarra"
                                        placeholder="Ingrese el código de barra"
                                        maxlength="50"
                                        required
                                        autocomplete="off"
                                    />
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Ingrese el código de barra del paquete que desea activar
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                    <button 
                                        type="button" 
                                        id="btnActivar" 
                                        class="btn btn-success btn-lg px-4"
                                    >
                                        <i class="fas fa-play-circle me-2"></i>
                                        Activar Código
                                    </button>
                                    <button 
                                        type="button" 
                                        id="btnLimpiar" 
                                        class="btn btn-outline-secondary btn-lg px-4"
                                    >
                                        <i class="fas fa-eraser me-2"></i>
                                        Limpiar
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Results area -->
                            <div id="resultArea" class="mt-4" style="display: none;">
                                <div id="resultMessage" class="alert" role="alert"></div>
                            </div>
                            
                            <!-- Loading indicator -->
                            <div id="loadingIndicator" class="text-center mt-4" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Procesando...</span>
                                </div>
                                <p class="mt-2 text-muted">Procesando activación...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Instructions card -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-question-circle me-2"></i>
                                        Instrucciones de Uso
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="mb-0">
                                        <li><strong>Propósito:</strong> Esta función activa un código de barra restableciendo su estado a "no facturado" y "no validado".</li>
                                        <li><strong>Uso:</strong> Ingrese el código de barra del paquete y haga clic en "Activar Código" o presione Enter.</li>
                                        <li><strong>Auto-activación:</strong> Los códigos se activarán automáticamente 1 segundo después de dejar de escribir (ideal para lectores de códigos).</li>
                                        <li><strong>Resultado:</strong> El sistema reiniciará los flags de facturación y validación del paquete.</li>
                                        <li><strong>Importante:</strong> Use esta función solo cuando sea necesario reactivar un paquete previamente procesado.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.barcode-input {
    font-family: 'Courier New', Monaco, 'Lucida Console', monospace !important;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-weight: 600;
    text-align: center;
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
}

.barcode-input:focus {
    border-color: #3d4a2d;
    box-shadow: 0 0 0 0.25rem rgba(61, 74, 45, 0.25);
    transform: scale(1.02);
}

.card-header {
    background: linear-gradient(135deg, #3d4a2d 0%, #2a3d1e 100%) !important;
}

.btn-success {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%);
    border: none;
    font-weight: normal;
    font-size: 12px;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: linear-gradient(135deg, #146c43 0%, #0f5132 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
}

.btn-outline-secondary {
    font-weight: normal;
    font-size: 12px;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    transform: translateY(-1px);
}

/* Loading animation improvements */
.spinner-border {
    animation: spinner-border 0.75s linear infinite;
}

/* Instructions card styling */
.border-info {
    border-color: #3d4a2d !important;
}

.bg-info {
    background: linear-gradient(135deg, #3d4a2d 0%, #2a3d1e 100%) !important;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .barcode-input {
        font-size: 1rem;
        letter-spacing: 1px;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
}
</style>

<script>
// Wait for jQuery to be available before setting up component
function waitForjQueryAndSetup() {
    console.log('[ActivarCodigo] Starting setup process...');
    if (typeof $ === 'undefined') {
        console.log('[ActivarCodigo] jQuery not available, waiting...');
        setTimeout(waitForjQueryAndSetup, 100);
        return;
    }
    
    console.log('[ActivarCodigo] jQuery available:', typeof $);
    
    $(document).ready(function() {
        console.log('[ActivarCodigo] DOM ready, starting initialization...');
        
        // Focus on the input field
        $('#codigoBarra').focus();
        
        // Handle Enter key press in the input
        $('#codigoBarra').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                activarCodigo();
            }
        });
        
        // Auto-focus and select text when input receives focus
        $('#codigoBarra').on('focus', function() {
            $(this).select();
        });
        
        // Clear result area when user starts typing
        $('#codigoBarra').on('input', function() {
            ocultarMensaje();
        });
        
        // Auto-activate after user stops typing (for barcode scanners)
        let typingTimer;
        const doneTypingInterval = 1000; // 1 second
        
        $('#codigoBarra').on('input', function() {
            clearTimeout(typingTimer);
            const value = $(this).val().trim();
            
            // Only auto-activate if input length suggests a complete barcode
            if (value.length >= 8) {
                typingTimer = setTimeout(function() {
                    if ($('#codigoBarra').val().trim().length >= 8) {
                        console.log('[ActivarCodigo] Auto-activating after typing pause...');
                        activarCodigo();
                    }
                }, doneTypingInterval);
            }
        });
        
        // Handle Activar button click
        $('#btnActivar').on('click', function(e) {
            e.preventDefault();
            activarCodigo();
        });
        
        // Handle Limpiar button click
        $('#btnLimpiar').on('click', function(e) {
            e.preventDefault();
            limpiarFormulario();
        });
        
        console.log('[ActivarCodigo] Component initialization complete');
    });
}

// Function to activate the barcode
function activarCodigo() {
    const codigoBarra = $('#codigoBarra').val().trim();
    
    // Validate input
    if (!codigoBarra) {
        // Use SweetAlert if available for better UX
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor, ingrese un código de barra.',
                confirmButtonColor: '#ffc107',
                focusConfirm: false
            }).then(() => {
                $('#codigoBarra').focus();
            });
        } else {
            mostrarMensaje('Por favor, ingrese un código de barra.', 'warning');
            $('#codigoBarra').focus();
        }
        return;
    }
    
    // Show loading indicator
    mostrarCargando(true);
    ocultarMensaje();
    
    // Disable button to prevent multiple clicks
    $('#btnActivar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Procesando...');
    
    // Make AJAX request
    $.ajax({
        url: 'db/activar.php',
        type: 'GET',
        data: {
            codigoBarra: codigoBarra,
            csrf_token: '<?php echo SecurityManager::generateCSRFToken(); ?>'
        },
        timeout: 30000, // 30 seconds timeout
        success: function(response) {
            console.log('[ActivarCodigo] Response received:', response);
            
            // Check if response indicates success
            if (response.trim().toLowerCase() === 'ok') {
                const successMessage = 'El código de barra <strong>' + codigoBarra + '</strong> ha sido activado correctamente.';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        html: successMessage,
                        confirmButtonColor: '#198754',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                } else {
                    mostrarMensaje('<strong>¡Éxito!</strong> ' + successMessage, 'success');
                }
                
                // Clear the input and focus for next entry
                setTimeout(function() {
                    limpiarFormulario();
                    $('#codigoBarra').focus();
                }, 3000);
                
            } else {
                // Show error message
                const errorMessage = response || 'No se pudo activar el código de barra. Verifique que el código sea válido.';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMessage,
                        confirmButtonColor: '#dc3545'
                    });
                } else {
                    mostrarMensaje('<strong>Error:</strong> ' + errorMessage, 'danger');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('[ActivarCodigo] AJAX Error:', status, error);
            
            let errorMessage = 'Error de conexión. Por favor, intente nuevamente.';
            if (status === 'timeout') {
                errorMessage = 'La solicitud ha tardado demasiado tiempo. Por favor, intente nuevamente.';
            } else if (xhr.status === 403) {
                errorMessage = 'Sesión expirada. Por favor, recargue la página e inicie sesión nuevamente.';
            } else if (xhr.status === 500) {
                errorMessage = 'Error interno del servidor. Contacte al administrador.';
            }
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonColor: '#dc3545'
                });
            } else {
                mostrarMensaje('<strong>Error:</strong> ' + errorMessage, 'danger');
            }
        },
        complete: function() {
            // Hide loading indicator and restore button
            mostrarCargando(false);
            $('#btnActivar').prop('disabled', false).html('<i class="fas fa-play-circle me-2"></i>Activar Código');
        }
    });
}

// Function to show/hide loading indicator
function mostrarCargando(mostrar) {
    if (mostrar) {
        $('#loadingIndicator').show();
    } else {
        $('#loadingIndicator').hide();
    }
}

// Function to show messages with SweetAlert if available, fallback to alert divs
function mostrarMensaje(mensaje, tipo) {
    // Use SweetAlert2 if available for better UX
    if (typeof Swal !== 'undefined') {
        const swalConfig = {
            icon: tipo === 'success' ? 'success' : tipo === 'danger' ? 'error' : tipo === 'warning' ? 'warning' : 'info',
            title: tipo === 'success' ? '¡Éxito!' : tipo === 'danger' ? 'Error' : tipo === 'warning' ? 'Atención' : 'Información',
            html: mensaje,
            confirmButtonColor: tipo === 'success' ? '#198754' : '#0d6efd',
            timer: tipo === 'success' ? 3000 : undefined,
            timerProgressBar: tipo === 'success',
            showConfirmButton: tipo !== 'success'
        };
        
        Swal.fire(swalConfig);
        return;
    }
    
    // Fallback to alert divs if SweetAlert is not available
    const alertClasses = {
        'success': 'alert-success',
        'danger': 'alert-danger', 
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const iconClasses = {
        'success': 'fas fa-check-circle',
        'danger': 'fas fa-exclamation-triangle',
        'warning': 'fas fa-exclamation-circle',
        'info': 'fas fa-info-circle'
    };
    
    const alertClass = alertClasses[tipo] || 'alert-info';
    const iconClass = iconClasses[tipo] || 'fas fa-info-circle';
    
    $('#resultMessage')
        .removeClass('alert-success alert-danger alert-warning alert-info')
        .addClass(alertClass)
        .html('<i class="' + iconClass + ' me-2"></i>' + mensaje);
    
    $('#resultArea').show();
    
    // Scroll to result area
    $('html, body').animate({
        scrollTop: $('#resultArea').offset().top - 100
    }, 500);
}

// Function to hide messages
function ocultarMensaje() {
    $('#resultArea').hide();
}

// Function to clear the form
function limpiarFormulario() {
    $('#codigoBarra').val('');
    ocultarMensaje();
    mostrarCargando(false);
}

// Start the setup process
waitForjQueryAndSetup();
</script>
