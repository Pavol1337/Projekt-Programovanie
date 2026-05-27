<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameTracker <?= isset($pageTitle) ? '– ' . $pageTitle : '' ?></title>
    <link rel="stylesheet" href="<?= $basePath ?? '' ?>assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="<?= $basePath ?? '' ?>index.php" class="nav-logo">
        <span class="logo-icon">🎮</span>
        <span class="logo-text">GameTracker</span>
    </a>
    <ul class="nav-links">
        <li><a href="<?= $basePath ?? '' ?>index.php">Zoznam hier</a></li>
        <li><a href="<?= $basePath ?? '' ?>pages/add_game.php">+ Pridať hru</a></li>
        <li><a href="<?= $basePath ?? '' ?>pages/genres.php">Žánre</a></li>
        <li><a href="<?= $basePath ?? '' ?>pages/stats.php">Štatistiky</a></li>
    </ul>
</nav>

<div class="container">
<?php if (isset($_GET['msg'])): ?>
    <?php
    $msgType = 'success';
    $msgText = '';
    switch ($_GET['msg']) {
        case 'added':    $msgText = '✅ Hra bola úspešne pridaná!'; break;
        case 'updated':  $msgText = '✅ Hra bola úspešne upravená!'; break;
        case 'deleted':  $msgText = '🗑️ Hra bola vymazaná.'; $msgType = 'warning'; break;
        case 'genre_added':   $msgText = '✅ Žáner bol pridaný!'; break;
        case 'genre_deleted': $msgText = '🗑️ Žáner bol vymazaný.'; $msgType = 'warning'; break;
        case 'error':    $msgText = '❌ Nastala chyba, skús znova.'; $msgType = 'error'; break;
    }
    ?>
    <div class="alert alert-<?= $msgType ?>"><?= $msgText ?></div>
<?php endif; ?>
