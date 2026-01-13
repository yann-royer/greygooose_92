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
        id,
        sport,
        title,
        distance,
        duration,
        date_time
    FROM Activity
    WHERE user_id = :user_id
    ORDER BY date_time DESC
    LIMIT 20
";

$stmt = $gd->prepare($sql);
$stmt->execute([
    'user_id' => $userId
]);

$activities = $stmt->fetchAll();
?>

<!-- Affichage des activités -->
<main class="activity-feed">

    <h1>Mes activités récentes</h1>

    <?php if (empty($activities)) : ?>
        <p>Tu n as encore enregistré aucune activité.</p>
    <?php else : ?>
        <?php foreach ($activities as $activity) : ?>
            <article class="activity-card">

                <h2><?= htmlspecialchars($activity['title']) ?></h2>

                <p>
                    <?= htmlspecialchars($activity['sport']) ?>
                    · <?= htmlspecialchars($activity['distance']) ?> km
                    · <?= htmlspecialchars($activity['duration']) ?>
                </p>

                <p>
                    <?= htmlspecialchars($activity['date_time']) ?>
                </p>

                <a href="activity_view.php?id=<?= $activity['id'] ?>">
                    Voir l activité
                </a>

            </article>
        <?php endforeach; ?>
    <?php endif; ?>

</main>


<!--inclue le footer qui contient : la fin de la structure HTML-->
<?php require __DIR__ . "/../../partials/layout/footer.php"; ?>