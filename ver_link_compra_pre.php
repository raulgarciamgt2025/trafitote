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

<!-- Ver Link de Compra Pre-Alerta component content -->
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #3d4a2d 0%, #2a3d1e 100%); border-bottom: 2px solid #243a1a;">
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        Consulta de Pre-Alertas y Enlaces
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-8 col-lg-6">
                            <form id="frmBuscar" name="frmBuscar" method="post" action="">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="seccion" class="form-label fw-bold">
                                            <i class="fas fa-layer-group me-2"></i>Sección:
                                        </label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-lg" 
                                            id="seccion" 
                                            name="seccion"
                                            placeholder="Ingrese la sección"
                                            maxlength="50"
                                            required
                                            autocomplete="off"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tracking" class="form-label fw-bold">
                                            <i class="fas fa-truck me-2"></i>Tracking:
                                        </label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-lg" 
                                            id="tracking" 
                                            name="tracking"
                                            placeholder="Tracking o parte de él"
                                            maxlength="100"
                                            required
                                            autocomplete="off"
                                        />
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                                    <button 
                                        type="button" 
                                        id="btnBuscar" 
                                        class="btn btn-primary btn-lg px-4"
                                    >
                                        <i class="fas fa-search me-2"></i>
                                        Ver Información
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
                        </div>
                    </div>
                    
                    <!-- Results area -->
                    <div id="resultArea" class="mt-4" style="display: none;">
                        <div id="resultMessage" class="alert" role="alert"></div>
                    </div>
                    
                    <!-- Loading indicator -->
                    <div id="loadingIndicator" class="text-center mt-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Buscando...</span>
                        </div>
                        <p class="mt-2 text-muted">Consultando pre-alertas...</p>
                    </div>
                    
                    <!-- Data Table -->
                    <div id="tableContainer" class="mt-4" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover" id="dataTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">Fecha Grabó</th>
                                        <th class="text-center">Tracking</th>
                                        <th class="text-start">Descripción</th>
                                        <th class="text-end">Valor</th>
                                        <th class="text-center">Carrier</th>
                                        <th class="text-center">Tienda</th>
                                        <th class="text-center">Enlace Compra</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyData">
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Results summary -->
                        <div id="resultsSummary" class="mt-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <span id="resultsCount">0</span> resultado(s) encontrado(s)
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
                                        <li><strong>Sección:</strong> Ingrese la sección específica a consultar (campo obligatorio).</li>
                                        <li><strong>Tracking:</strong> Ingrese el número de tracking completo o parte de él para búsqueda parcial.</li>
                                        <li><strong>Resultados:</strong> Los resultados se muestran en una tabla ordenada por fecha de grabación.</li>
                                        <li><strong>Enlaces:</strong> Los enlaces de compra válidos aparecen como botones "Ver" que abren en nueva ventana.</li>
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
.form-control:focus {
    border-color: #3d4a2d;
    box-shadow: 0 0 0 0.25rem rgba(61, 74, 45, 0.25);
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

/* Table styling improvements */
.table-responsive {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.table thead th {
    background: linear-gradient(135deg, #212529 0%, #495057 100%) !important;
    border: none;
    font-weight: 600;
    font-size: 0.9rem;
}

.table tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.table td {
    vertical-align: middle;
    font-size: 0.85rem;
}

/* Results summary styling */
#resultsSummary .alert {
    border-radius: 8px;
    border: none;
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
    font-weight: 500;
}

/* Responsive improvements */
@media (max-width: 768px) {
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
    
    .table-responsive {
        font-size: 0.8rem;
    }
}
</style>

<script>
// Wait for jQuery to be available before setting up component
function waitForjQueryAndSetup() {
    console.log('[VerLinkCompraPre] Starting setup process...');
    if (typeof $ === 'undefined') {
        console.log('[VerLinkCompraPre] jQuery not available, waiting...');
        setTimeout(waitForjQueryAndSetup, 100);
        return;
    }
    
    console.log('[VerLinkCompraPre] jQuery available:', typeof $);
    
    $(document).ready(function() {
        console.log('[VerLinkCompraPre] DOM ready, starting initialization...');
        
        // Focus on the first input field
        $('#seccion').focus();
        
        // Handle Enter key press in the inputs
        $('#seccion, #tracking').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                buscarInformacion();
            }
        });
        
        // Auto-focus next field when current is filled
        $('#seccion').on('input', function() {
            const value = $(this).val().trim();
            if (value.length >= 3) {
                setTimeout(() => $('#tracking').focus(), 100);
            }
        });
        
        // Clear result areas when user starts typing
        $('#seccion, #tracking').on('input', function() {
            ocultarResultados();
        });
        
        // Handle Buscar button click
        $('#btnBuscar').on('click', function(e) {
            e.preventDefault();
            buscarInformacion();
        });
        
        // Handle Limpiar button click
        $('#btnLimpiar').on('click', function(e) {
            e.preventDefault();
            limpiarFormulario();
        });
        
        console.log('[VerLinkCompraPre] Component initialization complete');
    });
}

