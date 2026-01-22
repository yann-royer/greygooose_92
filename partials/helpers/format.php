<?php
function formatAllure($duration, $distance)
{
    if ($distance <= 0) return '-';

    $secondsPerKm = (float)($duration / $distance);
    $totalSeconds = (int)$secondsPerKm;
    $minutes = (int)($totalSeconds / 60);
    $seconds = (int)($totalSeconds % 60);

    return sprintf("%d:%02d /km", $minutes, $seconds);
}

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


// activity_feed.php = fct pour le formatage de la date des commentaires 
function formatCommentDate(string $datetime): string
{
    $commentDate = new DateTime($datetime);
    $now = new DateTime();

    $today = $now->format('Y-m-d');
    $commentDay = $commentDate->format('Y-m-d');

    if ($commentDay === $today) {
        return 'Today at ' . $commentDate->format('H:i');
    }

    // Optionnel : hier
    $yesterday = (clone $now)->modify('-1 day')->format('Y-m-d');
    if ($commentDay === $yesterday) {
        return 'Yesterday at ' . $commentDate->format('H:i');
    }

    // Autres jours
    return $commentDate->format('F j \a\t H:i');
}
