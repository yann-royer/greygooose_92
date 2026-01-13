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

if (
    empty($data['comment_id']) ||
    empty($data['content'])
) {
    echo json_encode(['success' => false]);
    exit;
}

$commentId = (int)$data['comment_id'];
$content = trim($data['content']);
$userId = $_SESSION['user_id'];

$stmt = $gd->prepare("
    UPDATE comments
    SET content = :content
    WHERE id = :id AND user_id = :user_id
");
$stmt->execute([
    'content' => $content,
    'id' => $commentId,
    'user_id' => $userId
]);

echo json_encode([
    'success' => true,
    'content' => $content
]);
exit;
