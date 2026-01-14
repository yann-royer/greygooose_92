<?php
$isEdit = false;
$activity = null;

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $isEdit = true;
    $activityId = (int) $_GET['id'];

    $stmt = $gd->prepare("
        SELECT *
        FROM activity
        WHERE id = :id
        AND user_id = :user_id
        LIMIT 1
    ");
    $stmt->execute([
        'id' => $activityId,
        'user_id' => $_SESSION['user_id']
    ]);

    $activity = $stmt->fetch(PDO::FETCH_ASSOC);

    $activityPhotos = [];

    if ($isEdit) {
        $stmt = $gd->prepare("
            SELECT id, link
            FROM photo
            WHERE activity_id = :activity_id
            ORDER BY id ASC
        ");
        $stmt->execute([
            'activity_id' => $activityId
        ]);

        $activityPhotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    if (!$activity) {
        http_response_code(403);
        echo "<p>Access denied.</p>";
        return;
    }
}
?>

<div class="main-layout">
    <div class="main-right activity-form-container">

        <h1><?= $isEdit ? 'Edit activity' : 'New activity' ?></h1>

        <form
            action="<?= BASE_URL ?>/partials/activities/activity_save.php"
            method="POST"
            enctype="multipart/form-data"
            class="activity-form">

            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $activity['id'] ?>">
            <?php endif; ?>

            <!-- TITLE -->
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" required
                    value="<?= htmlspecialchars($activity['title'] ?? '') ?>">
            </div>

            <!-- SPORT -->
            <div class="form-group">
                <label>Sport</label>
                <select name="sport" required>
                    <?php
                    $sports = $gd->query("SELECT id, name FROM sports ORDER BY name")->fetchAll();
                    foreach ($sports as $sport):
                        $selected = ($activity['sport'] ?? null) == $sport['id'] ? 'selected' : '';
                    ?>
                        <option value="<?= $sport['id'] ?>" <?= $selected ?>>
                            <?= htmlspecialchars($sport['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- DATE -->
            <div class="form-group">
                <label>Date & time</label>
                <input type="datetime-local" name="date_time" required
                    value="<?= isset($activity['date_time'])
                                ? date('Y-m-d\TH:i', strtotime($activity['date_time']))
                                : '' ?>">
            </div>

            <!-- DISTANCE -->
            <div class="form-group">
                <label>Distance (km)</label>
                <input type="number" step="0.01" name="distance"
                    value="<?= $activity['distance'] ?? '' ?>">
            </div>

            <!-- DURATION -->
            <div class="form-group">
                <label>Duration (seconds)</label>
                <input type="number" name="duration"
                    value="<?= $activity['duration'] ?? '' ?>">
            </div>

            <!-- ELEVATION -->
            <div class="form-group">
                <label>Elevation gain (D+)</label>
                <input type="number" name="dplus"
                    value="<?= $activity['dplus'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label>Elevation loss (D-)</label>
                <input type="number" name="dminus"
                    value="<?= $activity['dminus'] ?? '' ?>">
            </div>

            <!-- HEART RATE -->
            <div class="form-group">
                <label>Max heart rate (FCM)</label>
                <input type="number" name="FCM"
                    value="<?= $activity['FCM'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label>Average heart rate</label>
                <input type="number" name="avg_hr"
                    value="<?= $activity['avg_hr'] ?? '' ?>">
            </div>

            <!-- CALORIES -->
            <div class="form-group">
                <label>Calories</label>
                <input type="number" name="calories"
                    value="<?= $activity['calories'] ?? '' ?>">
            </div>

            <!-- PLACE -->
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="place"
                    value="<?= htmlspecialchars($activity['place'] ?? '') ?>">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4"><?= htmlspecialchars($activity['description'] ?? '') ?></textarea>
            </div>

            <!-- VISIBILITY -->
            <div class="form-group">
                <label>Visibility</label>
                <select name="visibility">
                    <option value="public" <?= ($activity['visibility'] ?? 'public') === 'public' ? 'selected' : '' ?>>
                        Public
                    </option>
                    <option value="private" <?= ($activity['visibility'] ?? '') === 'private' ? 'selected' : '' ?>>
                        Private
                    </option>
                </select>
            </div>

            <!-- PHOTOS -->

            <?php if ($isEdit && !empty($activityPhotos)): ?>
                <div class="form-group">
                    <label>Photos existantes</label>

                    <div class="activity-photos-edit">
                        <?php foreach ($activityPhotos as $photo): ?>
                            <div class="photo-item">
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($photo['link']) ?>" alt="">

                                <button
                                    type="button"
                                    class="delete-photo-btn"
                                    data-photo-id="<?= $photo['id'] ?>">
                                    ✕
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>






            <div class="form-group">
                <label for="photos">Photos de l activité</label>

                <input
                    type="file"
                    name="photos[]"
                    id="photos"
                    multiple
                    accept="image/*">

                <small>
                    Tu peux sélectionner plusieurs images (jpg, png, webp) :
                    Astuce : sélectionne toutes les photos en une seule fois
                    (Ctrl / Cmd + clic)

                </small>
            </div>


            <!-- ACTIONS -->
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <?= $isEdit ? 'Save changes' : 'Create activity' ?>
                </button>

                <a
                    href="<?= BASE_URL ?>/pages/private/main_page.php"
                    class="btn-secondary btn-link">
                    Cancel
                </a>
            </div>


        </form>

    </div>
</div>



<script>
    document.querySelectorAll('.delete-photo-btn').forEach(btn => {
        btn.addEventListener('click', () => {

            if (!confirm('Supprimer cette photo ?')) return;

            const photoId = btn.dataset.photoId;

            fetch('<?= BASE_URL ?>/partials/activities/delete_photo.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'photo_id=' + photoId
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        btn.closest('.photo-item').remove();
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                });
        });
    });
</script>