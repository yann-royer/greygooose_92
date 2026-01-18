<?php
include __DIR__ . '/../../partials/layout/header.php';
?>

<div class="auth-page">
    <div class="auth-card auth-card-wide">

<?php if (isset($_GET['register']) && $_GET['register'] == '1'): ?>

        <h1 class="auth-title">Tell us more about yourself</h1>

        <form
            class="auth-form"
            enctype="multipart/form-data"
            method="POST"
            action="<?= BASE_URL ?>/partials/auth/register4.php"
        >

            <div class="form-group center">
                <label for="profile_pic" class="avatar-upload">
                    <img
                        src="<?= BASE_URL ?>/assets/images/avatar-placeholder.png"
                        class="avatar"
                    >
                    <span>Add profile picture</span>
                </label>

                <input
                    type="file"
                    name="profile_pic"
                    id="profile_pic"
                    accept="image/*"
                    hidden
                >
            </div>

            <div class="form-group">
                <label for="name">First name</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    placeholder="What's your name?"
                    required
                >
            </div>

            <div class="form-group">
                <label for="family_name">Last name</label>
                <input
                    type="text"
                    name="family_name"
                    id="family_name"
                    placeholder="What's your last name?"
                    required
                >
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input
                    type="number"
                    name="age"
                    id="age"
                    placeholder="What's your age?"
                    required
                >
            </div>

            <div class="form-group">
                <label for="phone">Phone number</label>
                <input
                    type="tel"
                    name="phone"
                    id="phone"
                    placeholder="What's your phone number?"
                    required
                >
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select name="gender" id="gender">
                    <option value="">Select</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <button type="submit" class="btn-primary">
                Done
            </button>
        </form>

<?php else: ?>

<?php
$userId = $_SESSION['user_id'];

$sql = "SELECT pp FROM user WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

        <h1 class="auth-title">Your profile</h1>

        <form
            class="auth-form"
            enctype="multipart/form-data"
            method="POST"
            action="<?= BASE_URL ?>/partials/auth/register4.php"
        >

            <div class="form-group center">
                <label for="profile_pic" class="avatar-upload">
                    <img
                        src="<?= htmlspecialchars($user['pp']) ?>"
                        alt="Profile picture"
                        class="avatar"
                    >
                    <span>Change picture</span>
                </label>

                <input
                    type="file"
                    name="profile_pic"
                    id="profile_pic"
                    accept="image/*"
                    hidden
                >
            </div>

            <div class="form-group">
                <label for="name">First name</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="<?= $_SESSION['user_name'] ?>"
                >
            </div>

            <div class="form-group">
                <label for="family_name">Last name</label>
                <input
                    type="text"
                    name="family_name"
                    id="family_name"
                    value="<?= $_SESSION['user_family_name'] ?>"
                >
            </div>

            <div class="form-group">
                <label for="phone">Phone number</label>
                <input
                    type="tel"
                    name="phone"
                    id="phone"
                    value="<?= $_SESSION['user_phone'] ?>"
                >
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="<?= $_SESSION['user_email'] ?>"
                >
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select name="gender" id="gender">
                    <option value="">Select</option>
                    <option value="0" <?= $_SESSION['user_gender'] === 0 || $_SESSION['user_gender'] === '0' ? 'selected' : '' ?>>Male</option>
                    <option value="1" <?= $_SESSION['user_gender'] === 1 || $_SESSION['user_gender'] === '1' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input
                    type="number"
                    name="age"
                    id="age"
                    value="<?= $_SESSION['user_age'] ?>"
                >
            </div>

            <button type="submit" class="btn-primary">
                Save changes
            </button>
        </form>

<?php endif; ?>

    </div>
</div>

<?php include __DIR__ . "/../../partials/layout/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('profile_pic');
    if (!input) return;

    input.addEventListener('change', e => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = ev => {
            const avatar = document.querySelector('.avatar');
            if (avatar) avatar.src = ev.target.result;
        };
        reader.readAsDataURL(file);
    });
});
</script>
