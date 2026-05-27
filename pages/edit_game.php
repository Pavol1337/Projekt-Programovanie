<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$pageTitle = 'Upraviť hru';
$basePath = '../';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../index.php');
    exit;
}

// Nacitanie existujucich dat hry
$stmt = mysqli_prepare($conn, "SELECT * FROM games WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$game = mysqli_fetch_assoc($result);

if (!$game) {
    header('Location: ../index.php?msg=error');
    exit;
}

$errors = [];
$formData = $game; // predvyplnenie formulara existujucimi datami

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $formData['title'] = clean($_POST['title'] ?? '');
    $formData['genre_id'] = (int)($_POST['genre_id'] ?? 0);
    $formData['platform'] = clean($_POST['platform'] ?? '');
    $formData['release_year'] = clean($_POST['release_year'] ?? '');
    $formData['status'] = clean($_POST['status'] ?? '');
    $formData['rating'] = $_POST['rating'] !== '' ? (int)$_POST['rating'] : null;
    $formData['note'] = clean($_POST['note'] ?? '');

    // Validacia – rovnaka ako pri pridavani
    if (empty($formData['title'])) {
        $errors[] = 'Názov hry je povinný!';
    }
    if ($formData['genre_id'] <= 0) {
        $errors[] = 'Vyber žáner!';
    }
    if (empty($formData['platform'])) {
        $errors[] = 'Platforma je povinná!';
    }
    if (!empty($formData['release_year'])) {
        $year = (int)$formData['release_year'];
        if ($year < 1970 || $year > 2030) {
            $errors[] = 'Rok vydania musí byť medzi 1970 a 2030.';
        }
    }

    if (empty($errors)) {
        $yearVal = !empty($formData['release_year']) ? (int)$formData['release_year'] : null;

        $sql = "UPDATE games SET title=?, genre_id=?, platform=?, release_year=?, status=?, rating=?, note=?
                WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sissisii',
            $formData['title'],
            $formData['genre_id'],
            $formData['platform'],
            $yearVal,
            $formData['status'],
            $formData['rating'],
            $formData['note'],
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header('Location: ../index.php?msg=updated');
            exit;
        } else {
            $errors[] = 'Chyba pri ukladaní.';
        }
    }
}

$genres = getGenres($conn);
include '../includes/header.php';
?>

<div class="form-page">
    <div class="form-card">
        <h1>✏️ Upraviť hru</h1>
        <p class="form-subtitle">Upravuješ: <strong><?= htmlspecialchars($game['title']) ?></strong></p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= $err ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="edit_game.php?id=<?= $id ?>">

            <div class="form-group">
                <label for="title">Názov hry *</label>
                <input type="text" id="title" name="title"
                       value="<?= htmlspecialchars($formData['title']) ?>"
                       maxlength="150" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="genre_id">Žáner *</label>
                    <select id="genre_id" name="genre_id" required>
                        <option value="">-- Vyber žáner --</option>
                        <?php foreach ($genres as $g): ?>
                            <option value="<?= $g['id'] ?>"
                                <?= $formData['genre_id'] == $g['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="platform">Platforma *</label>
                    <select id="platform" name="platform" required>
                        <?php
                        $platforms = ['PC', 'PS5', 'PS4', 'Xbox Series X', 'Xbox One', 'Nintendo Switch', 'Mobile'];
                        foreach ($platforms as $p):
                        ?>
                            <option value="<?= $p ?>" <?= $formData['platform'] === $p ? 'selected' : '' ?>>
                                <?= $p ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="release_year">Rok vydania</label>
                    <input type="number" id="release_year" name="release_year"
                           value="<?= htmlspecialchars($formData['release_year'] ?? '') ?>"
                           min="1970" max="2030">
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <option value="chcem hrat" <?= $formData['status'] === 'chcem hrat' ? 'selected' : '' ?>>Chcem hrať</option>
                        <option value="hrám" <?= $formData['status'] === 'hrám' ? 'selected' : '' ?>>Hrám</option>
                        <option value="dohraná" <?= $formData['status'] === 'dohraná' ? 'selected' : '' ?>>Dohraná</option>
                        <option value="nedohraná" <?= $formData['status'] === 'nedohraná' ? 'selected' : '' ?>>Nedohraná</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="rating">Hodnotenie (1–10)</label>
                <input type="number" id="rating" name="rating"
                       value="<?= $formData['rating'] !== null ? $formData['rating'] : '' ?>"
                       min="1" max="10">
            </div>

            <div class="form-group">
                <label for="note">Poznámka</label>
                <textarea id="note" name="note" rows="3"><?= htmlspecialchars($formData['note'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Uložiť zmeny</button>
                <a href="../index.php" class="btn btn-ghost">Zrušiť</a>
            </div>

        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
