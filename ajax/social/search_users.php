<?php
// Silence total côté PHP
error_reporting(0);
ini_set('display_errors', 0);

// Réponse JSON propre
header('Content-Type: application/json; charset=utf-8');

// Config AJAX minimale
require_once __DIR__ . '/../config_ajax.php';
require_once __DIR__ . '/../../partials/db/BD_connexion.php';

// Sécurité
if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$query = trim($_GET['q']);
if ($query === '') {
    echo json_encode([]);
    exit;
}

// Utilisateur connecté
$userId = $_SESSION['user']['id'] ?? 0;

// Requête SQL
$stmt = $pdo->prepare("
    SELECT id, name, family_name, pp
    FROM user
    WHERE (name LIKE ? OR family_name LIKE ?)
    AND id != ?
    ORDER BY name ASC
    LIMIT 10
");

$stmt->execute([
    $query . '%',
    $query . '%',
    $userId
]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
exit;

