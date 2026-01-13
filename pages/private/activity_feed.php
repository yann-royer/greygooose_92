<!-- activity_feed.php = fct pour l'allure et la durée -->
<?php
require_once __DIR__ . "/../../partials/helpers/format.php";
?>

<!--inclue le header qui contient : la barre de nav et le debut de lastructure HTML-->
<?php require __DIR__ . "/../../partials/layout/header.php"; ?>

<!-- Contenu spécifique à la page principale privée -->

<h1>Flux d'activités</h1>
<p>Bienvenue sur votre flux d'activités privé !</p>

<!-- requete php pour recupérer les activiters -->

<?php
$userId = $_SESSION['user_id'];

$sql = "
    SELECT
        a.id,
        a.sport,
        a.title,
        a.distance,
        a.duration,
        a.date_time,
        u.name,
        u.family_name,
        u.pp,

        COUNT(k.id) AS kudos_count,

        MAX(
            CASE
                WHEN k.user_id = :current_user THEN 1
                ELSE 0
            END
        ) AS has_kudo

    FROM Activity a
    JOIN user u ON a.user_id = u.id
    LEFT JOIN kudos k ON k.activity_id = a.id

    WHERE a.user_id = :user_id
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





<!-- Affichage des activités -->
<main class="activity-feed">

    <h1>Mes activités</h1>

    <?php if (empty($activities)) : ?>
        <p>Aucune activité pour le moment.</p>
    <?php else : ?>
        <?php foreach ($activities as $activity) : ?>

            <article class="activity-card">

                <!-- HEADER ACTIVITY -->
                <div class="activity-user">
                    <img
                        src="<?= htmlspecialchars($activity['pp']) ?>"
                        alt="Photo de profil"
                        class="activity-avatar">

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

                <!-- CONTENT ACTIVITY -->
                <ul class="activity-data">
                    <li><strong>Sport :</strong> <?= htmlspecialchars($activity['sport']) ?></li>
                    <li><strong>Distance :</strong> <?= htmlspecialchars($activity['distance']) ?> km</li>
                    <li>
                        <strong>Temps :</strong>
                        <?= formatDuration($activity['duration']) ?>
                    </li>

                    <li>
                        <strong>Allure :</strong>
                        <?= formatAllure($activity['duration'], $activity['distance']) ?>
                    </li>
                </ul>

                <!-- ACTIONS -->
                <div class="activity-actions">
                    <!-- KUDO TOGGLE FORM -->
                    <button
                        class="kudo-btn <?= $activity['has_kudo'] ? 'active' : '' ?>"
                        data-activity-id="<?= $activity['id'] ?>"
                        type="button">

                        <span class="kudo-icon">👍</span>
                        <span class="kudo-count">
                            <?= (int)$activity['kudos_count'] ?>
                        </span>
                    </button>

                    <!-- COMMENT BUTTON -->
                    <button type="button">💬 Commentaire</button>
                </div>

            </article>

        <?php endforeach; ?>
    <?php endif; ?>

</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        console.log('JS LOADED');

        document.querySelectorAll('.kudo-btn').forEach(button => {
            button.addEventListener('click', async () => {

                console.log('CLICK');

                try {
                    const response = await fetch(
                        '/greygooose_92/partials/activities/kudo_toggle_ajax.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                activity_id: button.dataset.activityId
                            })
                        }
                    );

                    const data = await response.json();
                    console.log(data);

                    if (data.success) {
                        button.classList.toggle('active', data.has_kudo);
                        button.querySelector('.kudo-count').textContent = data.kudos_count;
                    }

                } catch (e) {
                    console.error('FETCH ERROR:', e);
                }
            });
        });

    });
</script>



<!--inclue le footer qui contient : la fin de la structure HTML-->
<?php require __DIR__ . "/../../partials/layout/footer.php"; ?>