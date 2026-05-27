<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$pageTitle = 'Žánre';
$basePath = '../';

$errors = [];

// Pridanie zanru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = clean($_POST['name'] ?? '');
    $desc = clean($_POST['description'] ?? '');

    if (empty($name)) {
        $errors[] = 'Názov žánru je povinný!';
    } elseif (strlen($name) > 50) {
        $errors[] = 'Názov je príliš dlhý (max 50 znakov).';
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO genres (name, description) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'ss', $name, $desc);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: genres.php?msg=genre_added');
            exit;
        }
    }
}

// Nacitanie vsetkych zanerov s poctom hier
$result = mysqli_query($conn, "
    SELECT ge.*, COUNT(g.id) as game_count
    FROM genres ge
    LEFT JOIN games g ON ge.id = g.genre_id
    GROUP BY ge.id
    ORDER BY ge.name
");
$genres = mysqli_fetch_all($result, MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="form-page">
    <div class="page-header">
        <h1>🎭 Správa žánrov</h1>
    </div>

    <div class="genres-layout">

        <!-- Formular na pridanie -->
        <div class="form-card form-card-small">
            <h2>Pridať žáner</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $err): ?><p><?= $err ?></p><?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="genres.php">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="name">Názov *</label>
                    <input type="text" id="name" name="name" maxlength="50"
                           placeholder="napr. MOBA" required>
                </div>
                <div class="form-group">
                    <label for="description">Popis</label>
                    <input type="text" id="description" name="description"
                           maxlength="255" placeholder="Krátky popis žánru">
                </div>
                <button type="submit" class="btn btn-primary">Pridať</button>
            </form>
        </div>

        <!-- Zoznam zanerov -->
        <div class="genres-list">
            <table class="table">
                <thead>
                    <tr>
                        <th>Žáner</th>
                        <th>Popis</th>
                        <th>Počet hier</th>
                        <th>Akcia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($genres as $g): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($g['name']) ?></strong></td>
                            <td><?= htmlspecialchars($g['description'] ?? '—') ?></td>
                            <td><span class="count-badge"><?= $g['game_count'] ?></span></td>
                            <td>
                                <?php if ($g['game_count'] == 0): ?>
                                    <a href="delete_genre.php?id=<?= $g['id'] ?>"
                                       class="btn btn-sm btn-delete"
                                       onclick="return confirm('Vymazať žáner \'<?= htmlspecialchars($g['name']) ?>\'?')">
                                        Vymazať
                                    </a>
                                <?php else: ?>
                                    <span class="btn btn-sm btn-disabled" title="Nemôžeš vymazať žáner, ktorý má priradené hry">
                                        Vymazať
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
