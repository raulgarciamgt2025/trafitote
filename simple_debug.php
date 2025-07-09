<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Current working directory: " . getcwd() . "<br>";

echo "<h2>File Checks</h2>";
$files = ['security.php', 'tools.php', 'agentes.php'];
foreach ($files as $file) {
    echo "$file: " . (file_exists($file) ? "EXISTS" : "MISSING") . "<br>";
}

echo "<h2>Loading security.php</h2>";
if (file_exists('security.php')) {
    try {
        require_once('security.php');
        echo "✓ security.php loaded<br>";
        
        echo "SecurityManager class exists: " . (class_exists('SecurityManager') ? "YES" : "NO") . "<br>";
        
        if (class_exists('SecurityManager')) {
            echo "Testing sanitizeInput method...<br>";
            $test = SecurityManager::sanitizeInput('agentes', 'component');
            echo "sanitizeInput result: '$test'<br>";
        }
        
    } catch (Exception $e) {
        echo "Error loading security.php: " . $e->getMessage() . "<br>";
    } catch (Error $e) {
        echo "Fatal error loading security.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "security.php not found<br>";
}

echo "<h2>Testing session configuration</h2>";
try {
    configure_session_settings();
    echo "✓ Session configured<br>";
} catch (Exception $e) {
    echo "Error configuring session: " . $e->getMessage() . "<br>";
}

echo "<h2>Testing session start</h2>";
try {
    session_start();
    echo "✓ Session started<br>";
} catch (Exception $e) {
    echo "Error starting session: " . $e->getMessage() . "<br>";
}

echo "<h2>Loading tools.php</h2>";
if (file_exists('tools.php')) {
    try {
        require_once('tools.php');
        echo "✓ tools.php loaded<br>";
    } catch (Exception $e) {
        echo "Error loading tools.php: " . $e->getMessage() . "<br>";
    } catch (Error $e) {
        echo "Fatal error loading tools.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "tools.php not found<br>";
}

?>
