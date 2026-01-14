<!-- activity_feed.php = fct pour l'allure et la durée -->
<?php
require_once __DIR__ . "/../../partials/helpers/format.php";
?>

<!--inclue le header qui contient : la barre de nav et le debut de la structure HTML-->
<?php
$currentPage = 'activity';
require __DIR__ . "/../../partials/layout/header.php";
?>


<!-- Contenu spécifique à la page principale privée -->

<h1>Flux d'activités</h1>
<p>Bienvenue sur votre flux d'activités privé !</p>

<!-- requete php pour recupérer les activitées -->
<?php
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


    /* 🔥 JOIN RELATIONS */
    LEFT JOIN relations r
        ON r.target_id = a.user_id
        AND r.user_id = :user_id
        AND r.status = 'accepted'

    LEFT JOIN kudos k ON k.activity_id = a.id
    LEFT JOIN comments c ON c.activity_id = a.id

    /* 🔥 CONDITIONS FEED */
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


<!--                 Affichage des activités                       -->
<main class="activity-feed">

    <h1>Activités récentes</h1>

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
                    <li><strong>Sport :</strong> <?= htmlspecialchars($activity['sport_name']) ?></li>
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
                    <button
                        class="comment-btn"
                        data-activity-id="<?= $activity['id'] ?>"
                        type="button">
                        💬 commentaires : <span class="comment-count"><?= (int)$activity['comments_count'] ?></span>
                    </button>


                </div>

                <!-- COMMENTS CONTAINER -->
                <div
                    class="comments-container"
                    id="comments-<?= $activity['id'] ?>"
                    data-loaded="0"
                    style="display:none;">
                </div>

                <!-- COMMENT FORM -->
                <div class="comment-form" data-activity-id="<?= $activity['id'] ?>" style="display:none;">
                    <textarea placeholder="Ajouter un commentaire..." class="comment-input"></textarea>
                    <button type="button" class="comment-submit">Publier</button>
                </div>


            </article>

        <?php endforeach; ?>
    <?php endif; ?>

</main>




