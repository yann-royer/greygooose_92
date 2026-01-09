
<?php

require_once __DIR__ . '/../../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vider complètement la session
$_SESSION = [];
session_destroy();

// Redirection
header('Location: ' . BASE_URL . '/pages/public/login.php');
exit;
