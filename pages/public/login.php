<?php
require_once __DIR__ . '/../../partials/layout/header_public.php';
?>

<div class="auth-page">
    <div class="auth-card">

        <h1 class="auth-title">Login</h1>

        <form class="auth-form" method="POST" action="<?= BASE_URL ?>/partials/auth/login2.php">

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    placeholder="example@email.com"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="pw"
                    name="pw"
                    required
                    placeholder="••••••••"
                >
            </div>

            <button type="submit" class="btn-primary">
                Login
            </button>
        </form>

        <div class="auth-links">
            <a href="forget_pw.php">Forgot password?</a>
            <span>•</span>
            <a href="register.php">Create an account</a>
        </div>

    </div>
</div>

<?php
require_once __DIR__ . '/../../partials/layout/footer.php';
?>
