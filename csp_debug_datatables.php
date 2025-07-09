<?php
require_once 'security.php';
configure_session_settings();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSP Debug for DataTables</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS (same version as index2.php) -->
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>CSP Debug for DataTables</h2>
        <p>Check browser console for any CSP violations.</p>
        
        <table id="debugTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Test 1</td>
                    <td>Active</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Test 2</td>
                    <td>Inactive</td>
                </tr>
            </tbody>
        </table>
        
        <div class="mt-4">
            <h4>Debug Info</h4>
            <div id="debugInfo"></div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS (same version as index2.php) -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Listen for CSP violations
        document.addEventListener('securitypolicyviolation', function(event) {
            console.error('CSP Violation:', event);
            document.getElementById('debugInfo').innerHTML += 
                '<div class="alert alert-danger">CSP Violation: ' + event.violatedDirective + 
                ' - Blocked URI: ' + event.blockedURI + '</div>';
        });
        
        $(document).ready(function() {
            console.log('Starting CSP debug test...');
            document.getElementById('debugInfo').innerHTML += 
                '<div class="alert alert-info">jQuery loaded successfully</div>';
            
            try {
                // Test both DataTables 2.x and 1.x syntax
                let table;
                if (typeof DataTable !== 'undefined') {
                    console.log('DataTables 2.x syntax available');
                    document.getElementById('debugInfo').innerHTML += 
                        '<div class="alert alert-success">DataTables 2.x available</div>';
                    
                    // Try DataTables 2.x syntax first
                    table = new DataTable('#debugTable', {
                        language: {
                            url: "", // Explicitly disable external language loading
                            lengthMenu: "Mostrar _MENU_ entradas",
                            zeroRecords: "No se encontraron datos",
                            info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                            search: "Buscar:"
                        },
                        pageLength: 10
                    });
                } else if (typeof $.fn.DataTable !== 'undefined') {
                    console.log('DataTables 1.x syntax available');
                    document.getElementById('debugInfo').innerHTML += 
                        '<div class="alert alert-warning">DataTables 1.x available</div>';
                    
                    // Fallback to DataTables 1.x syntax
                    table = $('#debugTable').DataTable({
                        "language": {
                            "url": "", // Explicitly disable external language loading
                            "lengthMenu": "Mostrar _MENU_ entradas",
                            "zeroRecords": "No se encontraron datos",
                            "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                            "search": "Buscar:"
                        },
                        "pageLength": 10
                    });
                } else {
                    throw new Error('DataTables not available');
                }
                
                console.log('DataTable initialized successfully');
                document.getElementById('debugInfo').innerHTML += 
                    '<div class="alert alert-success">DataTable initialized without CSP violations</div>';
                    
            } catch (e) {
                console.error('Error initializing DataTable:', e);
                document.getElementById('debugInfo').innerHTML += 
                    '<div class="alert alert-danger">Error: ' + e.message + '</div>';
            }
        });
    </script>
</body>
</html>
