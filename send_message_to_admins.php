<?php
include 'db_connect.php';
session_start();

if (!isset($_POST['nadawca_ID']) || !isset($_POST['tresc'])) {
    die('Invalid request');
}

$nadawca_ID = (int)$_POST['nadawca_ID'];
$tresc = mysqli_real_escape_string($conn, $_POST['tresc']);
$odbiorca_ID = 22; 

$query = "INSERT INTO wiadomosci (nadawca_ID, odbiorca_ID, tresc) VALUES ($nadawca_ID, $odbiorca_ID, '$tresc')";
mysqli_query($conn, $query);
?>