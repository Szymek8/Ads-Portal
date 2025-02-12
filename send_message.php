<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['uzytkownik_ID'])) {
    echo json_encode(['message' => 'Musisz być zalogowany, aby wysłać wiadomość.']);
    exit;
}

if (!isset($_POST['nadawca_ID']) || !isset($_POST['odbiorca_ID']) || !isset($_POST['tresc'])) {
    echo json_encode(['message' => 'Brak wymaganych danych.']);
    exit;
}

$nadawca_ID = intval($_POST['nadawca_ID']);
$odbiorca_ID = intval($_POST['odbiorca_ID']);
$tresc = mysqli_real_escape_string($conn, $_POST['tresc']);

$sql = "INSERT INTO wiadomosci (nadawca_ID, odbiorca_ID, tresc) VALUES ($nadawca_ID, $odbiorca_ID, '$tresc')";
if (mysqli_query($conn, $sql)) {
    echo json_encode(['message' => 'Wiadomość została wysłana.']);
} else {
    echo json_encode(['message' => 'Nie udało się wysłać wiadomości.']);
}

mysqli_close($conn);
?>