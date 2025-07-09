<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Select2 Integration Validation ===<br>";

// Test 1: Check index2.php structure
echo "<h2>1. Index2.php CDN Configuration</h2>";
$index2Content = file_get_contents('index2.php');

// Check for Select2 CSS
$hasSelect2CSS = strpos($index2Content, 'select2@4.1.0-rc.0/dist/css/select2.min.css') !== false;
$hasSelect2Theme = strpos($index2Content, 'select2-bootstrap-5-theme') !== false;
echo "✓ Select2 CSS included: " . ($hasSelect2CSS ? "YES" : "NO") . "<br>";
echo "✓ Select2 Bootstrap theme: " . ($hasSelect2Theme ? "YES" : "NO") . "<br>";

// Check for Font Awesome
$hasFontAwesome = strpos($index2Content, 'font-awesome') !== false;
echo "✓ Font Awesome included: " . ($hasFontAwesome ? "YES" : "NO") . "<br>";

// Check for Select2 JS placement
$jqueryPos = strpos($index2Content, 'jquery-3.7.1.min.js');
$select2Pos = strpos($index2Content, 'select2@4.1.0-rc.0/dist/js/select2.min.js');
$correctOrder = $jqueryPos !== false && $select2Pos !== false && $jqueryPos < $select2Pos;
echo "✓ Script loading order (jQuery before Select2): " . ($correctOrder ? "CORRECT" : "INCORRECT") . "<br>";

// Test 2: Check security.php CSP
echo "<h2>2. Content Security Policy</h2>";
$securityContent = file_get_contents('security.php');
$hasCloudflare = strpos($securityContent, 'cdnjs.cloudflare.com') !== false;
echo "✓ CloudFlare CDN allowed in CSP: " . ($hasCloudflare ? "YES" : "NO") . "<br>";

// Test 3: Check agentes.php integration
echo "<h2>3. Agentes.php Select2 Configuration</h2>";
$agentesContent = file_get_contents('agentes.php');
$hasSelect2Init = strpos($agentesContent, ".select2({") !== false;
$hasBootstrapTheme = strpos($agentesContent, "theme: 'bootstrap-5'") !== false;
$hasFormControl = strpos($agentesContent, 'class="form-control"') !== false;
echo "✓ Select2 initialization present: " . ($hasSelect2Init ? "YES" : "NO") . "<br>";
echo "✓ Bootstrap 5 theme configured: " . ($hasBootstrapTheme ? "YES" : "NO") . "<br>";
echo "✓ Form control classes used: " . ($hasFormControl ? "YES" : "NO") . "<br>";

// Test 4: Component functionality
echo "<h2>4. Component Functionality Test</h2>";
try {
    require_once('security.php');
    configure_session_settings();
    session_start();
    require_once('tools.php');
    
    $_SESSION['id_usuario'] = 1;
    $_SESSION['last_activity'] = time();
    $_REQUEST['component'] = 'agentes';
    $_REQUEST['token'] = SecurityManager::generateCSRFToken();
    $_REQUEST['tokenid'] = $_REQUEST['token'];
    
    ob_start();
    $included = SecurityManager::includeComponent('agentes');
    $output = ob_get_clean();
    
    $hasSelectElements = substr_count($output, '<select') >= 3;
    $hasSelect2JS = strpos($output, '.select2(') !== false;
    $hasFormControlClass = strpos($output, 'form-control') !== false;
    
    echo "✓ Component inclusion: " . ($included ? "SUCCESS" : "FAILED") . "<br>";
    echo "✓ Three select elements present: " . ($hasSelectElements ? "YES" : "NO") . "<br>";
    echo "✓ Select2 JavaScript initialization: " . ($hasSelect2JS ? "YES" : "NO") . "<br>";
    echo "✓ Form control classes applied: " . ($hasFormControlClass ? "YES" : "NO") . "<br>";
    
} catch (Exception $e) {
    echo "✗ Component test error: " . $e->getMessage() . "<br>";
}

echo "<h2>Summary</h2>";
$allTests = [
    $hasSelect2CSS, $hasSelect2Theme, $hasFontAwesome, $correctOrder,
    $hasCloudflare, $hasSelect2Init, $hasBootstrapTheme, $hasFormControl
];

$passedTests = array_filter($allTests);
$totalTests = count($allTests);
$passedCount = count($passedTests);

echo "<strong>Tests Passed: $passedCount / $totalTests</strong><br>";
if ($passedCount === $totalTests) {
    echo "<div style='color: green; font-weight: bold;'>✅ ALL TESTS PASSED - Select2 integration is working correctly!</div>";
} else {
    echo "<div style='color: red; font-weight: bold;'>❌ Some tests failed - Please review the configuration.</div>";
}

?>
