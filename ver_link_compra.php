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

<!-- Ver Link de Compra component content -->
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #3d4a2d 0%, #2a3d1e 100%); border-bottom: 2px solid #243a1a;">
                    <h5 class="mb-0">
                        <i class="fas fa-link me-2"></i>
                        Ver Link de Compra
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <form id="frmVerLink" name="frmVerLink" method="post" action="">
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
                                        Ingrese el código de barra para ver o abrir el enlace de compra
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                    <button 
                                        type="button" 
                                        id="btnVerLink" 
                                        class="btn btn-primary btn-lg px-4"
                                    >
                                        <i class="fas fa-eye me-2"></i>
                                        Ver Link
                                    </button>
                                    <button 
                                        type="button" 
                                        id="btnAbrirLink" 
                                        class="btn btn-success btn-lg px-4"
                                    >
                                        <i class="fas fa-external-link-alt me-2"></i>
                                        Abrir Link
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
                            
                            <!-- Link display area -->
                            <div id="linkArea" class="mt-4" style="display: none;">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-link me-2"></i>
                                            Enlace de Compra
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Código de Barra:</label>
                                            <div class="input-group">
                                                <input type="text" id="displayCodigoBarra" class="form-control" readonly>
                                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('displayCodigoBarra')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Enlace:</label>
                                            <div class="input-group">
                                                <input type="text" id="displayLink" class="form-control" readonly>
                                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('displayLink')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <button class="btn btn-primary" type="button" onclick="openLink()">
                                                    <i class="fas fa-external-link-alt me-1"></i>
                                                    Abrir
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Loading indicator -->
                            <div id="loadingIndicator" class="text-center mt-4" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Procesando...</span>
                                </div>
                                <p class="mt-2 text-muted">Consultando enlace...</p>
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
                                        <li><strong>Ver Link:</strong> Muestra el enlace de compra asociado al código de barra en pantalla.</li>
                                        <li><strong>Abrir Link:</strong> Abre directamente el enlace de compra en una nueva ventana/pestaña.</li>
                                        <li><strong>Copiar:</strong> Use los botones de copiar para copiar el código o enlace al portapapeles.</li>
                                        <li><strong>Importante:</strong> Solo se mostrarán enlaces válidos asociados al código de barra ingresado.</li>
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

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
    font-weight: normal;
    font-size: 12px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
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

/* Instructions and link display card styling */
.border-info {
    border-color: #3d4a2d !important;
}

.bg-info {
    background: linear-gradient(135deg, #3d4a2d 0%, #2a3d1e 100%) !important;
}

/* Link display improvements */
#linkArea .card {
    border-color: #0dcaf0 !important;
}

#linkArea .bg-info {
    background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important;
}

/* Copy button styling */
.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
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
    
    .d-md-flex {
        flex-direction: column !important;
    }
    
    .d-md-flex .btn {
        margin-bottom: 0.5rem;
    }
}
</style>

<script>
// Wait for jQuery to be available before setting up component
function waitForjQueryAndSetup() {
    console.log('[VerLinkCompra] Starting setup process...');
    if (typeof $ === 'undefined') {
        console.log('[VerLinkCompra] jQuery not available, waiting...');
        setTimeout(waitForjQueryAndSetup, 100);
        return;
    }
    
    console.log('[VerLinkCompra] jQuery available:', typeof $);
    
    $(document).ready(function() {
        console.log('[VerLinkCompra] DOM ready, starting initialization...');
        
        // Focus on the input field
        $('#codigoBarra').focus();
        
        // Handle Enter key press in the input
        $('#codigoBarra').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                verLink();
            }
        });
        
        // Auto-focus and select text when input receives focus
        $('#codigoBarra').on('focus', function() {
            $(this).select();
        });
        
        // Clear result areas when user starts typing
        $('#codigoBarra').on('input', function() {
            ocultarResultados();
        });
        
        // Auto-show link after user stops typing (for barcode scanners)
        let typingTimer;
        const doneTypingInterval = 1000; // 1 second
        
        $('#codigoBarra').on('input', function() {
            clearTimeout(typingTimer);
            const value = $(this).val().trim();
            
            // Only auto-show if input length suggests a complete barcode
            if (value.length >= 8) {
                typingTimer = setTimeout(function() {
                    if ($('#codigoBarra').val().trim().length >= 8) {
                        console.log('[VerLinkCompra] Auto-showing link after typing pause...');
                        verLink();
                    }
                }, doneTypingInterval);
            }
        });
        
        // Handle Ver Link button click
        $('#btnVerLink').on('click', function(e) {
            e.preventDefault();
            verLink();
        });
        
        // Handle Abrir Link button click
        $('#btnAbrirLink').on('click', function(e) {
            e.preventDefault();
            abrirLink();
        });
        
        // Handle Limpiar button click
        $('#btnLimpiar').on('click', function(e) {
            e.preventDefault();
            limpiarFormulario();
        });
        
        console.log('[VerLinkCompra] Component initialization complete');
    });
}

