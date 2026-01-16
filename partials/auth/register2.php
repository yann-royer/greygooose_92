

<?php
// Chargement de la configuration globale (BASE_URL, constantes, etc.)
require_once __DIR__ . '/../../config.php';
// INCLUDE PHP (chemins système)
include __DIR__ . '/../db/BD_connexion.php';

// CHECK EMAIL EXISTS
$sql_EC = "SELECT 1 FROM user WHERE email = :1 LIMIT 1";
$stmt = $gd->prepare($sql_EC);
$stmt->bindParam(":1", $_POST['email'], PDO::PARAM_STR);
$stmt->execute();

if ($stmt->fetchColumn()) {
    header("Location: " . BASE_URL . "/pages/public/register.php?error=1");
    exit;
}

?>




<?php

// INSERT : création du compte
$sql = "INSERT INTO user (email, password) VALUES (:email, :pw)";
$stmt = $gd->prepare($sql);
$stmt->bindParam(":email", $_POST['email']);
$stmt->bindParam(":pw", $_POST['pw']);
$okInsert = $stmt->execute();

if (!$okInsert) {
    echo "Erreur lors de l'inscription";
    exit;
}

// SELECT (connexion / session)
$sql = "SELECT * FROM user WHERE email = :email AND password = :pw";
$stmt = $gd->prepare($sql);
$stmt->bindParam(":email", $_POST['email']);
$stmt->bindParam(":pw", $_POST['pw']);
$okSelect = $stmt->execute();

if ($okSelect && $stmt->rowCount() > 0) {

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    session_start();
    $_SESSION['user_id'] = $user['id'];

    // Redirection vers la page de complétion du profil
    header("Location: " . BASE_URL . "/pages/private/register3.php?register=1");  // remplacer register3.html plus tard par modification_profil.php

    exit;
} else {
    echo "Error creating session (email/mot de passe incorrect ou SELECT failed)";
    exit;
}

?>
