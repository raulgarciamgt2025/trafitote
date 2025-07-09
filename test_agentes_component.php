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
    <title>Test Agentes Component</title>
    
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
    
    <!-- Custom styles from index2.php -->
    <style>
        :root {
            --bs-primary: #56baed;
        }
        
        /* All CSS from index2.php here... */
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
        
        .dataTables_wrapper {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 1.5rem;
            border: 1px solid #e3e6ea;
        }
        
        .table-container {
            border-radius: 12px;
            overflow: hidden;
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
            background: linear-gradient(135deg, #495057 0%, #343a40 100%);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 1rem 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid mt-4">
        <h2>Test: Agentes Component</h2>
        
        <?php
        // Include the agentes component directly
        include 'agentes.php';
        ?>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
</body>
</html>
