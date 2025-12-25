<?php
include 'BD_connexion.php';

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
