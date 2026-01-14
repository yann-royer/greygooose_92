<?php
require_once __DIR__ . "/../helpers/format.php";

// 1. Validation ID
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    http_response_code(404);
    echo "<p>Activité introuvable.</p>";
    return;
}

$activityId = (int) $_GET['id'];

// 2. Requête activité
$sql = "
    SELECT 
        a.id,
        a.sport,
        a.user_id,
        a.date_time,
        a.duration,
        a.distance,
        a.title,
        a.description,
        a.place,
        a.dplus,
        a.dminus,
        a.FCM,
        a.avg_hr,
        a.calories,
        a.created_at,
        a.updated_at,
        a.visibility,

        s.name AS sport_name,
        u.name,
        u.family_name,
        u.pp,

        -- Kudos
        (
            SELECT COUNT(*) 
            FROM kudos k 
            WHERE k.activity_id = a.id
        ) AS kudos_count,

        (
            SELECT COUNT(*) 
            FROM comments c 
            WHERE c.activity_id = a.id
        ) AS comments_count,

        EXISTS (
            SELECT 1 
            FROM kudos k 
            WHERE k.activity_id = a.id
            AND k.user_id = :current_user
        ) AS has_kudo

    FROM activity a
    JOIN sports s ON s.id = a.sport
    JOIN user u ON u.id = a.user_id
    WHERE a.id = :id
    LIMIT 1
";





$stmt = $gd->prepare($sql);
$stmt->execute([
    'id' => $activityId,
    'current_user' => $_SESSION['user_id']
]);
$activity = $stmt->fetch();
$isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] === (int)$activity['user_id'];

// 3. Activité inexistante
if (!$activity) {
    http_response_code(404);
    echo "<p>Cette activité n'existe pas.</p>";
    return;
}
?>
<div class="main-layout">
    <div class="main-right activity-view-container">
        <article
            class="activity-card activity-detail"
            data-activity-id="<?= $activity['id'] ?>">

            <header>
                <h1><?= htmlspecialchars($activity['title']) ?></h1>
                <p>
                    <?= htmlspecialchars($activity['sport_name']) ?> ·
                    <?= formatActivityDate($activity['date_time']) ?>
                </p>
                <p>
                    📍 <?= htmlspecialchars($activity['place']) ?>
                </p>
                <?php if ($isOwner) : ?>
                    <div class="activity-owner-actions">

                        <!-- BOUTON MODIFIER -->
                        <form
                            action="<?= BASE_URL ?>/partials/activities/activity_edit_redirect.php"
                            method="GET"
                            style="display:inline;">

                            <input type="hidden" name="id" value="<?= $activity['id'] ?>">
                            <button type="submit" class="btn-edit">
                                ✏️ Modifier
                            </button>
                        </form>

                        <!-- BOUTON SUPPRIMER -->
                        <form
                            action="<?= BASE_URL ?>/partials/activities/activity_delete.php"
                            method="POST"
                            onsubmit="return confirm('Supprimer cette activité ?');"
                            style="display:inline;">

                            <input type="hidden" name="id" value="<?= $activity['id'] ?>">
                            <button type="submit" class="btn-delete">
                                🗑 Supprimer
                            </button>
                        </form>

                    </div>

                <?php endif; ?>

            </header>

            <?php if (!empty($activity['description'])) : ?>
                <section class="activity-description">
                    <p><?= nl2br(htmlspecialchars($activity['description'])) ?></p>
                </section>
            <?php endif; ?>

            <section class="activity-stats">
                <ul>
                    <li><strong>Distance :</strong> <?= $activity['distance'] ?> km</li>
                    <li><strong>Durée :</strong> <?= formatDuration($activity['duration']) ?></li>
                    <li><strong>Allure :</strong> <?= formatAllure($activity['duration'], $activity['distance']) ?></li>
                    <li><strong>D+ :</strong> <?= $activity['dplus'] ?> m</li>
                    <li><strong>D- :</strong> <?= $activity['dminus'] ?> m</li>

                    <?php if ($activity['FCM']) : ?>
                        <li><strong>FC max :</strong> <?= $activity['FCM'] ?> bpm</li>
                    <?php endif; ?>
                    <li><strong>FC moyenne :</strong> <?= (int)$activity['avg_hr'] ?> bpm</li>
                    <li><strong>Calories :</strong> <?= (int)$activity['calories'] ?> kcal</li>
                </ul>
            </section>

            <div class="activity-actions">
                <button
                    class="kudo-btn <?= $activity['has_kudo'] ? 'active' : '' ?>"
                    data-activity-id="<?= $activity['id'] ?>"
                    type="button">
                    👍 <span class="kudo-count"><?= (int)$activity['kudos_count'] ?></span>
                </button>
                <button
                    class="comment-btn"
                    data-activity-id="<?= $activity['id'] ?>"
                    type="button">
                    💬 commentaires :
                    <span class="comment-count"><?= (int)$activity['comments_count'] ?></span>
                </button>
            </div>

            <div
                class="comments-container"
                id="comments-<?= $activity['id'] ?>"
                data-loaded="0"
                style="display:none;">
            </div>

            <div
                class="comment-form"
                data-activity-id="<?= $activity['id'] ?>"
                style="display:none;">

                <textarea
                    class="comment-input"
                    placeholder="Ajouter un commentaire..."></textarea>
                <button type="button" class="comment-submit">
                    Publier
                </button>
            </div>


            <?php if ($activity['avg_hr'] || $activity['calories']) : ?>
                <section class="activity-stats-advanced">
                    <h3>Données complémentaires</h3>
                    <ul>
                        <?php if ($activity['avg_hr']) : ?>
                            <li><strong>FC moyenne :</strong> <?= (int)$activity['avg_hr'] ?> bpm</li>
                        <?php endif; ?>

                        <?php if ($activity['calories']) : ?>
                            <li><strong>Calories :</strong> <?= (int)$activity['calories'] ?> kcal</li>
                        <?php endif; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <section class="activity-meta">
                <h3>Informations</h3>
                <ul>
                    <li>
                        <strong>Visibilité :</strong>
                        <?= htmlspecialchars($activity['visibility']) ?>
                    </li>

                    <li>
                        <strong>Créée le :</strong>
                        <?= formatActivityDate($activity['created_at']) ?>
                    </li>

                    <?php if (!empty($activity['updated_at'])) : ?>
                        <li>
                            <strong>Dernière modification :</strong>
                            <?= formatActivityDate($activity['updated_at']) ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </section>


        </article>
    </div>
</div>

<script>
    const BASE_URL = "<?= BASE_URL ?>";
    window.CURRENT_USER_ID = <?= (int)$_SESSION['user_id'] ?>;
</script>
<script src="<?= BASE_URL ?>/partials/home/activity_feed.js"></script>