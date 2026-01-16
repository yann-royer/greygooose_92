<nav class="navbar">
    <ul class="navbar-list">

        <li>
            <a class="navbar-link <?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>"
                href="<?= BASE_URL ?>/pages/private/main_page.php">
                Accueil
            </a>
        </li>

        <li>
            <a class="navbar-link <?= ($currentPage ?? '') === 'profile' ? 'active' : '' ?>"
                href="<?= BASE_URL ?>/pages/private/profile.php">
                Profil
            </a>
        </li>

        <li>
            <a class="navbar-link <?= ($currentPage ?? '') === 'activity' ? 'active' : '' ?>"
                href="<?= BASE_URL ?>/pages/private/activity_form.php">
                Nouvelle activité
            </a>
        </li>

        <li>
            <a class="navbar-link <?= ($currentPage ?? '') === 'social' ? 'active' : '' ?>"
                href="<?= BASE_URL ?>/pages/private/social.php">
                Social
            </a>
        </li>

        <li>
            <a class="navbar-link logout"
                href="<?= BASE_URL ?>/partials/session/close_session.php">
                Déconnexion
            </a>
        </li>


    </ul>
</nav>