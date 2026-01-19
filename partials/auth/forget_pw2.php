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
	$email = $_POST['email'];

	$data = [
	    "sender" => [
	        "name" => "Team GreyGooose",
	        "email" => "darcmon54@gmail.com"
	    ],
	    "to" => [
	        [
	            "email" => $email
	        ]
	    ],
	    "subject" => "Recupération de mot de passe",
	    "htmlContent" => "<h1>Bonjour</h1><br>Voici votre mot de passe : ".$pw
	];

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.brevo.com/v3/smtp/email");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
	    "accept: application/json",
	    "api-key: " . BREVO_API_KEY,
	    "content-type: application/json"
	]);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

	$response = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if ($httpCode >= 200 && $httpCode < 300) {
	    header("Location: " . BASE_URL . "/pages/public/forget_pw.php?error=0");
	    exit;
	} else {
	    header("Location: " . BASE_URL . "/pages/public/forget_pw.php?error=2");
	    exit;
	}

	exit;
} else {
	header("Location: " . BASE_URL . "/pages/public/forget_pw.php?error=1");
	exit;
}