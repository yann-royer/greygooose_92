<?php

// Chargement de la configuration globale (BASE_URL, constantes, etc.)
require_once __DIR__ . '/../../config.php';
// INCLUDE PHP (chemins système)
include __DIR__ . '/../db/BD_connexion.php';

$sql = "SELECT password FROM user WHERE email = :1 LIMIT 1";
$stmt = $gd->prepare($sql);
$stmt->bindParam(":1", $_POST['email']);
$stmt->execute();

$pw = $stmt->fetchColumn();


if ($pw) {
	header("Location: " . BASE_URL . "/pages/public/forget_pw.php?pw=" . $pw);
	exit;
} else {
	header("Location: " . BASE_URL . "/pages/public/forget_pw.php?error=1");
	exit;
}
