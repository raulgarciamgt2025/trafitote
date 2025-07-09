<?php
/**
 * Test agentes_data.php with security bypass
 */

// Start session and set up environment
session_start();

// Mock session data
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'test@test.com';
$_SESSION['user_name'] = 'Test User';
$_SESSION['is_authenticated'] = true;
$_SESSION['login_time'] = time();

// Include tools
require_once 'tools.php';

// Mock exact DataTables POST parameters
$_POST = [
    'draw' => '1',
    'columns' => [
        ['data' => '0', 'name' => '', 'searchable' => 'false', 'orderable' => 'false', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '1', 'name' => '', 'searchable' => 'false', 'orderable' => 'false', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '2', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '3', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '4', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '5', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '6', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '7', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '8', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']]
    ],
    'order' => [
        ['column' => '7', 'dir' => 'asc']
    ],
    'start' => '0',
    'length' => '15',
    'search' => [
        'value' => '',
        'regex' => 'false'
    ],
    'csrf_token' => 'test_token'
];

echo "=== Testing agentes data logic directly ===\n\n";

try {
    // Initialize database helper
    $db = new DatabaseHelper();
    
    // Get DataTables parameters
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
    
    echo "Parameters:\n";
    echo "- Draw: $draw\n";
    echo "- Start: $start\n";
    echo "- Length: $length\n";
    echo "- Search: '$searchValue'\n\n";
    
    // Column definitions for ordering and searching
    $columns = [
        0 => 'contador', // Row number (not sortable)
        1 => 'acciones', // Actions (not sortable)
        2 => 'id_agente',
        3 => 'id_entidad', 
        4 => 'agente',
        5 => 'perfil_servicio',
        6 => 'modalidad',
        7 => 'orden',
        8 => 'estado'
    ];
    
    // Build base query
    $baseQuery = "SELECT a.* FROM VW_AGENTE_TRAFICO a";
    $whereConditions = [];
    $params = [];
    
    // Get ordering
    $orderColumn = 'orden'; // Default
    $orderDirection = 'ASC';
    
    if (isset($_POST['order'][0]['column'])) {
        $colIndex = intval($_POST['order'][0]['column']);
        if (isset($columns[$colIndex]) && $columns[$colIndex] !== 'contador' && $columns[$colIndex] !== 'acciones') {
            $orderColumn = $columns[$colIndex];
        }
    }
    
    if (isset($_POST['order'][0]['dir'])) {
        $orderDirection = ($_POST['order'][0]['dir'] === 'desc') ? 'DESC' : 'ASC';
    }
    
    echo "Ordering: $orderColumn $orderDirection\n\n";
    
    // Global search
    if (!empty($searchValue)) {
        $searchConditions = [
            "CAST(a.id_agente AS NVARCHAR) LIKE ?",
            "CAST(a.id_entidad AS NVARCHAR) LIKE ?",
            "a.agente LIKE ?",
            "a.perfil_servicio LIKE ?",
            "a.modalidad LIKE ?"
        ];
        $whereConditions[] = "(" . implode(" OR ", $searchConditions) . ")";
        $searchParam = "%$searchValue%";
        for ($i = 0; $i < 5; $i++) {
            $params[] = $searchParam;
        }
    }
    
    // Construct final query
    $query = $baseQuery;
    if (!empty($whereConditions)) {
        $query .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    // Get total count (without filters)
    $totalQuery = "SELECT COUNT(*) as total FROM VW_AGENTE_TRAFICO";
    $totalResult = $db->getRecords($totalQuery);
    $totalRecords = $totalResult[0]['total'] ?? 0;
    
    // Get filtered count
    $filteredQuery = "SELECT COUNT(*) as total FROM VW_AGENTE_TRAFICO a";
    if (!empty($whereConditions)) {
        $filteredQuery .= " WHERE " . implode(" AND ", $whereConditions);
    }
    $filteredResult = $db->getRecords($filteredQuery, $params);
    $filteredRecords = $filteredResult[0]['total'] ?? 0;
    
    echo "Total records: $totalRecords\n";
    echo "Filtered records: $filteredRecords\n\n";
    
    // Add ordering and pagination
    $query .= " ORDER BY a.$orderColumn $orderDirection";
    
    // Add pagination
    if ($length > 0) {
        $query .= " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";
    }
    
    echo "Final query: $query\n\n";
    
    // Execute query
    $records = $db->getRecords($query, $params);
    
    echo "Retrieved " . count($records) . " records\n\n";
    
    // Format data for DataTables
    $data = [];
    $contador = $start + 1;
    
    foreach ($records as $registro) {
        // Format actions
        $actions = "<div class='btn-group' role='group' aria-label='Acciones del agente'>";
        $actions .= "<button type='button' onclick='eliminar(\"".htmlspecialchars($registro['id_agente'], ENT_QUOTES, 'UTF-8')."\",\"".htmlspecialchars($registro['agente'], ENT_QUOTES, 'UTF-8')."\")' class='btn btn-sm btn-outline-danger' title='Eliminar Agente' data-bs-toggle='tooltip'><i class='fas fa-trash-alt'></i></button>";
        $actions .= "<button type='button' onclick='activar(\"".htmlspecialchars($registro['id_agente'], ENT_QUOTES, 'UTF-8')."\",\"".htmlspecialchars($registro['agente'], ENT_QUOTES, 'UTF-8')."\",\"".htmlspecialchars($registro['estado'], ENT_QUOTES, 'UTF-8')."\")' class='btn btn-sm btn-outline-warning' title='Activar/Desactivar Agente' data-bs-toggle='tooltip'><i class='fas fa-power-off'></i></button>";
        $actions .= "<button type='button' onclick='orden(\"".htmlspecialchars($registro['id_agente'], ENT_QUOTES, 'UTF-8')."\",\"".htmlspecialchars($registro['agente'], ENT_QUOTES, 'UTF-8')."\",\"".htmlspecialchars($registro['orden'], ENT_QUOTES, 'UTF-8')."\")' class='btn btn-sm btn-outline-info' title='Cambiar Orden' data-bs-toggle='tooltip'><i class='fas fa-sort-numeric-up'></i></button>";
        $actions .= "</div>";
        
        // Format badges and status
        $idBadge = '<span class="badge bg-light text-dark">'. htmlspecialchars($registro['id_agente'] ?? '', ENT_QUOTES, 'UTF-8').'</span>';
        $entityIdBadge = '<span class="badge bg-light text-dark">'. htmlspecialchars($registro['id_entidad'] ?? '', ENT_QUOTES, 'UTF-8').'</span>';
        
        // Format order column
        $orderColumn = '<span class="badge bg-secondary fs-6">'. htmlspecialchars($registro['orden'] ?? '0', ENT_QUOTES, 'UTF-8').'</span>';
        if (isset($registro['fecha_descarga']) && !empty($registro['fecha_descarga'])) {
            $orderColumn .= '<br/><small class="text-muted fst-italic">'.htmlspecialchars($registro['fecha_descarga'], ENT_QUOTES, 'UTF-8').'</small>';
        }
        
        // Format status column
        $estado = htmlspecialchars($registro['estado'] ?? '', ENT_QUOTES, 'UTF-8');
        if (strtolower($estado) === 'activo' || $estado === '1') {
            $statusColumn = '<span class="badge bg-success fs-6"><i class="fas fa-check-circle me-1"></i>Activo</span>';
        } else {
            $statusColumn = '<span class="badge bg-danger fs-6"><i class="fas fa-times-circle me-1"></i>Inactivo</span>';
        }
        
        $row = [
            $contador, // Row number
            $actions, // Action buttons
            $idBadge, // ID
            $entityIdBadge, // Entity ID
            htmlspecialchars($registro['agente'] ?? '', ENT_QUOTES, 'UTF-8'), // Agent name
            htmlspecialchars($registro['perfil_servicio'] ?? 'N/A', ENT_QUOTES, 'UTF-8'), // Profile
            htmlspecialchars($registro['modalidad'] ?? 'N/A', ENT_QUOTES, 'UTF-8'), // Modality
            $orderColumn, // Order
            $statusColumn // Status
        ];
        
        $data[] = $row;
        $contador++;
    }
    
    // Prepare response
    $response = [
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filteredRecords,
        "data" => $data
    ];
    
    echo "JSON Response:\n";
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
