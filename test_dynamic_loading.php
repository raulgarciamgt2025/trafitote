<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Dynamic Agentes Loading</title>
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Font Awesome for additional icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
</head>

<body class="bg-light">
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Dynamic Agentes Component Loading Test</h4>
                    </div>
                    <div class="card-body">
                        <button id="loadComponent" class="btn btn-primary">Load Agentes Component</button>
                        <button id="reinitializeSelect2" class="btn btn-secondary ms-2">Re-initialize Select2</button>
                        <div id="console-log" class="mt-3 p-3 bg-light border rounded" style="height: 200px; overflow-y: auto;">
                            <strong>Console Log:</strong><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dynamic content will be loaded here -->
        <div id="dynamic-content" class="mt-3">
            <!-- Agentes component will be loaded here -->
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
        // Console logging function
        function logToConsole(message) {
            console.log(message);
            const logDiv = document.getElementById('console-log');
            logDiv.innerHTML += new Date().toLocaleTimeString() + ': ' + message + '<br>';
            logDiv.scrollTop = logDiv.scrollHeight;
        }
        
        $(document).ready(function() {
            logToConsole('Page ready - jQuery and Select2 available');
            
            // Global Select2 initialization function (similar to index2.php)
            window.initializeSelect2 = function(container) {
                var $container = container ? $(container) : $(document);
                
                logToConsole('Initializing Select2 in container: ' + (container || 'document'));
                
                $container.find('select').not('.select2-hidden-accessible').each(function() {
                    var $select = $(this);
                    var id = $select.attr('id');
                    
                    logToConsole('Found select element: ' + id);
                    
                    try {
                        var placeholder = 'Seleccione una opción';
                        
                        if (id === 'slAgentes') {
                            placeholder = 'Seleccione un agente';
                        } else if (id === 'slPerfil') {
                            placeholder = 'Seleccione un perfil';
                        } else if (id === 'slModalidad') {
                            placeholder = 'Seleccione una modalidad';
                        }
                        
                        $select.select2({
                            theme: 'bootstrap-5',
                            placeholder: placeholder,
                            allowClear: true,
                            width: '100%'
                        });
                        
                        logToConsole('✓ Select2 initialized for: ' + id);
                    } catch (e) {
                        logToConsole('✗ Error initializing Select2 for ' + id + ': ' + e.message);
                    }
                });
            };
            
            $('#loadComponent').click(function() {
                logToConsole('Loading agentes component...');
                
                // Load the component via AJAX
                $.get('agentes.php')
                    .done(function(data) {
                        logToConsole('Component loaded successfully');
                        $('#dynamic-content').html(data);
                        
                        // Initialize Select2 after content is loaded
                        setTimeout(function() {
                            logToConsole('Triggering Select2 initialization...');
                            window.initializeSelect2('#dynamic-content');
                        }, 100);
                    })
                    .fail(function() {
                        logToConsole('✗ Failed to load component');
                    });
            });
            
            $('#reinitializeSelect2').click(function() {
                logToConsole('Re-initializing Select2...');
                window.initializeSelect2('#dynamic-content');
            });
        });
    </script>
</body>
</html>
