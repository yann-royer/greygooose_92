<?php

// Chargement de la configuration globale (BASE_URL, constantes, etc.)
require_once __DIR__ . '/../../config.php';
// connexion à la BDD
require_once __DIR__ . '/../../partials/db/BD_connexion.php';


session_start();

// Vérification connexion (pages protégées)
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/public/login.php');
    exit;
}
?>

<!-- Header commun à toutes les pages -->

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dessarollo</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/styles.css">
    <!-- Bootstrap plus tard : css -->
</head>

<body>

    <div class="layout">
        <?php include __DIR__ . "/nav.php"; ?>

        <main class="content">