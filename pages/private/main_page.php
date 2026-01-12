<!--inclue le header qui contient : la barre de nav et le debut de lastructure HTML-->
<?php require __DIR__ . "/../../partials/layout/header.php"; ?>


<!-- Contenu spécifique à la page principale privée -->

<?php
//gestion de la pp
$profilePic = !empty($_SESSION['user_pp'])
	? $_SESSION['user_pp']
	: BASE_URL . '/uploads/pp/default.webp'; // image par défaut
?>

<h1>Binevenue chez greygooose 92</h1>

<h2>
	Hello <?= htmlspecialchars($_SESSION['user_name']) ?>
	<?= htmlspecialchars($_SESSION['user_family_name']) ?> !
</h2>

<img
	src="<?= htmlspecialchars($profilePic) ?>"
	alt="Photo de profil"
	class="profile-pic"
	height="300px">

<br>
<a href="<?= BASE_URL ?>/partials/session/close_session.php">Log out</a>


<!--inclue le footer qui contient : la fin de la structure HTML-->
<?php require __DIR__ . "/../../partials/layout/footer.php"; ?>