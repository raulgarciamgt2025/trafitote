<?php
// Simple test for panel_avances_data.php
session_start();

// Mock session data for testing
$_SESSION['user_id'] = 'test_user';
$_SESSION['login_time'] = time();

// Test the AJAX endpoint
$url = 'http://localhost:8080/data/panel_avances_data.php';

// Prepare POST data
$postData = http_build_query([
    'csrf_token' => 'test_token'
]);

// Create context
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                   "Content-Length: " . strlen($postData) . "\r\n",
        'content' => $postData
    ]
]);

echo "Testing AJAX endpoint...\n";
echo "URL: $url\n";
echo "Post data: $postData\n\n";

// Make request
$result = file_get_contents($url, false, $context);

if ($result === false) {
    echo "ERROR: Could not connect to endpoint\n";
} else {
    echo "Response received:\n";
    echo $result . "\n";
    
    // Try to decode JSON
    $data = json_decode($result, true);
    if ($data === null) {
        echo "\nERROR: Invalid JSON response\n";
    } else {
        echo "\nJSON decoded successfully:\n";
        print_r($data);
    }
}
?>
