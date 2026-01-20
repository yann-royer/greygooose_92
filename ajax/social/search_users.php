<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config_ajax.php';
require_once __DIR__ . '/../../partials/db/BD_connexion.php';

$userId = $_SESSION['user_id'] ?? 0;
if (!$userId) {
    echo json_encode([]);
    exit;
}

if (!isset($_GET['q']) || trim($_GET['q']) === '') {
    $stmt = $pdo->prepare("
        SELECT u.id, u.name, u.family_name, u.pp, r.status
        FROM user u
        LEFT JOIN relations r ON r.user_id = ? AND r.target_id = u.id
        WHERE u.id != ? AND (r.status IS NULL OR r.status != 'accepted')
        ORDER BY u.id DESC LIMIT 8
    ");
    $stmt->execute([$userId, $userId]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

$query = trim($_GET['q']) . '%';
$stmt = $pdo->prepare("
    SELECT u.id, u.name, u.family_name, u.pp, r.status
    FROM user u
    LEFT JOIN relations r ON r.user_id = ? AND r.target_id = u.id
    WHERE (u.name LIKE ? OR u.family_name LIKE ?) AND u.id != ?
    ORDER BY u.name LIMIT 10
");
$stmt->execute([$userId, $query, $query, $userId]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
exit;
