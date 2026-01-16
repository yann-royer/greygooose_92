<?php
require_once __DIR__ . '/../../partials/auth/auth_check.php';
include __DIR__ . '/../../partials/db/BD_connexion.php';
include __DIR__ . '/../../partials/layout/header.php';
?>

<?php if (isset($_GET['register']) && $_GET['register'] == '1'): ?>


  <div class="contenedor">
    <h1>Tell us more about yourself</h1>
    <br />

    <form enctype="multipart/form-data" method="POST" action="<?= BASE_URL ?>/partials/auth/register4.php">
      <label for="profile_pic">Profile picture</label>
      <input
        type="file"
        name="profile_pic"
        id="profile_pic"
        accept="image/*" />
      <br />

      <label for="name">First name</label>
      <input
        type="text"
        name="name"
        id="name"
        placeholder="What's your name ?"
        required />
      <br />

      <label for="family_name">Last name</label>
      <input
        type="text"
        name="family_name"
        id="family_name"
        placeholder="What's your last name ?"
        required />
      <br />

      <label for="age">Age</label>
      <input
        type="number"
        name="age"
        id="age"
        placeholder="What's your age ?"
        required />
      <br />

      <label for="phone">Phone number</label>
      <input
        type="tel"
        name="phone"
        id="phone"
        placeholder="What's your phone number ?"
        required />
      <br />

      <label for="gender">Gender</label>
      <select name="gender" id="gender">
        <option value="">Select</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
      </select>
      <br />

      <button type="submit">Done</button>
    </form>
  </div>


<?php else: ?>

  <table>
    <tr>
      <?php

            $userId = $_SESSION['user_id'];

            // Requête SQL préparée
            $sql = "SELECT pp FROM user WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

      ?>

    </tr>
    <br>
    <tr>
      <form enctype="multipart/form-data" method="POST" action="<?= BASE_URL ?>/partials/auth/register4.php">

        <label for="profile_pic" style="cursor:pointer;">
          <img
            src="<?= htmlspecialchars($user['pp']) ?>"
            alt="Photo de profil"
            class="avatar"
            width="200px"
          >
        </label>

        <!-- Input file caché -->
        <input
          type="file"
          name="profile_pic"
          id="profile_pic"
          accept="image/*"
          style="display:none"
        />
        <br />

        <label for="name">First name</label>
        <input
          type="text"
          name="name"
          id="name"
          placeholder="What's your name ?"
          value="<?php echo $_SESSION['user_name']?>"/>

        <label for="f_name">Last name</label>
        <input
          type="text"
          name="f_name"
          id="f_name"
          placeholder="What's your last name ?"
          value="<?php echo $_SESSION['user_f_name']?>"/>
        <br>

      <label for="phone">Phone number</label>
      <input
        type="tel"
        name="phone"
        id="phone"
        placeholder="What's your phone number ?"
        value="<?php echo $_SESSION['user_phone']?>" />
      <br />

      <label for="phone">Email</label>
      <input
        type="email"
        name="email"
        id="email"
        placeholder="What's your email ?"
        value="<?php echo $_SESSION['user_email']?>" />
      <br />

      <label for="gender">Gender</label>
      <select name="gender" id="gender">
        <option value="">Select</option>
        <option value="0" <?= $_SESSION['user_gender'] === 0 || $_SESSION['user_gender'] === '0' ? 'selected' : '' ?>>
          Male
        </option>
        <option value="1" <?= $_SESSION['user_gender'] === 1 || $_SESSION['user_gender'] === '1' ? 'selected' : '' ?>>
          Female
        </option>
      </select>

      <label for="age">Age</label>
      <input
        type="number"
        name="age"
        id="age"
        placeholder="What's your age ?"
        value="<?php echo $_SESSION['user_age']?>" />
      <br />

      <button type="submit">Done</button>

      </form>
    </tr>
  </table>

<?php endif; 
  include __DIR__ . "/../../partials/layout/footer.php";
?>


<script>
document.addEventListener('DOMContentLoaded', function () {
  const input = document.getElementById('profile_pic');
  if (!input) return;

  input.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = e => document.querySelector('.avatar').src = e.target.result;
    reader.readAsDataURL(file);
  });
});
</script>
