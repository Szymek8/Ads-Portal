<?php
include 'db_connect.php';
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMS</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

.buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.buttons button {
    padding: 10px 20px;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.accept-button {
    background-color: #00796b;
}

.accept-button:hover {
    background-color: #005f5b;
}

.reject-button {
    background-color: #e74c3c;
}

.reject-button:hover {
    background-color: #c0392b;
}
.view-button {
    background-color: #4CAF50; 
    color: white;
    border: none;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.view-button:hover {
    background-color: #45a049;
}

.accept-button {
    background-color: #008CBA; 
    color: white;
    border: none;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.accept-button:hover {
    background-color: #007bb5;
}

.reject-button {
    background-color: #f44336;
    color: white;
    border: none;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.reject-button:hover {
    background-color: #da190b;
}
    </style>
</head>
<body>
    <header>
        <div class="container-nav">
            <div class="logo"><a href="index.php">MMS</a></div>
            <div class="user-options">
                <?php if (isset($_SESSION['uzytkownik_ID'])): ?>
                    <?php
                    $userId = $_SESSION['uzytkownik_ID'];
                    $sql = "SELECT rodzaj_konta FROM uzytkownicy WHERE uzytkownik_ID = $userId";
                    $result = $conn->query($sql);
                    $user = $result->fetch_assoc();
                    ?>
                    <?php if ($user['rodzaj_konta'] == 'admin'): ?>
                        <a href="adminpanel.php"><button>Admin Panel</button></a>
                    <?php endif; ?>
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
            <h1>Ogłoszenia do weryfikacji</h1>
            <div class="menu">
                <a href="adminpanel.php">Zarządzaj ogłoszeniami</a>
                <a href="uzytkownicy.php">Zarządzaj użytkownikami</a>
                <a href="kategorie.php">Zarządzaj kategoriami</a>
                <a href="adminczat.php">Czat</a>
                <a href="zgloszenia.php">Zarządzaj zgłoszeniami</a>
            </div>
            <div class="my-ads-container">
                <?php
                $sql = "SELECT 
                            ogloszenia.ogloszenie_ID, 
                            ogloszenia.tytul, 
                            ogloszenia.cena, 
                            ogloszenia.miasto, 
                            ogloszenia.data_zalozenia, 
                            MIN(ogloszenie_zdjecia.plik_sciezka) AS plik_sciezka 
                        FROM 
                            ogloszenia 
                        LEFT JOIN 
                            ogloszenie_zdjecia 
                        ON 
                            ogloszenia.ogloszenie_ID = ogloszenie_zdjecia.ogloszenie_ID
                        WHERE 
                            ogloszenia.status = 'oczekujace'
                        GROUP BY 
                            ogloszenia.ogloszenie_ID, 
                            ogloszenia.tytul, 
                            ogloszenia.cena, 
                            ogloszenia.miasto, 
                            ogloszenia.data_zalozenia";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='my-ads'>";
                        echo "<div class='content'>";
                        echo '<img src="' . htmlspecialchars($row['plik_sciezka']) . '" alt="Zdjęcie ogłoszenia"/>';
                        echo "<div class='details'>";
                        echo "<h1>" . htmlspecialchars($row['tytul']) . "</h1>";
                        echo "<h4>Status: oczekujące</h4>";
                        echo "<p>Data dodania: " . htmlspecialchars($row['data_zalozenia']) . "</p>";
                        echo "</div>";
                        echo "<div class='price'>Cena: " . htmlspecialchars($row['cena']) . " PLN</div>";
                        echo "</div>";
                        echo "<div class='buttons'>";
                        echo "<button class='accept-button' data-id='" . htmlspecialchars($row['ogloszenie_ID']) . "'>Akceptuj</button>";
                        echo "<button class='reject-button' data-id='" . htmlspecialchars($row['ogloszenie_ID']) . "'>Odrzuć</button>";
                        echo "<a href='szczegoly.php?id=" . htmlspecialchars($row['ogloszenie_ID']) . "'><button class='view-button'>Podgląd ogłoszenia</button></a>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "Brak ogłoszeń do weryfikacji.";
                }
                ?>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Platforma ogłoszeniowa. Wszystkie prawa zastrzeżone.</p>
    </footer>

    <script>
        $(document).ready(function() {
            $('.accept-button').on('click', function() {
                var adId = $(this).data('id');
                $.post('update_status.php', { id: adId, status: 'aktywne' }, function(response) {
                    location.reload();
                });
            });

            $('.reject-button').on('click', function() {
                var adId = $(this).data('id');
                $.post('update_status.php', { id: adId, status: 'anulowane przez administratora' }, function(response) {
                    location.reload();
                });
            });
        });
    </script>
</body>
</html>