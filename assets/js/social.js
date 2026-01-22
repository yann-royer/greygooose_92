const input = document.getElementById("social-search-input");
const resultsList = document.getElementById("social-results-list");
const BASE_URL = "/greygooose_92";

document.addEventListener("DOMContentLoaded", function () {
    loadSuggestions();

    const followingLink = document.getElementById("following-link");
    const followersLink = document.getElementById("followers-link");

    if (followingLink) {
        followingLink.addEventListener("click", function () {
            loadFollowing();
        });
    }

    if (followersLink) {
        followersLink.addEventListener("click", function () {
            loadFollowers();
        });
    }

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

function loadFollowing() {
    fetch(BASE_URL + "/ajax/social/get_following.php")
        .then(r => r.json())
        .then(users => showResults(users, "Following"))
        .catch(err => console.error(err));
}

function loadFollowers() {
    fetch(BASE_URL + "/ajax/social/get_followers.php")
        .then(r => r.json())
        .then(users => showResults(users, "Followers"))
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
        let btnText = "Follow";
        let btnClass = "btn-follow";
        if (user.status === 'accepted') {
            btnText = "Following";
            btnClass = "btn-following";
        }
        const canViewProfile = user.status === 'accepted';

        div.innerHTML = `
            <div class="user-info">
                ${canViewProfile
                ? `<a href="${BASE_URL}/pages/private/profil.php?id=${user.id}" class="user-link"><img src="${photo}" class="user-avatar"></a>`
                : `<span class="user-avatar-wrapper"><img src="${photo}" class="user-avatar"></span>`}
                <div>
                    ${canViewProfile
                ? `<a href="${BASE_URL}/pages/private/profil.php?id=${user.id}" class="user-link"><strong>${user.name} ${user.family_name}</strong></a>`
                : `<span class="user-name">${user.name} ${user.family_name}</span>`}
                </div>
            </div>
            <button class="btn ${btnClass}" onclick="toggleFollow(${user.id}, this)">${btnText}</button>
        `;
        resultsList.appendChild(div);
    });
}

function toggleFollow(userId, btn) {
    btn.disabled = true;
    const originalText = btn.textContent;
    const originalClass = btn.className;
    btn.textContent = "...";

    const url = BASE_URL + "/ajax/social/follow_toggle.php";
    console.log("Fetching URL:", url);

    fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ target_id: userId })
    })
        .then(r => {
            if (!r.ok) {
                throw new Error(`HTTP error! status: ${r.status}`);
            }
            return r.json();
        })
        .then(data => {
            console.log("Follow toggle response:", data);
            if (data.success) {
                if (data.action === 'followed') {
                    btn.textContent = "Following";
                    btn.className = "btn btn-following";
                    btn.disabled = false;
                } else if (data.action === 'unfollowed' || data.action === 'cancelled') {
                    btn.textContent = "Follow";
                    btn.className = "btn btn-follow";
                    btn.disabled = false;
                } else {
                    btn.disabled = false;
                }
            } else {
                console.error("Follow toggle failed:", data);
                btn.textContent = originalText;
                btn.className = originalClass;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error("Toggle follow error:", err);
            alert("Error: Could not connect to server. Check console for details.");
            btn.textContent = originalText;
            btn.className = originalClass;
            btn.disabled = false;
        });
}
