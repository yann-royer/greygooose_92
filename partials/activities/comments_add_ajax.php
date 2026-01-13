<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../db/BD_connexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'no_session']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (
    !$data ||
    empty($data['activity_id']) ||
    empty(trim($data['content']))
) {
    echo json_encode(['success' => false, 'error' => 'invalid_data']);
    exit;
}

$activityId = (int)$data['activity_id'];
$userId     = $_SESSION['user_id'];
$content    = trim($data['content']);

/* INSERT COMMENT */
$stmt = $gd->prepare("
    INSERT INTO comments (activity_id, user_id, content, created_at)
    VALUES (:activity_id, :user_id, :content, NOW())
");
$stmt->execute([
    'activity_id' => $activityId,
    'user_id'     => $userId,
    'content'     => $content
]);

$commentId = $gd->lastInsertId();

/* FETCH COMMENT INFO */
$stmt = $gd->prepare("
    SELECT
        c.id,
        c.content,
        c.created_at,
        u.name,
        u.family_name,
        u.pp
    FROM comments c
    JOIN user u ON c.user_id = u.id
    WHERE c.id = :id
");
$stmt->execute(['id' => $commentId]);

$comment = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'comments' => $comment
]);
exit;
