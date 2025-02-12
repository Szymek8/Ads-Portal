<?php
include 'db_connect.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: kategorie.php");
    exit();
}

$id = $_GET['id'];

$sql = "SELECT plik_sciezka FROM kategorie WHERE kategoria_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($plik_sciezka);
$stmt->fetch();
$stmt->close();


if ($plik_sciezka) {
    $file_path = "img/categories/" . $plik_sciezka;
    if (file_exists($file_path)) {
        unlink($file_path); 
    }
}

$sql = "DELETE FROM kategorie WHERE kategoria_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: kategorie.php");
exit();
?>