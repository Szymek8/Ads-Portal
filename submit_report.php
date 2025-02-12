<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['uzytkownik_ID'])) {
    echo json_encode(['message' => 'Musisz być zalogowany, aby zgłosić naruszenie.']);
    exit;
}

if (!isset($_POST['uzytkownik_ID']) || !isset($_POST['ogloszenie_ID']) || !isset($_POST['rodzaj']) || !isset($_POST['opis'])) {
    echo json_encode(['message' => 'Brak wymaganych danych.']);
    exit;
}

$uzytkownik_ID = intval($_POST['uzytkownik_ID']);
$ogloszenie_ID = intval($_POST['ogloszenie_ID']);
$rodzaj = mysqli_real_escape_string($conn, $_POST['rodzaj']);
$opis = mysqli_real_escape_string($conn, $_POST['opis']);

$sql = "INSERT INTO zgloszenia (uzytkownik_ID, ogloszenie_ID, rodzaj, opis) VALUES ($uzytkownik_ID, $ogloszenie_ID, '$rodzaj', '$opis')";
if (mysqli_query($conn, $sql)) {
    echo json_encode(['message' => 'Zgłoszenie zostało wysłane.']);
} else {
    echo json_encode(['message' => 'Nie udało się wysłać zgłoszenia.', 'error' => mysqli_error($conn)]);
}

mysqli_close($conn);
?>