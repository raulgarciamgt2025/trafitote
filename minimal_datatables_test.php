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
    <title>Minimal DataTables Test</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Minimal DataTables Test</h2>
        <table id="testTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Test Agent 1</td>
                    <td>Activo</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Test Agent 2</td>
                    <td>Inactivo</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            console.log('Starting minimal DataTable test...');
            
            // Same configuration as agentes.php
            $('#testTable').DataTable({
                "language": {
                    "url": "", // Explicitly disable external language loading
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
                    "searchPlaceholder": "Buscar agentes..."
                },
                "pageLength": 15,
                "lengthMenu": [[10, 15, 25, 50, -1], [10, 15, 25, 50, "Todos"]],
                "responsive": true,
                "order": [[ 0, "asc" ]],
                "dom": '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "processing": false,
                "serverSide": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "paging": true,
                "autoWidth": false,
                "scrollX": true
            });
            
            console.log('DataTable initialized successfully');
        });
    </script>
</body>
</html>
