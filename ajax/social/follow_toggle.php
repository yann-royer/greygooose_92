<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config_ajax.php';
require_once __DIR__ . '/../../partials/db/BD_connexion.php';

$userId = $_SESSION['user_id'] ?? 0;
if (!$userId) {
    echo json_encode(['success' => false]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$targetId = intval($data['target_id'] ?? 0);

if ($targetId == 0 || $targetId == $userId) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM relations WHERE user_id = ? AND target_id = ?");
$stmt->execute([$userId, $targetId]);

if ($stmt->fetch()) {
    $pdo->prepare("DELETE FROM relations WHERE user_id = ? AND target_id = ?")->execute([$userId, $targetId]);
    echo json_encode(['success' => true, 'action' => 'unfollowed']);
} else {
    $pdo->prepare("INSERT INTO relations (user_id, target_id, status) VALUES (?, ?, 'accepted')")->execute([$userId, $targetId]);
    echo json_encode(['success' => true, 'action' => 'followed']);
}
exit;
