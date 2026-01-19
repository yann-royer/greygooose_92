document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("social-search-input");
    const resultsContainer = document.getElementById("social-results-list");

    if (!input || !resultsContainer) return;

    input.addEventListener("input", () => {
        const query = input.value.trim();

        if (query === "") {
            resultsContainer.innerHTML = "";
            return;
        }

        fetch(`/greygooose_92/ajax/social/search_users.php?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(users => {
                resultsContainer.innerHTML = "";

                if (!Array.isArray(users) || users.length === 0) {
                    resultsContainer.innerHTML = "<p>Aucun résultat</p>";
                    return;
                }

                users.forEach(user => {
                    const div = document.createElement("div");
                    div.style.display = "flex";
                    div.style.alignItems = "center";
                    div.style.gap = "10px";
                    div.style.padding = "8px";
                    div.style.borderBottom = "1px solid #ddd";

                    div.innerHTML = `
                        <img src="${user.pp}" style="width:40px;height:40px;border-radius:50%">
                        <strong>${user.name} ${user.family_name}</strong>
                    `;

                    resultsContainer.appendChild(div);
                });
            })
            .catch(err => {
                console.error(err);
                resultsContainer.innerHTML = "<p>Erreur lors de la recherche</p>";
            });
    });
});
