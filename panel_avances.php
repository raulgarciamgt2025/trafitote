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

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!-- Panel Avances Loading -->\n";

try {
    // Initialize database helper
    $db = new DatabaseHelper();
    
    // Handle form submission for refresh
    $refreshRequested = isset($_POST['cmdActualizar']);
    if ($refreshRequested) {
        // Validate CSRF token
        if (!SecurityManager::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception('Token de seguridad inválido');
        }
    }
    
    // We'll load data via AJAX for better performance and user experience
    $packages = [];
    $error_message = null;
    
    error_log("Panel initialized successfully, data will be loaded via AJAX");

} catch (Exception $e) {
    error_log("Error in panel_avances.php: " . $e->getMessage());
    $packages = [];
    $error_message = 'Error al inicializar la página: ' . $e->getMessage();
}
?>

<!-- Panel de Avances component content -->
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #3d4a2d 0%, #2a3d1e 100%); border-bottom: 2px solid #243a1a;">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Panel de Avances - Estado de Paquetes
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Refresh Button -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <form id="frmRefresh" method="post" action="">
                                <input type="hidden" name="csrf_token" value="<?php echo SecurityManager::generateCSRFToken(); ?>">
                                <button id="btnActualizar" name="cmdActualizar" class="btn btn-primary btn-lg" type="submit">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Actualizar Información
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4" id="summaryCards">
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <div class="text-primary">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                    <div class="mt-2">
                                        <h4 class="mb-0" id="totalAgentes">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                        </h4>
                                        <small class="text-muted">Agentes Activos</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <div class="text-info">
                                        <i class="fas fa-boxes fa-2x"></i>
                                    </div>
                                    <div class="mt-2">
                                        <h4 class="mb-0" id="totalPaquetes">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                        </h4>
                                        <small class="text-muted">Total Paquetes</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <div class="text-success">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                    <div class="mt-2">
                                        <h4 class="mb-0" id="totalValidados">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                        </h4>
                                        <small class="text-muted" id="porcentajeValidados">Validados</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <div class="text-warning">
                                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    </div>
                                    <div class="mt-2">
                                        <h4 class="mb-0" id="totalNoValidados">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                        </h4>
                                        <small class="text-muted">No Validados</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-container">
                        <!-- Loading indicator -->
                        <div id="tableLoadingIndicator" class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <br><br>
                            <span class="text-muted">Cargando datos del panel...</span>
                        </div>
                        
                        <table id="panelTable" class="table table-hover table-sm" style="display: none;">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center" style="width: 60px;">#</th>
                                    <th class="text-center" style="width: 80px;">ID</th>
                                    <th class="text-start" style="min-width: 200px;">Agente</th>
                                    <th class="text-center" style="width: 120px;">Validados</th>
                                    <th class="text-center" style="width: 120px;">No Válidos</th>
                                    <th class="text-center" style="width: 140px;">Total Paquetes</th>
                                    <th class="text-center" style="width: 120px;">% Validación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Last Update Info -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Última actualización:</strong> <?php echo date('d/m/Y H:i:s'); ?>
                                <?php if ($refreshRequested): ?>
                                    <span class="badge bg-success ms-2">Actualizado</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions card -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-question-circle me-2"></i>
                                        Información del Panel
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="mb-0">
                                        <li><strong>Código de colores:</strong> Verde (≥90% validación), Amarillo (70-89%), Rojo (<70%)</li>
                                        <li><strong>Actualización:</strong> Use el botón "Actualizar Información" para obtener los datos más recientes</li>
                                        <li><strong>Estadísticas:</strong> Las tarjetas superiores muestran resúmenes generales del estado</li>
                                        <li><strong>Progreso:</strong> La barra de progreso muestra el porcentaje de validación por agente</li>
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

<script>
// Component-specific functionality
var panelTableInitialized = false;
var panelData = [];

// Wait for jQuery to be available before setting up component
function waitForjQueryAndSetup() {
    console.log('[PanelAvances] Starting setup process...');
    if (typeof $ === 'undefined') {
        console.log('[PanelAvances] jQuery not available, waiting...');
        setTimeout(waitForjQueryAndSetup, 100);
        return;
    }
    
    console.log('[PanelAvances] jQuery available:', typeof $);
    
    $(document).ready(function() {
        console.log('[PanelAvances] DOM ready, starting initialization...');
        
        // Prevent multiple initializations
        if (panelTableInitialized) {
            console.log('[PanelAvances] Already initialized, skipping...');
            return;
        }

        // Load data immediately
        loadPanelData();

        // Handle refresh button
        $('#frmRefresh').on('submit', function(e) {
            e.preventDefault();
            loadPanelData(true);
        });
        
        // Initialize tooltips if Bootstrap is available
        if (typeof bootstrap !== 'undefined') {
            initializeTooltips();
        }
        
        console.log('[PanelAvances] Component initialization complete');
    });
}

