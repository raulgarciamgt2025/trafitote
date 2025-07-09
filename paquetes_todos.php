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

// Sanitize and validate inputs
$seccion = isset($_REQUEST['txtSeccion']) ? SecurityManager::sanitizeInput($_REQUEST['txtSeccion'], 'alphanumeric') : 
           (isset($_SESSION['txtSeccion']) ? $_SESSION['txtSeccion'] : '');

$codigo_barra = isset($_REQUEST['txtCodigoBarra']) ? SecurityManager::sanitizeInput($_REQUEST['txtCodigoBarra'], 'codigo_barra') : 
                (isset($_SESSION['txtCodigoBarra']) ? $_SESSION['txtCodigoBarra'] : '');

$tracking = isset($_REQUEST['txtTracking']) ? SecurityManager::sanitizeInput($_REQUEST['txtTracking'], 'alphanumeric') : 
            (isset($_SESSION['txtTracking']) ? $_SESSION['txtTracking'] : '');

$factura = isset($_REQUEST['slFactura']) ? SecurityManager::sanitizeInput($_REQUEST['slFactura'], 'alphanumeric') : 
           (isset($_SESSION['slFactura']) ? $_SESSION['slFactura'] : 'N');

$estado = isset($_REQUEST['slEstado']) ? SecurityManager::sanitizeInput($_REQUEST['slEstado'], 'alphanumeric') : 
          (isset($_SESSION['slEstado']) ? $_SESSION['slEstado'] : 'N');

$partida = isset($_REQUEST['slPartida']) ? SecurityManager::sanitizeInput($_REQUEST['slPartida'], 'alphanumeric') : 
           (isset($_SESSION['slPartida']) ? $_SESSION['slPartida'] : 'N');

$agente = isset($_REQUEST['slAgente']) ? SecurityManager::sanitizeInput($_REQUEST['slAgente'], 'int') : 
          (isset($_SESSION['slAgente']) ? (int)$_SESSION['slAgente'] : 0);

// Store in session for persistence
$_SESSION['txtSeccion'] = $seccion;
$_SESSION['txtCodigoBarra'] = $codigo_barra;
$_SESSION['txtTracking'] = $tracking;
$_SESSION['slFactura'] = $factura;
$_SESSION['slEstado'] = $estado;
$_SESSION['slPartida'] = $partida;
$_SESSION['slAgente'] = $agente;

try {
    // Get agents with prepared statement
    $consulta = "SELECT 0 as id_entidad, 'TODOS LOS AGENTES' as agente, 0 as orden
                 UNION ALL
                 SELECT DISTINCT a.id_entidad, b.descripcion as agente, a.orden
                 FROM dbo.FACTURA_AGENTE a 
                 INNER JOIN dbo.ENTIDAD b ON a.id_entidad = b.id_entidad
                 WHERE a.conectado = 1
                 ORDER BY orden";
    
    $agentes = $db->getRecords($consulta);

    $cmbAgentes = "";
    foreach ($agentes as $registro) {
        $selected = ($registro["id_entidad"] == $agente) ? 'selected' : '';
        $agente_name = htmlspecialchars($registro["agente"], ENT_QUOTES, 'UTF-8');
        $cmbAgentes .= "<option value='" . $registro["id_entidad"] . "' $selected>$agente_name</option>";
    }

    // Execute stored procedure with parameters
    $packages = $db->executeStoredProcedure('SP_PBX_PAQUETES_TODOSV2', [
        $seccion, $codigo_barra, $tracking, $factura, $estado, $agente, $partida
    ]);

} catch (Exception $e) {
    error_log("Error in paquetes_todos.php: " . $e->getMessage());
    echo '<div class="alert alert-danger">Error al cargar los datos. Por favor, contacte al administrador.</div>';
    return;
}

?>

