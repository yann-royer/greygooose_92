<?php
// Chargement de la configuration globale (BASE_URL, constantes, etc.)
require_once __DIR__ . '/../../config.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>
    <div class="contenedor">
        <h1>Create your account</h1>
        <p class="nuevo"><a href="<?= BASE_URL ?>/pages/public/login.php">Log in</a></p> <!-- pk classe nuevo ?  et changer login.html par login.php -->
        <form method="POST" action="<?= BASE_URL ?>/partials/auth/register2.php"> <!-- remplacer register2.php plus tard par register_handler.php -->
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Write your email">
            <?php
            if (isset($_GET['error']) && $_GET['error'] == 1) {
                echo "<p style='color:red'>This email adress is already associated to an account</p>";
            }
            ?>
            <label for="pw">Password</label>
            <input type="password" name="pw" id="pw" placeholder="Write your password">

            <button type="submit">Register</button>
        </form>

    </div>
</body>

</html>