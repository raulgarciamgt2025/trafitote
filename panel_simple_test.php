<?php
// Simple test version without authentication
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load sample data directly for testing
$packages = [
    [
        'id_entidad_asignado' => '1',
        'agente' => 'Agente de Tráfico 1',
        'validados' => 45,
        'no_validados' => 5,
        'paquetes' => 50
    ],
    [
        'id_entidad_asignado' => '2', 
        'agente' => 'Agente de Tráfico 2',
        'validados' => 32,
        'no_validados' => 18,
        'paquetes' => 50
    ],
    [
        'id_entidad_asignado' => '3',
        'agente' => 'Agente de Tráfico 3', 
        'validados' => 67,
        'no_validados' => 8,
        'paquetes' => 75
    ]
];

$summary = [
    'total_agents' => count($packages),
    'total_packages' => array_sum(array_column($packages, 'paquetes')),
    'total_validated' => array_sum(array_column($packages, 'validados')),
    'total_pending' => array_sum(array_column($packages, 'no_validados'))
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Avances - Test Simple</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="jquery/jquery-3.5.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #3d4a2d 0%, #2a3d1e 100%); border-bottom: 2px solid #243a1a;">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Panel de Avances - Estado de Paquetes (Test)
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <div class="text-primary">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                        <div class="mt-2">
                                            <h4 class="mb-0"><?php echo number_format($summary['total_agents']); ?></h4>
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
                                            <h4 class="mb-0"><?php echo number_format($summary['total_packages']); ?></h4>
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
                                            <h4 class="mb-0"><?php echo number_format($summary['total_validated']); ?></h4>
                                            <small class="text-muted">Validados</small>
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
                                            <h4 class="mb-0"><?php echo number_format($summary['total_pending']); ?></h4>
                                            <small class="text-muted">No Validados</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="table-container">
                            <table id="panelTable" class="table table-hover table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">ID</th>
                                        <th class="text-start">Agente</th>
                                        <th class="text-center">Validados</th>
                                        <th class="text-center">No Válidos</th>
                                        <th class="text-center">Total Paquetes</th>
                                        <th class="text-center">% Validación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($packages as $index => $registro): ?>
                                        <?php
                                        $validados = intval($registro['validados']);
                                        $noValidados = intval($registro['no_validados']);
                                        $totalPaquetes = intval($registro['paquetes']);
                                        $porcentaje = $totalPaquetes > 0 ? round(($validados / $totalPaquetes) * 100, 1) : 0;
                                        
                                        $rowClass = '';
                                        if ($porcentaje >= 90) {
                                            $rowClass = 'table-success';
                                        } elseif ($porcentaje >= 70) {
                                            $rowClass = 'table-warning';
                                        } elseif ($totalPaquetes > 0) {
                                            $rowClass = 'table-danger';
                                        }
                                        ?>
                                        <tr class="<?php echo $rowClass; ?>">
                                            <td class="text-center"><?php echo $index + 1; ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($registro['id_entidad_asignado']); ?></td>
                                            <td class="text-start"><?php echo htmlspecialchars($registro['agente']); ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-success"><?php echo number_format($validados); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning"><?php echo number_format($noValidados); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary"><?php echo number_format($totalPaquetes); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar <?php echo $porcentaje >= 90 ? 'bg-success' : ($porcentaje >= 70 ? 'bg-warning' : 'bg-danger'); ?>" 
                                                         role="progressbar" 
                                                         style="width: <?php echo $porcentaje; ?>%">
                                                        <?php echo $porcentaje; ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        console.log('Initializing simple test panel...');
        
        try {
            $('#panelTable').DataTable({
                language: {
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
                    emptyTable: "No hay datos disponibles en la tabla"
                },
                pageLength: 15,
                responsive: true,
                order: [[ 6, "desc" ]],
                columnDefs: [
                    { targets: 0, orderable: false, searchable: false },
                    { targets: 6, type: "num" }
                ]
            });
            
            console.log('DataTable initialized successfully');
        } catch (e) {
            console.error('Error initializing DataTable:', e);
        }
    });
    </script>
</body>
</html>
