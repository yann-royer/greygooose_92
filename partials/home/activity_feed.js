/* ===========================
   UTIL : format date comment
=========================== */
function formatCommentDate(sqlDate) {
    const date = new Date(sqlDate.replace(' ', 'T'));
    const now = new Date();

    const isToday = date.toDateString() === now.toDateString();

    const yesterday = new Date();
    yesterday.setDate(now.getDate() - 1);

    const isYesterday =
        date.toDateString() === yesterday.toDateString();

    const time = date.toLocaleTimeString('en-GB', {
        hour: '2-digit',
        minute: '2-digit'
    });

    if (isToday) return `Today at ${time}`;
    if (isYesterday) return `Yesterday at ${time}`;

    return (
        date.toLocaleDateString('en-GB', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }) + ` at ${time}`
    );
}

/* ===========================
   MAIN LISTENER
=========================== */
document.addEventListener('click', async (e) => {

    /* =======================
       KUDO TOGGLE
    ======================= */
    const kudoBtn = e.target.closest('.kudo-btn');
    if (kudoBtn) {
        try {
            const response = await fetch(
                '/greygooose_92/partials/activities/kudo_toggle_ajax.php',
                {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        activity_id: kudoBtn.dataset.activityId
                    })
                }
            );

            const data = await response.json();

            if (data.success) {
                kudoBtn.classList.toggle('active', data.has_kudo);
                kudoBtn.querySelector('.kudo-count').textContent =
                    data.kudos_count;
            }
        } catch (err) {
            console.error('KUDO ERROR', err);
        }
        return;
    }

    /* =======================
       TOGGLE COMMENTS
    ======================= */
    const commentBtn = e.target.closest('.comment-btn');
    if (commentBtn) {
        const activityId = commentBtn.dataset.activityId;
        const container =
            document.getElementById('comments-' + activityId);
        const form = container.nextElementSibling;

        const isHidden = container.style.display === 'none';

        container.style.display = isHidden ? 'block' : 'none';
        form.style.display = isHidden ? 'block' : 'none';

        if (!isHidden) return;

        form.querySelector('.comment-input').value = '';

        if (container.dataset.loaded === '1') return;

        try {
            const response = await fetch(
                '/greygooose_92/partials/activities/comments_fetch_ajax.php',
                {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ activity_id: activityId })
                }
            );

            const data = await response.json();
            if (!data.success) return;

            let html = '';

            if (data.comments.length === 0) {
                html = '<p>Aucun commentaire.</p>';
            } else {
                data.comments.forEach((comment) => {
                    const canEdit =
                        comment.user_id == window.CURRENT_USER_ID;

                    html += `
                        <div class="comment" data-comment-id="${comment.id}">
                            <img src="${comment.pp}" class="comment-avatar">

                            <div class="comment-body">
                                <div class="comment-header">
                                    <strong>
                                        ${comment.name} ${comment.family_name}
                                    </strong>
                                    <span class="comment-date">
                                        ${formatCommentDate(comment.created_at)}
                                    </span>
                                </div>

                                <div class="comment-content">
                                    ${comment.content}
                                </div>

                                ${
                                    canEdit
                                        ? `
                                    <div class="comment-actions">
                                        <button class="comment-edit-btn">✏️ Modifier</button>
                                        <button class="comment-delete-btn">🗑 Supprimer</button>
                                    </div>
                                `
                                        : ''
                                }
                            </div>
                        </div>
                    `;
                });
            }

            container.innerHTML = html;
            container.dataset.loaded = '1';
        } catch (err) {
            console.error('FETCH COMMENTS ERROR', err);
        }
        return;
    }

    /* =======================
       ADD COMMENT
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
                '/greygooose_92/partials/activities/comments_add_ajax.php',
                {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        activity_id: activityId,
                        content: content
                    })
                }
            );

            const data = await response.json();
            if (!data.success) return;

            const c = data.comments;

            const container =
                document.getElementById('comments-' + activityId);

            container.insertAdjacentHTML(
                'beforeend',
                `
                <div class="comment" data-comment-id="${c.id}">
                    <img src="${c.pp}" class="comment-avatar">
                    <div class="comment-body">
                        <div class="comment-header">
                            <strong>${c.name} ${c.family_name}</strong>
                            <span class="comment-date">
                                ${formatCommentDate(c.created_at)}
                            </span>
                        </div>
                        <div class="comment-content">${c.content}</div>
                        <div class="comment-actions">
                            <button class="comment-edit-btn">✏️ Modifier</button>
                            <button class="comment-delete-btn">🗑 Supprimer</button>
                        </div>
                    </div>
                </div>
            `
            );

            textarea.value = '';

            const countSpan = document.querySelector(
                `.comment-btn[data-activity-id="${activityId}"] .comment-count`
            );
            countSpan.textContent =
                parseInt(countSpan.textContent) + 1;
        } catch (err) {
            console.error('ADD COMMENT ERROR', err);
        }
        return;
    }

    /* =======================
       DELETE COMMENT
    ======================= */
    const deleteBtn = e.target.closest('.comment-delete-btn');
    if (deleteBtn) {
        const commentDiv = deleteBtn.closest('.comment');
        const commentId = commentDiv.dataset.commentId;

        if (!confirm('Supprimer ce commentaire ?')) return;

        try {
            const response = await fetch(
                '/greygooose_92/partials/activities/comments_delete_ajax.php',
                {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ comment_id: commentId })
                }
            );

            const data = await response.json();
            if (!data.success) return;

            commentDiv.remove();

            const countSpan = document.querySelector(
                `.comment-btn[data-activity-id="${data.activity_id}"] .comment-count`
            );
            if (countSpan) {
                countSpan.textContent = data.comments_count;
            }
        } catch (err) {
            console.error('DELETE COMMENT ERROR', err);
        }
        return;
    }

    /* =======================
       EDIT COMMENT
    ======================= */
    const editBtn = e.target.closest('.comment-edit-btn');
    if (editBtn) {
        const commentDiv = editBtn.closest('.comment');
        const contentDiv =
            commentDiv.querySelector('.comment-content');

        if (commentDiv.querySelector('textarea')) return;

        const oldContent = contentDiv.textContent.trim();

        contentDiv.innerHTML = `
            <textarea class="comment-edit-input">${oldContent}</textarea>
            <div class="comment-edit-actions">
                <button class="comment-save-btn">💾 Save</button>
                <button class="comment-cancel-btn">❌ Cancel</button>
            </div>
        `;
        return;
    }

    /* =======================
       SAVE EDIT
    ======================= */
    const saveBtn = e.target.closest('.comment-save-btn');
    if (saveBtn) {
        const commentDiv = saveBtn.closest('.comment');
        const commentId = commentDiv.dataset.commentId;
        const textarea =
            commentDiv.querySelector('.comment-edit-input');
        const newContent = textarea.value.trim();

        if (!newContent) return;

        try {
            const response = await fetch(
                '/greygooose_92/partials/activities/comments_edit_ajax.php',
                {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        comment_id: commentId,
                        content: newContent
                    })
                }
            );

            const data = await response.json();
            if (!data.success) return;

            commentDiv.querySelector('.comment-content').textContent =
                data.content;
        } catch (err) {
            console.error('EDIT COMMENT ERROR', err);
        }
        return;
    }

    /* =======================
       CANCEL EDIT
    ======================= */
    const cancelBtn = e.target.closest('.comment-cancel-btn');
    if (cancelBtn) {
        const commentDiv = cancelBtn.closest('.comment');
        const textarea =
            commentDiv.querySelector('.comment-edit-input');

        commentDiv.querySelector('.comment-content').textContent =
            textarea.defaultValue;
    }
});
