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
    $db = new DatabaseHelper();
    
    // Get traffic agents
    $agentes = $db->getRecords("SELECT a.* FROM VW_AGENTE_TRAFICO a ORDER BY a.ORDEN ASC");
    
    // Debug: Show count
    error_log("Agentes found: " . count($agentes));

    // Get entities for agent selection
    $entidades = $db->getRecords("SELECT a.* FROM entidad a WHERE a.inactivo = 0 ORDER BY a.descripcion ASC");

    $cmbAgentes = "<option value='0'>Seleccione un agente</option>";
    foreach ($entidades as $registro) {
        $descripcion = htmlspecialchars($registro["descripcion"], ENT_QUOTES, 'UTF-8');
        $cmbAgentes .= "<option value=\"" . $registro["id_entidad"] . "\">$descripcion</option>";
    }

    // Get service profiles
    $perfiles = $db->getRecords("SELECT a.* FROM dbo.PERFIL_SERVICIO a WHERE a.id_perfil_servicio <> 0 ORDER BY a.descripcion ASC");

    $cmbPerfiles = "<option value='0'>NO APLICA</option>";
    foreach ($perfiles as $registro) {
        $descripcion = htmlspecialchars($registro["descripcion"], ENT_QUOTES, 'UTF-8');
        $cmbPerfiles .= "<option value=\"" . $registro["id_perfil_servicio"] . "\">$descripcion</option>";
    }

    // Get service modalities
    $modalidades = $db->getRecords("SELECT a.* FROM dbo.CLI_MODALIDAD_SERVICIO a ORDER BY a.descripcion ASC");

    $cmbModalidades = "<option value='0'>NO APLICA</option>";
    foreach ($modalidades as $registro) {
        $descripcion = htmlspecialchars($registro["descripcion"], ENT_QUOTES, 'UTF-8');
        $cmbModalidades .= "<option value=\"" . $registro["ID"] . "\">$descripcion</option>";
    }

} catch (Exception $e) {
    error_log("Error in agentes.php: " . $e->getMessage());
    echo '<div class="alert alert-danger">Error al cargar los datos. Por favor, contacte al administrador.</div>';
    return;
}
?>

<!-- Agentes component content -->
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #3d4a2d 0%, #2a3d1e 100%); border-bottom: 2px solid #243a1a;">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Gestión de Agentes de Tráfico
                    </h5>
                </div>
                <div class="card-body">
                    <form id="frmData" name="frmData" method="post" action="">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="slAgentes" class="form-label fw-bold">Agente</label>
                                <select id="slAgentes" class="form-select" aria-label="Seleccione un agente">
                                    <?php echo $cmbAgentes ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="slPerfil" class="form-label fw-bold">Perfil Servicio</label>
                                <select id="slPerfil" class="form-select" aria-label="Seleccione un Servicio">
                                    <?php echo $cmbPerfiles ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="slModalidad" class="form-label fw-bold">Modalidad</label>
                                <select id="slModalidad" class="form-select" aria-label="Seleccione una modalidad">
                                    <?php echo $cmbModalidades ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                                <button id="btnGrabar" class="btn btn-success btn-lg" type="button">
                                    <i class="fas fa-plus me-2"></i>
                                    Agregar Agente
                                </button>
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

                    <div class="table-container">
                        <table id="agentesTable" class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center" style="width: 60px;">#</th>
                                    <th class="text-center" style="width: 140px;">Acciones</th>
                                    <th class="text-center" style="width: 80px;">ID</th>
                                    <th class="text-center" style="width: 100px;">ID Entidad</th>
                                    <th class="text-start" style="min-width: 200px;">Agente</th>
                                    <th class="text-center" style="width: 150px;">Perfil</th>
                                    <th class="text-center" style="width: 150px;">Modalidad</th>
                                    <th class="text-center" style="width: 100px;">Orden</th>
                                    <th class="text-center" style="width: 100px;">Estado</th>
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
// Component-specific functionality (Select2 initialization handled globally by index2.php)

// Global flag to prevent multiple DataTable initializations
var agentesTableInitialized = false;

