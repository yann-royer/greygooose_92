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
$requesterId = intval($data['requester_id'] ?? 0);
$action = $data['action'] ?? '';

if ($requesterId == 0 || $requesterId == $userId) {
    echo json_encode(['success' => false]);
    exit;
}

if ($action === 'accept') {
    $stmt = $pdo->prepare("SELECT id FROM relations WHERE user_id = ? AND target_id = ? AND status = 'pending'");
    $stmt->execute([$requesterId, $userId]);
    $relation = $stmt->fetch();
    
    if ($relation) {
        $pdo->prepare("UPDATE relations SET status = 'accepted' WHERE id = ?")->execute([$relation['id']]);
        echo json_encode(['success' => true, 'action' => 'accepted']);
    } else {
        echo json_encode(['success' => false]);
    }
} else if ($action === 'decline') {
    $stmt = $pdo->prepare("DELETE FROM relations WHERE user_id = ? AND target_id = ? AND status = 'pending'");
    $stmt->execute([$requesterId, $userId]);
    echo json_encode(['success' => true, 'action' => 'declined']);
} else if ($action === 'follow_back') {
    $checkStmt = $pdo->prepare("SELECT id FROM relations WHERE user_id = ? AND target_id = ?");
    $checkStmt->execute([$userId, $requesterId]);
    
    if (!$checkStmt->fetch()) {
        $insertStmt = $pdo->prepare("INSERT INTO relations (user_id, target_id, status) VALUES (?, ?, 'accepted')");
        if ($insertStmt->execute([$userId, $requesterId])) {
            echo json_encode(['success' => true, 'action' => 'followed_back']);
        } else {
            echo json_encode(['success' => false, 'error' => 'insert_failed']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'already_following']);
    }
} else {
    echo json_encode(['success' => false]);
}
exit;
