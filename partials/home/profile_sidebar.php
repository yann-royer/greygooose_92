<?php
// gestion de la photo de profil
$profilePic = !empty($_SESSION['user_pp'])
    ? $_SESSION['user_pp']
    : BASE_URL . '/uploads/pp/default.webp';
?>

<h1>Bienvenue chez greygooose 92</h1>

<h2>
    Hello <?= htmlspecialchars($_SESSION['user_name']) ?>
    <?= htmlspecialchars($_SESSION['user_family_name']) ?> !
</h2>

<img
    src="<?= htmlspecialchars($profilePic) ?>"
    alt="Photo de profil"
    class="profile-pic"
    height="300">

<br>

<a href="<?= BASE_URL ?>/partials/session/close_session.php">
    Log out
</a>

<?php
// Notifications de demandes d'amis en attente
$pendingStmt = $gd->prepare("
    SELECT r.id, u.name, u.family_name, u.id AS user_id
    FROM relations r
    JOIN user u ON u.id = r.user_id
    WHERE r.target_id = :me
      AND r.status = 'pending'
    ORDER BY r.id DESC
");
$pendingStmt->execute(['me' => $_SESSION['user_id']]);
$pendingRequests = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (!empty($pendingRequests)): ?>
    <div class="notifications">
        <h3>Notifications</h3>
        <ul class="notifications-list">
            <?php foreach ($pendingRequests as $req): ?>
                <li class="notification-item">
                    <span class="notif-name">
                        <?= htmlspecialchars($req['name']) ?> <?= htmlspecialchars($req['family_name']) ?>
                    </span>
                    <div class="notif-actions">
                        <form
                            method="POST"
                            action="<?= BASE_URL ?>/partials/social/relation_action.php"
                            style="display:inline;">
                            <input type="hidden" name="target_id" value="<?= $req['user_id'] ?>">
                            <input type="hidden" name="redirect" value="<?= BASE_URL ?>/pages/private/main_page.php">
                            <button type="submit" name="action" value="accept" class="notif-btn accept">✔</button>
                        </form>
                        <form
                            method="POST"
                            action="<?= BASE_URL ?>/partials/social/relation_action.php"
                            style="display:inline;">
                            <input type="hidden" name="target_id" value="<?= $req['user_id'] ?>">
                            <input type="hidden" name="redirect" value="<?= BASE_URL ?>/pages/private/main_page.php">
                            <button type="submit" name="action" value="decline" class="notif-btn decline">✖</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>