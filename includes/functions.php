<?php
// Pomocne funkcie

// Ocistenie vstupu od pouzivatela
function clean($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Ziskanie vsetkych zanerov
function getGenres($conn) {
    $result = mysqli_query($conn, "SELECT * FROM genres ORDER BY name");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Prevod statusu na CSS triedu pre badge
function statusBadge($status) {
    $map = [
        'chcem hrat'  => 'badge-want',
        'hrám'        => 'badge-playing',
        'dohraná'     => 'badge-done',
        'nedohraná'   => 'badge-dropped',
    ];
    return $map[$status] ?? 'badge-default';
}

// Hviezdickove hodnotenie (1-10 -> ikony)
function renderRating($rating) {
    if ($rating === null) return '<span class="no-rating">—</span>';
    $full = floor($rating / 2);
    $half = ($rating % 2 === 1) ? 1 : 0;
    $empty = 5 - $full - $half;
    $html = '';
    for ($i = 0; $i < $full; $i++) $html .= '★';
    if ($half) $html .= '½';
    for ($i = 0; $i < $empty; $i++) $html .= '☆';
    return '<span class="stars" title="' . $rating . '/10">' . $html . '</span>';
}
?>
