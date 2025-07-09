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

// Generate CSRF token for forms
$csrf_token = SecurityManager::generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Agentes DataTable</title>
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Font Awesome for additional icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- DataTables with Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <script>
        // Global CSRF token for AJAX requests
        window.csrf_token = '<?php echo $csrf_token; ?>';
    </script>
</head>

<body class="bg-light">
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-users me-2"></i>Test Agentes Component with DataTables</h4>
                    </div>
                    <div class="card-body">
                        <div id="debug-console" class="alert alert-info mb-3">
                            <strong>Debug Console:</strong><br>
                            <div id="debug-log" style="max-height: 150px; overflow-y: auto;"></div>
                        </div>
                        
                        <button id="testComponents" class="btn btn-success">Test Component Initialization</button>
                        <button id="reloadTable" class="btn btn-warning">Reload DataTable</button>
                        <hr>
                        
                        <?php
                        // Include the agentes component directly
                        $_REQUEST['component'] = 'agentes';
                        $_REQUEST['token'] = $csrf_token;
                        $_REQUEST['tokenid'] = $csrf_token; // For backward compatibility
                        
                        if (SecurityManager::validateCSRFToken($_REQUEST['token'])) {
                            $component = SecurityManager::sanitizeInput($_REQUEST['component'], 'component');
                            if (!SecurityManager::includeComponent($component)) {
                                echo '<div class="alert alert-danger d-flex align-items-center" role="alert">';
                                echo '<i class="bi bi-exclamation-triangle-fill me-2"></i>';
                                echo 'Componente no encontrado o no autorizado.';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger d-flex align-items-center" role="alert">';
                            echo '<i class="bi bi-shield-exclamation me-2"></i>';
                            echo 'Token de seguridad inválido. Por favor, recargue la página.';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts in correct order -->
    <!-- Bootstrap 5.3.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 JS (must be loaded after jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Debug logging function
        function debugLog(message) {
            console.log(message);
            const debugDiv = document.getElementById('debug-log');
            if (debugDiv) {
                debugDiv.innerHTML += new Date().toLocaleTimeString() + ': ' + message + '<br>';
                debugDiv.scrollTop = debugDiv.scrollHeight;
            }
        }
        
        $(document).ready(function() {
            debugLog('Page ready - initializing components...');
            
            // Global functions similar to index2.php
            window.initializeSelect2 = function(container) {
                var $container = container ? $(container) : $(document);
                debugLog('Initializing Select2 in: ' + (container || 'document'));
                
                $container.find('select').not('.select2-hidden-accessible').each(function() {
                    var $select = $(this);
                    var id = $select.attr('id');
                    
                    try {
                        var placeholder = 'Seleccione una opción';
                        if (id === 'slAgentes') placeholder = 'Seleccione un agente';
                        else if (id === 'slPerfil') placeholder = 'Seleccione un perfil';
                        else if (id === 'slModalidad') placeholder = 'Seleccione una modalidad';
                        
                        $select.select2({
                            theme: 'bootstrap-5',
                            placeholder: placeholder,
                            allowClear: true,
                            width: '100%'
                        });
                        
                        debugLog('✓ Select2 initialized for: ' + id);
                    } catch (e) {
                        debugLog('✗ Error initializing Select2 for ' + id + ': ' + e.message);
                    }
                });
            };
            
            window.initializeDataTables = function(container) {
                var $container = container ? $(container) : $(document);
                debugLog('Initializing DataTables in: ' + (container || 'document'));
                
                $container.find('table[id$="Table"]').not('.dataTable').each(function() {
                    var $table = $(this);
                    var id = $table.attr('id');
                    
                    debugLog('Found table: ' + id + ', rows: ' + $table.find('tbody tr').length);
                    
                    try {
                        var config = {
                            "language": {
                                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                            },
                            "pageLength": 25,
                            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
                            "responsive": true,
                            "processing": false,
                            "serverSide": false,
                            "searching": true,
                            "ordering": true,
                            "info": true,
                            "paging": true,
                            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
                        };
                        
                        if (id === 'agentesTable') {
                            config.order = [[ 7, "asc" ]];
                            config.columnDefs = [
                                { "targets": 0, "orderable": false, "searchable": false, "width": "5%" },
                                { "targets": 1, "orderable": false, "searchable": false, "width": "15%" },
                                { "targets": [2, 3], "width": "8%" },
                                { "targets": 4, "width": "20%" },
                                { "targets": [5, 6], "width": "15%" },
                                { "targets": 7, "width": "10%" },
                                { "targets": 8, "width": "10%" }
                            ];
                        }
                        
                        $table.DataTable(config);
                        debugLog('✓ DataTable initialized for: ' + id);
                    } catch (e) {
                        debugLog('✗ Error initializing DataTable for ' + id + ': ' + e.message);
                    }
                });
            };
            
            // Initialize components
            debugLog('Initializing all components...');
            setTimeout(function() {
                window.initializeSelect2();
                window.initializeDataTables();
            }, 500);
            
            // Test buttons
            $('#testComponents').click(function() {
                debugLog('Manual component test initiated...');
                window.initializeSelect2();
                window.initializeDataTables();
            });
            
            $('#reloadTable').click(function() {
                debugLog('Reloading DataTable...');
                if ($.fn.DataTable.isDataTable('#agentesTable')) {
                    $('#agentesTable').DataTable().destroy();
                    debugLog('DataTable destroyed');
                }
                setTimeout(function() {
                    window.initializeDataTables();
                }, 100);
            });
            
            debugLog('Setup complete');
        });
    </script>
</body>
</html>
