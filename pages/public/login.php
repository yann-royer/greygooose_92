<?php
// Chargement de la configuration globale (BASE_URL, constantes, etc.)
require_once __DIR__ . '/../../config.php';
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Greygoose</title>
	<link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>
	<div class="container">
		<table>

		</table>
		<h1>Start sharing</h1>
		<p>Log in to access to all of our fonctionnalities</p>
		<form method="POST" action="<?= BASE_URL ?>/partials/auth/login2.php"> <!-- remplacer login2.php par login_handler.php plus tard -->
			<label for="email">Email</label>
			<input type="email" name="email" id="email" placeholder="Write your email">


			<label for="pw">Password</label>
			<input type="password" name="pw" id="pw" placeholder="Write your password">

			<?php if (isset($_GET['error'])): ?>
				<p style="color:red">
					<?= $_GET['error'] == 1
						? "No account has been found with this email. Please register to use our website."
						: "The email or the password is wrong." ?>
				</p>
			<?php endif; ?>

			<button type="submit">Log in</button>
		</form>
		<a href="<?= BASE_URL ?>/pages/public/forget_pw.php">I forgot my password</a>
		<a href="<?= BASE_URL ?>/pages/public/register.php">Create my account</a>
</body>

</html>