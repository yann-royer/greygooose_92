<?php
require_once __DIR__ . '/../../partials/layout/header_public.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>

<div class="auth-page">
    <div class="auth-card">

        <h1 class="auth-title">Create your account</h1>

        <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
            <div class="auth-alert error">
                This email address is already associated to an account
            </div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="<?= BASE_URL ?>/partials/auth/register2.php">

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
                <label for="pw">Password</label>
                <input
                    type="password"
                    id="pw"
                    name="pw"
                    required
                    placeholder="••••••••"
                >
            </div>

            <button type="submit" class="btn-primary">
                Register
            </button>
        </form>

        <div class="auth-links">
            <a href="<?= BASE_URL ?>/pages/public/login.php">Log in</a>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../../partials/layout/footer.php'; ?>
