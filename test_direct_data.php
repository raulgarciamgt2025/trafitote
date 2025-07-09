<?php
// Simple test to check what's wrong with agentes_data.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate AJAX call
$_POST['draw'] = '1';
$_POST['start'] = '0';
$_POST['length'] = '10';
$_POST['search'] = ['value' => ''];
$_POST['order'] = [['column' => '7', 'dir' => 'asc']];
$_POST['columns'] = array_fill(0, 9, ['search' => ['value' => '']]);
$_POST['csrf_token'] = 'test_token';

echo "Testing agentes_data.php directly...\n\n";

// Capture all output
ob_start();
try {
    include 'data/agentes_data.php';
} catch (Exception $e) {
    echo "Exception caught: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "Error caught: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
$output = ob_get_clean();

echo "Output length: " . strlen($output) . "\n";
echo "Output:\n" . $output . "\n";

if (function_exists('http_response_code')) {
    echo "Response code: " . http_response_code() . "\n";
}
?>
