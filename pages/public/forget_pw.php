<?php
require_once __DIR__ . '/../../partials/layout/header_public.php';
?>

<div class="auth-page">
    <div class="auth-card">

        <h1 class="auth-title">Forgot your password?</h1>

        <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
            <div class="auth-alert error">
                This email isn't associated to an account
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['pw'])): ?>
            <div class="auth-alert success">
                Your password is: <strong><?= htmlspecialchars($_GET['pw']) ?></strong>
            </div>
        <?php endif; ?>

        <form
            class="auth-form"
            method="POST"
            action="<?= BASE_URL ?>/partials/auth/forget_pw2.php"
        >

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Write the email of your lost account"
                    required
                >
            </div>

            <button type="submit" class="btn-primary">
                Find my password
            </button>
        </form>

        <div class="auth-links">
            <a href="<?= BASE_URL ?>/pages/public/login.php">Log in</a>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../../partials/layout/footer.php'; ?>
