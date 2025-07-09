<?php
// Test the actual login.php with POST data
require_once('security.php');
configure_session_settings();
session_start();

// Simulate POST data for testing
$_POST['usuarioLogin'] = 'usuario';
$_POST['passwordLogin'] = 'usuario';
$_POST['csrf_token'] = SecurityManager::generateCSRFToken();

echo "<h2>Testing Actual Login Process</h2>";
echo "Username: " . $_POST['usuarioLogin'] . "<br>";
echo "Password: " . str_repeat('*', strlen($_POST['passwordLogin'])) . "<br>";
echo "CSRF Token: " . substr($_POST['csrf_token'], 0, 10) . "...<br><br>";

// Include the login script
ob_start();
include('login.php');
$output = ob_get_clean();

if (!empty($output)) {
    echo "Login output:<br>";
    echo $output;
} else {
    echo "<div style='color: green;'>✓ Login completed successfully (redirect expected)</div>";
}

echo "<br><br>Session data after login:<br>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";
?>
