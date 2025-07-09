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

echo "CSRF Token: " . $csrf_token;
?>
