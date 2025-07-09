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

// Create proper URL with token
$agentes_url = "index2.php?component=agentes&token=" . urlencode($csrf_token);

echo "<!DOCTYPE html>";
echo "<html><head><title>Agentes Redirect</title></head>";
echo "<body>";
echo "<h2>Loading Agentes Component...</h2>";
echo "<p>Token: " . htmlspecialchars($csrf_token) . "</p>";
echo "<p>URL: " . htmlspecialchars($agentes_url) . "</p>";
echo "<script>window.location.href = '" . htmlspecialchars($agentes_url, ENT_QUOTES) . "';</script>";
echo "</body></html>";
?>
