<?php

include 'BD_connexion.php';

$sql = "SELECT * FROM user WHERE email=:1 AND password=:2";
$stmt = $gd->prepare($sql);
$stmt->bindParam(":1", $_POST['email']);
$stmt->bindParam(":2", $_POST['pw']);
$stmt->execute();


//Verifico
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    session_start();
    $_SESSION['user_id'] = $usuario['id'];
    header('Location: main_page.php');
    exit;
} 

else {
    $sql_EC = "SELECT 1 FROM user WHERE email = :1 LIMIT 1";
    $stmt = $gd->prepare($sql_EC);
    $stmt -> bindParam(":1", $_POST['email'], PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetchColumn()) {
        header("Location: login.php?error=2");
        exit;
    }
    header('Location: login.php?error=1');
    exit;
}



?>