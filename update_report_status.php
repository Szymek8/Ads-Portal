<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['uzytkownik_ID'])) {
    echo json_encode(['message' => 'Musisz być zalogowany, aby zarządzać zgłoszeniami.']);
    exit;
}

if (!isset($_POST['report_id']) || !isset($_POST['action'])) {
    echo json_encode(['message' => 'Brak wymaganego identyfikatora zgłoszenia lub akcji.']);
    exit;
}

$reportId = intval($_POST['report_id']);
$action = $_POST['action'];

if ($action === 'accept' && isset($_POST['ad_id'])) {
    $adId = intval($_POST['ad_id']);
    $sql = "UPDATE ogloszenia SET status = 'anulowane przez administratora' WHERE ogloszenie_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $adId);
    $stmt->execute();
}

$sql = "DELETE FROM zgloszenia WHERE zgloszenie_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $reportId);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Zgłoszenie zostało przetworzone.']);
} else {
    echo json_encode(['message' => 'Nie udało się przetworzyć zgłoszenia.', 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>