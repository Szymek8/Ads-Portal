<?php
include 'db_connect.php';
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zgłoszenia - MMS</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

.reports-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    justify-content: center;
    padding: 2rem;
    height: auto;
}

.report {
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

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.report-header h2 {
    margin: 0;
    font-size: 1.2rem;
    color: #00796b;
}

.report-details {
    margin-bottom: 1rem;
}

.report-info {
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.report-info strong {
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

.view-button {
    background-color: #00796b;
}

.view-button:hover {
    background-color: #005f5b;
}

.accept-button {
    background-color: #27ae60;
}

.accept-button:hover {
    background-color: #229954;
}

.reject-button {
    background-color: #e74c3c;
}

.reject-button:hover {
    background-color: #c0392b;
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
            <h1>Zarządzaj zgłoszeniami</h1>
            <div class="menu">
                <a href="adminpanel.php">Zarządzaj ogłoszeniami</a>
                <a href="uzytkownicy.php">Zarządzaj użytkownikami</a>
                <a href="kategorie.php">Zarządzaj kategoriami</a>
                <a href="adminczat.php">Czat</a>
                <a href="zgloszenia.php">Zarządzaj zgłoszeniami</a>
            </div>
        </div>

        <div class="reports-container">
            <?php
            $sql = "SELECT z.zgloszenie_ID, z.rodzaj, z.opis, z.ogloszenie_ID, u.nazwa as uzytkownik_nazwa 
                    FROM zgloszenia z
                    JOIN uzytkownicy u ON z.uzytkownik_ID = u.uzytkownik_ID";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='report'>";
                    echo "<div class='report-header'>";
                    echo "<h2>Zgłoszenie: " . htmlspecialchars($row['zgloszenie_ID']) . "</h2>";
                    echo "</div>";
                    echo "<div class='report-details'>";
                    echo "<div class='report-info'><strong>Rodzaj zgłoszenia:</strong> " . htmlspecialchars($row['rodzaj']) . "</div>";
                    echo "<div class='report-info'><strong>Nazwa użytkownika:</strong> " . htmlspecialchars($row['uzytkownik_nazwa']) . "</div>";
                    echo "<div class='report-info'><strong>ID ogłoszenia:</strong> " . htmlspecialchars($row['ogloszenie_ID']) . "</div>";
                    echo "<div class='report-info'><strong>Opis zgłoszenia:</strong> " . htmlspecialchars($row['opis']) . "</div>";
                    echo "</div>";
                    echo "<div class='buttons'>";
                    echo "<button class='accept-button' data-id='" . htmlspecialchars($row['zgloszenie_ID']) . "' data-ogloszenie-id='" . htmlspecialchars($row['ogloszenie_ID']) . "'>Akceptuj</button>";
                    echo "<button class='reject-button' data-id='" . htmlspecialchars($row['zgloszenie_ID']) . "'>Odrzuć</button>";
                    echo "<a href='szczegoly.php?id=" . htmlspecialchars($row['ogloszenie_ID']) . "'><button class='view-button'>Podgląd ogłoszenia</button></a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "Brak zgłoszeń.";
            }
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Platforma ogłoszeniowa. Wszystkie prawa zastrzeżone.</p>
    </footer>

    <script>
        $(document).ready(function() {
            $('.accept-button').on('click', function() {
                var reportId = $(this).data('id');
                var adId = $(this).data('ogloszenie-id');
                if (confirm('Czy na pewno chcesz zaakceptować to zgłoszenie? Ogłoszenie zostanie anulowane.')) {
                    $.post('update_report_status.php', { report_id: reportId, ad_id: adId, action: 'accept' }, function(response) {
                        alert(response.message);
                        location.reload();
                    }, 'json');
                }
            });

            $('.reject-button').on('click', function() {
                var reportId = $(this).data('id');
                if (confirm('Czy na pewno chcesz odrzucić to zgłoszenie?')) {
                    $.post('update_report_status.php', { report_id: reportId, action: 'reject' }, function(response) {
                        alert(response.message);
                        location.reload();
                    }, 'json');
                }
            });
        });
    </script>
</body>
</html>