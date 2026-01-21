<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../db/BD_connexion.php';

$viewerId = (int) $_SESSION['user_id'];
$targetId = isset($_POST['target_id']) ? (int) $_POST['target_id'] : 0;
$action = $_POST['action'] ?? '';

$redirect = $_POST['redirect'] ?? (BASE_URL . '/pages/private/profil.php?id=' . $targetId);
if (strpos($redirect, BASE_URL) !== 0) {
    $redirect = BASE_URL . '/pages/private/profil.php?id=' . $targetId;
}

if ($targetId <= 0 || $targetId === $viewerId) {
    header('Location: ' . $redirect);
    exit;
}

// Fetch relations
$stmtOut = $pdo->prepare("
    SELECT id, status
    FROM relations
    WHERE user_id = :viewer AND target_id = :target
    LIMIT 1
");
$stmtOut->execute([
    'viewer' => $viewerId,
    'target' => $targetId
]);
$relationOut = $stmtOut->fetch(PDO::FETCH_ASSOC);

$stmtIn = $pdo->prepare("
    SELECT id, status
    FROM relations
    WHERE user_id = :target AND target_id = :viewer
    LIMIT 1
");
$stmtIn->execute([
    'viewer' => $viewerId,
    'target' => $targetId
]);
$relationIn = $stmtIn->fetch(PDO::FETCH_ASSOC);

switch ($action) {
    case 'request':
        if ($relationOut || ($relationIn && $relationIn['status'] === 'accepted')) {
            break;
        }

        // If the other user already sent a request, accept it
        if ($relationIn && $relationIn['status'] === 'pending') {
            $acceptStmt = $pdo->prepare("UPDATE relations SET status = 'accepted' WHERE id = :id");
            $acceptStmt->execute(['id' => $relationIn['id']]);
            break;
        }

        $insert = $pdo->prepare("
            INSERT INTO relations (user_id, target_id, status)
            VALUES (:viewer, :target, 'pending')
        ");
        $insert->execute([
            'viewer' => $viewerId,
            'target' => $targetId
        ]);
        break;

    case 'unsend':
        if ($relationOut && $relationOut['status'] === 'pending') {
            $delete = $pdo->prepare("DELETE FROM relations WHERE id = :id");
            $delete->execute(['id' => $relationOut['id']]);
        }
        break;

    case 'remove':
        $delete = $pdo->prepare("
            DELETE FROM relations
            WHERE status = 'accepted'
              AND (
                  (user_id = :viewer AND target_id = :target)
                  OR (user_id = :target AND target_id = :viewer)
              )
        ");
        $delete->execute([
            'viewer' => $viewerId,
            'target' => $targetId
        ]);
        break;

    case 'accept':
        if ($relationIn && $relationIn['status'] === 'pending') {
            $accept = $pdo->prepare("UPDATE relations SET status = 'accepted' WHERE id = :id");
            $accept->execute(['id' => $relationIn['id']]);
        }
        break;

    case 'decline':
        if ($relationIn && $relationIn['status'] === 'pending') {
            $decline = $pdo->prepare("DELETE FROM relations WHERE id = :id");
            $decline->execute(['id' => $relationIn['id']]);
        }
        break;

    default:
        // No action
        break;
}

header('Location: ' . $redirect);
exit;
