<?php
include 'db_connect.php';
session_start();

if (!isset($_POST['odbiorca_ID'])) {
    die('Invalid request');
}

$admin_ID = 22; 
$odbiorca_ID = (int)$_POST['odbiorca_ID'];

$query = "
    SELECT w.*, u.nazwa 
    FROM wiadomosci w 
    JOIN uzytkownicy u ON w.nadawca_ID = u.uzytkownik_ID 
    WHERE (w.odbiorca_ID = $admin_ID AND w.nadawca_ID = $odbiorca_ID)
       OR (w.nadawca_ID = $admin_ID AND w.odbiorca_ID = $odbiorca_ID)
    ORDER BY w.data_wyslania ASC";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $class = ($row['nadawca_ID'] == $admin_ID) ? 'user' : 'receiver';
    echo '<div class="message ' . $class . '">';
    echo '<p>' . htmlspecialchars($row['tresc'], ENT_QUOTES) . '</p>';
    echo '</div>';
}
?>