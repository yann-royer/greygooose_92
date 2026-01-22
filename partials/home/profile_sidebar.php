<?php
if (!isset($pdo)) {
    require_once __DIR__ . '/../db/BD_connexion.php';
}

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
if (!isset($pdo)) {
    require_once __DIR__ . '/../db/BD_connexion.php';
}

try {
    $newFollowersStmt = $pdo->prepare("
        SELECT u.id AS user_id, u.name, u.family_name, u.pp
        FROM relations r
        JOIN user u ON u.id = r.user_id
        WHERE r.target_id = ? 
          AND r.status = 'accepted'
          AND NOT EXISTS (
              SELECT 1 FROM relations r2 
              WHERE r2.user_id = ? AND r2.target_id = r.user_id
          )
        ORDER BY r.id DESC
        LIMIT 5
    ");
    $newFollowersStmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
    $newFollowers = $newFollowersStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $newFollowers = [];
    error_log("Error fetching new followers: " . $e->getMessage());
}
?>

<?php if (!empty($newFollowers)): ?>
    <div class="follow-requests">
        <h3>New Followers</h3>
        <div id="follow-requests-list">
            <?php foreach ($newFollowers as $follower): 
                $pp = $follower['pp'] ?: BASE_URL . '/uploads/pp/default.webp';
            ?>
                <div class="follow-request-item" data-user-id="<?= $follower['user_id'] ?>">
                    <img src="<?= htmlspecialchars($pp) ?>" class="request-avatar">
                    <div class="request-info">
                        <strong><?= htmlspecialchars($follower['name']) ?> <?= htmlspecialchars($follower['family_name']) ?></strong>
                        <span class="request-text">started following you</span>
                    </div>
                    <button class="btn-follow-back" onclick="followBack(<?= $follower['user_id'] ?>, this)">Follow Back</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<script>
function followBack(userId, btn) {
    btn.disabled = true;
    const originalText = btn.textContent;
    
    fetch('/greygooose_92/ajax/social/follow_toggle.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({target_id: userId})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.action === 'followed') {
            btn.textContent = 'Following';
            btn.className = 'btn-following';
            btn.disabled = false;
        } else {
            btn.disabled = false;
            btn.textContent = originalText;
            alert('Error following user');
        }
    })
    .catch(err => {
        console.error('Follow back error:', err);
        btn.disabled = false;
        btn.textContent = originalText;
    });
}
</script>

<style>
.follow-requests {
    margin-top: 20px;
    padding: 15px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}

.follow-requests h3 {
    margin: 0 0 15px 0;
    font-size: 1.1em;
    color: #333;
}

.follow-request-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
    transition: opacity 0.3s;
}

.follow-request-item:last-child {
    border-bottom: none;
}

.request-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #e0e0e0;
}

.request-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.request-info strong {
    font-size: 0.9em;
    color: #333;
}

.request-text {
    font-size: 0.85em;
    color: #666;
}

.request-actions {
    display: flex;
    gap: 8px;
}

.btn-accept, .btn-decline, .btn-follow-back {
    padding: 6px 16px;
    border: none;
    border-radius: 4px;
    font-size: 0.85em;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-accept {
    background: #ff8800;
    color: white;
}

.btn-accept:hover:not(:disabled) {
    background: #cc6d00;
}

.btn-decline {
    background: #f0f0f0;
    color: #333;
}

.btn-decline:hover:not(:disabled) {
    background: #e0e0e0;
}

.btn-follow-back {
    background: #ff8800;
    color: white;
}

.btn-follow-back:hover:not(:disabled) {
    background: #cc6d00;
}

.btn-following {
    background: #6c757d;
    color: white;
    cursor: default;
}

button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>