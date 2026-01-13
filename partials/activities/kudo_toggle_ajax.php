<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../db/BD_connexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'no session']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['activity_id'])) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'no activity id']);
    exit;
}

$activityId = (int)$data['activity_id'];
$userId = $_SESSION['user_id'];

/* CHECK */
$stmt = $gd->prepare("
    SELECT id FROM kudos
    WHERE activity_id = :activity_id
    AND user_id = :user_id
");
$stmt->execute([
    'activity_id' => $activityId,
    'user_id' => $userId
]);

$hasKudo = $stmt->fetch();

/* TOGGLE */
if ($hasKudo) {
    $stmt = $gd->prepare("
        DELETE FROM kudos
        WHERE activity_id = :activity_id
        AND user_id = :user_id
    ");
    $hasKudoNow = false;
} else {
    $stmt = $gd->prepare("
        INSERT INTO kudos (activity_id, user_id)
        VALUES (:activity_id, :user_id)
    ");
    $hasKudoNow = true;
}

$stmt->execute([
    'activity_id' => $activityId,
    'user_id' => $userId
]);

/* COUNT */
$stmt = $gd->prepare("SELECT COUNT(*) FROM kudos WHERE activity_id = :activity_id");
$stmt->execute(['activity_id' => $activityId]);

$kudosCount = (int)$stmt->fetchColumn();

ob_clean();
echo json_encode([
    'success' => true,
    'has_kudo' => $hasKudoNow,
    'kudos_count' => $kudosCount
]);
exit;