<!--              JS pour le kudo toggle AJAX et commentaires                     -->
<script>
    //<!--  fct pour le formatage de la date de commentaire -->
    function formatCommentDate(sqlDate) {
        const date = new Date(sqlDate.replace(' ', 'T'));
        const now = new Date();

        const isToday =
            date.toDateString() === now.toDateString();

        const yesterday = new Date();
        yesterday.setDate(now.getDate() - 1);

        const isYesterday =
            date.toDateString() === yesterday.toDateString();

        const time = date.toLocaleTimeString('en-GB', {
            hour: '2-digit',
            minute: '2-digit'
        });

        if (isToday) {
            return `Today at ${time}`;
        }
        if (isYesterday) {
            return `Yesterday at ${time}`;
        }

        return date.toLocaleDateString('en-GB', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }) + ` at ${time}`;
    }





    document.addEventListener('DOMContentLoaded', () => {

        console.log('JS LOADED');

        document.addEventListener('click', async (e) => {

            /* =======================
               KUDO CLICK
            ======================= */
            const kudoBtn = e.target.closest('.kudo-btn');
            if (kudoBtn) {

                console.log('KUDO CLICK');

                try {
                    const response = await fetch(
                        '/greygooose_92/partials/activities/kudo_toggle_ajax.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                activity_id: kudoBtn.dataset.activityId
                            })
                        }
                    );

                    const data = await response.json();
                    console.log(data);

                    if (data.success) {
                        kudoBtn.classList.toggle('active', data.has_kudo);
                        kudoBtn.querySelector('.kudo-count').textContent = data.kudos_count;
                    }

                } catch (e) {
                    console.error('KUDO FETCH ERROR', e);
                }
            }

            /* =======================
               COMMENT CLICK
            ======================= */
            const commentBtn = e.target.closest('.comment-btn');
            if (commentBtn) {

                console.log('COMMENT CLICK');

                const activityId = commentBtn.dataset.activityId;
                const container = document.getElementById('comments-' + activityId);

                /* toggle affichage */
                const form = container.nextElementSibling;

                if (container.style.display === 'none') {
                    container.style.display = 'block';
                    form.style.display = 'block';
                } else {
                    container.style.display = 'none';
                    form.style.display = 'none';
                    return;
                }
                form.querySelector('.comment-input').value = '';


                /* déjà chargé */
                if (container.dataset.loaded === "1") return;

                try {
                    console.log('ACTIVITY ID =', activityId);

                    const response = await fetch(
                        '/greygooose_92/partials/activities/comments_fetch_ajax.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                activity_id: activityId
                            })
                        }
                    );

                    const text = await response.text();
                    console.log('RAW RESPONSE:', text);

                    const data = JSON.parse(text);


                    if (!data.success) return;

                    let html = '';

                    if (data.comments.length === 0) {
                        html = '<p>Aucun commentaire.</p>';
                    } else {
                        data.comments.forEach(comment => {

                            const canEdit = comment.user_id == <?= $_SESSION['user_id'] ?>;

                            html += `
                                <div class="comment" data-comment-id="${comment.id}">
                                    
                                    <img src="${comment.pp}" class="comment-avatar">

                                    <div class="comment-body">

                                        <div class="comment-header">
                                            <strong>${comment.name} ${comment.family_name}</strong>

                                            <span class="comment-date">
                                                ${formatCommentDate(comment.created_at)}
                                            </span>
                                        </div>

                                        <div class="comment-content">
                                            ${comment.content}
                                        </div>

                                        ${canEdit ? `
                                            <div class="comment-actions">
                                                <button class="comment-edit-btn">✏️ Modifier</button>
                                                <button class="comment-delete-btn">🗑 Supprimer</button>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `;
                        });


                    }

                    container.innerHTML = html;
                    container.dataset.loaded = "1";

                    const form = container.nextElementSibling;
                    form.style.display = 'block';


                } catch (e) {
                    console.error('COMMENT FETCH ERROR', e);
                }
            }

            /* =======================
                  COMMENT SUBMIT
            ======================= */
            const submitBtn = e.target.closest('.comment-submit');
            if (submitBtn) {

                const form = submitBtn.closest('.comment-form');
                const activityId = form.dataset.activityId;
                const textarea = form.querySelector('.comment-input');
                const content = textarea.value.trim();

                if (!content) return;

                try {
                    const response = await fetch(
                        '/greygooose_92/partials/activities/comments_add_ajax.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                activity_id: activityId,
                                content: content
                            })
                        }
                    );

                    const data = await response.json();

                    if (!data.success) return;

                    const container = document.getElementById('comments-' + activityId);

                    const comment = data.comments;

                    const html = `
                        <div class="comment" data-comment-id="${comment.id}">
                            
                            <img src="${comment.pp}" class="comment-avatar">

                            <div class="comment-body">

                                <div class="comment-header">
                                    <strong>${comment.name} ${comment.family_name}</strong>

                                    <span class="comment-date">
                                        ${formatCommentDate(comment.created_at)}
                                    </span>
                                </div>

                                <div class="comment-content">
                                    ${comment.content}
                                </div>

                                <div class="comment-actions">
                                    <button class="comment-edit-btn">✏️ Modifier</button>
                                    <button class="comment-delete-btn">🗑 Supprimer</button>
                                </div>

                            </div>
                        </div>
                    `;




                    container.insertAdjacentHTML('beforeend', html);

                    textarea.value = '';
                    const countSpan = document
                        .querySelector(`.comment-btn[data-activity-id="${activityId}"] .comment-count`);

                    countSpan.textContent = parseInt(countSpan.textContent) + 1;


                } catch (e) {
                    console.error('COMMENT ADD ERROR', e);
                }
            }

            /* =======================
            COMMENT DELETE
            ======================= */
            const deleteBtn = e.target.closest('.comment-delete-btn');
            if (deleteBtn) {

                const commentDiv = deleteBtn.closest('.comment');
                if (!commentDiv) return;

                const commentId = commentDiv.dataset.commentId;

                if (!commentId) {
                    console.error('COMMENT ID MANQUANT');
                    return;
                }

                if (!confirm('Supprimer ce commentaire ?')) return;

                try {
                    const response = await fetch(
                        '/greygooose_92/partials/activities/comments_delete_ajax.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                comment_id: commentId
                            })
                        }
                    );

                    const data = await response.json();

                    if (!data.success) return;

                    /* 1️⃣ supprimer le commentaire */
                    commentDiv.remove();

                    /* 2️⃣ mise à jour compteur — VRAIE valeur backend */
                    const countSpan = document.querySelector(
                        `.comment-btn[data-activity-id="${data.activity_id}"] .comment-count`
                    );

                    if (countSpan) {
                        countSpan.textContent = data.comments_count;
                    }





                } catch (e) {
                    console.error('COMMENT DELETE ERROR', e);
                }
            }

            /* =======================
            COMMENT EDIT
            ======================= */
            const editBtn = e.target.closest('.comment-edit-btn');
            if (editBtn) {

                const commentDiv = editBtn.closest('.comment');
                if (!commentDiv) return;

                const contentDiv = commentDiv.querySelector('.comment-content');
                const oldContent = contentDiv.textContent.trim();

                /* empêcher double edit */
                if (commentDiv.querySelector('textarea')) return;

                contentDiv.innerHTML = `
                    <textarea class="comment-edit-input">${oldContent}</textarea>
                    <div class="comment-edit-actions">
                        <button class="comment-save-btn">💾 Save</button>
                        <button class="comment-cancel-btn">❌ Cancel</button>
                    </div>
                `;
            }

            /* =======================
            COMMENT EDIT SAVE 
            ======================= */

            const saveBtn = e.target.closest('.comment-save-btn');
            if (saveBtn) {

                const commentDiv = saveBtn.closest('.comment');
                const commentId = commentDiv.dataset.commentId;
                const textarea = commentDiv.querySelector('.comment-edit-input');
                const newContent = textarea.value.trim();

                if (!newContent) return;

                try {
                    const response = await fetch(
                        '/greygooose_92/partials/activities/comments_edit_ajax.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                comment_id: commentId,
                                content: newContent
                            })
                        }
                    );

                    const data = await response.json();
                    if (!data.success) return;

                    commentDiv.querySelector('.comment-content').innerHTML = data.content;

                } catch (e) {
                    console.error('COMMENT EDIT ERROR', e);
                }
            }

            /* =======================
            COMMENT EDIT CANCEL 
            ======================= */
            const cancelBtn = e.target.closest('.comment-cancel-btn');
            if (cancelBtn) {

                const commentDiv = cancelBtn.closest('.comment');
                const textarea = commentDiv.querySelector('.comment-edit-input');

                commentDiv.querySelector('.comment-content').textContent =
                    textarea.defaultValue;
            }

        });
    });
</script>




<!--inclue le footer qui contient : la fin de la structure HTML-->
<?php require __DIR__ . "/../../partials/layout/footer.php"; ?>