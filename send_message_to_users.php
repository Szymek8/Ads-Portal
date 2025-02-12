<?php
include 'db_connect.php';
session_start();

if (!isset($_POST['odbiorca_ID']) || !isset($_POST['tresc'])) {
    die('Invalid request');
}

$nadawca_ID = 22; 
$odbiorca_ID = (int)$_POST['odbiorca_ID'];
$tresc = mysqli_real_escape_string($conn, $_POST['tresc']);

$query = "INSERT INTO wiadomosci (nadawca_ID, odbiorca_ID, tresc) VALUES ($nadawca_ID, $odbiorca_ID, '$tresc')";
mysqli_query($conn, $query);
?>