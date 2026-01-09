<?php
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_family_name']);
unset($_SESSION['user_pp']);
unset($_SESSION['user_gender']);
session_destroy();

header('Location: login.php');
