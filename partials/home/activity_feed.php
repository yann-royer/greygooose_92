<?php
require_once __DIR__ . "/../helpers/format.php";

//--------------- requete php pour recupérer les activitées --------------->
$userId = $_SESSION['user_id'];

$sql = "
    SELECT
        a.id,
        s.name AS sport_name,
        a.title,
        a.distance,
        a.duration,
        a.date_time,

        u.name,
        u.family_name,
        u.pp,

        COUNT(DISTINCT k.id) AS kudos_count,
        COUNT(DISTINCT c.id) AS comments_count,

        SUM(k.user_id = :current_user) AS has_kudo

    FROM Activity a
    JOIN user u ON a.user_id = u.id
    JOIN sports s ON s.id = a.sport

    LEFT JOIN relations r
        ON r.target_id = a.user_id
        AND r.user_id = :user_id
        AND r.status = 'accepted'

    LEFT JOIN kudos k ON k.activity_id = a.id
    LEFT JOIN comments c ON c.activity_id = a.id

    WHERE
        a.user_id = :user_id
        OR r.id IS NOT NULL

    GROUP BY a.id
    ORDER BY a.date_time DESC
    LIMIT 20
";

$stmt = $gd->prepare($sql);
$stmt->execute([
    'user_id' => $userId,
    'current_user' => $userId
]);

$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- ---------------Affichage des activités--------------- -->
<section class="activity-feed">

    <h2>Activités récentes</h2>

    <?php if (empty($activities)) : ?>
        <p>Aucune activité pour le moment.</p>
    <?php else : ?>
        <?php foreach ($activities as $activity) : ?>

            <article
                class="activity-card"
                data-activity-id="<?= $activity['id'] ?>">


                <div class="activity-user activity-clickable">

                    <img src="<?= htmlspecialchars($activity['pp']) ?>" class="activity-avatar">

                    <div>
                        <strong>
                            <?= htmlspecialchars($activity['name']) ?>
                            <?= htmlspecialchars($activity['family_name']) ?>
                        </strong>
                        <p class="activity-date">
                            <?= formatActivityDate($activity['date_time']) ?>
                        </p>
                    </div>
                </div>

                <ul class="activity-data activity-clickable">

                    <li><strong>Sport :</strong> <?= htmlspecialchars($activity['sport_name']) ?></li>
                    <li><strong>Distance :</strong> <?= htmlspecialchars($activity['distance']) ?> km</li>
                    <li><strong>Temps :</strong> <?= formatDuration($activity['duration']) ?></li>
                    <li><strong>Allure :</strong>
                        <?= formatAllure($activity['duration'], $activity['distance']) ?>
                    </li>
                </ul>

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

                <div class="comment-form" data-activity-id="<?= $activity['id'] ?>" style="display:none;">
                    <textarea class="comment-input" placeholder="Ajouter un commentaire..."></textarea>
                    <button type="button" class="comment-submit">Publier</button>
                </div>

            </article>

        <?php endforeach; ?>
    <?php endif; ?>

</section>

<script>
    window.CURRENT_USER_ID = <?= (int)$_SESSION['user_id'] ?>;
</script>

<script src="<?= BASE_URL ?>/partials/home/activity_feed.js"></script>