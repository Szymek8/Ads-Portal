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
    <style>
        .container-filter {
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .filter {
            display: flex;
            justify-content: space-between;
            max-width: 1200px;
            width: 100%;
            gap: 1rem;
        }

        .filter-left, .filter-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .filter input, .filter select, .filter button {
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
        }

        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: url('data:image/svg+xml;utf8,<svg fill="%23ccc" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 10px center;
            background-color: white;
            padding-right: 30px;
        }

        select:focus {
            border: 1px solid dodgerblue;
        }

        .container-search-bar button:hover {
            background-color: deepskyblue;
        }

        .container-advertisement {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            justify-content: center;
            padding: 2rem;
            height: auto;
        }

        .advertisement {
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            padding: 1rem;
            max-width: 1200px;
            width: 100%;
        }

        .advertisement img {
            max-width: 100px;
            border-radius: 8px;
            margin-right: 1rem;
        }

        .advertisement h2 {
            font-size: 1.25rem;
            margin: 0 0 0.5rem;
            flex: 1;
        }

        .advertisement p {
            color: gray;
            margin-top: 5rem;
            flex: 2;
        }

        .advertisement .price {
            font-size: 1.5rem;
            color: #333;
            margin-left: auto;
        }

        .advertisement a {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
    </style>
</head>
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
        <div class="container-search">
            <form method="GET" action="ogloszenia.php">
                <div class="container-search-bar">
                    <div class="search-bar">
                            <input type="text" name="search" placeholder="Czego potrzebujesz?" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    </div>
                    <button type="submit">Wyszukaj</button>
                </div>
            </form>
        </div>

        <div class="container-filter">
            <form method="GET" action="ogloszenia.php" class="filter">
                <input type="hidden" name="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <div class="filter-left">
                    <p>Cena:</p>
                    <input type="text" name="price_min" placeholder="Od" value="<?php echo isset($_GET['price_min']) ? $_GET['price_min'] : ''; ?>">
                    <input type="text" name="price_max" placeholder="Do" value="<?php echo isset($_GET['price_max']) ? $_GET['price_max'] : ''; ?>">

                    <p>Kategoria:</p>
                    <select name="kategoria">
                        <option value="">Wszystkie</option>
                        <?php
                        $sql = "SELECT kategoria_ID, nazwa FROM kategorie";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $selected = (isset($_GET['kategoria']) && $_GET['kategoria'] == $row['kategoria_ID']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($row['kategoria_ID']) . '" ' . $selected . '>' . htmlspecialchars($row['nazwa']) . '</option>';
                            }
                        }
                        ?>
                    </select>

                    <p>Sortuj:</p>
                    <select name="sort">
                        <option value="new" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'new') ? 'selected' : ''; ?>>Najnowsze</option>
                        <option value="min" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'min') ? 'selected' : ''; ?>>Najtańsze</option>
                        <option value="max" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'max') ? 'selected' : ''; ?>>Najdroższe</option>
                    </select>
                </div>
                <div class="filter-right">
                    <button type="submit">Filtruj</button>
                    <a href="ogloszenia.php" class="button-clear"><button type="button">Usuń filtry</button></a>
                </div>
            </form>
        </div>

        <div class="container-advertisement">
            <?php
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $kategoria = isset($_GET['kategoria']) ? $_GET['kategoria'] : '';
                $price_min = isset($_GET['price_min']) ? $_GET['price_min'] : '';
                $price_max = isset($_GET['price_max']) ? $_GET['price_max'] : '';
                $sort = isset($_GET['sort']) ? $_GET['sort'] : '';

                $sql = "SELECT ogloszenia.ogloszenie_ID, ogloszenia.tytul, ogloszenia.cena, ogloszenia.miasto, DATE_FORMAT(ogloszenia.data_zalozenia, '%d-%m-%Y') as data_zalozenia, 
                        (SELECT ogloszenie_zdjecia.plik_sciezka 
                         FROM ogloszenie_zdjecia 
                         WHERE ogloszenie_zdjecia.ogloszenie_ID = ogloszenia.ogloszenie_ID 
                         LIMIT 1) AS plik_sciezka 
                        FROM ogloszenia 
                        WHERE ogloszenia.status = 'aktywne'";

                if ($search) {
                    $sql .= " AND ogloszenia.tytul LIKE '%" . $conn->real_escape_string($search) . "%'";
                }

                if ($kategoria) {
                    $sql .= " AND ogloszenia.kategoria_ID = '" . $conn->real_escape_string($kategoria) . "'";
                }

                if ($price_min) {
                    $sql .= " AND ogloszenia.cena >= '" . $conn->real_escape_string($price_min) . "'";
                }

                if ($price_max) {
                    $sql .= " AND ogloszenia.cena <= '" . $conn->real_escape_string($price_max) . "'";
                }

                if ($sort) {
                    if ($sort == 'new') {
                        $sql .= " ORDER BY ogloszenia.data_zalozenia DESC";
                    } elseif ($sort == 'min') {
                        $sql .= " ORDER BY ogloszenia.cena ASC";
                    } elseif ($sort == 'max') {
                        $sql .= " ORDER BY ogloszenia.cena DESC";
                    }
                } else {
                    $sql .= " ORDER BY ogloszenia.data_zalozenia DESC";
                }

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<a href="szczegoly.php?id=' . $row["ogloszenie_ID"] . '" class="advertisement">';
                        echo '<img src="' . $row["plik_sciezka"] . '" alt="Zdjęcie Ogłoszenia">';
                        echo '<div>';
                        echo '<h2>' . $row["tytul"] . '</h2>';
                        echo '<p>' . $row["miasto"] . ' - ' . $row["data_zalozenia"] . '</p>';
                        echo '</div>';
                        echo '<div class="price">' . $row["cena"] . ' zł</div>';
                        echo '</a>';
                    }
                } else {
                    echo "Brak aktywnych ogłoszeń.";
                }

                $conn->close();
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Platforma ogłoszeniowa. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>