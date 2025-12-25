<?php
session_start();
include 'BD_connexion.php';

$gender = null;
if (isset($_POST['gender'])) {
    if ($_POST['gender'] === 'male') {
        $gender = 0;
    } elseif ($_POST['gender'] === 'female') {
        $gender = 1;
    }
}

$sql = "UPDATE user 
        SET age = :age,
            name = :name,
            f_name = :f_name,
            phone = :phone,
            gender = :gender
        WHERE id = :id";

$stmt = $gd->prepare($sql);

$ok = $stmt->execute([
    ':age'    => $_POST['age'],
    ':name'   => $_POST['name'],
    ':f_name' => $_POST['f_name'],
    ':phone'  => $_POST['phone'],
    ':gender' => $gender,
    ':id'     => $_SESSION['user_id']
]);

if ($ok) {
    header("Location: main_page.php");
    exit;
} else {
    echo "<p>Erreur base de données.</p>";
}
?>
