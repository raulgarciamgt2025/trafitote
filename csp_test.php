<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSP Test - Bootstrap & jQuery Loading</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php
    // Include security configuration
    require_once('security.php');
    configure_session_settings();
    session_start();
    ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-check-circle"></i> CSP Test Page</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Testing Bootstrap & jQuery Loading</strong><br>
                            If you can see this styled page with icons, the CSP is working correctly.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="bi bi-bootstrap"></i> Bootstrap Test</h5>
                                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample">
                                    Toggle Collapse
                                </button>
                                <div class="collapse mt-2" id="collapseExample">
                                    <div class="card card-body">
                                        Bootstrap collapse is working!
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5><i class="bi bi-code-square"></i> jQuery Test</h5>
                                <button id="jqueryTest" class="btn btn-success">Test jQuery</button>
                                <div id="jqueryResult" class="mt-2"></div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h5><i class="bi bi-info-circle"></i> Current CSP Policy</h5>
                            <pre class="bg-light p-3 rounded small">
<?php
// Display current CSP policy
$headers = headers_list();
foreach ($headers as $header) {
    if (strpos($header, 'Content-Security-Policy') !== false) {
        echo htmlspecialchars($header);
        break;
    }
}
?>
                            </pre>
                        </div>
                        
                        <div class="mt-3">
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left"></i> Back to Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#jqueryTest').click(function() {
                $('#jqueryResult').html('<div class="alert alert-success">✅ jQuery is working correctly!</div>');
            });
            
            // Test if Bootstrap is loaded
            if (typeof bootstrap !== 'undefined') {
                console.log('✅ Bootstrap JS loaded successfully');
            } else {
                console.error('❌ Bootstrap JS failed to load');
            }
            
            // Test if jQuery is loaded
            if (typeof $ !== 'undefined') {
                console.log('✅ jQuery loaded successfully');
            } else {
                console.error('❌ jQuery failed to load');
            }
        });
    </script>
</body>
</html>
