<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['uzytkownik_ID'])) {
    echo json_encode(['message' => 'Musisz być zalogowany, aby zmienić uprawnienia.']);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(['message' => 'Brak wymaganego identyfikatora użytkownika.']);
    exit;
}

$userId = intval($_POST['id']);
$sql = "SELECT rodzaj_konta FROM uzytkownicy WHERE uzytkownik_ID = $userId";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$newRole = ($user['rodzaj_konta'] === 'admin') ? 'uzytkownik' : 'admin';

$sql = "UPDATE uzytkownicy SET rodzaj_konta = ? WHERE uzytkownik_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $newRole, $userId);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Uprawnienia użytkownika zostały zmienione.']);
} else {
    echo json_encode(['message' => 'Nie udało się zmienić uprawnień użytkownika.', 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>