<!-- Paquetes Todos component content -->
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #3d4a2d 0%, #2a3d1e 100%); border-bottom: 2px solid #243a1a;">
                    <h5 class="mb-0">
                        <i class="fas fa-boxes me-2"></i>
                        Gestión de Paquetes - Todos
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form id="frmData" name="frmData" method="post" action="">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="txtSeccion" class="form-label fw-bold">Sección:</label>
                                        <input name="txtSeccion" type="text" class="form-control" id="txtSeccion" maxlength="25" value="<?php echo htmlspecialchars($seccion, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ingrese sección"/>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="txtCodigoBarra" class="form-label fw-bold">Código Barra:</label>
                                        <input name="txtCodigoBarra" type="text" class="form-control" id="txtCodigoBarra" maxlength="25" value="<?php echo htmlspecialchars($codigo_barra, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ingrese código barra"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="txtTracking" class="form-label fw-bold">Tracking:</label>
                                        <input name="txtTracking" type="text" class="form-control" id="txtTracking" maxlength="35" value="<?php echo htmlspecialchars($tracking, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ingrese número de tracking"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-2">
                                <label for="slFactura" class="form-label fw-bold">Factura:</label>
                                <select name="slFactura" id="slFactura" class="form-select" aria-label="Seleccione estado de factura">
                                    <option value="T" <?php echo ($factura == 'T' ? 'selected' : ''); ?>>Todos</option>
                                    <option value="S" <?php echo ($factura == 'S' ? 'selected' : ''); ?>>SÍ</option>
                                    <option value="N" <?php echo ($factura == 'N' ? 'selected' : ''); ?>>NO</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="slEstado" class="form-label fw-bold">Estado:</label>
                                <select name="slEstado" id="slEstado" class="form-select" aria-label="Seleccione estado">
                                    <option value="T" <?php echo ($estado == 'T' ? 'selected' : ''); ?>>TODOS</option>
                                    <option value="S" <?php echo ($estado == 'S' ? 'selected' : ''); ?>>VALIDADOS</option>
                                    <option value="N" <?php echo ($estado == 'N' ? 'selected' : ''); ?>>NO VALIDADOS</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="slPartida" class="form-label fw-bold">Partida:</label>
                                <select name="slPartida" id="slPartida" class="form-select" aria-label="Seleccione partida">
                                    <option value="T" <?php echo ($partida == 'T' ? 'selected' : ''); ?>>TODOS</option>
                                    <option value="S" <?php echo ($partida == 'S' ? 'selected' : ''); ?>>SÍ</option>
                                    <option value="N" <?php echo ($partida == 'N' ? 'selected' : ''); ?>>NO</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="slAgente" class="form-label fw-bold">Agente:</label>
                                <select name="slAgente" id="slAgente" class="form-select" aria-label="Seleccione agente">
                                    <?php echo $cmbAgentes; ?>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="btn-group w-100" role="group">
                                    <button type="submit" name="cmdActualizar" id="cmdActualizar" class="btn btn-primary">
                                        <i class="fas fa-sync-alt me-2"></i>Actualizar
                                    </button>
                                    <button type="button" name="cmdAplicar" id="cmdAplicar" class="btn btn-success" onclick="aplicar_todos();">
                                        <i class="fas fa-check me-2"></i>Aplicar Validación
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Column Filters Section -->
                    <div class="column-filters" id="columnFilters" style="display: none;">
                        <h6><i class="fas fa-filter me-2"></i>Filtros por Columna</h6>
                        <div class="row g-3" id="filterControls">
                            <!-- Filters will be dynamically generated here -->
                        </div>
                    </div>

                    <!-- DataTable Container -->
                    <div class="table-container">
                        <table id="paquetesTable" class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th class="text-center" style="width: 120px;">Acciones</th>
                                    <th class="text-center" style="width: 60px;">PA</th>
                                    <th class="text-center" style="width: 60px;">Multi</th>
                                    <th class="text-center" style="width: 60px;">Docs</th>
                                    <th class="text-center" style="width: 100px;">Fec.Cargo</th>
                                    <th class="text-center" style="width: 60px;">FT</th>
                                    <th class="text-center" style="width: 70px;">Reemp</th>
                                    <th class="text-center" style="width: 70px;">Factura</th>
                                    <th class="text-center" style="width: 120px;">Código Barra</th>
                                    <th class="text-center" style="width: 100px;">Fecha</th>
                                    <th class="text-center" style="width: 150px;">Remitente</th>
                                    <th class="text-center" style="width: 150px;">Consignatario</th>
                                    <th class="text-center" style="width: 120px;">Tracking</th>
                                    <th class="text-center" style="width: 80px;">Retail</th>
                                    <th class="text-center" style="width: 80px;">Peso</th>
                                    <th class="text-center" style="width: 150px;">Contenido(Miami)</th>
                                    <th class="text-center" style="width: 100px;">Valor Dec.(Miami)</th>
                                    <th class="text-center" style="width: 150px;">Contenido(Cliente)</th>
                                    <th class="text-center" style="width: 100px;">Valor Dec.(Cliente)</th>
                                    <th class="text-center" style="width: 80px;">Documento</th>
                                    <th class="text-center" style="width: 80px;">Sección</th>
                                    <th class="text-center" style="width: 70px;">Emails</th>
                                    <th class="text-center" style="width: 70px;">Llamadas</th>
                                    <th class="text-center" style="width: 120px;">Usuario Ult. Acción</th>
                                    <th class="text-center" style="width: 120px;">Fec.Ultima Acción</th>
                                    <th class="text-center" style="width: 150px;">Ultima Acción</th>
                                    <th class="text-center" style="width: 100px;">Agente</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    // Connection is automatically closed when $db goes out of scope
