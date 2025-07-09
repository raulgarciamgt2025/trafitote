<?php
// Test the sanitization process
require_once('tools.php');

echo "<h2>Testing Username Sanitization</h2>";

$securityManager = SecurityManager::getInstance();

$original_username = "raul.garcia";
$sanitized_username = $securityManager->sanitizeInput($original_username, 'alphanumeric');

echo "Original username: '" . htmlspecialchars($original_username) . "'<br>";
echo "Sanitized username: '" . htmlspecialchars($sanitized_username) . "'<br>";
echo "Characters removed: " . (strlen($original_username) - strlen($sanitized_username)) . "<br>";

if ($original_username !== $sanitized_username) {
    echo "<div style='color: red;'>⚠️ Username was modified during sanitization!</div>";
    echo "The dot (.) character is being removed by 'alphanumeric' sanitization.<br>";
} else {
    echo "<div style='color: green;'>✓ Username unchanged during sanitization</div>";
}

echo "<br><h3>Testing different sanitization types:</h3>";
$types = ['string', 'alphanumeric', 'username', 'codigo_barra'];

foreach ($types as $type) {
    $result = $securityManager->sanitizeInput($original_username, $type);
    echo "Type '$type': '" . htmlspecialchars($result) . "'<br>";
}

echo "<br><h3>Testing the login process with username type:</h3>";
$username_sanitized = $securityManager->sanitizeInput($original_username, 'username');
echo "Username after 'username' sanitization: '" . htmlspecialchars($username_sanitized) . "'<br>";

if ($original_username === $username_sanitized) {
    echo "<div style='color: green;'>✓ Username preserved correctly!</div>";
} else {
    echo "<div style='color: red;'>✗ Username still being modified</div>";
}
?>
