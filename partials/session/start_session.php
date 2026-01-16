<?php

session_start();
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_f_name'] = $user['f_name'];
$_SESSION['user_pp'] = $user['pp'];
$_SESSION['user_gender'] = $user['gender'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_phone'] = $user['phone'];
$_SESSION['user_age'] = $user['age'];



