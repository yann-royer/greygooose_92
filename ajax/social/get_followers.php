<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config_ajax.php';
require_once __DIR__ . '/../../partials/db/BD_connexion.php';

$userId = $_SESSION['user_id'] ?? 0;
if (!$userId) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT u.id, u.name, u.family_name, u.pp
    FROM user u
    JOIN relations r ON r.user_id = u.id AND r.target_id = ?
    WHERE r.status = 'accepted'
    ORDER BY u.name
");
$stmt->execute([$userId]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
exit;