// Load panel data via AJAX
function loadPanelData(showAlert = false) {
    console.log('[PanelAvances] Loading data via AJAX...');
    
    const $btn = $('#btnActualizar');
    const originalText = $btn.html();
    
    // Show loading state on button
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Actualizando...');
    
    // Show loading alert if refresh was clicked
    if (showAlert && typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: 'Actualizando...',
            text: 'Obteniendo los datos más recientes...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
    
    // Prepare CSRF token
    const csrfToken = $('input[name="csrf_token"]').val() || '';
    
    $.ajax({
        url: 'data/panel_avances_data.php',
        type: 'POST',
        dataType: 'json',
        data: {
            csrf_token: csrfToken
        },
        timeout: 30000, // 30 second timeout
        success: function(response) {
            console.log('[PanelAvances] Data loaded successfully:', response);
            
            if (response.success) {
                // Update summary cards
                updateSummaryCards(response.summary);
                
                // Update table
                updateDataTable(response.data);
                
                // Update last update time
                updateLastUpdateTime();
                
                // Store data
                panelData = response.data;
                
                // Show success message
                if (showAlert) {
                    showSuccessMessage(response);
                }
                
            } else {
                console.error('[PanelAvances] Server returned error:', response.error);
                showErrorMessage(response.error || 'Error al cargar los datos');
            }
        },
        error: function(xhr, status, error) {
            console.error('[PanelAvances] AJAX error:', error, 'Status:', status);
            
            // Hide loading indicator and show table
            $('#tableLoadingIndicator').hide();
            $('#panelTable').show();
            
            // Show error message in table
            const $tbody = $('#panelTable tbody');
            $tbody.html(`
                <tr>
                    <td colspan="7" class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>
                        Error al cargar los datos: ${error}
                    </td>
                </tr>
            `);
            
            showErrorMessage('Error de conexión: ' + error);
        },
        complete: function() {
            // Restore button state
            $btn.prop('disabled', false).html(originalText);
            
            // Close loading alert
            if (showAlert && typeof Swal !== 'undefined') {
                Swal.close();
            }
        }
    });
}

// Update summary cards
function updateSummaryCards(summary) {
    console.log('[PanelAvances] Updating summary cards:', summary);
    
    // Animate counters
    animateCounter('#totalAgentes', summary.total_agents);
    animateCounter('#totalPaquetes', summary.total_packages);
    animateCounter('#totalValidados', summary.total_validated);
    animateCounter('#totalNoValidados', summary.total_pending);
    
    // Update percentage text
    if (summary.total_packages > 0) {
        const percentage = Math.round((summary.total_validated / summary.total_packages) * 100);
        $('#porcentajeValidados').text(`Validados (${percentage}%)`);
    }
}

// Animate counter
function animateCounter(selector, finalValue) {
    const $element = $(selector);
    const startValue = 0;
    const duration = 1000; // 1 second
    const startTime = Date.now();
    
    function updateCounter() {
        const elapsed = Date.now() - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const currentValue = Math.floor(startValue + (finalValue - startValue) * progress);
        
        $element.text(new Intl.NumberFormat('es-GT').format(currentValue));
        
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        }
    }
    
    updateCounter();
}

// Update data table
function updateDataTable(data) {
    console.log('[PanelAvances] Updating data table with', data.length, 'records');
    
    // Hide loading indicator and show table
    $('#tableLoadingIndicator').hide();
    $('#panelTable').show();
    
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#panelTable')) {
        $('#panelTable').DataTable().destroy();
    }
    
    // Clear table body
    const $tbody = $('#panelTable tbody');
    $tbody.empty();
    
    // Add rows
    if (data && data.length > 0) {
        data.forEach(function(registro, index) {
            const validados = parseInt(registro.validados) || 0;
            const noValidados = parseInt(registro.no_validados) || 0;
            const totalPaquetes = parseInt(registro.paquetes) || 0;
            const porcentaje = totalPaquetes > 0 ? Math.round((validados / totalPaquetes) * 100) : 0;
            
            // Color coding based on validation percentage
            let rowClass = '';
            if (porcentaje >= 90) {
                rowClass = 'table-success';
            } else if (porcentaje >= 70) {
                rowClass = 'table-warning';
            } else if (totalPaquetes > 0) {
                rowClass = 'table-danger';
            }
            
            const row = `
                <tr class="${rowClass}">
                    <td class="text-center align-middle fw-bold text-muted">${index + 1}</td>
                    <td class="text-center align-middle">${escapeHtml(registro.id_entidad_asignado)}</td>
                    <td class="text-start align-middle">${escapeHtml(registro.agente)}</td>
                    <td class="text-center align-middle">
                        <span class="badge bg-success fs-6">${new Intl.NumberFormat('es-GT').format(validados)}</span>
                    </td>
                    <td class="text-center align-middle">
                        <span class="badge bg-warning fs-6">${new Intl.NumberFormat('es-GT').format(noValidados)}</span>
                    </td>
                    <td class="text-center align-middle">
                        <span class="badge bg-primary fs-6">${new Intl.NumberFormat('es-GT').format(totalPaquetes)}</span>
                    </td>
                    <td class="text-center align-middle">
                        ${totalPaquetes > 0 ? `
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar ${porcentaje >= 90 ? 'bg-success' : (porcentaje >= 70 ? 'bg-warning' : 'bg-danger')}" 
                                     role="progressbar" 
                                     style="width: ${porcentaje}%"
                                     aria-valuenow="${porcentaje}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    ${porcentaje}%
                                </div>
                            </div>
                        ` : '<span class="text-muted">N/A</span>'}
                    </td>
                </tr>
            `;
            
            $tbody.append(row);
        });
    } else {
        $tbody.html(`
            <tr data-empty="true">
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                    No hay datos disponibles
                </td>
            </tr>
        `);
    }
    
    // Initialize DataTable
    initializeDataTable();
}

