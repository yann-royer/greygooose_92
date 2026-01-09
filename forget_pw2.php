<?php

include 'BD_connexion.php';

$sql = "SELECT password FROM user WHERE email = :1 LIMIT 1";
$stmt = $gd->prepare($sql);
$stmt->bindParam(":1", $_POST['email']);
$stmt->execute();

$pw = $stmt->fetchColumn();


if($pw) {
	header("Location: forget_pw.php?pw=".$pw);
	exit;
}
else {
	header("Location: forget_pw.php?error=1");
	exit;
}


?>