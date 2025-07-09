<?php
session_start();

// Set mock user session for testing
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'test@test.com';
$_SESSION['user_name'] = 'Test User';
$_SESSION['is_authenticated'] = true;
$_SESSION['login_time'] = time();

// Set component for agentes
$_GET['component'] = 'agentes';

// Include the main layout
include 'index2.php';
?>
