<?php
include 'BD_connexion.php';

$sql_EC = "SELECT 1 FROM user WHERE email = :1 LIMIT 1";
$stmt = $gd->prepare($sql_EC);
$stmt -> bindParam(":1", $_POST['email'], PDO::PARAM_STR);
$stmt->execute();

if ($stmt->fetchColumn()) {
    header("Location: register.php?error=1");
    exit;
}

?>




<?php

// INSERT
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
    session_start();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['user_id'] = $usuario['id'];

    header("Location: register3.html");
    exit;
} else {
    echo "Error creating session (email/mot de passe incorrect ou SELECT failed)";
    exit;
}
?>
