const input = document.getElementById("social-search-input");
const resultsList = document.getElementById("social-results-list");
const BASE_URL = "/greygooose_92";

document.addEventListener("DOMContentLoaded", function () {
    loadSuggestions();
    if (input) {
        input.addEventListener("input", function () {
            const q = input.value.trim();
            q === "" ? loadSuggestions() : search(q);
        });
    }
});

function loadSuggestions() {
    fetch(BASE_URL + "/ajax/social/search_users.php")
        .then(r => r.json())
        .then(users => showResults(users, "Suggestions for you"))
        .catch(err => console.error(err));
}

function search(query) {
    fetch(BASE_URL + "/ajax/social/search_users.php?q=" + encodeURIComponent(query))
        .then(r => r.json())
        .then(users => showResults(users, "Search results"))
        .catch(err => console.error(err));
}

function showResults(users, title) {
    document.getElementById("results-title").textContent = title;
    resultsList.innerHTML = "";

    if (users.length === 0) {
        resultsList.innerHTML = "<p class='no-results'>No results</p>";
        return;
    }

    users.forEach(user => {
        const div = document.createElement("div");
        div.className = "social-user-card";
        const photo = user.pp || BASE_URL + "/uploads/pp/default.webp";
        const btnText = user.status === 'accepted' ? "Following" : "Follow";
        const btnClass = user.status === 'accepted' ? "btn-following" : "btn-follow";

        div.innerHTML = `
            <div class="user-info">
                <img src="${photo}" class="user-avatar">
                <div><strong>${user.name} ${user.family_name}</strong></div>
            </div>
            <button class="btn ${btnClass}" onclick="toggleFollow(${user.id}, this)">${btnText}</button>
        `;
        resultsList.appendChild(div);
    });
}

function toggleFollow(userId, btn) {
    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = "...";

    fetch(BASE_URL + "/ajax/social/follow_toggle.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ target_id: userId })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (data.action === 'followed') {
                    btn.textContent = "Following";
                    btn.className = "btn btn-following";
                } else {
                    btn.textContent = "Follow";
                    btn.className = "btn btn-follow";
                }
            } else {
                alert("Error");
                btn.textContent = originalText;
            }
        })
        .catch(err => {
            console.error(err);
            alert("Error");
            btn.textContent = originalText;
        })
        .finally(() => btn.disabled = false);
}
