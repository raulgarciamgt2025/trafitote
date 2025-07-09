<?php
/**
 * Test agentes_data.php with exact DataTables parameters
 */

// Start session and set up environment
session_start();

// Mock session data
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'test@test.com';
$_SESSION['user_name'] = 'Test User';
$_SESSION['is_authenticated'] = true;
$_SESSION['login_time'] = time();

// Mock exact DataTables POST parameters
$_POST = [
    'draw' => '1',
    'columns' => [
        ['data' => '0', 'name' => '', 'searchable' => 'false', 'orderable' => 'false', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '1', 'name' => '', 'searchable' => 'false', 'orderable' => 'false', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '2', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '3', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '4', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '5', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '6', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '7', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
        ['data' => '8', 'name' => '', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']]
    ],
    'order' => [
        ['column' => '7', 'dir' => 'asc']
    ],
    'start' => '0',
    'length' => '15',
    'search' => [
        'value' => '',
        'regex' => 'false'
    ],
    'csrf_token' => 'test_token'
];

echo "=== Testing agentes_data.php with DataTables parameters ===\n\n";

// Capture the output
ob_start();

try {
    // Include the data provider (this will output JSON)
    include 'data/agentes_data.php';
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$output = ob_get_clean();

echo "JSON Response:\n";
echo $output . "\n\n";

// Try to decode the JSON
$data = json_decode($output, true);
if ($data) {
    echo "Decoded successfully!\n";
    echo "Draw: " . $data['draw'] . "\n";
    echo "Records Total: " . $data['recordsTotal'] . "\n";
    echo "Records Filtered: " . $data['recordsFiltered'] . "\n";
    echo "Data rows: " . count($data['data']) . "\n\n";
    
    if (count($data['data']) > 0) {
        echo "First row sample:\n";
        print_r($data['data'][0]);
    }
} else {
    echo "Failed to decode JSON!\n";
    echo "JSON Error: " . json_last_error_msg() . "\n";
}
?>
