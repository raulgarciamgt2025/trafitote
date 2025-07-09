<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minimal Select2 Test</title>
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Minimal Select2 Test</h4>
                <div id="status" class="mt-2"></div>
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

    <!-- Bootstrap 5.3.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 JS (must be loaded after jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function updateStatus(message) {
            console.log(message);
            document.getElementById('status').innerHTML += new Date().toLocaleTimeString() + ': ' + message + '<br>';
        }
        
        updateStatus('Script section started');
        
        // Immediate check
        updateStatus('Immediate check - jQuery: ' + (typeof $ !== 'undefined' ? 'YES' : 'NO'));
        updateStatus('Immediate check - Select2: ' + (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined' ? 'YES' : 'NO'));
        
        function tryInitializeSelect2() {
            updateStatus('Trying to initialize Select2...');
            
            if (typeof $ === 'undefined') {
                updateStatus('jQuery not available');
                setTimeout(tryInitializeSelect2, 100);
                return;
            }
            
            if (typeof $.fn.select2 === 'undefined') {
                updateStatus('Select2 not available');
                setTimeout(tryInitializeSelect2, 100);
                return;
            }
            
            updateStatus('Both jQuery and Select2 available!');
            
            $(document).ready(function() {
                updateStatus('DOM ready, initializing Select2...');
                try {
                    $('#testSelect').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Seleccione una opción',
                        allowClear: true,
                        width: '100%'
                    });
                    updateStatus('✓ Select2 initialized successfully!');
                } catch (e) {
                    updateStatus('✗ Error initializing Select2: ' + e.message);
                }
            });
        }
        
        // Start trying
        setTimeout(tryInitializeSelect2, 100);
        
        document.addEventListener('DOMContentLoaded', function() {
            updateStatus('DOMContentLoaded event fired');
        });
        
        window.addEventListener('load', function() {
            updateStatus('Window load event fired');
        });
    </script>
</body>
</html>