// Initialize DataTable
function initializeDataTable() {
    if (typeof $.fn.DataTable === 'undefined') {
        console.log('[PanelAvances] DataTables not available');
        return;
    }
    
    // Check if table has any data rows (not just colspan message)
    const $tbody = $('#panelTable tbody');
    const hasData = $tbody.find('tr').length > 0 && $tbody.find('tr:not([data-empty="true"])').length > 0;
    
    console.log('[PanelAvances] Initializing DataTables... Has data:', hasData);
    
    try {
        // DataTables language configuration
        const languageConfig = {
            lengthMenu: "Mostrar _MENU_ entradas",
            zeroRecords: "No se encontraron datos",
            info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
            infoEmpty: "Mostrando 0 a 0 de 0 entradas",
            infoFiltered: "(filtrado de _MAX_ entradas totales)",
            search: "Buscar:",
            paginate: {
                first: "Primera",
                last: "Última",
                next: "Siguiente",
                previous: "Anterior"
            },
            processing: "Procesando...",
            loadingRecords: "Cargando...",
            emptyTable: "No hay datos disponibles en la tabla",
            searchPlaceholder: "Buscar agentes..."
        };

        const tableConfig = {
            language: languageConfig,
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "Todos"]],
            responsive: true,
            order: hasData ? [[ 6, "desc" ]] : [], // Order by percentage validation descending only if we have data
            columnDefs: [
                { 
                    targets: 0, // No. column
                    orderable: false,
                    searchable: false,
                    className: "text-center align-middle fw-bold text-muted",
                    width: "50px"
                },
                { 
                    targets: [1], // ID column
                    className: "text-center align-middle",
                    width: "80px"
                },
                { 
                    targets: 2, // Agente column
                    className: "text-start align-middle",
                    width: "250px"
                },
                { 
                    targets: [3, 4, 5], // Numeric columns
                    className: "text-center align-middle",
                    width: "120px"
                },
                { 
                    targets: 6, // Percentage column
                    className: "text-center align-middle",
                    orderable: hasData,
                    width: "120px",
                    type: "num"
                }
            ],
            searching: hasData,
            ordering: hasData,
            info: true,
            paging: hasData,
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            dom: hasData ? 'lfrtip' : 'tip' // Simplified controls if no data
        };

        // Initialize DataTable
        if (typeof DataTable !== 'undefined') {
            // DataTables 2.x
            tableConfig.layout = {
                topStart: hasData ? 'pageLength' : null,
                topEnd: hasData ? 'search' : null,
                bottomStart: 'info',
                bottomEnd: hasData ? 'paging' : null
            };
            new DataTable('#panelTable', tableConfig);
        } else {
            // DataTables 1.x
            $('#panelTable').DataTable(tableConfig);
        }
        
        console.log('[PanelAvances] DataTables initialized successfully');
        panelTableInitialized = true;
        
    } catch (e) {
        console.error('[PanelAvances] Error initializing DataTables:', e);
        // If DataTables fails, at least show the table
        $('#panelTable').show();
    }
}

// Update last update time
function updateLastUpdateTime() {
    const now = new Date();
    const timeString = now.toLocaleDateString('es-GT') + ' ' + now.toLocaleTimeString('es-GT');
    $('.alert-info strong').next().text(' ' + timeString);
}

// Show success message
function showSuccessMessage(response) {
    if (typeof Swal !== 'undefined') {
        let message = 'Datos actualizados correctamente';
        if (response.query_time) {
            message += ` (${response.query_time}s)`;
        }
        if (response.data_source) {
            const sourceText = response.data_source === 'database' ? 'Base de datos' : 
                             response.data_source === 'database_simplified' ? 'Base de datos (consulta simplificada)' : 
                             'Datos de demostración';
            message += `\nFuente: ${sourceText}`;
        }
        
        Swal.fire({
            icon: 'success',
            title: 'Actualizado',
            text: message,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    }
}

// Show error message
function showErrorMessage(error) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error,
            confirmButtonText: 'Entendido'
        });
    } else {
        alert('Error: ' + error);
    }
}

// HTML escape function
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text || '').replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Initialize tooltips
function initializeTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Start the setup process
waitForjQueryAndSetup();
</script>