?>

<script>
// Global flag to prevent multiple DataTable initializations
var paquetesTableInitialized = false;

// Wait for jQuery to be available before setting up component
function waitForjQueryAndSetup() {
    console.log('[Paquetes] Starting setup process...');
    if (typeof $ === 'undefined') {
        console.log('[Paquetes] jQuery not available, waiting...');
        setTimeout(waitForjQueryAndSetup, 100);
        return;
    }
    
    console.log('[Paquetes] jQuery available:', typeof $);
    console.log('[Paquetes] DataTables available:', typeof $.fn.DataTable);
    
    // Add CSRF token to all AJAX requests
    $.ajaxSetup({
        beforeSend: function(xhr, settings) {
            if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                xhr.setRequestHeader("X-CSRFToken", '<?php echo SecurityManager::generateCSRFToken(); ?>');
            }
        }
    });
    
    $(document).ready(function() {
        console.log('[Paquetes] DOM ready, starting initialization...');
        
        // Prevent multiple initializations
        if (paquetesTableInitialized) {
            console.log('[Paquetes] Already initialized, skipping...');
            return;
        }
        
        // Manually trigger Select2 initialization if the global function is available
        if (typeof window.initializeSelect2 === 'function') {
            console.log('[Paquetes] Triggering global Select2 initialization...');
            setTimeout(function() {
                window.initializeSelect2('#frmData');
            }, 100);
        }
        
        // Initialize DataTables
        if ($('#paquetesTable').length > 0) {
            console.log('[Paquetes] Table element found, initializing DataTables...');
            try {
                let table;
                
                // Check if DataTable is already initialized
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#paquetesTable')) {
                    console.log('[Paquetes] DataTable already initialized, destroying first...');
                    $('#paquetesTable').DataTable().destroy();
                }
                
                // DataTables language configuration (inline to avoid CSP issues)
                const languageConfig = {
                    url: "", // Explicitly disable external language loading
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
                    infoThousands: ",",
                    searchPlaceholder: "Buscar paquetes..."
                };
                
                const tableConfig = {
                    language: languageConfig,
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
                    responsive: true,
                    order: [[ 10, "desc" ]], // Order by "Fecha" column (newest first)
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: 'data/paquetes_data.php',
                        type: 'POST',
                        data: function(d) {
                            // Add CSRF token and form data
                            d.csrf_token = '<?php echo SecurityManager::generateCSRFToken(); ?>';
                            d.txtSeccion = $('#txtSeccion').val();
                            d.txtCodigoBarra = $('#txtCodigoBarra').val();
                            d.txtTracking = $('#txtTracking').val();
                            d.slFactura = $('#slFactura').val();
                            d.slEstado = $('#slEstado').val();
                            d.slPartida = $('#slPartida').val();
                            d.slAgente = $('#slAgente').val();
                            return d;
                        },
                        error: function(xhr, error, thrown) {
                            console.error('[Paquetes] AJAX Error:', error, thrown);
                            alert('Error al cargar los datos. Por favor, recargue la página.');
                        }
                    },
                    columnDefs: [
                        { targets: 0, orderable: false, searchable: false, className: "text-center align-middle fw-bold text-muted", width: "50px" },
                        { targets: 1, orderable: false, searchable: false, className: "text-center align-middle", width: "120px" },
                        { targets: [2, 3, 4, 6, 7, 8], className: "text-center align-middle", width: "60px" },
                        { targets: 5, className: "text-center align-middle", width: "100px" },
                        { targets: 9, className: "text-center align-middle", width: "120px" },
                        { targets: 10, className: "text-center align-middle", width: "100px" },
                        { targets: [11, 12], className: "text-start align-middle", width: "150px" },
                        { targets: 13, className: "text-center align-middle", width: "120px" },
                        { targets: [14, 15], className: "text-center align-middle", width: "80px" },
                        { targets: [16, 18], className: "text-start align-middle", width: "150px" },
                        { targets: [17, 19], className: "text-center align-middle", width: "100px" },
                        { targets: [20, 21], className: "text-center align-middle", width: "80px" },
                        { targets: [22, 23], className: "text-center align-middle", width: "70px" },
                        { targets: [24, 25], className: "text-center align-middle", width: "120px" },
                        { targets: 26, className: "text-start align-middle", width: "150px" },
                        { targets: 27, className: "text-center align-middle", width: "100px" }
                    ],
                    searching: true,
                    ordering: true,
                    info: true,
                    paging: true,
                    autoWidth: false,
                    scrollX: true,
                    scrollCollapse: true,
                    orderCellsTop: true,
                    fixedHeader: false,
                    drawCallback: function(settings) {
                        // Re-initialize tooltips after table draw
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }
                };
                
                // Initialize DataTables with 1.x syntax for better compatibility
                if (typeof $.fn.DataTable !== 'undefined') {
                    console.log('[Paquetes] Using DataTables 1.x syntax');
                    const oldConfig = {
                        "language": languageConfig,
                        "pageLength": tableConfig.pageLength,
                        "lengthMenu": tableConfig.lengthMenu,
                        "responsive": true,
                        "order": tableConfig.order,
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": 'data/paquetes_data.php',
                            "type": 'POST',
                            "data": function(d) {
                                d.csrf_token = '<?php echo SecurityManager::generateCSRFToken(); ?>';
                                d.txtSeccion = $('#txtSeccion').val();
                                d.txtCodigoBarra = $('#txtCodigoBarra').val();
                                d.txtTracking = $('#txtTracking').val();
                                d.slFactura = $('#slFactura').val();
                                d.slEstado = $('#slEstado').val();
                                d.slPartida = $('#slPartida').val();
                                d.slAgente = $('#slAgente').val();
                                return d;
                            },
                            "error": function(xhr, error, thrown) {
                                console.error('[Paquetes] AJAX Error:', error, thrown);
                                alert('Error al cargar los datos. Por favor, recargue la página.');
                            }
                        },
                        "columnDefs": tableConfig.columnDefs.map(def => ({
                            "targets": def.targets,
                            "orderable": def.orderable !== undefined ? def.orderable : true,
                            "searchable": def.searchable !== undefined ? def.searchable : true,
                            "className": def.className,
                            "width": def.width
                        })),
                        "dom": 'lfrtip',
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "paging": true,
                        "autoWidth": false,
                        "scrollX": true,
                        "scrollCollapse": true,
                        "orderCellsTop": true,
                        "drawCallback": function(settings) {
                            $('[data-bs-toggle="tooltip"]').tooltip();
                        }
                    };
                    
                    try {
                        table = $('#paquetesTable').DataTable(oldConfig);
                    } catch (dtError) {
                        console.error('[Paquetes] DataTables 1.x initialization failed:', dtError);
                        const fallbackConfig = {...oldConfig};
                        delete fallbackConfig.responsive;
                        table = $('#paquetesTable').DataTable(fallbackConfig);
                        console.log('[Paquetes] DataTables initialized with fallback config');
                    }
                } else {
                    throw new Error('DataTables library not found');
                }
                
                console.log('[Paquetes] DataTables initialized successfully');
                paquetesTableInitialized = true;
                
                // Store table reference globally
                window.paquetesTable = table;
                
                // Add column filters after DataTables initialization
                setTimeout(function() {
                    console.log('[Paquetes] Adding modern column filters...');
                    addModernColumnFilters(table);
                }, 500);
                
                // Add refresh button functionality
                $('#cmdActualizar').on('click', function(e) {
                    e.preventDefault();
                    if (window.paquetesTable && typeof window.paquetesTable.ajax === 'object') {
                        window.paquetesTable.ajax.reload();
                    }
                });
                
            } catch (e) {
                console.error('[Paquetes] Error initializing DataTables:', e);
            }
        } else {
            console.warn('[Paquetes] DataTables not available or table not found');
        }
        
        // Function to add modern column filters above the table
        function addModernColumnFilters(table) {
            try {
                // Check if table is valid
                if (!table) {
                    console.error('[Paquetes] Invalid table object for filters');
                    return;
                }
                
                var api;
                if (table.api && typeof table.api === 'function') {
                    api = table.api();
                } else if (table.columns && typeof table.columns === 'function') {
                    api = table;
                } else {
                    console.error('[Paquetes] Cannot get API from table object');
                    return;
                }
                
                var $filterContainer = $('#filterControls');
                var $filtersSection = $('#columnFilters');
                
                // Clear existing filters
                $filterContainer.empty();
                
                // Column definitions for filters (based on the 28 columns)
                var filterColumns = [
                    { index: 2, name: 'PA', type: 'select', options: ['Todos', 'S', 'N'] },
                    { index: 3, name: 'Multi', type: 'select', options: ['Todos', 'S', 'N'] },
                    { index: 4, name: 'Docs', type: 'text' },
                    { index: 6, name: 'FT', type: 'select', options: ['Todos', 'SÍ', 'NO'] },
                    { index: 7, name: 'Reemp', type: 'select', options: ['Todos', 'SÍ', 'NO'] },
                    { index: 8, name: 'Factura', type: 'select', options: ['Todos', 'SÍ', 'NO'] },
                    { index: 9, name: 'Código Barra', type: 'text' },
                    { index: 11, name: 'Remitente', type: 'text' },
                    { index: 12, name: 'Consignatario', type: 'text' },
                    { index: 13, name: 'Tracking', type: 'text' },
                    { index: 14, name: 'Retail', type: 'text' },
                    { index: 16, name: 'Contenido(Miami)', type: 'text' },
                    { index: 18, name: 'Contenido(Cliente)', type: 'text' },
                    { index: 21, name: 'Sección', type: 'text' },
                    { index: 24, name: 'Usuario Ult. Acción', type: 'text' },
                    { index: 27, name: 'Agente', type: 'text' }
                ];
                
                // Create filter controls with improved styling
                filterColumns.forEach(function(col) {
                    var column = api.column(col.index);
                    var filterGroup = $('<div class="col-md-3 col-lg-2 filter-group"></div>');
                    var label = $('<label>' + col.name + '</label>');
                    var input;
                    
                    if (col.type === 'select') {
                        input = $('<select class="form-select form-select-sm"></select>');
                        col.options.forEach(function(option) {
                            input.append('<option value="' + (option === 'Todos' ? '' : option) + '">' + option + '</option>');
                        });
                    } else {
                        input = $('<input type="text" class="form-control form-control-sm" placeholder="Filtrar ' + col.name.toLowerCase() + '..." />');
                    }
                    
                    // Add event listeners with debouncing for better performance
                    var timeout;
                    input.on('keyup change clear', function() {
                        var val = $(this).val();
                        clearTimeout(timeout);
                        timeout = setTimeout(function() {
                            if (column.search() !== val) {
                                column.search(val).draw();
                            }
                        }, 300);
                    });
                    
                    filterGroup.append(label).append(input);
                    $filterContainer.append(filterGroup);
                });
                
                // Show the filters section
                $filtersSection.show();
                console.log('[Paquetes] Modern column filters added successfully');
                
            } catch (e) {
                console.error('[Paquetes] Error adding modern column filters:', e);
            }
        }
        
        // Initialize tooltips if Bootstrap tooltips are available
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
        
        console.log('[Paquetes] Component initialization complete');
    });
}

