<!-- format.php = fct pour le formatage de l'allure -->
<?php
function formatAllure($duration, $distance)
{
    if ($distance <= 0) return '-';

    $secondsPerKm = $duration / $distance;
    $minutes = floor($secondsPerKm / 60);
    $seconds = floor($secondsPerKm % 60);

    return sprintf("%d:%02d /km", $minutes, $seconds);
}
?>

<!-- format.php = fct pour le formatage de la durée -->
<?php
function formatDuration($seconds)
{
    if ($seconds <= 0) return '0s';

    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;

    $parts = [];

    if ($hours > 0) {
        $parts[] = $hours . 'h';
    }

    if ($minutes > 0) {
        $parts[] = $minutes . 'min';
    }

    // afficher les secondes seulement si < 1h
    if ($hours == 0 && $secs > 0) {
        $parts[] = $secs . 's';
    }

    return implode(' ', $parts);
}
?>

<!-- activity_feed.php = fct pour le formatage de la date d'activité -->
<?php

function formatActivityDate($dateTime)
{
    if (empty($dateTime)) return '';

    $activityDate = new DateTime($dateTime);
    $now = new DateTime();

    $diff = $now->diff($activityDate);
    $daysDiff = (int)$diff->days;

    // Heure formatée
    $time = $activityDate->format('H:i');

    // Aujourd'hui
    if ($daysDiff === 0) {
        return "Today at $time";
    }

    // Hier
    if ($daysDiff === 1) {
        return "Yesterday at $time";
    }

    // Moins d'une semaine
    if ($daysDiff < 7) {
        return $daysDiff . " days ago";
    }

    // Date complète (mois anglais)
    return $activityDate->format('M j, Y');
}
