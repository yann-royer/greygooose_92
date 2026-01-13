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

if (!isset($data['activity_id'])) {
    echo json_encode(['success' => false, 'error' => 'no_activity_id']);
    exit;
}

$activityId = (int)$data['activity_id'];

$sql = "
    SELECT
        c.id,
        c.user_id,
        c.content,
        c.created_at,
        u.name,
        u.family_name,
        u.pp
    FROM comments c
    JOIN user u ON c.user_id = u.id
    WHERE c.activity_id = :activity_id
    ORDER BY c.created_at ASC
";

$stmt = $gd->prepare($sql);
$stmt->execute([
    'activity_id' => $activityId
]);

$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);


echo json_encode([
    'success' => true,
    'comments' => $comments
]);
exit;
