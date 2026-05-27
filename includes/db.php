<?php
// Pripojenie k databaze

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gametracker');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Kontrola pripojenia
if (!$conn) {
    die("Chyba pripojenia k databaze: " . mysqli_connect_error());
}

// Nastavenie znakovy set
mysqli_set_charset($conn, 'utf8mb4');
?>
