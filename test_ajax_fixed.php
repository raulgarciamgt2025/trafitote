<?php
// Quick test for AJAX endpoint
session_start();
$_SESSION['user_id'] = 'test_user'; // Mock session

// Test the AJAX endpoint directly
$url = 'http://localhost:8080/data/panel_avances_data.php';

$postData = http_build_query([
    'csrf_token' => 'test_token'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                   "Content-Length: " . strlen($postData) . "\r\n" .
                   "Cookie: " . session_name() . "=" . session_id() . "\r\n",
        'content' => $postData
    ]
]);

echo "Testing AJAX endpoint...\n";
$result = file_get_contents($url, false, $context);

if ($result === false) {
    echo "ERROR: Could not connect to endpoint\n";
} else {
    echo "Response received:\n";
    echo $result . "\n";
    
    $data = json_decode($result, true);
    if ($data === null) {
        echo "\nERROR: Invalid JSON response\n";
    } else {
        echo "\nJSON decoded successfully - Success: " . ($data['success'] ? 'YES' : 'NO') . "\n";
        echo "Data source: " . ($data['data_source'] ?? 'unknown') . "\n";
        echo "Records found: " . count($data['data'] ?? []) . "\n";
        if (!empty($data['data'])) {
            echo "First record: " . print_r($data['data'][0], true) . "\n";
        }
    }
}
?>
