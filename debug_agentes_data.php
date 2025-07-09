<?php
// Debug version of agentes_data.php to catch errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "Starting debug...\n";

// Include security configuration first to get session settings function
try {
    require_once(dirname(__DIR__) . '/security.php');
    echo "Security loaded...\n";
} catch (Exception $e) {
    echo "Error loading security: " . $e->getMessage() . "\n";
    exit;
}

// Configure session settings before starting session
try {
    configure_session_settings();
    echo "Session configured...\n";
} catch (Exception $e) {
    echo "Error configuring session: " . $e->getMessage() . "\n";
    exit;
}

// Start session
try {
    session_start();
    echo "Session started...\n";
} catch (Exception $e) {
    echo "Error starting session: " . $e->getMessage() . "\n";
    exit;
}

// Include tools after session is started
try {
    require_once(dirname(__DIR__) . '/tools.php');
    echo "Tools loaded...\n";
} catch (Exception $e) {
    echo "Error loading tools: " . $e->getMessage() . "\n";
    exit;
}

// Check session
echo "Session data: " . print_r($_SESSION, true) . "\n";

// Validate session
try {
    SecurityManager::validateSession();
    echo "Session validated...\n";
} catch (Exception $e) {
    echo "Error validating session: " . $e->getMessage() . "\n";
    exit;
}

echo "All includes successful!\n";
?>
