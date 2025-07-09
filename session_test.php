<?php
// Test file to verify session functionality
// Include security configuration first to get session settings function
require_once('security.php');

// Configure session settings before starting session
configure_session_settings();

// Start session
session_start();
require_once('tools.php');

echo "Session test successful!<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Session status: " . session_status() . "<br>";

if (isset($_SESSION['test'])) {
    echo "Previous session data found: " . $_SESSION['test'] . "<br>";
} else {
    $_SESSION['test'] = "Session data set at " . date('Y-m-d H:i:s');
    echo "New session data created: " . $_SESSION['test'] . "<br>";
}

echo "<br><a href='session_test.php'>Refresh this page</a>";
echo "<br><a href='index.php'>Go to main page</a>";
?>
