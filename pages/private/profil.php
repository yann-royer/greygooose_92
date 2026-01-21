<?php
$currentPage = 'profile';
require __DIR__ . '/../../partials/layout/header.php';
require_once __DIR__ . '/../../partials/helpers/format.php';

$viewerId = (int) $_SESSION['user_id'];
$profileId = isset($_GET['id']) && ctype_digit($_GET['id'])
    ? (int) $_GET['id']
    : $viewerId;

// Fetch target user
$userStmt = $gd->prepare("
    SELECT id, name, family_name, age, gender, pp
    FROM user
    WHERE id = :id
    LIMIT 1
");
$userStmt->execute(['id' => $profileId]);
$profileUser = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$profileUser) {
    echo '<div class="profile-page"><p>Profil introuvable.</p></div>';
    require __DIR__ . '/../../partials/layout/footer.php';
    exit;
}

$isOwner = $viewerId === (int) $profileUser['id'];

// Relation status
$relationOutgoing = null;
$relationIncoming = null;

if (!$isOwner) {
    $relationOutgoing = $gd->prepare("
        SELECT id, status
        FROM relations
        WHERE user_id = :viewer AND target_id = :target
        LIMIT 1
    ");
    $relationOutgoing->execute([
        'viewer' => $viewerId,
        'target' => $profileId
    ]);
    $relationOutgoing = $relationOutgoing->fetch(PDO::FETCH_ASSOC);

    $relationIncoming = $gd->prepare("
        SELECT id, status
        FROM relations
        WHERE user_id = :target AND target_id = :viewer
        LIMIT 1
    ");
    $relationIncoming->execute([
        'viewer' => $viewerId,
        'target' => $profileId
    ]);
    $relationIncoming = $relationIncoming->fetch(PDO::FETCH_ASSOC);
}

$isFriend = false;
$relationState = 'none'; // none | pending_out | pending_in | friend

if (
    ($relationOutgoing && $relationOutgoing['status'] === 'accepted') ||
    ($relationIncoming && $relationIncoming['status'] === 'accepted')
) {
    $isFriend = true;
    $relationState = 'friend';
} elseif ($relationOutgoing && $relationOutgoing['status'] === 'pending') {
    $relationState = 'pending_out';
} elseif ($relationIncoming && $relationIncoming['status'] === 'pending') {
    $relationState = 'pending_in';
}

// Block profile access if not owner and not friend
if (!$isOwner && !$isFriend) {
    echo '<div class="profile-page"><p>Ce profil est réservé aux amis.</p></div>';
    require __DIR__ . '/../../partials/layout/footer.php';
    exit;
}

// Friends count
$friendsStmt = $gd->prepare("
    SELECT COUNT(DISTINCT CASE
        WHEN r.user_id = :uid THEN r.target_id
        ELSE r.user_id
    END) AS total
    FROM relations r
    WHERE r.status = 'accepted'
      AND (r.user_id = :uid OR r.target_id = :uid)
");
$friendsStmt->execute(['uid' => $profileId]);
$friendsCount = (int) $friendsStmt->fetchColumn();

// Filters
$sports = $gd->query("SELECT id, name FROM sports ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$selectedSport = $_GET['sport'] ?? 'all';
if ($selectedSport !== 'all' && !ctype_digit($selectedSport)) {
    $selectedSport = 'all';
}

$period = $_GET['period'] ?? 'all';
$allowedPeriods = ['week', 'month', 'year', 'all'];
if (!in_array($period, $allowedPeriods, true)) {
    $period = 'all';
}

$periodStart = null;
$now = new DateTime();
switch ($period) {
    case 'week':
        $periodStart = (clone $now)->modify('-7 days')->format('Y-m-d H:i:s');
        break;
    case 'month':
        $periodStart = (clone $now)->modify('-1 month')->format('Y-m-d H:i:s');
        break;
    case 'year':
        $periodStart = (clone $now)->modify('-1 year')->format('Y-m-d H:i:s');
        break;
    default:
        $periodStart = null;
}

// Stats query base
$visibilityCondition = "
    a.user_id = :profile_id
    AND (
        :is_owner = 1
        OR a.visibility = 'public'
        OR (:is_friend = 1 AND a.visibility = 'friends')
    )
";

$statsSql = "
    SELECT
        COUNT(*) AS total_activities,
        COALESCE(SUM(a.distance), 0) AS total_distance,
        COALESCE(SUM(a.dplus), 0) AS total_elevation,
        COALESCE(SUM(a.duration), 0) AS total_time
    FROM activity a
    WHERE $visibilityCondition
";

if ($selectedSport !== 'all') {
    $statsSql .= " AND a.sport = :sport_id";
}

if ($periodStart) {
    $statsSql .= " AND a.date_time >= :period_start";
}

$statsStmt = $gd->prepare($statsSql);
$statsStmt->bindValue(':profile_id', $profileId, PDO::PARAM_INT);
$statsStmt->bindValue(':is_owner', $isOwner ? 1 : 0, PDO::PARAM_INT);
$statsStmt->bindValue(':is_friend', $isFriend ? 1 : 0, PDO::PARAM_INT);

if ($selectedSport !== 'all') {
    $statsStmt->bindValue(':sport_id', (int) $selectedSport, PDO::PARAM_INT);
}

if ($periodStart) {
    $statsStmt->bindValue(':period_start', $periodStart);
}

$statsStmt->execute();
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

// Activities list with pagination
$perPage = 5;

$countSql = "SELECT COUNT(*) FROM activity a WHERE $visibilityCondition";
if ($selectedSport !== 'all') {
    $countSql .= " AND a.sport = :sport_id";
}
if ($periodStart) {
    $countSql .= " AND a.date_time >= :period_start";
}

$countStmt = $gd->prepare($countSql);
$countStmt->bindValue(':profile_id', $profileId, PDO::PARAM_INT);
$countStmt->bindValue(':is_owner', $isOwner ? 1 : 0, PDO::PARAM_INT);
$countStmt->bindValue(':is_friend', $isFriend ? 1 : 0, PDO::PARAM_INT);

if ($selectedSport !== 'all') {
    $countStmt->bindValue(':sport_id', (int) $selectedSport, PDO::PARAM_INT);
}
if ($periodStart) {
    $countStmt->bindValue(':period_start', $periodStart);
}

$countStmt->execute();
$totalActivities = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalActivities / $perPage));

$pageParam = isset($_GET['page']) && ctype_digit($_GET['page']) ? (int) $_GET['page'] : 1;
$currentPageNumber = min(max($pageParam, 1), $totalPages);
$offset = ($currentPageNumber - 1) * $perPage;

$activitiesSql = "
    SELECT
        a.id,
        a.title,
        a.date_time,
        a.distance,
        a.duration,
        a.dplus,
        a.visibility,
        s.name AS sport_name
    FROM activity a
    JOIN sports s ON s.id = a.sport
    WHERE $visibilityCondition
";

if ($selectedSport !== 'all') {
    $activitiesSql .= " AND a.sport = :sport_id";
}
if ($periodStart) {
    $activitiesSql .= " AND a.date_time >= :period_start";
}

$activitiesSql .= " ORDER BY a.date_time DESC LIMIT :limit OFFSET :offset";

$activitiesStmt = $gd->prepare($activitiesSql);
$activitiesStmt->bindValue(':profile_id', $profileId, PDO::PARAM_INT);
$activitiesStmt->bindValue(':is_owner', $isOwner ? 1 : 0, PDO::PARAM_INT);
$activitiesStmt->bindValue(':is_friend', $isFriend ? 1 : 0, PDO::PARAM_INT);

if ($selectedSport !== 'all') {
    $activitiesStmt->bindValue(':sport_id', (int) $selectedSport, PDO::PARAM_INT);
}
if ($periodStart) {
    $activitiesStmt->bindValue(':period_start', $periodStart);
}

$activitiesStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$activitiesStmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$activitiesStmt->execute();
$activities = $activitiesStmt->fetchAll(PDO::FETCH_ASSOC);

// Helpers
$profilePic = !empty($profileUser['pp'])
    ? $profileUser['pp']
    : BASE_URL . '/uploads/pp/default.webp';

$genderLabel = 'Not specified';
if ($profileUser['gender'] !== null) {
    $genderLabel = ((string) $profileUser['gender'] === '1') ? 'Female' : 'Male';
}

$ageLabel = $profileUser['age'] !== null ? (int) $profileUser['age'] . ' years' : 'Age not set';
$fullName = trim(($profileUser['name'] ?? '') . ' ' . ($profileUser['family_name'] ?? ''));
$redirectUrl = htmlspecialchars($_SERVER['REQUEST_URI'] ?? (BASE_URL . '/pages/private/profil.php?id=' . $profileId), ENT_QUOTES);
?>

<div class="profile-page">
    <section class="profile-header">
        <div class="profile-identity">
            <img
                src="<?= htmlspecialchars($profilePic) ?>"
                alt="Photo de profil"
                class="profile-avatar">
            <div>
                <h1><?= htmlspecialchars($fullName ?: 'Utilisateur') ?></h1>
                <p class="profile-meta">
                    <?= htmlspecialchars($ageLabel) ?> • <?= htmlspecialchars($genderLabel) ?>
                </p>
                <p class="profile-meta">
                    <?= $friendsCount ?> friend<?= $friendsCount > 1 ? 's' : '' ?>
                </p>
            </div>
        </div>

        <?php if ($isOwner): ?>
            <div class="profile-actions">
                <a class="btn-primary" href="<?= BASE_URL ?>/pages/private/register3.php">
                    Modify profile
                </a>
            </div>
        <?php else: ?>
            <form
                class="profile-actions"
                method="POST"
                action="<?= BASE_URL ?>/partials/social/relation_action.php">
                <input type="hidden" name="target_id" value="<?= $profileId ?>">
                <input type="hidden" name="redirect" value="<?= $redirectUrl ?>">

                <?php if ($relationState === 'friend'): ?>
                    <button type="submit" name="action" value="remove" class="btn-secondary">
                        Remove friend
                    </button>
                <?php elseif ($relationState === 'pending_out'): ?>
                    <button type="submit" name="action" value="unsend" class="btn-secondary">
                        Unsend request
                    </button>
                <?php elseif ($relationState === 'pending_in'): ?>
                    <button type="submit" name="action" value="accept" class="btn-primary">
                        Accept request
                    </button>
                    <button type="submit" name="action" value="decline" class="btn-secondary">
                        Decline
                    </button>
                <?php else: ?>
                    <button type="submit" name="action" value="request" class="btn-primary">
                        Request friend
                    </button>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </section>

    <section class="profile-stats">
        <div class="profile-filters">
            <form method="GET" class="filters-form">
                <input type="hidden" name="id" value="<?= $profileId ?>">
                <div class="filter-group">
                    <label for="sport">Sport</label>
                    <select name="sport" id="sport">
                        <option value="all" <?= $selectedSport === 'all' ? 'selected' : '' ?>>
                            Overall
                        </option>
                        <?php foreach ($sports as $sport): ?>
                            <option
                                value="<?= $sport['id'] ?>"
                                <?= $selectedSport == $sport['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sport['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="period">Period</label>
                    <select name="period" id="period">
                        <option value="all" <?= $period === 'all' ? 'selected' : '' ?>>Total</option>
                        <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>Last 7 days</option>
                        <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>Last month</option>
                        <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>Last year</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary btn-compact">Update</button>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <span>Activities</span>
                <strong><?= (int) $stats['total_activities'] ?></strong>
            </div>
            <div class="stat-card">
                <span>Total distance</span>
                <strong><?= number_format((float) $stats['total_distance'], 2) ?> km</strong>
            </div>
            <div class="stat-card">
                <span>Total elevation</span>
                <strong><?= (int) $stats['total_elevation'] ?> m</strong>
            </div>
            <div class="stat-card">
                <span>Total time</span>
                <strong><?= formatDuration((int) $stats['total_time']) ?></strong>
            </div>
        </div>
    </section>

    <section class="profile-activities">
        <div class="activities-header">
            <h2>Activities</h2>
            <p class="activities-subtitle">
                Showing <?= $totalActivities ?> activit<?= $totalActivities > 1 ? 'ies' : 'y' ?> (<?= $perPage ?> per page)
            </p>
        </div>

        <?php if (empty($activities)): ?>
            <p class="muted">No activity available for this selection.</p>
        <?php else: ?>
            <?php foreach ($activities as $activity): ?>
                <article class="activity-card profile-activity-card">
                    <header class="profile-activity-header">
                        <div>
                            <h3><?= htmlspecialchars($activity['title'] ?: $activity['sport_name']) ?></h3>
                            <p class="activity-date">
                                <?= formatActivityDate($activity['date_time']) ?>
                                • <?= htmlspecialchars($activity['sport_name']) ?>
                            </p>
                        </div>
                        <span class="badge visibility">
                            <?= htmlspecialchars($activity['visibility']) ?>
                        </span>
                    </header>

                    <ul class="activity-data profile-activity-data">
                        <li><strong>Distance:</strong> <?= htmlspecialchars($activity['distance']) ?> km</li>
                        <li><strong>Time:</strong> <?= formatDuration((int) $activity['duration']) ?></li>
                        <li><strong>D+:</strong> <?= (int) $activity['dplus'] ?> m</li>
                    </ul>

                    <div class="profile-activity-actions">
                        <a
                            class="btn-link"
                            href="<?= BASE_URL ?>/pages/private/activity_view.php?id=<?= $activity['id'] ?>">
                            View details
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php
                $baseQuery = [
                    'id' => $profileId,
                    'sport' => $selectedSport,
                    'period' => $period
                ];
                ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                    $query = http_build_query(array_merge($baseQuery, ['page' => $i]));
                    $url = BASE_URL . '/pages/private/profil.php?' . $query;
                    ?>
                    <?php if ($i === $currentPageNumber): ?>
                        <span class="page-item active"><?= $i ?></span>
                    <?php else: ?>
                        <a class="page-item" href="<?= $url ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php require __DIR__ . '/../../partials/layout/footer.php'; ?>
