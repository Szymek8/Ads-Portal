<?php
include 'db_connect.php';
session_start();

if (!isset($_POST['odbiorca_ID']) || !isset($_SESSION['uzytkownik_ID'])) {
    die('Invalid request');
}

$odbiorca_ID = (int)$_POST['odbiorca_ID'];
$uzytkownik_ID = (int)$_SESSION['uzytkownik_ID'];

$query = "
    SELECT * FROM wiadomosci 
    WHERE 
        (nadawca_ID = $uzytkownik_ID AND odbiorca_ID = $odbiorca_ID) 
        OR (nadawca_ID = $odbiorca_ID AND odbiorca_ID = $uzytkownik_ID) 
    ORDER BY data_wyslania ASC";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $class = ($row['nadawca_ID'] == $uzytkownik_ID) ? 'user' : 'receiver';
    echo '<div class="message ' . $class . '">';
    echo '<p>' . htmlspecialchars($row['tresc'], ENT_QUOTES) . '</p>';
    echo '</div>';
}
?>