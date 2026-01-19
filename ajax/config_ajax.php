<?php
// Configuration minimale pour endpoints AJAX

// Base URL (sans conflit avec le reste du projet)
if (!defined('BASE_URL')) {
    define('BASE_URL', '/greygooose_92');
}

// Timezone
date_default_timezone_set('Europe/Paris');

// Session (utile pour connaître l'utilisateur connecté)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