// Wait for jQuery to be available before setting up component
function waitForjQueryAndSetup() {
    console.log('[Agentes] Starting setup process...');
    if (typeof $ === 'undefined') {
        console.log('[Agentes] jQuery not available, waiting...');
        setTimeout(waitForjQueryAndSetup, 100);
        return;
    }
    
    console.log('[Agentes] jQuery available:', typeof $);
    console.log('[Agentes] DataTables available:', typeof $.fn.DataTable);
    
    $(document).ready(function() {
        console.log('[Agentes] DOM ready, starting initialization...');
        
        // Prevent multiple initializations
        if (agentesTableInitialized) {
            console.log('[Agentes] Already initialized, skipping...');
            return;
        }
        
        // Manually trigger Select2 initialization if the global function is available
        if (typeof window.initializeSelect2 === 'function') {
            console.log('[Agentes] Triggering global Select2 initialization...');
            setTimeout(function() {
                window.initializeSelect2('#frmData');
            }, 100);
        }
        
        // Manually trigger DataTables initialization if the global function is available
        if (typeof window.initializeDataTables === 'function') {
            console.log('[Agentes] Triggering global DataTables initialization...');
            setTimeout(function() {
                window.initializeDataTables('#frmData');
            }, 200);
        }
        
        // Initialize DataTables
        if ($('#agentesTable').length > 0) {
            console.log('[Agentes] Table element found, initializing DataTables...');
            console.log('[Agentes] Table rows count:', $('#agentesTable tbody tr').length);
            try {
                let table;
                
                // Check if DataTable is already initialized
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#agentesTable')) {
                    console.log('[Agentes] DataTable already initialized, destroying first...');
                    $('#agentesTable').DataTable().destroy();
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
                    searchPlaceholder: "Buscar agentes..."
                };
                
                const tableConfig = {
                    language: languageConfig,
                    pageLength: 15,
                    lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "Todos"]],
                    responsive: true, // Simple responsive without complex configuration
                    order: [[ 7, "asc" ]], // Order by "Orden" column
                    processing: true, // Show processing indicator
                    serverSide: true, // Enable server-side processing
                    ajax: {
                        url: 'data/agentes_data.php',
                        type: 'POST',
                        data: function(d) {
                            // Add CSRF token
                            d.csrf_token = '<?php echo SecurityManager::generateCSRFToken(); ?>';
                            return d;
                        },
                        error: function(xhr, error, thrown) {
                            console.error('[Agentes] AJAX Error:', error, thrown);
                            alert('Error al cargar los datos. Por favor, recargue la página.');
                        }
                    },
                    columnDefs: [
                        { 
                            targets: 0, // No. column
                            orderable: false,
                            searchable: false,
                            className: "text-center align-middle fw-bold text-muted",
                            width: "50px"
                        },
                        { 
                            targets: 1, // Acciones column
                            orderable: false,
                            searchable: false,
                            className: "text-center align-middle",
                            width: "120px"
                        },
                        { 
                            targets: [2, 3], // ID columns
                            className: "text-center align-middle",
                            width: "80px"
                        },
                        { 
                            targets: 4, // Agente column
                            className: "text-start align-middle",
                            width: "250px"
                        },
                        { 
                            targets: [5, 6], // Perfil and Modalidad columns
                            className: "text-center align-middle",
                            width: "150px"
                        },
                        { 
                            targets: 7, // Orden column
                            className: "text-center align-middle",
                            width: "80px"
                        },
                        { 
                            targets: 8, // Estado column
                            className: "text-center align-middle",
                            width: "100px"
                        }
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
                
                // Try DataTables 2.x syntax first
                if (typeof DataTable !== 'undefined') {
                    console.log('[Agentes] Using DataTables 2.x syntax');
                    // For DataTables 2.x, use layout instead of dom
                    tableConfig.layout = {
                        topStart: 'pageLength',
                        topEnd: 'search',
                        bottomStart: 'info',
                        bottomEnd: 'paging'
                    };
                    table = new DataTable('#agentesTable', tableConfig);
                } else if (typeof $.fn.DataTable !== 'undefined') {
                    console.log('[Agentes] Using DataTables 1.x syntax');
                    // For DataTables 1.x, use server-side configuration
                    const oldConfig = {
                        "language": languageConfig,
                        "pageLength": tableConfig.pageLength,
                        "lengthMenu": tableConfig.lengthMenu,
                        "responsive": true, // Simple responsive setting
                        "order": tableConfig.order,
                        "processing": true, // Enable processing indicator
                        "serverSide": true, // Enable server-side processing
                        "ajax": {
                            "url": 'data/agentes_data.php',
                            "type": 'POST',
                            "data": function(d) {
                                // Add CSRF token
                                d.csrf_token = '<?php echo SecurityManager::generateCSRFToken(); ?>';
                                return d;
                            },
                            "error": function(xhr, error, thrown) {
                                console.error('[Agentes] AJAX Error:', error, thrown);
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
                        "dom": 'lfrtip', // Simple DOM - shows all default controls
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "paging": true,
                        "autoWidth": false,
                        "scrollX": true,
                        "scrollCollapse": true,
                        "orderCellsTop": true,
                        "drawCallback": function(settings) {
                            // Re-initialize tooltips after table draw
                            $('[data-bs-toggle="tooltip"]').tooltip();
                        }
                    };
                    
                    try {
                        table = $('#agentesTable').DataTable(oldConfig);
                    } catch (dtError) {
                        console.error('[Agentes] DataTables 1.x initialization failed:', dtError);
                        // Fallback configuration without responsive
                        const fallbackConfig = {...oldConfig};
                        delete fallbackConfig.responsive;
                        table = $('#agentesTable').DataTable(fallbackConfig);
                        console.log('[Agentes] DataTables initialized with fallback config');
                    }
                } else {
                    throw new Error('DataTables library not found');
                }
                console.log('[Agentes] DataTables initialized successfully');
                agentesTableInitialized = true;
                
                // Store table reference globally for action functions
                window.agentesTable = table;
                
                // Add column filters after DataTables initialization
                setTimeout(function() {
                    console.log('[Agentes] Adding modern column filters...');
                    addModernColumnFilters(table);
                }, 500);
                
            } catch (e) {
                console.error('[Agentes] Error initializing DataTables:', e);
            }
        } else {
            console.warn('[Agentes] DataTables not available or table not found');
        }
        
        // Function to add modern column filters above the table
        function addModernColumnFilters(table) {
            try {
                // Check if table is valid
                if (!table) {
                    console.error('[Agentes] Invalid table object for filters');
                    return;
                }
                
                var api;
                if (table.api && typeof table.api === 'function') {
                    api = table.api();
                } else if (table.columns && typeof table.columns === 'function') {
                    api = table;
                } else {
                    console.error('[Agentes] Cannot get API from table object');
                    return;
                }
                
                var $filterContainer = $('#filterControls');
                var $filtersSection = $('#columnFilters');
                
                // Clear existing filters
                $filterContainer.empty();
                
                // Column definitions for filters
                var filterColumns = [
                    { index: 2, name: 'ID', type: 'text' },
                    { index: 3, name: 'ID Entidad', type: 'text' },
                    { index: 4, name: 'Agente', type: 'text' },
                    { index: 5, name: 'Perfil', type: 'text' },
                    { index: 6, name: 'Modalidad', type: 'text' },
                    { index: 7, name: 'Orden', type: 'text' },
                    { index: 8, name: 'Estado', type: 'select', options: ['Todos', 'Activo', 'Inactivo'] }
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
                console.log('[Agentes] Modern column filters added successfully');
                
            } catch (e) {
                console.error('[Agentes] Error adding modern column filters:', e);
            }
        }
        
        // Initialize tooltips if Bootstrap tooltips are available
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
        
        // Button click handler
        $("#btnGrabar").off('click').on('click', function() {
            if ($("#slAgentes").val() === "0" || $("#slAgentes").val() === null) {
                // Show better alert with SweetAlert if available, otherwise use regular alert
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: 'Por favor, seleccione un agente',
                        confirmButtonColor: '#007bff'
                    });
                } else {
                    alert("Por favor, seleccione un agente");
                }
                $("#slAgentes").focus();
                return;
            }
            
            // Show loading state
            var $btn = $(this);
            var originalText = $btn.html();
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Procesando...');
            
            $.ajax({
                type: 'POST',
                url: 'edit/agregar_agente.php',
                data: {
                    'id_agente': $("#slAgentes").val(),
                    'perfil': $("#slPerfil").val(),
                    'modalidad': $("#slModalidad").val(),
                    'orden': 1,
                    'csrf_token': '<?php echo SecurityManager::generateCSRFToken(); ?>'
                },
                success: function(data) {
                    if (data.trim() === "") {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: 'Agente agregado exitosamente',
                                confirmButtonColor: '#28a745'
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            alert("Agente agregado exitosamente");
                            location.reload();
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data,
                                confirmButtonColor: '#dc3545'
                            });
                        } else {
                            alert(data);
                        }
                    }
                },
                error: function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al agregar el agente. Por favor, intente nuevamente.',
                            confirmButtonColor: '#dc3545'
                        });
                    } else {
                        alert("Error al agregar el agente. Por favor, intente nuevamente.");
                    }
                },
                complete: function() {
                    // Restore button state
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });
        
        console.log('[Agentes] Component initialization complete');
    });
}