// Start the setup process
waitForjQueryAndSetup();
// Global functions
function aplicar_todos() {
    if (confirm("¿Seguro de aplicar validación?")) {
        // Add CSRF token to form
        var form = document.getElementById("frmData");
        var csrfInput = document.createElement("input");
        csrfInput.type = "hidden";
        csrfInput.name = "csrf_token";
        csrfInput.value = '<?php echo SecurityManager::generateCSRFToken(); ?>';
        form.appendChild(csrfInput);
        
        form.action = "db/aplicar_validacion.php";
        form.submit();
    }
}

// Function to close action modal and reload table
function closeActionModal() {
    const modal = document.getElementById('actionModal');
    if (modal) {
        const bootstrapModal = bootstrap.Modal.getInstance(modal);
        if (bootstrapModal) {
            bootstrapModal.hide();
        }
    }
    
    // Reload DataTable
    if (window.paquetesTable && typeof window.paquetesTable.ajax === 'object') {
        window.paquetesTable.ajax.reload();
    }
}

// Make closeActionModal globally available
window.closeActionModal = closeActionModal;

// Function to reload DataTable from modal context
function reloadParentTable() {
    if (window.paquetesTable && typeof window.paquetesTable.ajax === 'object') {
        window.paquetesTable.ajax.reload();
    }
}
function ver_documento_pdf(url, codigo_barra) {
    if (url == "") {
        alert("No tiene documento cargado");
        return;
    }
    
    // Create Bootstrap modal
    const modalId = 'pdfModal';
    let modal = document.getElementById(modalId);
    
    if (!modal) {
        modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = modalId;
        modal.tabIndex = -1;
        modal.setAttribute('aria-labelledby', modalId + 'Label');
        modal.setAttribute('aria-hidden', 'true');
        
        modal.innerHTML = `
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="${modalId}Label">Documento PDF - Código Barra: ${codigo_barra}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe src="${url}" width="100%" height="600" style="border: none;"></iframe>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    } else {
        // Update existing modal
        modal.querySelector('.modal-title').textContent = `Documento PDF - Código Barra: ${codigo_barra}`;
        modal.querySelector('iframe').src = url;
    }
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

function ver_archivos_miami(codigo_barra) {
    // Create or reuse Bootstrap modal
    const modalId = 'miamiFilesModal';
    let modal = document.getElementById(modalId);
    
    if (!modal) {
        modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = modalId;
        modal.tabIndex = -1;
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Archivos Miami</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Load content via AJAX
    fetch('modulos/ver_archivos_miami.php?codigo_barra=' + encodeURIComponent(codigo_barra))
        .then(response => response.text())
        .then(data => {
            modal.querySelector('.modal-body').innerHTML = data;
        })
        .catch(error => {
            modal.querySelector('.modal-body').innerHTML = '<div class="alert alert-danger">Error cargando archivos: ' + error.message + '</div>';
        });
}

function ver_informacion(codigo_barra, seccion) {
    // Create or reuse Bootstrap modal
    const modalId = 'infoModal';
    let modal = document.getElementById(modalId);
    
    if (!modal) {
        modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = modalId;
        modal.tabIndex = -1;
        modal.innerHTML = `
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Información del Paquete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Load content via AJAX
    fetch('ver_datos_contactos.php?seccion=' + encodeURIComponent(seccion) + '&codigo_barra=' + encodeURIComponent(codigo_barra))
        .then(response => response.text())
        .then(data => {
            modal.querySelector('.modal-body').innerHTML = data;
        })
        .catch(error => {
            modal.querySelector('.modal-body').innerHTML = '<div class="alert alert-danger">Error cargando información: ' + error.message + '</div>';
        });
}

function ver_acciones(codigo_barra, seccion) {
    // Create or reuse Bootstrap modal
    const modalId = 'accionesModal';
    let modal = document.getElementById(modalId);
    
    if (!modal) {
        modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = modalId;
        modal.tabIndex = -1;
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Acciones Realizadas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Load content via AJAX
    fetch('ver_acciones.php?seccion=' + encodeURIComponent(seccion) + '&codigo_barra=' + encodeURIComponent(codigo_barra))
        .then(response => response.text())
        .then(data => {
            modal.querySelector('.modal-body').innerHTML = data;
        })
        .catch(error => {
            modal.querySelector('.modal-body').innerHTML = '<div class="alert alert-danger">Error cargando acciones: ' + error.message + '</div>';
        });
}

function ver_fuera_tiempo(codigo_barra, seccion) {
    // Create or reuse Bootstrap modal
    const modalId = 'fueraTiempoModal';
    let modal = document.getElementById(modalId);
    
    if (!modal) {
        modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = modalId;
        modal.tabIndex = -1;
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Documentos Fuera de Tiempo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Load content via AJAX
    fetch('ver_fuera_de_tiempo.php?seccion=' + encodeURIComponent(seccion) + '&codigo_barra=' + encodeURIComponent(codigo_barra))
        .then(response => response.text())
        .then(data => {
            modal.querySelector('.modal-body').innerHTML = data;
        })
        .catch(error => {
            modal.querySelector('.modal-body').innerHTML = '<div class="alert alert-danger">Error cargando documentos: ' + error.message + '</div>';
        });
}

function change(combo_name, codigo_barra, seccion, tracking, ciudad) {
    var combo = document.getElementById(combo_name);
    var indice = combo.selectedIndex;
    var accion = combo.options[indice].value;
    
    switch(accion) {
        case "1":
            openActionModal('Cancelar Paquete', 'cancelar_paquete_todos.php?codigo_barra=' + codigo_barra);
            break;
        case "2":
            openActionModal('Cargar Documento', 'cargar_documento_todos.php?codigo_barra=' + codigo_barra);
            break;
        case "3":
            openActionModal('Revisar Prealertas', 'revisar_prealertas_todos.php?codigo_barra=' + codigo_barra);
            break;
        case "4":
            openActionModal('Revisar Códigos Barra', 'revisar_codigosbarra_todos.php?codigo_barra=' + codigo_barra);
            break;
        case "5":
            openActionModal('Paquete Llamada', 'paquete_llamada_todos.php?codigo_barra=' + codigo_barra);
            break;
        case "6":
            openActionModal('Enviar Link', 'enviar_link_todos.php?codigo_barra=' + codigo_barra + '&todos=1');
            break;
        case "7":
            openActionModal('Modificar Valor Contenido', 'modificar_valor_contenido_todos.php?codigo_barra=' + codigo_barra + '&todos=1');
            break;
        case "8":
            openActionModal('Modificar Valor Declarado', 'modificar_valor_declarado.php?codigo_barra=' + codigo_barra + '&todos=1');
            break;
        case "9":
            openActionModal('Modificar Multiple', 'modificar_multiple.php?codigo_barra=' + codigo_barra + '&todos=1');
            break;
        case "10":
            if (confirm("¿Está seguro de reemplazar factura el CB " + codigo_barra + "?") == true) {
                $.post("db/reemplazar_factura.php", {
                    codigo_barra: codigo_barra,
                    usuario_reemplazo: '<?php echo $_SESSION['nombre_usuario'] ?>',
                    csrf_token: '<?php echo SecurityManager::generateCSRFToken(); ?>'
                }, function (respuesta) {
                    alert("Factura reemplazada");
                    if (window.paquetesTable && typeof window.paquetesTable.ajax === 'object') {
                        window.paquetesTable.ajax.reload();
                    } else {
                        window.location = "index2.php?component=paquetes_todos";
                    }
                });
            }
            break;
        case "11":
            openActionModal('Validar Paquete', 'validar_paquete.php?codigo_barra=' + codigo_barra + '&todos=1');
            break;
        case "12":
            openActionModal('Actualizar Partida', 'actualizar_partida.php?codigo_barra=' + codigo_barra + '&todos=1');
            break;
        case "13":
            openActionModal('Modificar Multiple', 'modificar_multiple.php?codigo_barra=' + codigo_barra + '&todos=1');
            break;
    }
}

// Function to open action modals
function openActionModal(title, url) {
    const modalId = 'actionModal';
    let modal = document.getElementById(modalId);
    
    if (!modal) {
        modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = modalId;
        modal.tabIndex = -1;
        modal.innerHTML = `
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="actionModalLabel">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="actionModalBody">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    } else {
        // Update existing modal
        modal.querySelector('.modal-title').textContent = title;
        modal.querySelector('#actionModalBody').innerHTML = `
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        `;
    }
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Load content via AJAX
    fetch(url)
        .then(response => response.text())
        .then(data => {
            modal.querySelector('#actionModalBody').innerHTML = data;
            
            // Ensure jQuery is available in the modal context
            if (typeof $ !== 'undefined') {
                // Make jQuery available to the modal content
                modal.querySelector('#actionModalBody').$ = $;
            }
            
            // Execute any scripts in the loaded content
            const scripts = modal.querySelectorAll('#actionModalBody script');
            scripts.forEach(script => {
                const newScript = document.createElement('script');
                if (script.src) {
                    newScript.src = script.src;
                } else {
                    // Modify script content to work in modal context
                    let scriptContent = script.textContent;
                    
                    // Replace window.location redirects with modal close
                    scriptContent = scriptContent.replace(
                        /window\.location\s*=\s*["']index2\.php\?component=paquetes_todos["']/g,
                        'window.closeActionModal()'
                    );
                    
                    newScript.textContent = scriptContent;
                }
                document.head.appendChild(newScript);
            });
        })
        .catch(error => {
            modal.querySelector('#actionModalBody').innerHTML = '<div class="alert alert-danger">Error cargando contenido: ' + error.message + '</div>';
        });
    
    // Add event listener for modal close to reload DataTable
    modal.addEventListener('hidden.bs.modal', function () {
        if (window.paquetesTable && typeof window.paquetesTable.ajax === 'object') {
            window.paquetesTable.ajax.reload();
        }
    });
}
</script>