// Function to view the link
function verLink() {
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
    ocultarResultados();
    
    // Disable button to prevent multiple clicks
    $('#btnVerLink').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Consultando...');
    
    // Make AJAX request
    $.ajax({
        url: 'ver_enlace.php',
        type: 'POST',
        data: {
            cb: codigoBarra,
            csrf_token: '<?php echo SecurityManager::generateCSRFToken(); ?>'
        },
        timeout: 30000, // 30 seconds timeout
        success: function(response) {
            console.log('[VerLinkCompra] Response received:', response);
            
            const link = response.trim();
            
            if (link && link !== '') {
                // Show the link
                $('#displayCodigoBarra').val(codigoBarra);
                $('#displayLink').val(link);
                $('#linkArea').show();
                
                // Store link globally for openLink function
                window.currentLink = link;
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Enlace encontrado!',
                        text: 'El enlace de compra se muestra abajo.',
                        confirmButtonColor: '#198754',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                } else {
                    mostrarMensaje('<strong>¡Éxito!</strong> Enlace de compra encontrado.', 'success');
                }
                
                // Scroll to link area
                $('html, body').animate({
                    scrollTop: $('#linkArea').offset().top - 100
                }, 500);
                
            } else {
                // No link found
                const errorMessage = 'No se encontró enlace de compra para el código de barra: <strong>' + codigoBarra + '</strong>';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Sin enlace',
                        html: errorMessage,
                        confirmButtonColor: '#0dcaf0'
                    });
                } else {
                    mostrarMensaje(errorMessage, 'info');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('[VerLinkCompra] AJAX Error:', status, error);
            
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
            $('#btnVerLink').prop('disabled', false).html('<i class="fas fa-eye me-2"></i>Ver Link');
        }
    });
}

// Function to open the link directly
function abrirLink() {
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
    
    // Open the link in a new window/tab
    const url = 'enlace.php?cb=' + encodeURIComponent(codigoBarra) + '&csrf_token=' + encodeURIComponent('<?php echo SecurityManager::generateCSRFToken(); ?>');
    window.open(url, '_blank');
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: 'Enlace abierto',
            text: 'El enlace se abrió en una nueva ventana/pestaña.',
            confirmButtonColor: '#0dcaf0',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    }
}

// Function to open the stored link
function openLink() {
    if (window.currentLink) {
        window.open(window.currentLink, '_blank');
    }
}

// Function to copy text to clipboard
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡Copiado!',
                text: 'Texto copiado al portapapeles.',
                confirmButtonColor: '#198754',
                timer: 1500,
                timerProgressBar: true,
                showConfirmButton: false
            });
        } else {
            mostrarMensaje('Texto copiado al portapapeles.', 'success');
        }
    } catch (err) {
        console.error('Error al copiar:', err);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo copiar el texto.',
                confirmButtonColor: '#dc3545'
            });
        }
    }
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

// Function to hide all result areas
function ocultarResultados() {
    $('#resultArea').hide();
    $('#linkArea').hide();
}

// Function to clear the form
function limpiarFormulario() {
    $('#codigoBarra').val('');
    ocultarResultados();
    mostrarCargando(false);
    window.currentLink = null;
    $('#codigoBarra').focus();
}

// Start the setup process
waitForjQueryAndSetup();
</script>

