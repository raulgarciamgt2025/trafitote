<?php
// Include security configuration first to get session settings function
require_once(dirname(__DIR__) . '/security.php');

// Configure session settings before starting session
configure_session_settings();

// Start session
session_start();

// Include tools after session is started
require_once(dirname(__DIR__) . '/tools.php');

// Validate session
SecurityManager::validateSession();

// Verify CSRF token for security
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (empty($csrfToken) || !SecurityManager::validateCSRFToken($csrfToken)) {
        http_response_code(403);
        echo json_encode([
            'draw' => intval($_POST['draw'] ?? 1),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => 'Token de seguridad inválido'
        ]);
        exit;
    }
}

// Set JSON content type
header('Content-Type: application/json; charset=utf-8');

// Enable CORS if needed (for local development)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Initialize database helper
    $db = new DatabaseHelper();
    
    // Get DataTables parameters
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
    
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
    
    // Add search conditions
    if (!empty($searchValue)) {
        $searchConditions = [
            "CAST(a.id_agente AS NVARCHAR) LIKE ?",
            "CAST(a.id_entidad AS NVARCHAR) LIKE ?", 
            "a.agente LIKE ?",
            "a.perfil_servicio LIKE ?",
            "a.modalidad LIKE ?",
            "CAST(a.orden AS NVARCHAR) LIKE ?",
            "a.estado LIKE ?"
        ];
        
        $whereConditions[] = "(" . implode(" OR ", $searchConditions) . ")";
        $searchParam = "%$searchValue%";
        for ($i = 0; $i < count($searchConditions); $i++) {
            $params[] = $searchParam;
        }
    }
    
    // Add individual column filters
    for ($i = 2; $i <= 8; $i++) { // Skip row number and actions columns
        if (isset($_POST['columns'][$i]['search']['value']) && !empty($_POST['columns'][$i]['search']['value'])) {
            $columnSearch = $_POST['columns'][$i]['search']['value'];
            $columnName = $columns[$i];
            
            if ($columnName === 'estado') {
                // Special handling for status column
                if (strtolower($columnSearch) === 'activo') {
                    $whereConditions[] = "(LOWER(a.estado) = 'activo' OR a.estado = '1')";
                } elseif (strtolower($columnSearch) === 'inactivo') {
                    $whereConditions[] = "(LOWER(a.estado) != 'activo' AND a.estado != '1')";
                }
            } else {
                $whereConditions[] = "CAST(a.$columnName AS NVARCHAR) LIKE ?";
                $params[] = "%$columnSearch%";
            }
        }
    }
    
    // Build WHERE clause
    $whereClause = "";
    if (!empty($whereConditions)) {
        $whereClause = " WHERE " . implode(" AND ", $whereConditions);
    }
    
    // Get total records count
    $totalQuery = "SELECT COUNT(*) as total FROM VW_AGENTE_TRAFICO a";
    $totalResult = $db->getRecords($totalQuery);
    $totalRecords = $totalResult[0]['total'];
    
    // Get filtered records count
    $filteredQuery = "SELECT COUNT(*) as total FROM VW_AGENTE_TRAFICO a" . $whereClause;
    $filteredResult = $db->getRecords($filteredQuery, $params);
    $filteredRecords = $filteredResult[0]['total'];
    
    // Build ORDER BY clause
    $orderBy = " ORDER BY a.ORDEN ASC"; // Default ordering
    if (isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
        $orderColumnIndex = intval($_POST['order'][0]['column']);
        $orderDir = $_POST['order'][0]['dir'] === 'desc' ? 'DESC' : 'ASC';
        
        if (isset($columns[$orderColumnIndex]) && $orderColumnIndex >= 2) { // Skip non-sortable columns
            $orderColumn = $columns[$orderColumnIndex];
            $orderBy = " ORDER BY a.$orderColumn $orderDir";
        }
    }
    
    // Build final query with pagination
    $finalQuery = $baseQuery . $whereClause . $orderBy;
    
    // Add pagination for SQL Server
    if ($length != -1) {
        $finalQuery .= " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";
    }
    
    // Execute query
    $agentes = $db->getRecords($finalQuery, $params);
    
    // Format data for DataTables
    $data = [];
    $contador = $start + 1;
    
    foreach ($agentes as $registro) {
        // Generate action buttons
        $actions = '<div class="btn-group" role="group" aria-label="Acciones del agente">';
        $actions .= '<button type="button" onclick="eliminar(\'' . htmlspecialchars($registro['id_agente'], ENT_QUOTES, 'UTF-8') . '\',\'' . htmlspecialchars($registro['agente'], ENT_QUOTES, 'UTF-8') . '\')" class="btn btn-sm btn-outline-danger" title="Eliminar Agente" data-bs-toggle="tooltip"><i class="fas fa-trash-alt"></i></button>';
        $actions .= '<button type="button" onclick="activar(\'' . htmlspecialchars($registro['id_agente'], ENT_QUOTES, 'UTF-8') . '\',\'' . htmlspecialchars($registro['agente'], ENT_QUOTES, 'UTF-8') . '\',\'' . htmlspecialchars($registro['estado'], ENT_QUOTES, 'UTF-8') . '\')" class="btn btn-sm btn-outline-warning" title="Activar/Desactivar Agente" data-bs-toggle="tooltip"><i class="fas fa-power-off"></i></button>';
        $actions .= '<button type="button" onclick="orden(\'' . htmlspecialchars($registro['id_agente'], ENT_QUOTES, 'UTF-8') . '\',\'' . htmlspecialchars($registro['agente'], ENT_QUOTES, 'UTF-8') . '\',\'' . htmlspecialchars($registro['orden'], ENT_QUOTES, 'UTF-8') . '\')" class="btn btn-sm btn-outline-info" title="Cambiar Orden" data-bs-toggle="tooltip"><i class="fas fa-sort-numeric-up"></i></button>';
        $actions .= '</div>';
        
        // Format ID badges
        $idBadge = '<span class="badge bg-light text-dark">' . htmlspecialchars($registro['id_agente'] ?? '', ENT_QUOTES, 'UTF-8') . '</span>';
        $entityIdBadge = '<span class="badge bg-light text-dark">' . htmlspecialchars($registro['id_entidad'] ?? '', ENT_QUOTES, 'UTF-8') . '</span>';
        
        // Format order column
        $orderColumn = '<span class="badge bg-secondary fs-6">' . htmlspecialchars($registro['orden'] ?? '0', ENT_QUOTES, 'UTF-8') . '</span>';
        if (isset($registro['fecha_descarga']) && !empty($registro['fecha_descarga'])) {
            $orderColumn .= '<br/><small class="text-muted fst-italic">' . htmlspecialchars($registro['fecha_descarga'], ENT_QUOTES, 'UTF-8') . '</small>';
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
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Error in agentes_data.php: " . $e->getMessage());
    
    // Return error response
    $response = [
        "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Error al cargar los datos. Por favor, contacte al administrador."
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
?>
