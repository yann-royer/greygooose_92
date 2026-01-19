<?php
$userId = $_SESSION['user_id'];

$stmtFollowing = $pdo->prepare("SELECT COUNT(*) FROM relations WHERE user_id = ? AND status = 'accepted'");
$stmtFollowing->execute([$userId]);
$followingCount = $stmtFollowing->fetchColumn();

$stmtFollowers = $pdo->prepare("SELECT COUNT(*) FROM relations WHERE target_id = ? AND status = 'accepted'");
$stmtFollowers->execute([$userId]);
$followersCount = $stmtFollowers->fetchColumn();
?>

<section class="social-page">
    <h1>Social Network</h1>

    <div class="social-stats">
        <strong><?= $followingCount ?> following</strong> • <strong><?= $followersCount ?> followers</strong>
    </div>

    <div class="social-search">
        <input type="text" id="social-search-input" placeholder="Search an athlete..." autocomplete="off">
    </div>

    <div class="social-results">
        <h2 id="results-title">Suggestions for you</h2>
        <div id="social-results-list" class="users-list"></div>
    </div>
</section>

<style>
.social-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.social-page h1 {
    margin-bottom: 20px;
    font-size: 2em;
}

.social-stats {
    padding: 15px 0;
    margin-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
    font-size: 1.1em;
}

.social-search {
    margin-bottom: 30px;
}

.social-search input {
    width: 100%;
    padding: 12px 15px;
    font-size: 16px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
}

.social-search input:focus {
    outline: none;
    border-color: #007bff;
}

.social-results {
    margin-bottom: 30px;
}

.social-results h2 {
    margin-bottom: 15px;
    font-size: 1.3em;
}

.users-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.social-user-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}

.social-user-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e0e0e0;
}

.social-user-card .btn {
    padding: 8px 20px;
    border: none;
    border-radius: 6px;
    font-size: 0.9em;
    cursor: pointer;
    font-weight: 500;
}

.social-user-card .btn-follow {
    background: #007bff;
    color: white;
}

.social-user-card .btn-follow:hover:not(:disabled) {
    background: #0056b3;
}

.social-user-card .btn-following {
    background: #6c757d;
    color: white;
}

.social-user-card .btn-following:hover:not(:disabled) {
    background: #5a6268;
}

.social-user-card .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.no-results {
    padding: 20px;
    text-align: center;
    color: #666;
    font-style: italic;
}
</style>
