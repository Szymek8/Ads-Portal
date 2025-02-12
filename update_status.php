<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['uzytkownik_ID'])) {
    echo json_encode(['message' => 'Musisz być zalogowany, aby zmienić status ogłoszenia.']);
    exit;
}

if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo json_encode(['message' => 'Brak wymaganych danych.']);
    exit;
}

$ogloszenie_ID = intval($_POST['id']);
$status = $_POST['status'];

$sql = "UPDATE ogloszenia SET status = ? WHERE ogloszenie_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $status, $ogloszenie_ID);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Status ogłoszenia został zaktualizowany.']);
} else {
    echo json_encode(['message' => 'Nie udało się zaktualizować statusu ogłoszenia.', 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>