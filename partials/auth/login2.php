<?php
require_once __DIR__ . '/../../config.php';
// INCLUDE PHP (chemins système)
include __DIR__ . '/../db/BD_connexion.php';

if (!isset($_POST['email'], $_POST['pw'])) {
    header('Location: ' . BASE_URL . '/pages/public/login.php');
    exit;
}

// 1️⃣ Une seule requête
$sql = "SELECT * FROM user WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
$stmt->execute();

// 2️⃣ Récupération de l'utilisateur
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 3️⃣ Email inexistant
if (!$user) {
    header('Location: ' . BASE_URL . '/pages/public/login.php?error=1');
    exit;
}

// 4️⃣ Mot de passe incorrect 
if ($user['password'] !== $_POST['pw']) {
    header('Location: ' . BASE_URL . '/pages/public/login.php?error=2');
    exit;
}

// 5️⃣ Login OK
include __DIR__ . '/../session/start_session.php';
$_SESSION['user_id'] = $user['id'];

header('Location: ' . BASE_URL . '/pages/private/main_page.php');
exit;
