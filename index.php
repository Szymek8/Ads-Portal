<?php
include 'db_connect.php';
session_start();

$uzytkownik_ID = isset($_SESSION['uzytkownik_ID']) ? $_SESSION['uzytkownik_ID'] : null;
$rodzaj_konta = null;

if ($uzytkownik_ID) {
    $query = "SELECT rodzaj_konta FROM uzytkownicy WHERE uzytkownik_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $uzytkownik_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $rodzaj_konta = $row['rodzaj_konta'];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .testowa{
            min-height: 300px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container-nav">
            <div class="logo"><a href="index.php">MMS</a></div>
            <div class="user-options">
                <?php if (isset($_SESSION['uzytkownik_ID'])): ?>
                    <?php if ($rodzaj_konta === 'admin'): ?>
                        <a href="adminpanel.php"><button>Panel Admina</button></a>
                    <?php else: ?>
                        <a href="konto.php"><button>Konto</button></a>
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
        <div class="container-search">
            <form method="GET" action="ogloszenia.php">
                <div class="container-search-bar">
                    <div class="search-bar">
                            <input type="text" name="search" placeholder="Czego potrzebujesz?" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    </div>
                    <button type="submit">Wyszukaj</button>
                </div>
            </form>

            <div class="categories">
                <?php
                $sql = "SELECT kategoria_ID,nazwa, plik_sciezka FROM kategorie";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $filename = strtolower($row['plik_sciezka']);
                        echo '<div class="cat">';
                        echo '<a href="ogloszenia.php?kategoria=' . urlencode($row['kategoria_ID']) . '">';
                        echo '<img src="img/categories/' . htmlspecialchars($filename) . '" alt="' . htmlspecialchars($row['plik_sciezka']) . '">';
                        echo '<p><b>' . htmlspecialchars($row['nazwa']) . '</b></p>';
                        echo '</a>';
                        echo '</div>';
                    }
                }
                ?>
            </div>


        </div>
        <div class="container">
            <div class="ads">
                <?php
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $category = isset($_GET['category']) ? $_GET['category'] : 'all';

                $sql = "SELECT ogloszenia.ogloszenie_ID, ogloszenia.tytul, ogloszenia.cena, ogloszenia.miasto, ogloszenie_zdjecia.plik_sciezka 
                        FROM ogloszenia 
                        LEFT JOIN ogloszenie_zdjecia ON ogloszenia.ogloszenie_ID = ogloszenie_zdjecia.ogloszenie_ID
                        WHERE ogloszenia.status = 'aktywne'";

                if ($search) {
                    $sql .= " AND ogloszenia.tytul LIKE '%" . $conn->real_escape_string($search) . "%'";
                }

                if ($category && $category != 'all') {
                    $sql .= " AND ogloszenia.kategoria_ID = (SELECT kategoria_ID FROM kategorie WHERE nazwa = '" . $conn->real_escape_string($category) . "')";
                }

                $sql .= " GROUP BY ogloszenia.ogloszenie_ID ORDER BY RAND() LIMIT 12"; 

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<a href='szczegoly.php?id=" . htmlspecialchars($row['ogloszenie_ID']) . "' class='ad'>";
                        echo '<div class="img-container"><img src="' . htmlspecialchars($row['plik_sciezka']) . '" alt="' . htmlspecialchars($row['tytul']) . '"></div>';
                        echo "<h2>" . htmlspecialchars($row['tytul']) . "</h2>";
                        echo "<p>Cena: " . htmlspecialchars($row['cena']) . " PLN</p>";
                        echo "<p>Lokalizacja: " . htmlspecialchars($row['miasto']) . "</p>";
                        echo "</a>";
                    }
                } else {
                    echo "Brak ogłoszeń.";
                }

                $conn->close();
                ?>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Platforma ogłoszeniowa. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>