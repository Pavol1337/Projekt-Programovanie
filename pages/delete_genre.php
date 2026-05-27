<?php
require_once '../includes/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: genres.php');
    exit;
}

// Kontrola ci na zaner su priradene hry
$check = mysqli_prepare($conn, "SELECT COUNT(*) as cnt FROM games WHERE genre_id = ?");
mysqli_stmt_bind_param($check, 'i', $id);
mysqli_stmt_execute($check);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($check));

if ($row['cnt'] > 0) {
    // Nemoze vymazat lebo su priradene hry
    header('Location: genres.php?msg=error');
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM genres WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    header('Location: genres.php?msg=genre_deleted');
} else {
    header('Location: genres.php?msg=error');
}
exit;
?>
