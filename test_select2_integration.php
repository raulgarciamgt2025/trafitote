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

// Generate CSRF token
$csrf_token = SecurityManager::generateCSRFToken();

// Generate the URL with proper parameters
$agentes_url = "index2.php?component=agentes&token=" . urlencode($csrf_token);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Agentes in Index2</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .link-box { 
            padding: 20px; 
            background: #f0f0f0; 
            border-radius: 5px; 
            margin-bottom: 20px;
        }
        .link-box a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .instructions {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Test Agentes Component in Index2</h1>
    
    <div class="instructions">
        <h3>Testing Instructions:</h3>
        <ol>
            <li>Click the link below to load the agentes component</li>
            <li>Open browser developer tools (F12) and check the Console tab</li>
            <li>Look for Select2 initialization messages</li>
            <li>Verify that the dropdowns work with Select2 styling</li>
        </ol>
    </div>
    
    <div class="link-box">
        <h3>Test Link:</h3>
        <a href="<?php echo htmlspecialchars($agentes_url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
            Open Agentes Component in Index2
        </a>
    </div>
    
    <div class="link-box">
        <h3>Alternative Direct Test:</h3>
        <a href="test_agentes_direct.php" target="_blank">
            Direct Agentes Test (without index2 wrapper)
        </a>
    </div>
    
    <div class="link-box">
        <h3>Minimal Select2 Test:</h3>
        <a href="minimal_select2_test.php" target="_blank">
            Basic Select2 Functionality Test
        </a>
    </div>
    
    <div style="margin-top: 30px; padding: 15px; background: #e9ecef; border-radius: 5px;">
        <h4>Debug Information:</h4>
        <p><strong>CSRF Token:</strong> <?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Complete URL:</strong> <code><?php echo htmlspecialchars($agentes_url, ENT_QUOTES, 'UTF-8'); ?></code></p>
    </div>
</body>
</html>
