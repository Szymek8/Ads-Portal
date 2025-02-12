<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['uzytkownik_ID'])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST['id'])) {
    $ogloszenie_ID = $_POST['id'];
    $uzytkownik_ID = $_SESSION['uzytkownik_ID'];


    $sql = "UPDATE ogloszenia SET status = 'nieaktywne' WHERE ogloszenie_ID = ? AND uzytkownik_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $ogloszenie_ID, $uzytkownik_ID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {

        header("Location: konto.php?message=Ad deactivated successfully");
    } else {

        header("Location: konto.php?message=Failed to deactivate ad");
    }

    $stmt->close();
} else {
    header("Location: konto.php?message=No ad ID provided");
}

$conn->close();
?>