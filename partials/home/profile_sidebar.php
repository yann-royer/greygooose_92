<?php
// gestion de la photo de profil
$profilePic = !empty($_SESSION['user_pp'])
    ? $_SESSION['user_pp']
    : BASE_URL . '/uploads/pp/default.webp';
?>

<h1>Bienvenue chez greygooose 92</h1>

<h2>
    Hello <?= htmlspecialchars($_SESSION['user_name']) ?>
    <?= htmlspecialchars($_SESSION['user_family_name']) ?> !
</h2>

<img
    src="<?= htmlspecialchars($profilePic) ?>"
    alt="Photo de profil"
    class="profile-pic"
    height="300">

<br>

<a href="<?= BASE_URL ?>/partials/session/close_session.php">
    Log out
</a>