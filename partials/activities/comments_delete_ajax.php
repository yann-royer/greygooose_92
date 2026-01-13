<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../db/BD_connexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['comment_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$commentId = (int)$data['comment_id'];
$userId = $_SESSION['user_id'];

/* 1️⃣ récupérer l’activity_id */
$stmt = $gd->prepare("
    SELECT activity_id
    FROM comments
    WHERE id = :id AND user_id = :user_id
");
$stmt->execute([
    'id' => $commentId,
    'user_id' => $userId
]);

$activityId = $stmt->fetchColumn();

if (!$activityId) {
    echo json_encode(['success' => false]);
    exit;
}

/* 2️⃣ supprimer le commentaire */
$stmt = $gd->prepare("
    DELETE FROM comments
    WHERE id = :id AND user_id = :user_id
");
$stmt->execute([
    'id' => $commentId,
    'user_id' => $userId
]);

/* 3️⃣ recompter les commentaires */
$stmt = $gd->prepare("
    SELECT COUNT(*) 
    FROM comments 
    WHERE activity_id = :activity_id
");
$stmt->execute([
    'activity_id' => $activityId
]);

$commentsCount = (int)$stmt->fetchColumn();

/* 4️⃣ réponse */
echo json_encode([
    'success' => true,
    'activity_id' => $activityId,
    'comments_count' => $commentsCount
]);



exit;
