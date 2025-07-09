<?php
// Include security configuration first
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
    // Validate CSRF token if provided
    if (isset($_POST['csrf_token'])) {
        if (!SecurityManager::validateCSRFToken($_POST['csrf_token'])) {
            http_response_code(403);
            echo '<tr><td colspan="7" class="text-center text-danger">Token de seguridad inválido</td></tr>';
            exit;
        }
    }
    
    // Initialize database helper
    $dbHelper = new DatabaseHelper();
    
    // Sanitize inputs
    $seccion = SecurityManager::sanitizeInput($_POST['seccion'] ?? '');
    $tracking = SecurityManager::sanitizeInput($_POST['tracking'] ?? '');
    
    if (empty($seccion) || empty($tracking)) {
        echo '<tr><td colspan="7" class="text-center text-warning">Sección y tracking son requeridos</td></tr>';
        exit;
    }

    $str_sql = "SELECT a.fecha_grabo, a.tracking, a.descripcion, a.valor_declarado, a.carrier, a.tienda, a.url_compra ";
    $str_sql .= "FROM RETAIL_PRE_ALERTA a ";
    $str_sql .= "WHERE a.seccion = ? AND a.tracking LIKE ? ";
    $str_sql .= "ORDER BY a.fecha_grabo DESC";
    
    $params = [$seccion, "%$tracking%"];
    $results = $dbHelper->query($str_sql, $params);
    
    $tbody = "";
    if ($results && count($results) > 0) {
        foreach ($results as $fila) {
            $tbody .= "<tr>";
            $tbody .= "<td class='text-center'>" . htmlspecialchars($fila["fecha_grabo"] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
            $tbody .= "<td class='text-center'>" . htmlspecialchars($fila["tracking"] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
            $tbody .= "<td class='text-start'>" . htmlspecialchars($fila["descripcion"] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
            $tbody .= "<td class='text-end'>" . htmlspecialchars($fila["valor_declarado"] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
            $tbody .= "<td class='text-center'>" . htmlspecialchars($fila["carrier"] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
            $tbody .= "<td class='text-center'>" . htmlspecialchars($fila["tienda"] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
            
            // Handle URL with proper link display
            $url = urldecode($fila["url_compra"] ?? '');
            if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                $tbody .= "<td class='text-center'>";
                $tbody .= "<a href='" . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . "' target='_blank' class='btn btn-sm btn-outline-primary'>";
                $tbody .= "<i class='fas fa-external-link-alt me-1'></i>Ver";
                $tbody .= "</a>";
                $tbody .= "</td>";
            } else {
                $tbody .= "<td class='text-center text-muted'>Sin enlace</td>";
            }
            
            $tbody .= "</tr>";
        }
    } else {
        $tbody = '<tr><td colspan="7" class="text-center text-muted">No se encontraron resultados</td></tr>';
    }
    
    echo $tbody;
    
} catch (Exception $e) {
    error_log("Error in data_prealerta_enlaces.php: " . $e->getMessage());
    echo '<tr><td colspan="7" class="text-center text-danger">Error interno del servidor</td></tr>';
}
?>
