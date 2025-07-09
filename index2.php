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
    <title>TransExpress Guatemala</title>
    
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
    
    <!-- Custom styles -->
    <style>
        :root {
            --bs-primary: #56baed;
            --navbar-dark-blue: #1e3a5f;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        
        .main-content {
            min-height: calc(100vh - 80px);
            padding-top: 20px;
        }
        
        .card {
            border: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            border-radius: 16px;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--bs-primary) 0%, #4a9fd1 100%);
            color: white;
            font-weight: 600;
            padding: 1.5rem;
            border-bottom: none;
        }
        
        .card-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-top: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--bs-primary) 0%, #4a9fd1 100%);
            border: none;
            font-size: 12px;
            font-weight: normal;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #4a9fd1 0%, #3d8bc7 100%);
        }
        
        /* Global button styling override */
        .btn {
            font-size: 12px !important;
            font-weight: normal !important;
        }
        
        /* DataTables styling improvements */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
            padding: 0.375rem 0.75rem;
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .dataTables_wrapper .dataTables_info {
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.375rem 0.75rem;
            margin: 0 0.125rem;
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
            color: #495057;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--bs-primary);
            border-color: var(--bs-primary);
            color: white;
        }
        
        /* Column filter styling - Clean modern approach */
        .column-filters {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border: 1px solid #e3e6ea;
            box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        }
        
        .column-filters h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1.25rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
        }
        
        .column-filters h6 i {
            color: #6c757d;
        }
        
        .filter-group {
            margin-bottom: 0;
        }
        
        .filter-group label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .filter-group input,
        .filter-group select {
            width: 100%;
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 8px;
            background-color: white;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .filter-group input:focus,
        .filter-group select:focus {
            border-color: #007bff;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.15), 0 2px 6px rgba(0,0,0,0.15);
            transform: translateY(-1px);
        }
        
        .filter-group input::placeholder {
            color: #adb5bd;
            font-style: italic;
        }
        
        /* DataTables container styling - Clean and modern */
        .dataTables_wrapper {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 1.5rem;
            border: 1px solid #e3e6ea;
            margin-top: 1rem;
        }
        
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
            float: left;
        }
        
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
            float: right;
        }
        
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.2s ease-in-out;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            width: 250px;
            margin-left: 0.5rem;
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #007bff;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.15), 0 2px 6px rgba(0,0,0,0.15);
            transform: translateY(-1px);
        }
        
        .dataTables_wrapper .dataTables_info {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 1rem;
            font-weight: 500;
            float: left;
            clear: both;
        }
        
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1rem;
            float: right;
            clear: both;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5rem 0.75rem;
            margin: 0 0.125rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            color: #495057;
            background-color: white;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            display: inline-block;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
            color: #495057;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-color: #007bff;
            color: white;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
            border-color: #004085;
            transform: translateY(-1px);
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background-color: #fff;
            border-color: #dee2e6;
            transform: none;
            box-shadow: none;
        }
        
        /* Clearfix for DataTables wrapper */
        .dataTables_wrapper::after {
            content: "";
            display: table;
            clear: both;
        }
        
        /* Remove horizontal scrollbar from wrapper */
        .dataTables_wrapper {
            overflow-x: visible;
        }
        
        .dataTables_scrollBody {
            overflow-x: visible !important;
        }
        
        /* Table styling improvements - Clean and professional */
        .table-container {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            background: white;
            border: 1px solid #e3e6ea;
        }
        
        .table-responsive {
            border-radius: 12px;
            overflow: visible;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            background: white;
            border: 1px solid #e3e6ea;
        }
        
        #agentesTable {
            border-radius: 0;
            margin-bottom: 0;
            border: none;
        }
        
        #agentesTable thead th {
            background: linear-gradient(135deg, #1e3a5f 0%, #2c4a6b 100%);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 1rem 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }
        
        #agentesTable thead th:first-child {
            border-top-left-radius: 0;
        }
        
        #agentesTable thead th:last-child {
            border-top-right-radius: 0;
        }
        
        #agentesTable tbody td {
            padding: 0.875rem 0.75rem;
            font-size: 0.85rem;
            vertical-align: middle;
            border-color: #f1f3f4;
            transition: background-color 0.2s ease-in-out;
        }
        
        #agentesTable tbody tr {
            transition: all 0.2s ease-in-out;
        }
        
        #agentesTable tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        #agentesTable tbody tr:last-child td:first-child {
            border-bottom-left-radius: 0;
        }
        
        #agentesTable tbody tr:last-child td:last-child {
            border-bottom-right-radius: 0;
        }
        
        /* Improved button styling in table */
        #agentesTable .btn-group .btn {
            padding: 0.375rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 6px;
            margin: 0 1px;
            transition: all 0.2s ease-in-out;
        }
        
        #agentesTable .btn-group .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* Badge improvements */
        .badge {
            font-size: 0.7rem;
            padding: 0.4em 0.6em;
            border-radius: 6px;
            font-weight: 600;
        }
        
        .badge.bg-light {
            background-color: #f8f9fa !important;
            color: #495057 !important;
            border: 1px solid #e9ecef;
        }
        
        .badge.bg-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        }
        
        .badge.bg-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%) !important;
        }
        
        .badge.bg-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
        }
        
        /* Form improvements */
        .form-label {
            color: #495057;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .form-select,
        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 0.75rem;
            font-size: 0.9rem;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .form-select:focus,
        .form-control:focus {
            border-color: #007bff;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.15), 0 2px 6px rgba(0,0,0,0.15);
            transform: translateY(-1px);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #218838 0%, #1fa085 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }
            margin-bottom: 0.5rem;
        }
        
        .card {
            border-radius: 0.75rem;
        }
        
        .card-header {
            border-radius: 0.75rem 0.75rem 0 0 !important;
        }
    </style>
    
    <script>
        // Global CSRF token for AJAX requests
        window.csrf_token = '<?php echo $csrf_token; ?>';
    </script>
</head>

<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow" style="background-color: var(--navbar-dark-blue);">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-truck me-2"></i>
                Tráfico Trans-Express
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-box-seam me-1"></i>
                            Paquetes
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="index2.php?component=agentes&token=<?php echo $csrf_token; ?>">
                                <i class="bi bi-people me-2"></i>Agentes Tráfico
                            </a></li>
                            <li><a class="dropdown-item" href="index2.php?component=panel_avances&token=<?php echo $csrf_token; ?>">
                                <i class="bi bi-speedometer2 me-2"></i>Panel Avances
                            </a></li>
                            <li><a class="dropdown-item" href="index2.php?component=paquetes_todos&token=<?php echo $csrf_token; ?>">
                                <i class="bi bi-grid me-2"></i>Panel Paquetería
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="index2.php?component=activar_codigo_barra&token=<?php echo $csrf_token; ?>">
                                <i class="bi bi-upc-scan me-2"></i>Activar Código de Barra
                            </a></li>
                            <li><a class="dropdown-item" href="index2.php?component=ver_link_compra&token=<?php echo $csrf_token; ?>">
                                <i class="bi bi-link-45deg me-2"></i>Ver Link de Compra - Código de Barra
                            </a></li>
                            <li><a class="dropdown-item" href="index2.php?component=ver_link_compra_pre&token=<?php echo $csrf_token; ?>">
                                <i class="bi bi-link me-2"></i>Ver Link de Compra - Pre-Alerta
                            </a></li>
                        </ul>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario', ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Salir del Sistema
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid main-content">
        <?php
        // Secure component inclusion
        if (isset($_REQUEST['component']) && isset($_REQUEST['token'])) {
            // Validate CSRF token
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
        } else {
            // Default dashboard content
            echo '<div class="row">';
            echo '<div class="col-12">';
            echo '<div class="card">';
            echo '<div class="card-header">';
            echo '<h4 class="mb-0"><i class="bi bi-house-door me-2"></i>Panel de Control</h4>';
            echo '</div>';
            echo '<div class="card-body">';
            echo '<h5>Bienvenido al Sistema de Tráfico Trans-Express</h5>';
            echo '<p class="text-muted">Seleccione una opción del menú superior para comenzar a trabajar.</p>';
            echo '<div class="row">';
            echo '<div class="col-md-4 mb-3">';
            echo '<div class="card bg-primary text-white">';
            echo '<div class="card-body text-center">';
            echo '<i class="bi bi-people display-4"></i>';
            echo '<h6 class="mt-2">Agentes</h6>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '<div class="col-md-4 mb-3">';
            echo '<div class="card bg-success text-white">';
            echo '<div class="card-body text-center">';
            echo '<i class="bi bi-box-seam display-4"></i>';
            echo '<h6 class="mt-2">Paquetes</h6>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '<div class="col-md-4 mb-3">';
            echo '<div class="card bg-info text-white">';
            echo '<div class="card-body text-center">';
            echo '<i class="bi bi-speedometer2 display-4"></i>';
            echo '<h6 class="mt-2">Avances</h6>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>

    <!-- Bootstrap 5.3.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- SweetAlert2 for better notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Select2 JS (must be loaded after jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Global component initialization for dynamically loaded content
        $(document).ready(function() {
            console.log('Index2: jQuery ready, setting up global handlers');
            
            // Function to initialize Select2 for any new selects
            window.initializeSelect2 = function(container) {
                var $container = container ? $(container) : $(document);
                
                console.log('Initializing Select2 in container:', container || 'document');
                
                // Find all select elements that don't already have Select2
                $container.find('select').not('.select2-hidden-accessible').each(function() {
                    var $select = $(this);
                    var id = $select.attr('id');
                    
                    console.log('Initializing Select2 for:', id);
                    
                    try {
                        var placeholder = 'Seleccione una opción';
                        
                        // Custom placeholders for specific selects
                        if (id === 'slAgentes') {
                            placeholder = 'Seleccione un agente';
                        } else if (id === 'slPerfil') {
                            placeholder = 'Seleccione un perfil';
                        } else if (id === 'slModalidad') {
                            placeholder = 'Seleccione una modalidad';
                        }
                        
                        $select.select2({
                            theme: 'bootstrap-5',
                            placeholder: placeholder,
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $select.closest('.container-fluid, .container, .modal, body')
                        });
                        
                        console.log('✓ Select2 initialized for:', id);
                    } catch (e) {
                        console.error('✗ Error initializing Select2 for ' + id + ':', e);
                    }
                });
            };
            
            // Function to initialize DataTables for any new tables
            window.initializeDataTables = function(container) {
                var $container = container ? $(container) : $(document);
                
                console.log('Initializing DataTables in container:', container || 'document');
                
                // Find all tables that should be DataTables but aren't initialized yet
                $container.find('table[id$="Table"]').not('.dataTable').each(function() {
                    var $table = $(this);
                    var id = $table.attr('id');
                    
                    console.log('Initializing DataTable for:', id);
                    
                    try {
                        var config = {
                            "language": {
                                "lengthMenu": "Mostrar _MENU_ entradas",
                                "zeroRecords": "No se encontraron datos",
                                "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                                "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
                                "infoFiltered": "(filtrado de _MAX_ entradas totales)",
                                "search": "Buscar:",
                                "paginate": {
                                    "first": "Primera",
                                    "last": "Última",
                                    "next": "Siguiente",
                                    "previous": "Anterior"
                                },
                                "processing": "Procesando...",
                                "loadingRecords": "Cargando...",
                                "emptyTable": "No hay datos disponibles en la tabla",
                                "infoThousands": ",",
                                "searchPlaceholder": "Buscar..."
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
                        
                        // Table-specific configurations
                        if (id === 'agentesTable') {
                            config.pageLength = 15;
                            config.lengthMenu = [[10, 15, 25, 50, -1], [10, 15, 25, 50, "Todos"]];
                            config.order = [[ 7, "asc" ]]; // Order by "Orden" column
                            config.autoWidth = false;
                            config.scrollX = true;
                            config.dom = '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>';
                            config.columnDefs = [
                                { 
                                    "targets": 0, // No. column
                                    "orderable": false,
                                    "searchable": false,
                                    "className": "text-center"
                                },
                                { 
                                    "targets": 1, // Acciones column
                                    "orderable": false,
                                    "searchable": false,
                                    "className": "text-center"
                                },
                                { 
                                    "targets": [2, 3], // ID columns
                                    "className": "text-center"
                                },
                                { 
                                    "targets": 4, // Agente column
                                    "className": "text-start"
                                },
                                { 
                                    "targets": [5, 6, 7, 8], // Other columns
                                    "className": "text-center"
                                }
                            ];
                            config.drawCallback = function(settings) {
                                // Re-initialize tooltips after table draw
                                $('[data-bs-toggle="tooltip"]').tooltip();
                            };
                        }
                        
                        $table.DataTable(config);
                        
                        console.log('✓ DataTable initialized for:', id);
                    } catch (e) {
                        console.error('✗ Error initializing DataTable for ' + id + ':', e);
                    }
                });
            };
            
            // Initialize Select2 for any existing selects
            window.initializeSelect2();
            
            // Initialize DataTables for any existing tables
            window.initializeDataTables();
            
            // Auto-initialize components when new content is added
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) { // Element node
                                var $node = $(node);
                                
                                // Check if the node contains select elements
                                if ($node.find('select').length > 0) {
                                    console.log('New select elements detected, initializing Select2...');
                                    setTimeout(function() {
                                        window.initializeSelect2($node);
                                    }, 100);
                                }
                                
                                // Check if the node contains table elements that should be DataTables
                                if ($node.find('table[id$="Table"]').length > 0) {
                                    console.log('New table elements detected, initializing DataTables...');
                                    setTimeout(function() {
                                        window.initializeDataTables($node);
                                    }, 200);
                                }
                            }
                        });
                    }
                });
            });
            
            // Start observing
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
            
            console.log('Global Select2 and DataTables initialization complete');
        });
    </script>

</body>
</html>
