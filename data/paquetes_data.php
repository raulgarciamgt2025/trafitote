<?php
// Include security configuration first to get session settings function
require_once('../security.php');

// Configure session settings before starting session
configure_session_settings();

// Start session
session_start();

// Include tools after session is started
require_once('../tools.php');

// Validate session
SecurityManager::validateSession();

// Set JSON content type
header('Content-Type: application/json');

try {
    // Validate CSRF token
    if (!SecurityManager::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid CSRF token');
    }

    // Initialize database helper
    $db = new DatabaseHelper();

    // Sanitize and validate inputs from DataTables and form filters
    $draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 1;
    $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
    $length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
    $search_value = isset($_POST['search']['value']) ? SecurityManager::sanitizeInput($_POST['search']['value'], 'general') : '';

    // Form filter inputs
    $seccion = isset($_POST['txtSeccion']) ? SecurityManager::sanitizeInput($_POST['txtSeccion'], 'alphanumeric') : '';
    $codigo_barra = isset($_POST['txtCodigoBarra']) ? SecurityManager::sanitizeInput($_POST['txtCodigoBarra'], 'codigo_barra') : '';
    $tracking = isset($_POST['txtTracking']) ? SecurityManager::sanitizeInput($_POST['txtTracking'], 'alphanumeric') : '';
    $factura = isset($_POST['slFactura']) ? SecurityManager::sanitizeInput($_POST['slFactura'], 'alphanumeric') : 'T';
    $estado = isset($_POST['slEstado']) ? SecurityManager::sanitizeInput($_POST['slEstado'], 'alphanumeric') : 'T';
    $partida = isset($_POST['slPartida']) ? SecurityManager::sanitizeInput($_POST['slPartida'], 'alphanumeric') : 'T';
    $agente = isset($_POST['slAgente']) ? (int)$_POST['slAgente'] : 0;

    // Column ordering
    $order_column = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 10; // Default to fecha column
    $order_dir = isset($_POST['order'][0]['dir']) && $_POST['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC';

    // Column mapping for ordering (based on stored procedure result columns)
    $columns = [
        0 => 'row_number', // No.
        1 => 'codigo_barra', // Acciones (not orderable, but use codigo_barra as fallback)
        2 => 'partida_cargada', // PA
        3 => 'multi', // Multi
        4 => 'documentos_cargados', // Docs
        5 => 'fecha_cargo_cliente', // Fec.Cargo
        6 => 'fuera_tiempo', // FT
        7 => 'reemplazar_factura', // Reemp
        8 => 'factura_miami', // Factura
        9 => 'codigo_barra', // Código Barra
        10 => 'fecha_formato', // Fecha
        11 => 'Remitente', // Remitente
        12 => 'Consignatario', // Consignatario
        13 => 'Tracking', // Tracking
        14 => 'retail', // Retail
        15 => 'peso', // Peso
        16 => 'contenido', // Contenido(Miami)
        17 => 'valordeclarado', // Valor Dec.(Miami)
        18 => 'contenido_cliente', // Contenido(Cliente)
        19 => 'valor_declarado', // Valor Dec.(Cliente)
        20 => 'archivo_alerta', // Documento
        21 => 'seccion', // Sección
        22 => 'emails', // Emails
        23 => 'llamadas', // Llamadas
        24 => 'usuario_ultima_accion', // Usuario Ult. Acción
        25 => 'fecha_ultima_accion', // Fec.Ultima Acción
        26 => 'ultima_accion', // Ultima Acción
        27 => 'agente' // Agente
    ];

    $order_by = isset($columns[$order_column]) ? $columns[$order_column] : 'fecha_formato';

    // Execute stored procedure to get data
    $stmt = $db->executeStoredProcedure('SP_PBX_PAQUETES_TODOSV2', [
        $seccion, $codigo_barra, $tracking, $factura, $estado, $agente, $partida
    ]);
    
    // Fetch all results
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter results based on search
    if (!empty($search_value)) {
        $packages = array_filter($packages, function($row) use ($search_value) {
            $search_lower = strtolower($search_value);
            return (
                stripos($row['codigo_barra'] ?? '', $search_value) !== false ||
                stripos($row['Tracking'] ?? '', $search_value) !== false ||
                stripos($row['Remitente'] ?? '', $search_value) !== false ||
                stripos($row['Consignatario'] ?? '', $search_value) !== false ||
                stripos($row['contenido'] ?? '', $search_value) !== false ||
                stripos($row['contenido_cliente'] ?? '', $search_value) !== false ||
                stripos($row['agente'] ?? '', $search_value) !== false ||
                stripos($row['seccion'] ?? '', $search_value) !== false
            );
        });
    }

    // Get total count
    $total_records = count($packages);
    $filtered_records = $total_records;

    // Sort the data
    if (isset($columns[$order_column])) {
        usort($packages, function($a, $b) use ($order_by, $order_dir) {
            $val_a = $a[$order_by] ?? '';
            $val_b = $b[$order_by] ?? '';
            
            // Handle numeric fields
            if (in_array($order_by, ['valordeclarado', 'valor_declarado', 'peso', 'emails', 'llamadas'])) {
                $val_a = (float)$val_a;
                $val_b = (float)$val_b;
            }
            
            if ($val_a == $val_b) return 0;
            
            if ($order_dir === 'ASC') {
                return ($val_a < $val_b) ? -1 : 1;
            } else {
                return ($val_a > $val_b) ? -1 : 1;
            }
        });
    }

    // Paginate results
    $paged_packages = array_slice($packages, $start, $length);

    // Format data for DataTables
    $data = [];
    $counter = $start + 1;

    foreach ($paged_packages as $registro) {
        // Determine background color based on business rules
        $bgcolor = "#BFFFCF"; // Default green
        if (($registro['emails'] ?? 0) > 0 || ($registro['llamadas'] ?? 0) > 0) {
            $bgcolor = "#FFFFBF"; // Yellow for packages with emails/calls
        }
        if (($registro['valordeclarado'] ?? 0) >= 1000 || ($registro['valor_declarado'] ?? 0) >= 1000) {
            $bgcolor = "#d98880"; // Red for high value packages
        }

        // Build actions dropdown
        $actions_select = '<select name="sl_acciones_cb' . htmlspecialchars($registro['codigo_barra'] ?? '', ENT_QUOTES, 'UTF-8') . '" 
                                  id="sl_acciones_cb' . htmlspecialchars($registro['codigo_barra'] ?? '', ENT_QUOTES, 'UTF-8') . '" 
                                  class="form-select form-select-sm"
                                  onchange="change(this.id, \'' . htmlspecialchars($registro['codigo_barra'] ?? '', ENT_QUOTES, 'UTF-8') . '\', 
                                                  \'' . htmlspecialchars($registro['seccion'] ?? '', ENT_QUOTES, 'UTF-8') . '\', 
                                                  \'' . htmlspecialchars($registro['Tracking'] ?? '', ENT_QUOTES, 'UTF-8') . '\', 
                                                  \'' . htmlspecialchars($registro['ciudad'] ?? '', ENT_QUOTES, 'UTF-8') . '\')">
                            <option value="0" selected>Seleccione una acción</option>
                            <option value="1">Cancelar paquete</option>
                            <option value="2">Cargar Factura</option>
                            <option value="3">Revisar Prealertas</option>
                            <option value="4">Revisar Docs. Cargados</option>
                            <option value="5">Realizo Llamada</option>
                            <option value="6">Enviar link para carga</option>
                            <option value="7">Modificar Valor y contenido</option>
                            <option value="8">Modificar Valor Declarado</option>
                            <option value="9">Modificar Contenido</option>
                            <option value="13">Modificar Multiple</option>
                            <option value="10">Reemplazar Factura</option>
                            <option value="12">Cargar Partida Arancelaria</option>
                            <option value="11">Validar información</option>
                          </select>';

        // Format date cargo
        $fecha_cargo = "-";
        if (!empty($registro['fecha_cargo_cliente']) && $registro['fecha_cargo_cliente'] !== "Jan 1 1900 12:00:00:000AM") {
            $fecha_cargo = htmlspecialchars($registro['fecha_cargo_cliente'], ENT_QUOTES, 'UTF-8');
        }

        // Format fuera de tiempo
        $fuera_tiempo = "NO";
        if (($registro['fuera_tiempo'] ?? '') == "1") {
            $fuera_tiempo = '<a href="javascript:ver_fuera_tiempo(\'' . htmlspecialchars($registro['codigo_barra'] ?? '', ENT_QUOTES, 'UTF-8') . '\', ' . 
                           htmlspecialchars($registro['seccion'] ?? '', ENT_QUOTES, 'UTF-8') . ')" class="text-decoration-none">SÍ</a>';
        }

        // Format document link
        $documento_link = "Sin documento";
        if (!empty($registro['archivo_alerta'])) {
            $documento_link = '<a href="javascript:ver_documento_pdf(\'' . htmlspecialchars($registro['archivo_alerta'], ENT_QUOTES, 'UTF-8') . '\', \'' . 
                             htmlspecialchars($registro['codigo_barra'] ?? '', ENT_QUOTES, 'UTF-8') . '\')" class="text-decoration-none">Ver docto.</a>';
        }

        // Format section link
        $seccion_link = '<a href="javascript:ver_informacion(\'' . htmlspecialchars($registro['codigo_barra'] ?? '', ENT_QUOTES, 'UTF-8') . '\', ' . 
                       htmlspecialchars($registro['seccion'] ?? '', ENT_QUOTES, 'UTF-8') . ')" class="text-decoration-none">' . 
                       htmlspecialchars($registro['seccion'] ?? '', ENT_QUOTES, 'UTF-8') . '</a>';

        // Format last action link
        $ultima_accion_link = '<a href="javascript:ver_acciones(\'' . htmlspecialchars($registro['codigo_barra'] ?? '', ENT_QUOTES, 'UTF-8') . '\', ' . 
                             htmlspecialchars($registro['seccion'] ?? '', ENT_QUOTES, 'UTF-8') . ')" class="text-decoration-none">' . 
                             htmlspecialchars($registro['ultima_accion'] ?? '', ENT_QUOTES, 'UTF-8') . '</a>';

        // Format fecha with date and download date
        $fecha_display = htmlspecialchars($registro['fecha_formato'] ?? '', ENT_QUOTES, 'UTF-8');
        if (!empty($registro['fecha_descarga'])) {
            $fecha_display .= '<br/><small class="text-muted">' . htmlspecialchars($registro['fecha_descarga'], ENT_QUOTES, 'UTF-8') . '</small>';
        }

        // Create row data
        $row = [
            '<div class="text-center fw-bold text-muted" style="background-color:' . $bgcolor . ';">' . $counter . '</div>',
            '<div style="background-color:' . $bgcolor . ';">' . $actions_select . '</div>',
            '<div class="text-center"><small>' . htmlspecialchars($registro['partida_cargada'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center"><small>' . htmlspecialchars($registro['multi'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center"><small>' . htmlspecialchars($registro['documentos_cargados'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center"><small>' . $fecha_cargo . '</small></div>',
            '<div class="text-center"><small>' . $fuera_tiempo . '</small></div>',
            '<div class="text-center"><small>' . (($registro['reemplazar_factura'] ?? '') == "1" ? "SÍ" : "NO") . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . (($registro['factura_miami'] ?? '') == '1' ? "SÍ" : "NO") . '</small></div>',
            '<div class="text-center fw-bold" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['codigo_barra'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . $fecha_display . '</small></div>',
            '<div class="text-start" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['Remitente'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-start" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['Consignatario'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['Tracking'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['retail'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center fw-bold" style="background-color:#99FF99;"><small>' . htmlspecialchars($registro['peso'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-start" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['contenido'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['valordeclarado'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-start" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['contenido_cliente'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['valor_declarado'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . $documento_link . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . $seccion_link . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['emails'] ?? '0', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['llamadas'] ?? '0', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['usuario_ultima_accion'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['fecha_ultima_accion'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . $ultima_accion_link . '</small></div>',
            '<div class="text-center" style="background-color:' . $bgcolor . ';"><small>' . htmlspecialchars($registro['agente'] ?? '', ENT_QUOTES, 'UTF-8') . '</small></div>'
        ];

        $data[] = $row;
        $counter++;
    }

    // Prepare response
    $response = [
        "draw" => $draw,
        "recordsTotal" => $total_records,
        "recordsFiltered" => $filtered_records,
        "data" => $data
    ];

    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error in paquetes_data.php: " . $e->getMessage());
    
    // Return error response in DataTables format
    $error_response = [
        "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Error al cargar los datos: " . $e->getMessage()
    ];
    
    echo json_encode($error_response);
}
?>
