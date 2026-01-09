<?php
include 'auth_check.php';

//gestion de la pp
$profilePic = !empty($_SESSION['user_pp'])
	? $_SESSION['user_pp']
	: 'uploads/pp/default.webp'; // image par défaut

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
	<h1>Binevenue chez greygooose 92</h1>

	<?php echo "<h2>Hello " . $_SESSION['user_name'] . " " . $_SESSION['user_family_name'] . " !</h2>"; ?>

	<img
		src="<?= htmlspecialchars($profilePic) ?>"
		alt="Photo de profil"
		class="profile-pic"
		height="300px">

	<br>
	<a href="close_session.php">Log out</a>
</body>

</html>