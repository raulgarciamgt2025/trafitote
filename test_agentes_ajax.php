<?php
// Simple test to verify agentes_data.php works
session_start();

// Mock DataTables POST parameters
$_POST = [
    'draw' => 1,
    'start' => 0,
    'length' => 10,
    'search' => ['value' => ''],
    'order' => [['column' => 7, 'dir' => 'asc']],
    'columns' => [
        ['search' => ['value' => '']],
        ['search' => ['value' => '']],
        ['search' => ['value' => '']],
        ['search' => ['value' => '']],
        ['search' => ['value' => '']],
        ['search' => ['value' => '']],
        ['search' => ['value' => '']],
        ['search' => ['value' => '']],
        ['search' => ['value' => '']]
    ],
    'csrf_token' => 'test_token'
];

// Set mock user session
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'test@test.com';
$_SESSION['user_name'] = 'Test User';
$_SESSION['is_authenticated'] = true;
$_SESSION['login_time'] = time();

echo "<h3>Testing agentes_data.php</h3>";
echo "<pre>";

// Capture output from agentes_data.php
ob_start();
include 'data/agentes_data.php';
$output = ob_get_clean();

echo "Raw output:\n";
echo htmlspecialchars($output);

echo "\n\nJSON decoded:\n";
$data = json_decode($output, true);
if ($data) {
    print_r($data);
} else {
    echo "Failed to decode JSON\n";
    echo "JSON Error: " . json_last_error_msg();
}

echo "</pre>";
?>
