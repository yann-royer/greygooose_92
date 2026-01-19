<?php
// Sécurité : utilisateur connecté
$userId = $_SESSION['user_id'];

// Nombre d'abonnements (je suis celui qui suit)
$stmtFollowing = $pdo->prepare("
    SELECT COUNT(*) 
    FROM relations 
    WHERE user_id = ? 
      AND status = 'accepted'
");
$stmtFollowing->execute([$userId]);
$followingCount = $stmtFollowing->fetchColumn();

// Nombre d'abonnés (je suis celui qui est suivi)
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
            placeholder="Rechercher un athlète..."
            disabled
        >
        <p style="font-size: 0.85em; color: #777;">
            La recherche sera bientôt disponible
        </p>
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
        <p>Aucun résultat pour le moment</p>
    </div>

    <!-- Suggestions -->
    <div class="social-suggestions">
        <h2>Suggestions pour vous</h2>
        <p>Aucune suggestion pour le moment</p>
    </div>

</section>


