<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Select2</title>
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for additional icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Test Select2</h4>
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="testSelect" class="form-label">Test Select</label>
                        <select id="testSelect" class="form-select">
                            <option value="">Seleccione una opción</option>
                            <option value="1">Opción 1</option>
                            <option value="2">Opción 2</option>
                            <option value="3">Opción 3</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts in correct order -->
    <!-- Bootstrap 5.3.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 JS (must be loaded after jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Wait for the document to be fully loaded and jQuery to be available
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');
            
            // Check if jQuery is loaded, if not wait for it
            function waitForJQuery() {
                console.log('Checking for jQuery...');
                if (typeof $ !== 'undefined') {
                    console.log('jQuery found, initializing Select2');
                    initializeComponents();
                } else {
                    console.log('jQuery not found, waiting...');
                    setTimeout(waitForJQuery, 100);
                }
            }
            
            function initializeComponents() {
                $(document).ready(function() {
                    console.log('jQuery ready, initializing Select2');
                    
                    // Initialize Select2 for test select
                    $('#testSelect').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Seleccione una opción',
                        allowClear: true,
                        width: '100%'
                    });
                    
                    console.log('Select2 initialized');
                });
            }
            
            waitForJQuery();
        });
    </script>
</body>
</html>
