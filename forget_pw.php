<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Forget Password</title>
	<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
	<h1>Forgot your password ?</h1>
	<br>
	<form method="POST" action="forget_pw2.php">

		<label for="email">Email</label>
		<input type="email" name="email" id="email" placeholder="Write the email of your lost account">

		<?php
			if(isset($_GET['error']) && $_GET['error']==1) {
				echo "<p style='color:red'>This email isn't associated to an account</p>";
			}

			if(isset($_GET['pw'])) {
				echo "<h2>Your password is : " . htmlspecialchars($_GET['pw']) . "</h2>";
			}
		?>

		<button type="submit">Find my Password</button>

	</form>
	<a href="login.php">Log in</a>

</body>
</html>