// Function to search for information
function buscarInformacion() {
    const seccion = $('#seccion').val().trim();
    const tracking = $('#tracking').val().trim();
    
    // Validate inputs
    if (!seccion) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor, ingrese una sección.',
                confirmButtonColor: '#ffc107',
                focusConfirm: false
            }).then(() => {
                $('#seccion').focus();
            });
        } else {
            mostrarMensaje('Por favor, ingrese una sección.', 'warning');
            $('#seccion').focus();
        }
        return;
    }
    
    if (!tracking) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor, ingrese un tracking o parte de él.',
                confirmButtonColor: '#ffc107',
                focusConfirm: false
            }).then(() => {
                $('#tracking').focus();
            });
        } else {
            mostrarMensaje('Por favor, ingrese un tracking o parte de él.', 'warning');
            $('#tracking').focus();
        }
        return;
    }
    
    // Show loading indicator
    mostrarCargando(true);
    ocultarResultados();
    $('#tableContainer').hide();
    
    // Disable button to prevent multiple clicks
    $('#btnBuscar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Buscando...');
    
    // Make AJAX request
    $.ajax({
        url: 'data_prealerta_enlaces.php',
        type: 'POST',
        data: {
            seccion: seccion,
            tracking: tracking,
            csrf_token: '<?php echo SecurityManager::generateCSRFToken(); ?>'
        },
        timeout: 30000, // 30 seconds timeout
        success: function(response) {
            console.log('[VerLinkCompraPre] Response received:', response);
            
            // Clear previous results
            $('#bodyData').empty();
            
            if (response.trim()) {
                // Check if response contains error messages
                if (response.includes('text-danger') || response.includes('text-warning')) {
                    $('#bodyData').html(response);
                    $('#tableContainer').show();
                    $('#resultsCount').text('0');
                } else {
                    // Parse and display results
                    $('#bodyData').html(response);
                    $('#tableContainer').show();
                    
                    // Count results (excluding header and error rows)
                    const resultRows = $('#bodyData tr').not(':contains("No se encontraron")').not(':contains("Error")').not(':contains("requeridos")');
                    const count = resultRows.length;
                    $('#resultsCount').text(count);
                    
                    if (count > 0) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Resultados encontrados!',
                                text: `Se encontraron ${count} resultado(s).`,
                                confirmButtonColor: '#198754',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        } else {
                            mostrarMensaje(`<strong>¡Éxito!</strong> Se encontraron ${count} resultado(s).`, 'success');
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Sin resultados',
                                text: 'No se encontraron resultados para los criterios especificados.',
                                confirmButtonColor: '#0dcaf0'
                            });
                        } else {
                            mostrarMensaje('No se encontraron resultados para los criterios especificados.', 'info');
                        }
                    }
                    
                    // Scroll to table
                    $('html, body').animate({
                        scrollTop: $('#tableContainer').offset().top - 100
                    }, 500);
                }
            } else {
                // Empty response
                $('#bodyData').html('<tr><td colspan="7" class="text-center text-muted">No se encontraron resultados</td></tr>');
                $('#tableContainer').show();
                $('#resultsCount').text('0');
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Sin resultados',
                        text: 'No se encontraron resultados para los criterios especificados.',
                        confirmButtonColor: '#0dcaf0'
                    });
                } else {
                    mostrarMensaje('No se encontraron resultados para los criterios especificados.', 'info');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('[VerLinkCompraPre] AJAX Error:', status, error);
            
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
            $('#btnBuscar').prop('disabled', false).html('<i class="fas fa-search me-2"></i>Ver Información');
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

// Function to hide all result areas
function ocultarResultados() {
    $('#resultArea').hide();
}

// Function to clear the form
function limpiarFormulario() {
    $('#seccion').val('');
    $('#tracking').val('');
    $('#bodyData').empty();
    $('#tableContainer').hide();
    ocultarResultados();
    mostrarCargando(false);
    $('#seccion').focus();
}

// Start the setup process
waitForjQueryAndSetup();
</script>

