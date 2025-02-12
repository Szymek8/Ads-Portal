<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['uzytkownik_ID'])) {
    echo json_encode(['message' => 'Musisz być zalogowany, aby usunąć użytkownika.']);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(['message' => 'Brak wymaganego identyfikatora użytkownika.']);
    exit;
}

$userId = intval($_POST['id']);

$sql = "DELETE FROM wiadomosci WHERE nadawca_ID = ? OR odbiorca_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $userId, $userId);
$stmt->execute();

$sql = "DELETE FROM zgloszenia WHERE uzytkownik_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();


$sql = "DELETE FROM ogloszenia WHERE uzytkownik_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();

$sql = "DELETE FROM uzytkownicy WHERE uzytkownik_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Użytkownik został usunięty.']);
} else {
    echo json_encode(['message' => 'Nie udało się usunąć użytkownika.', 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>