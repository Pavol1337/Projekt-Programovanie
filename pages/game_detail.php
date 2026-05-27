<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../index.php');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT g.*, ge.name AS genre_name FROM games g JOIN genres ge ON g.genre_id = ge.id WHERE g.id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$game = mysqli_fetch_assoc($result);

if (!$game) {
    header('Location: ../index.php');
    exit;
}

$pageTitle = htmlspecialchars($game['title']);
$basePath = '../';
include '../includes/header.php';
?>

<div class="detail-page">
    <div class="detail-back">
        <a href="../index.php">← Späť na zoznam</a>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <div>
                <h1><?= htmlspecialchars($game['title']) ?></h1>
                <div class="detail-meta">
                    <span class="badge <?= statusBadge($game['status']) ?>"><?= htmlspecialchars($game['status']) ?></span>
                    <span class="platform-tag"><?= htmlspecialchars($game['platform']) ?></span>
                    <span class="genre-tag"><?= htmlspecialchars($game['genre_name']) ?></span>
                </div>
            </div>
            <div class="detail-rating">
                <?= renderRating($game['rating']) ?>
                <?php if ($game['rating']): ?>
                    <div class="rating-number"><?= $game['rating'] ?>/10</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-info">
            <div class="info-item">
                <span class="info-label">Rok vydania</span>
                <span class="info-value"><?= $game['release_year'] ?? '—' ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Pridané</span>
                <span class="info-value"><?= date('d.m.Y', strtotime($game['added_at'])) ?></span>
            </div>
        </div>

        <?php if (!empty($game['note'])): ?>
            <div class="detail-note">
                <h3>Moja poznámka</h3>
                <p><?= nl2br(htmlspecialchars($game['note'])) ?></p>
            </div>
        <?php endif; ?>

        <div class="detail-actions">
            <a href="edit_game.php?id=<?= $game['id'] ?>" class="btn btn-primary">✏️ Upraviť</a>
            <a href="delete_game.php?id=<?= $game['id'] ?>" class="btn btn-delete"
               onclick="return confirm('Naozaj vymazať?')">🗑️ Vymazať</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
