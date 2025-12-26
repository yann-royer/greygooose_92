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
		<form method="POST" action="login2.php">
			<label for="email">Email</label>
			<input type="email" name="email" id="email" placeholder="Write your email">

			<?php
				if(isset($_GET['error']) && $_GET['error']==1) {
					echo"<p style='color:red'>No account has been found with this email. Please register to use our website.</p>";
				}
				if(isset($_GET['error']) && $_GET['error']==2) {
					echo"<p style='color:red'>The email or the password is wrong.</p>";
				}
			?>

			<label for="pw">Password</label>
			<input type="password" name="pw" id="pw" placeholder="Write your password">

			<button type="submit">Log in</button>
		</form>
		<a	href="forget_pw.php">I forgot my password</a>
		<a href="register.php">Create my account</a>
</body>
</html>