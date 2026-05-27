<?php
require_once '../includes/db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: ../index.php');
    exit;
}

// Kontrola ci hra existuje pred vymazanim
$check = mysqli_prepare($conn, "SELECT id FROM games WHERE id = ?");
mysqli_stmt_bind_param($check, 'i', $id);
mysqli_stmt_execute($check);
$checkResult = mysqli_stmt_get_result($check);

if (!mysqli_fetch_assoc($checkResult)) {
    header('Location: ../index.php?msg=error');
    exit;
}

// Vymazanie
$stmt = mysqli_prepare($conn, "DELETE FROM games WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    header('Location: ../index.php?msg=deleted');
} else {
    header('Location: ../index.php?msg=error');
}
exit;
?>
