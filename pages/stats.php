<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$pageTitle = 'Štatistiky';
$basePath = '../';

// Celkovy pocet hier
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM games"))['cnt'];

// Pocet podla statusu
$statusRes = mysqli_query($conn, "SELECT status, COUNT(*) as cnt FROM games GROUP BY status");
$statusStats = [];
while ($row = mysqli_fetch_assoc($statusRes)) {
    $statusStats[$row['status']] = $row['cnt'];
}

// Priemerne hodnotenie (len ohodnotene hry)
$avgRes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ROUND(AVG(rating), 1) as avg_rating, COUNT(*) as rated FROM games WHERE rating IS NOT NULL"));

// Najlepsie hodnotena hra
$bestGame = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title, rating FROM games WHERE rating IS NOT NULL ORDER BY rating DESC LIMIT 1"));

// Pocet hier podla zanru
$genreRes = mysqli_query($conn, "
    SELECT ge.name, COUNT(g.id) as cnt
    FROM genres ge
    LEFT JOIN games g ON ge.id = g.genre_id
    GROUP BY ge.id
    ORDER BY cnt DESC
");
$genreStats = mysqli_fetch_all($genreRes, MYSQLI_ASSOC);

// Pocet hier podla platformy
$platformRes = mysqli_query($conn, "SELECT platform, COUNT(*) as cnt FROM games GROUP BY platform ORDER BY cnt DESC");
$platformStats = mysqli_fetch_all($platformRes, MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="stats-page">
    <h1>📊 Štatistiky</h1>

    <!-- Rychle karty -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-number"><?= $total ?></div>
            <div class="stat-label">Celkom hier</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $statusStats['dohraná'] ?? 0 ?></div>
            <div class="stat-label">Dohraných</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $statusStats['hrám'] ?? 0 ?></div>
            <div class="stat-label">Práve hrám</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $avgRes['avg_rating'] ?? '—' ?></div>
            <div class="stat-label">Priemerné hodnotenie</div>
        </div>
    </div>

    <div class="stats-grid">
        <!-- Podla statusu -->
        <div class="stats-block">
            <h2>Podľa statusu</h2>
            <?php
            $allStatuses = ['chcem hrat', 'hrám', 'dohraná', 'nedohraná'];
            foreach ($allStatuses as $s):
                $cnt = $statusStats[$s] ?? 0;
                $pct = $total > 0 ? round(($cnt / $total) * 100) : 0;
            ?>
                <div class="bar-item">
                    <div class="bar-label">
                        <span class="dot dot-<?= statusBadge($s) ?>"></span>
                        <?= htmlspecialchars($s) ?>
                        <span class="bar-count"><?= $cnt ?></span>
                    </div>
                    <div class="bar-track">
                        <div class="bar-fill bar-badge-<?= str_replace('badge-', '', statusBadge($s)) ?>" style="width: <?= max($pct, 3) ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Podla zanru -->
        <div class="stats-block">
            <h2>Podľa žánru</h2>
            <?php foreach ($genreStats as $g):
                $pct = $total > 0 ? round(($g['cnt'] / $total) * 100) : 0;
            ?>
                <div class="bar-item">
                    <div class="bar-label">
                        <?= htmlspecialchars($g['name']) ?>
                        <span class="bar-count"><?= $g['cnt'] ?></span>
                    </div>
                    <div class="bar-track">
                        <div class="bar-fill bar-genre" style="width: <?= $pct ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Podla platformy -->
        <div class="stats-block">
            <h2>Podľa platformy</h2>
            <?php foreach ($platformStats as $p):
                $pct = $total > 0 ? round(($p['cnt'] / $total) * 100) : 0;
            ?>
                <div class="bar-item">
                    <div class="bar-label">
                        <?= htmlspecialchars($p['platform']) ?>
                        <span class="bar-count"><?= $p['cnt'] ?></span>
                    </div>
                    <div class="bar-track">
                        <div class="bar-fill bar-platform" style="width: <?= $pct ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Zaujimavosti -->
        <div class="stats-block">
            <h2>Zaujímavosti</h2>
            <ul class="fun-stats">
                <?php if ($bestGame): ?>
                    <li>🏆 Najlepšia hra: <strong><?= htmlspecialchars($bestGame['title']) ?></strong> (<?= $bestGame['rating'] ?>/10)</li>
                <?php endif; ?>
                <li>⭐ Ohodnotených hier: <strong><?= $avgRes['rated'] ?></strong> z <?= $total ?></li>
                <li>📋 Čaká na zahranie: <strong><?= $statusStats['chcem hrat'] ?? 0 ?></strong> hier</li>
            </ul>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
