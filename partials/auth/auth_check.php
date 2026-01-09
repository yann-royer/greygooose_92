<?php
// Chargement de la configuration globale (BASE_URL, constantes, etc.)
require_once __DIR__ . '/../../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/public/login.php');
    exit;
}
