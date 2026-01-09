<?php
require_once __DIR__ . '/../../partials/auth/auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register</title>
  <link rel="stylesheet" type="text/css" href="styles.css" />
</head>

<body>
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

      <label for="age">Age</label>
      <input
        type="number"
        name="age"
        id="age"
        placeholder="What's your age ?"
        required />
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
</body>

</html>