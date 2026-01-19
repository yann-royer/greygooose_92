<?php
// =======================
// Données sociales utilisateur
// =======================

// Utilisateur connecté
$userId = $_SESSION['user_id'];

// Abonnements (je suis celui qui suit)
$stmtFollowing = $pdo->prepare("
    SELECT COUNT(*) 
    FROM relations 
    WHERE user_id = ? 
      AND status = 'accepted'
");
$stmtFollowing->execute([$userId]);
$followingCount = $stmtFollowing->fetchColumn();

// Abonnés (ils me suivent)
$stmtFollowers = $pdo->prepare("
    SELECT COUNT(*) 
    FROM relations 
    WHERE target_id = ? 
      AND status = 'accepted'
");
$stmtFollowers->execute([$userId]);
$followersCount = $stmtFollowers->fetchColumn();
?>

<section class="social-page">

    <h1>Social</h1>

    <!-- Barre de recherche -->
    <div class="social-search">
        <input
            type="text"
            id="social-search-input"
            placeholder="Rechercher un athlète..."
            data-search-url="<?= BASE_URL ?>/ajax/social/search_users.php"
        >
    </div>

    <!-- Statistiques sociales -->
    <div class="social-stats">
        <strong><?= $followingCount ?> abonnements</strong>
        •
        <strong><?= $followersCount ?> abonnés</strong>
    </div>

    <!-- Résultats de recherche -->
    <div class="social-results">
        <h2>Résultats</h2>
        <div id="social-results-list" style="border: 3px solid blue; min-height: 100px;">
    <p>ZONE RÉSULTATS (TEST)</p>
</div>

    </div>

    <!-- Suggestions -->
    <div class="social-suggestions">
        <h2>Suggestions pour vous</h2>
        <p>(à venir)</p>
    </div>

</section>
