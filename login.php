<?php

include 'BD_connexion.php';

$sql = "SELECT * FROM user WHERE email=:1 AND password=:2";
$resultoConsulta = $gd->prepare($sql);
$resultoConsulta->bindParam(":1", $_POST['email']);
$resultoConsulta->bindParam(":2", $_POST['pw']);
$ok = $resultoConsulta->execute();

//Verifico
if($ok) {
	if($resultoConsulta->rowCount()>0) {
		session_start();
		$usuario = $resultoConsulta->fetch();
		$_SESSION['user_id'] = $usuario['id'];
	}
	header('Location: main_page.php');
}
else {
	echo "Error en la base de Datos";
}


?>