<?php
session_start();

// Vérification connexion (pages protégées)
if (!isset($_SESSION['user_id'])) {
    header("Location: /pages/public/login.php");
    exit;
}
?>

<!-- Header commun à toutes les pages -->

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dessarollo</title>
    <link rel="stylesheet" href="/css/style.css">
    <!-- Bootstrap plus tard : css -->
</head>

<body>

    <div class="layout">
        <?php include __DIR__ . "/nav.php"; ?>

        <main class="content">