// Start the setup process
waitForjQueryAndSetup();

// Global functions that don't depend on jQuery initialization
function eliminar(id, agente) {
    if (confirm("¿Está seguro de eliminar al agente: " + agente + "?")) {
        // Wait for jQuery to be available
        function doEliminar() {
            if (typeof $ !== 'undefined') {
                $.ajax({
                    type: 'POST',
                    url: 'edit/eliminar_agente.php',
                    data: {
                        'id_agente': id,
                        'csrf_token': '<?php echo SecurityManager::generateCSRFToken(); ?>'
                    },
                    success: function(data) {
                        if (data.trim() === "") {
                            alert("Agente eliminado exitosamente");
                            location.reload();
                        } else {
                            alert(data);
                        }
                    },
                    error: function() {
                        alert("Error al eliminar el agente. Por favor, intente nuevamente.");
                    }
                });
            } else {
                setTimeout(doEliminar, 100);
            }
        }
        doEliminar();
    }
}

function activar(id, agente, estado) {
    if (confirm("¿Está seguro de cambiar el estado del agente: " + agente + ", estado actual: " + estado + "?")) {
        function doActivar() {
            if (typeof $ !== 'undefined') {
                $.ajax({
                    type: 'POST',
                    url: 'edit/estado_agente.php',
                    data: {
                        'id_agente': id,
                        'estado': estado,
                        'csrf_token': '<?php echo SecurityManager::generateCSRFToken(); ?>'
                    },
                    success: function(data) {
                        if (data.trim() === "") {
                            alert("Estado actualizado exitosamente");
                            // Reload DataTable instead of full page reload
                            if (window.agentesTable && typeof window.agentesTable.ajax === 'object') {
                                window.agentesTable.ajax.reload();
                            } else {
                                location.reload();
                            }
                        } else {
                            alert(data);
                        }
                    },
                    error: function() {
                        alert("Error al actualizar el estado. Por favor, intente nuevamente.");
                    }
                });
            } else {
                setTimeout(doActivar, 100);
            }
        }
        doActivar();
    }
}

