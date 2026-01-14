<?php
$currentPage = 'home';
require __DIR__ . "/../../partials/layout/header.php";
?>

<div class="main-layout">

	<!-- COLONNE GAUCHE : PROFIL -->
	<aside class="main-left">
		<?php require __DIR__ . "/../../partials/home/profile_sidebar.php"; ?>
	</aside>

	<!-- COLONNE DROITE : FEED -->
	<section class="main-right">
		<?php require __DIR__ . "/../../partials/home/activity_feed.php"; ?>
	</section>

</div>

<?php require __DIR__ . "/../../partials/layout/footer.php"; ?>