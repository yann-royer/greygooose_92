<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../db/BD_connexion.php';

if (!isset($_POST['id']) || !ctype_digit($_POST['id'])) {
    header('Location: /');
    exit;
}

$activityId = (int) $_POST['id'];
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: /login.php');
    exit;
}

/* Vérifier que l’utilisateur est bien propriétaire */
$stmt = $gd->prepare("
    SELECT user_id
    FROM activity
    WHERE id = :id
    LIMIT 1
");
$stmt->execute(['id' => $activityId]);
$activity = $stmt->fetch();

if (!$activity || (int)$activity['user_id'] !== $userId) {
    http_response_code(403);
    exit('Action interdite');
}

/* Suppression */
$stmt = $gd->prepare("
    DELETE FROM activity
    WHERE id = :id
");
$stmt->execute(['id' => $activityId]);

/* Redirection */
header('Location: ' . BASE_URL . '/pages/private/main_page.php');
exit;