function orden(id, agente, orden) {
    var nuevoOrden = prompt("Cambiar orden del agente " + agente + ":", orden);
    if (nuevoOrden !== null && nuevoOrden !== "") {
        function doOrden() {
            if (typeof $ !== 'undefined') {
                $.ajax({
                    type: 'POST',
                    url: 'edit/orden_agente.php',
                    data: {
                        'id_agente': id,
                        'orden': nuevoOrden,
                        'csrf_token': '<?php echo SecurityManager::generateCSRFToken(); ?>'
                    },
                    success: function(data) {
                        if (data.trim() === "") {
                            alert("Orden actualizado exitosamente");
                            // Reload DataTable instead of full page reload
                            if (window.agentesTable && typeof window.agentesTable.ajax === 'object') {
                                window.agentesTable.ajax.reload();
                            } else {
                                location.reload();
                            }
                        } else {
                            alert(data);
                        }
                    },
                    error: function() {
                        alert("Error al actualizar el orden. Por favor, intente nuevamente.");
                    }
                });
            } else {
                setTimeout(doOrden, 100);
            }
        }
        doOrden();
    }
}

// Additional functions for DataTables action buttons
function editarAgente(id) {
    // Placeholder for edit functionality
    alert('Función de edición en desarrollo para agente ID: ' + id);
}

function toggleEstado(id, nuevoEstado) {
    var mensaje = nuevoEstado ? 'activar' : 'desactivar';
    if (confirm('¿Está seguro que desea ' + mensaje + ' este agente?')) {
        activar(id, nuevoEstado);
    }
}

function eliminarAgente(id) {
    if (confirm('¿Está seguro que desea eliminar este agente? Esta acción no se puede deshacer.')) {
        function doEliminar() {
            if (typeof $ !== 'undefined') {
                $.ajax({
                    type: 'POST',
                    url: 'edit/eliminar_agente.php',
                    data: {
                        'id_agente': id,
                        'csrf_token': '<?php echo SecurityManager::generateCSRFToken(); ?>'
                    },
                    success: function(data) {
                        if (data.trim() === "") {
                            alert("Agente eliminado exitosamente");
                            // Reload DataTable instead of full page reload
                            if (window.agentesTable && typeof window.agentesTable.ajax === 'object') {
                                window.agentesTable.ajax.reload();
                            } else {
                                location.reload();
                            }
                        } else {
                            alert(data);
                        }
                    },
                    error: function() {
                        alert("Error al eliminar el agente. Por favor, intente nuevamente.");
                    }
                });
            } else {
                setTimeout(doEliminar, 100);
            }
        }
        doEliminar();
    }
}
</script>