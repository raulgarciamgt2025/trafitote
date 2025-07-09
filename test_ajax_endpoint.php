<?php
// Direct test of agentes_data.php via web server
session_start();

// Set mock user session for testing
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'test@test.com';
$_SESSION['user_name'] = 'Test User';
$_SESSION['is_authenticated'] = true;
$_SESSION['login_time'] = time();

require_once 'tools.php';

// Generate a real CSRF token
$csrf_token = SecurityManager::generateCSRFToken();

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test AJAX Data Provider</title>
    <script src='jquery/jquery-3.5.1.min.js'></script>
</head>
<body>
    <h2>Testing AJAX Data Provider</h2>
    <p>CSRF Token: " . htmlspecialchars($csrf_token) . "</p>
    <button id='testBtn'>Test AJAX Call</button>
    <div id='result'></div>
    
    <script>
    $(document).ready(function() {
        $('#testBtn').click(function() {
            var data = {
                draw: 1,
                start: 0,
                length: 5,
                search: { value: '' },
                order: [{ column: 7, dir: 'asc' }],
                columns: [
                    { search: { value: '' } },
                    { search: { value: '' } },
                    { search: { value: '' } },
                    { search: { value: '' } },
                    { search: { value: '' } },
                    { search: { value: '' } },
                    { search: { value: '' } },
                    { search: { value: '' } },
                    { search: { value: '' } }
                ],
                csrf_token: '" . $csrf_token . "'
            };
            
            $.ajax({
                url: 'data/agentes_data.php',
                type: 'POST',
                data: data,
                success: function(response) {
                    $('#result').html('<h3>Success!</h3><pre>' + JSON.stringify(response, null, 2) + '</pre>');
                },
                error: function(xhr, status, error) {
                    $('#result').html('<h3>Error!</h3><p>Status: ' + status + '</p><p>Error: ' + error + '</p><p>Response: ' + xhr.responseText + '</p>');
                }
            });
        });
    });
    </script>
</body>
</html>";
?>
