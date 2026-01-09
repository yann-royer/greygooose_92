<?php
// Chargement de la configuration globale (BASE_URL, constantes, etc.)
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../auth/auth_check.php';
// INCLUDE PHP (chemins système)
include __DIR__ . '/../db/BD_connexion.php';


$gender = null;
if (isset($_POST['gender'])) {
    if ($_POST['gender'] === 'male') {
        $gender = 0;
    } elseif ($_POST['gender'] === 'female') {
        $gender = 1;
    }
}


//---Gestion de la photo---

$ppPathToStore = null; // ce qu'on va stocker en DB (ex: uploads/profile_pics/abc.jpg)


$uploadDir = __DIR__ . '/../../uploads/pp/'; // chemin physique
$publicDir = BASE_URL . '/uploads/pp/';      // chemin public (URL)  chemin public stocké en DB



// Si un fichier a été envoyé
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES['profile_pic']['error'] !== UPLOAD_ERR_OK) {
        die("Erreur upload : " . $_FILES['profile_pic']['error']);
    }

    // (Optionnel mais recommandé) limite taille (ex: 5MB)
    //if ($_FILES['profile_pic']['size'] > 5 * 1024 * 1024) {
    //    die("Fichier trop volumineux (max 5MB).");
    //}

    // Vérifie que c’est une image (plus fiable que l’extension)
    $imgInfo = getimagesize($_FILES['profile_pic']['tmp_name']);
    if ($imgInfo === false) {
        die("Le fichier envoyé n'est pas une image valide.");
    }

    // Autorise seulement certains types MIME
    $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
    $mime = $imgInfo['mime'];
    if (!in_array($mime, $allowedMime, true)) {
        die("Format non autorisé. Utilise JPG, PNG ou WEBP.");
    }

    // Détermine une extension cohérente
    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        default      => 'bin'
    };

    // Nom de fichier unique
    $newName = 'user_' . $_SESSION['user_id']  . '.' . $ext;
    $destination = $uploadDir . $newName;

    // Déplace le fichier
    if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $destination)) {
        die("Impossible d'enregistrer l'image sur le serveur.");
    }

    // Chemin public à stocker en DB
    $ppPathToStore = $publicDir . $newName;
}


//---Fin de gestion photo---







$sql = "UPDATE user 
        SET age = :age,
            name = :name,
            family_name = :family_name,
            phone = :phone,
            gender = :gender,
            pp = :pp
        WHERE id = :id";

$stmt = $gd->prepare($sql);

$ok = $stmt->execute([
    ':age'    => $_POST['age'],
    ':name'   => $_POST['name'],
    ':family_name' => $_POST['family_name'],
    ':phone'  => $_POST['phone'],
    ':gender' => $gender,
    ':pp' => $ppPathToStore,
    ':id'     => $_SESSION['user_id']
]);

if ($ok) {

    $_SESSION['user_name'] = $_POST['name'];
    $_SESSION['user_family_name'] = $_POST['family_name'];
    $_SESSION['user_pp'] = $ppPathToStore;
    $_SESSION['user_gender'] = $_POST['gender'];

    header("Location:" . BASE_URL . "/pages/private/main_page.php");
    exit;
} else {
    echo "<p>Erreur base de données.</p>";
}
