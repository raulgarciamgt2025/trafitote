<?php
// Simple test file to check if basic PHP works
echo "Test panel starting...\n";

try {
    require_once('security.php');
    echo "Security loaded\n";
} catch (Exception $e) {
    echo "Error loading security: " . $e->getMessage() . "\n";
    exit;
}

try {
    require_once('tools.php');
    echo "Tools loaded\n";
} catch (Exception $e) {
    echo "Error loading tools: " . $e->getMessage() . "\n";
    exit;
}

echo "All dependencies loaded successfully\n";

// Test database connection
try {
    $db = new DatabaseHelper();
    echo "Database helper created\n";
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "Test completed\n";
?>
