<?php
include 'db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['uzytkownik_ID'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user's ads from the database, ensuring only one entry per ad and sorting by status
$uzytkownik_ID = $_SESSION['uzytkownik_ID'];
$sql = "SELECT o.ogloszenie_ID, o.tytul, o.cena, o.data_zalozenia, o.status, MIN(z.plik_sciezka) AS plik_sciezka
        FROM ogloszenia o
        LEFT JOIN ogloszenie_zdjecia z ON o.ogloszenie_ID = z.ogloszenie_ID
        WHERE o.uzytkownik_ID = ?
        GROUP BY o.ogloszenie_ID
        ORDER BY FIELD(o.status, 'aktywne', 'oczekujace', 'nieaktywne', 'anulowane przez administratora')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uzytkownik_ID);
$stmt->execute();
$result = $stmt->get_result();

$ads = [];
while ($row = $result->fetch_assoc()) {
    $ads[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMS</title>
    <link rel="stylesheet" href="style.css">

    <script>
        function confirmAction(formId, message) {
            if (confirm(message)) {
                document.getElementById(formId).submit();
            }
        }
    </script>
</head>
<style>
    .container {
        background-color: white;
        padding: 1rem;
        border-radius: 1rem;
    }

    .menu {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        border-bottom: 2px solid #00796b;
    }

    .menu a {
        flex: 1;
        text-align: center;
        color: #00796b;
        padding: 15px 0;
        text-decoration: none;
        transition: background-color 0.3s ease, color 0.3s ease;
        border-right: 1px solid #00796b;
    }

    .menu a:last-child {
        border-right: none;
    }

    .menu a:hover {
        color: #095c52;
        background-color: #e9ecef;
    }

    .my-ads-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        justify-content: center;
        padding: 2rem;
        height: auto;
    }

    .my-ads {
        background-color: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        padding: 1rem;
        max-width: 1200px;
        width: 100%;
    }

    .content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .content img {
        margin-right: 20px;
        max-width: 150px;
        max-height: 150px;
    }

    .content .details {
        display: flex;
        flex-direction: column;
    }

    .content .details h1 {
        margin: 0;
        font-size: 1.5rem;
    }

    .content .details p {
        margin: 5px 0;
    }

    .content .price {
        margin-left: auto;
        font-size: 1.2rem;
        font-weight: bold;
    }

    /* .buttons {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding-top: 10px;
    }

    .buttons button{
        background-color:transparent;
        border-radius:5px;
        border:4px solid #00796b;
        display:inline-block;
        cursor:pointer;
        color:#00796b;
        font-family:Arial;
        font-weight:bold;
        padding:12px 26px;
        text-decoration:none;
    }

    .buttons button:hover {
        background-color:rgb(5, 163, 145);
        border:4px solid rgb(0, 77, 68);
        color: rgb(0, 77, 68);
    }

    .buttons button:active {
        position:relative;
        top:1px;
    } */

    .buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 10px;
    }

    .buttons button {
        padding: 10px 20px;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .delete-button {
        background-color: #e74c3c;
    }

    .delete-button:hover {
        background-color: #c0392b;
    }

    .accept-button {
        background-color: #27ae60;
    }

    .accept-button:hover {
        background-color: #229954;
    }

    .edit-button {
        background-color: #f39c12;
    }

    .edit-button:hover {
        background-color: #e67e22;
    }
</style>
<body>
    <header>
        <div class="container-nav">
            <div class="logo"><a href="index.php">MMS</a></div>
            <div class="user-options">
                <?php if (isset($_SESSION['uzytkownik_ID'])): ?>
                    <a href="konto.php"><button>Konto</button></a>
                    <a href="logout.php"><button>Wyloguj się</button></a>
                    <a href="addAd.php"><button>Dodaj ogłoszenie +</button></a>
                <?php else: ?>
                    <a href="login.php"><button>Zaloguj się</button></a>
                    <a href="register.php"><button>Zarejestruj się</button></a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <h1>Moje ogłoszenia</h1>
            <div class="menu">
                <a href="konto.php">Ogłoszenia</a>
                <a href="czat.php">Czat</a>
                <a href="profil.php">Profil</a>
                <a href="wsparcie.php">Wsparcie</a>
            </div>
        </div>
        <div class="my-ads-container">
            <?php if (count($ads) > 0): ?>
                <?php foreach ($ads as $ad): ?>
                    <div class="my-ads" onclick="window.location.href='szczegoly.php?id=<?php echo $ad['ogloszenie_ID']; ?>'">
                        <div class="content">
                            <?php $image_path = $ad['plik_sciezka'] ?: 'https://via.placeholder.com/150?text=Brak+zdjęcia'; ?>
                            <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Zdjęcie ogłoszenia"/>
                            <div class="details">
                                <h1><?php echo htmlspecialchars($ad['tytul']); ?></h1>
                                <h4>Status: <?php echo htmlspecialchars($ad['status']); ?></h4>
                                <p><?php echo htmlspecialchars($ad['data_zalozenia']); ?></p>
                            </div>
                            <div class="price"><?php echo htmlspecialchars($ad['cena']); ?> PLN</div>
                        </div>
                        <div class="buttons">
                            <button class="edit-button" onclick="event.stopPropagation(); window.location.href='editAd.php?id=<?php echo $ad['ogloszenie_ID']; ?>'">Edytuj</button>
                            <?php if ($ad['status'] == 'aktywne' || $ad['status'] == 'oczekujace'): ?>
                                <button  class="delete-button" onclick="event.stopPropagation(); confirmAction('deactivateForm-<?php echo $ad['ogloszenie_ID']; ?>', 'Czy na pewno chcesz dezaktywować to ogłoszenie?')">Dezaktywuj</button>
                                <form id="deactivateForm-<?php echo $ad['ogloszenie_ID']; ?>" action="deactivate.php" method="POST" style="display: none;">
                                    <input type="hidden" name="id" value="<?php echo $ad['ogloszenie_ID']; ?>">
                                </form>
                            <?php endif; ?>
                            <?php if ($ad['status'] == 'nieaktywne' || $ad['status'] == 'anulowane przez administratora'): ?>
                                <button class="delete-button" type="button" onclick="event.stopPropagation(); confirmAction('deleteForm-<?php echo $ad['ogloszenie_ID']; ?>', 'Czy na pewno chcesz usunąć to ogłoszenie?')">Usuń</button>
                                <form id="deleteForm-<?php echo $ad['ogloszenie_ID']; ?>" action="delete.php" method="POST" style="display: none;">
                                    <input type="hidden" name="id" value="<?php echo $ad['ogloszenie_ID']; ?>">
                                </form>
                            <?php endif; ?>
                            <?php if ($ad['status'] == 'nieaktywne'): ?>
                                <button class="accept-button" onclick="event.stopPropagation(); confirmAction('activateForm-<?php echo $ad['ogloszenie_ID']; ?>', 'Czy na pewno chcesz aktywować to ogłoszenie?')">Aktywuj</button>
                                <form id="activateForm-<?php echo $ad['ogloszenie_ID']; ?>" action="activate.php" method="POST" style="display: none;">
                                    <input type="hidden" name="id" value="<?php echo $ad['ogloszenie_ID']; ?>">
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Brak ogłoszeń do wyświetlenia.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Platforma ogłoszeniowa. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>