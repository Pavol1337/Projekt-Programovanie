<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pageTitle = 'Zoznam hier';
$basePath = '';

// Filter podla statusu alebo zanru
$where = [];
$params = [];
$types = '';

if (!empty($_GET['status'])) {
    $status = clean($_GET['status']);
    $where[] = "g.status = ?";
    $params[] = $status;
    $types .= 's';
}

if (!empty($_GET['genre_id'])) {
    $genreId = (int)$_GET['genre_id'];
    $where[] = "g.genre_id = ?";
    $params[] = $genreId;
    $types .= 'i';
}

if (!empty($_GET['search'])) {
    $search = '%' . clean($_GET['search']) . '%';
    $where[] = "g.title LIKE ?";
    $params[] = $search;
    $types .= 's';
}

$whereSQL = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT g.*, ge.name AS genre_name
        FROM games g
        JOIN genres ge ON g.genre_id = ge.id
        $whereSQL
        ORDER BY g.added_at DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$games = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Ziskanie poctu hier podla statusu (pre sidebar)
$countResult = mysqli_query($conn, "SELECT status, COUNT(*) as cnt FROM games GROUP BY status");
$counts = [];
while ($row = mysqli_fetch_assoc($countResult)) {
    $counts[$row['status']] = $row['cnt'];
}

$totalGames = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM games"))['total'];

$genres = getGenres($conn);

include 'includes/header.php';
?>

<div class="page-layout">

    <!-- Sidebar s filtrami -->
    <aside class="sidebar">
        <div class="sidebar-block">
            <h3>Filtrovať</h3>
            <form method="GET" action="index.php">
                <div class="form-group">
                    <label>Hľadať</label>
                    <input type="text" name="search" placeholder="Názov hry..."
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="input-sm">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="input-sm">
                        <option value="">Všetky</option>
                        <option value="chcem hrat" <?= ($_GET['status'] ?? '') === 'chcem hrat' ? 'selected' : '' ?>>Chcem hrať</option>
                        <option value="hrám" <?= ($_GET['status'] ?? '') === 'hrám' ? 'selected' : '' ?>>Hrám</option>
                        <option value="dohraná" <?= ($_GET['status'] ?? '') === 'dohraná' ? 'selected' : '' ?>>Dohraná</option>
                        <option value="nedohraná" <?= ($_GET['status'] ?? '') === 'nedohraná' ? 'selected' : '' ?>>Nedohraná</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Žáner</label>
                    <select name="genre_id" class="input-sm">
                        <option value="">Všetky</option>
                        <?php foreach ($genres as $g): ?>
                            <option value="<?= $g['id'] ?>"
                                <?= (int)($_GET['genre_id'] ?? 0) === (int)$g['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Hľadať</button>
                <a href="index.php" class="btn btn-ghost btn-sm">Reset</a>
            </form>
        </div>

        <div class="sidebar-block">
            <h3>Prehľad</h3>
            <ul class="status-list">
                <li><span class="dot dot-want"></span> Chcem hrať: <strong><?= $counts['chcem hrat'] ?? 0 ?></strong></li>
                <li><span class="dot dot-playing"></span> Hrám: <strong><?= $counts['hrám'] ?? 0 ?></strong></li>
                <li><span class="dot dot-done"></span> Dohraná: <strong><?= $counts['dohraná'] ?? 0 ?></strong></li>
                <li><span class="dot dot-dropped"></span> Nedohraná: <strong><?= $counts['nedohraná'] ?? 0 ?></strong></li>
            </ul>
            <p class="total-count">Celkom: <strong><?= $totalGames ?></strong> hier</p>
        </div>
    </aside>

    <!-- Hlavny obsah -->
    <main class="main-content">
        <div class="page-header">
            <h1>Moje hry</h1>
            <a href="pages/add_game.php" class="btn btn-primary">+ Pridať hru</a>
        </div>

        <?php if (count($games) === 0): ?>
            <div class="empty-state">
                <p>😕 Žiadne hry nenájdené.</p>
                <a href="index.php">Zobraziť všetky</a>
            </div>
        <?php else: ?>
            <div class="games-count">Nájdených: <strong><?= count($games) ?></strong> hier</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Názov</th>
                        <th>Žáner</th>
                        <th>Platforma</th>
                        <th>Rok</th>
                        <th>Status</th>
                        <th>Hodnotenie</th>
                        <th>Akcie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($games as $game): ?>
                        <tr>
                            <td>
                                <a href="pages/game_detail.php?id=<?= $game['id'] ?>" class="game-title-link">
                                    <?= htmlspecialchars($game['title']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($game['genre_name']) ?></td>
                            <td><span class="platform-tag"><?= htmlspecialchars($game['platform']) ?></span></td>
                            <td><?= $game['release_year'] ?? '—' ?></td>
                            <td><span class="badge <?= statusBadge($game['status']) ?>"><?= htmlspecialchars($game['status']) ?></span></td>
                            <td><?= renderRating($game['rating']) ?></td>
                            <td class="actions">
                                <a href="pages/edit_game.php?id=<?= $game['id'] ?>" class="btn btn-sm btn-edit">Upraviť</a>
                                <a href="pages/delete_game.php?id=<?= $game['id'] ?>"
                                   class="btn btn-sm btn-delete"
                                   onclick="return confirm('Naozaj vymazať hru \'<?= htmlspecialchars($game['title']) ?>\'?')">Vymazať</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

</div>

<?php include 'includes/footer.php'; ?>
