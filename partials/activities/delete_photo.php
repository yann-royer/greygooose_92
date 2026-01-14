<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../db/BD_connexion.php';

if (!isset($_POST['photo_id']) || !ctype_digit($_POST['photo_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$photoId = (int) $_POST['photo_id'];

// Vérifier propriété
$stmt = $gd->prepare("
    SELECT p.link
    FROM photo p
    JOIN activity a ON p.activity_id = a.id
    WHERE p.id = :photo_id
    AND a.user_id = :user_id
    LIMIT 1
");
$stmt->execute([
    'photo_id' => $photoId,
    'user_id' => $_SESSION['user_id']
]);

$photo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$photo) {
    echo json_encode(['success' => false]);
    exit;
}

// Supprimer fichier
$filePath = $_SERVER['DOCUMENT_ROOT'] . $photo['link'];
if (file_exists($filePath)) {
    unlink($filePath);
}

// Supprimer DB
$stmt = $gd->prepare("DELETE FROM photo WHERE id = ?");
$stmt->execute([$photoId]);

echo json_encode(['success' => true]);
