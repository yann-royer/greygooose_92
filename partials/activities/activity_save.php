<?php
require_once __DIR__ . "/../db/BD_connexion.php";
require_once __DIR__ . "/../../config.php";
session_start();

/* =========================
   1. SECURITY
========================= */

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Access denied');
}

$userId = (int) $_SESSION['user_id'];

/* =========================
   2. REQUIRED FIELDS
========================= */

if (
    empty($_POST['title']) ||
    empty($_POST['sport']) ||
    empty($_POST['date_time'])
) {
    http_response_code(400);
    exit('Missing required fields');
}

/* =========================
   3. SANITIZE / NORMALIZE
========================= */

$id         = isset($_POST['id']) && ctype_digit($_POST['id']) ? (int) $_POST['id'] : null;
$title      = trim($_POST['title']);
$sport      = (int) $_POST['sport'];
$dateTime   = $_POST['date_time'];

$description = trim($_POST['description'] ?? '');
$place       = trim($_POST['place'] ?? '');
$visibility  = $_POST['visibility'] ?? 'public';

$distance = $_POST['distance'] !== '' ? (float) $_POST['distance'] : null;
$duration = $_POST['duration'] !== '' ? (int) $_POST['duration'] : null;

$dplus  = $_POST['dplus'] !== '' ? (int) $_POST['dplus'] : null;
$dminus = $_POST['dminus'] !== '' ? (int) $_POST['dminus'] : null;

$FCM     = $_POST['FCM'] !== '' ? (int) $_POST['FCM'] : null;
$avg_hr  = $_POST['avg_hr'] !== '' ? (int) $_POST['avg_hr'] : null;
$calories = $_POST['calories'] !== '' ? (int) $_POST['calories'] : null;

/* =========================
   4. EDIT MODE (UPDATE)
========================= */

if ($id) {

    // Ownership check
    $check = $gd->prepare("
        SELECT id
        FROM activity
        WHERE id = :id
        AND user_id = :user_id
        LIMIT 1
    ");
    $check->execute([
        'id' => $id,
        'user_id' => $userId
    ]);

    if (!$check->fetch()) {
        http_response_code(403);
        exit('Unauthorized');
    }

    $sql = "
        UPDATE activity SET
            sport = :sport,
            title = :title,
            description = :description,
            place = :place,
            distance = :distance,
            duration = :duration,
            dplus = :dplus,
            dminus = :dminus,
            FCM = :FCM,
            avg_hr = :avg_hr,
            calories = :calories,
            date_time = :date_time,
            visibility = :visibility,
            updated_at = NOW()
        WHERE id = :id
    ";

    $stmt = $gd->prepare($sql);
    $stmt->execute([
        'sport' => $sport,
        'title' => $title,
        'description' => $description ?: null,
        'place' => $place ?: null,
        'distance' => $distance,
        'duration' => $duration,
        'dplus' => $dplus,
        'dminus' => $dminus,
        'FCM' => $FCM,
        'avg_hr' => $avg_hr,
        'calories' => $calories,
        'date_time' => $dateTime,
        'visibility' => $visibility,
        'id' => $id
    ]);

    $activityId = $id;

    /* =========================
   5. CREATE MODE (INSERT)
========================= */
} else {

    $sql = "
        INSERT INTO activity (
            user_id, sport, title, description, place,
            distance, duration,
            dplus, dminus,
            FCM, avg_hr, calories,
            date_time, visibility,
            created_at
        ) VALUES (
            :user_id, :sport, :title, :description, :place,
            :distance, :duration,
            :dplus, :dminus,
            :FCM, :avg_hr, :calories,
            :date_time, :visibility,
            NOW()
        )
    ";

    $stmt = $gd->prepare($sql);
    $stmt->execute([
        'user_id' => $userId,
        'sport' => $sport,
        'title' => $title,
        'description' => $description ?: null,
        'place' => $place ?: null,
        'distance' => $distance,
        'duration' => $duration,
        'dplus' => $dplus,
        'dminus' => $dminus,
        'FCM' => $FCM,
        'avg_hr' => $avg_hr,
        'calories' => $calories,
        'date_time' => $dateTime,
        'visibility' => $visibility
    ]);

    $activityId = (int) $gd->lastInsertId();
}
/* =========================
    5. PHOTOS UPLOAD
    ========================= */

if (!empty($_FILES['photos']['name'][0])) {

    $uploadDir = __DIR__ . '/../../uploads/photos/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5 Mo

    foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {

        if (!is_uploaded_file($tmpName)) {
            continue;
        }

        $type = $_FILES['photos']['type'][$index];
        $size = $_FILES['photos']['size'][$index];

        if (!in_array($type, $allowedTypes)) {
            continue;
        }

        if ($size > $maxSize) {
            continue;
        }

        $extension = pathinfo(
            $_FILES['photos']['name'][$index],
            PATHINFO_EXTENSION
        );

        $filename = uniqid('photo_') . '.' . $extension;

        if (!move_uploaded_file(
            $tmpName,
            $uploadDir . $filename
        )) {
            continue;
        }

        // INSERT DB
        $stmt = $gd->prepare("
                INSERT INTO photo (activity_id, link)
                VALUES (:activity_id, :link)
            ");
        $stmt->execute([
            'activity_id' => $activityId,
            'link' => '/uploads/photos/' . $filename
        ]);
    }
}

/* =========================
   6. REDIRECT
========================= */

header("Location: " . BASE_URL . "/pages/private/activity_view.php?id=" . $activityId);
exit;
