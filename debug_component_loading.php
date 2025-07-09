<?php
// Debug component loading mechanism
require_once('security.php');
configure_session_settings();
session_start();
require_once('tools.php');

echo "<h1>Component Loading Debug Test</h1>";

// Test 1: Check if SecurityManager methods work
echo "<h2>1. SecurityManager Testing</h2>";
try {
    $testComponent = "agentes";
    $sanitized = SecurityManager::sanitizeInput($testComponent, 'component');
    echo "Original: '$testComponent'<br>";
    echo "Sanitized: '$sanitized'<br>";
    
    $isAllowed = SecurityManager::includeComponent($testComponent);
    echo "Component allowed: " . ($isAllowed ? "YES" : "NO") . "<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

// Test 2: Simulate the exact component loading process
echo "<h2>2. Simulating Component Loading</h2>";
$_REQUEST['component'] = 'agentes';
$_REQUEST['token'] = SecurityManager::generateCSRFToken();

echo "Component parameter: " . $_REQUEST['component'] . "<br>";
echo "Token parameter: " . substr($_REQUEST['token'], 0, 10) . "...<br>";

if (isset($_REQUEST['component']) && isset($_REQUEST['token'])) {
    echo "✓ Both parameters present<br>";
    
    if (SecurityManager::validateCSRFToken($_REQUEST['token'])) {
        echo "✓ CSRF token valid<br>";
        
        $component = SecurityManager::sanitizeInput($_REQUEST['component'], 'component');
        echo "Sanitized component: '$component'<br>";
        
        echo "<h3>Attempting to include component...</h3>";
        ob_start();
        $included = SecurityManager::includeComponent($component);
        $output = ob_get_clean();
        
        echo "Include result: " . ($included ? "SUCCESS" : "FAILED") . "<br>";
        echo "Output length: " . strlen($output) . " characters<br>";
        
        if ($included && !empty($output)) {
            echo "<h3>Component Output Preview (first 500 chars):</h3>";
            echo "<pre>" . htmlspecialchars(substr($output, 0, 500)) . "</pre>";
        } else {
            echo "<div style='color: red;'>No output captured or component failed to load</div>";
        }
        
    } else {
        echo "✗ CSRF token invalid<br>";
    }
} else {
    echo "✗ Missing parameters<br>";
}

// Test 3: Direct file check
echo "<h2>3. Direct File Access Test</h2>";
$agentesFile = "agentes.php";
echo "File exists: " . (file_exists($agentesFile) ? "YES" : "NO") . "<br>";
echo "File readable: " . (is_readable($agentesFile) ? "YES" : "NO") . "<br>";
echo "File size: " . filesize($agentesFile) . " bytes<br>";

// Test 4: Check if there are PHP errors in agentes.php
echo "<h2>4. PHP Syntax Check</h2>";
$syntaxCheck = shell_exec("php -l agentes.php 2>&1");
echo "Syntax check result: <pre>$syntaxCheck</pre>";

?>
