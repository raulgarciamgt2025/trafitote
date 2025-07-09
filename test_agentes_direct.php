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
    <title>Test Agentes with Select2</title>
    
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
        <div id="console-output" class="alert alert-info">
            <strong>Debug Console:</strong><br>
            <div id="debug-log"></div>
        </div>
        
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
            }
        }
        
        // Log script loading status
        debugLog('Scripts starting to load...');
        
        // Check when scripts are available
        function checkScripts() {
            debugLog('Checking script availability...');
            debugLog('jQuery available: ' + (typeof $ !== 'undefined' ? 'YES' : 'NO'));
            debugLog('Select2 available: ' + (typeof $.fn !== 'undefined' && typeof $.fn.select2 !== 'undefined' ? 'YES' : 'NO'));
            debugLog('Bootstrap available: ' + (typeof bootstrap !== 'undefined' ? 'YES' : 'NO'));
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            debugLog('DOM Content Loaded');
            setTimeout(checkScripts, 100);
            setTimeout(checkScripts, 500);
            setTimeout(checkScripts, 1000);
            setTimeout(checkScripts, 2000);
        });
        
        window.addEventListener('load', function() {
            debugLog('Window Load Complete');
            checkScripts();
        });
    </script>
</body>
</html>
