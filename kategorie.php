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
    <link rel="stylesheet" href="style1.css">
</head>
<style>.container {
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

        .wrapper {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .add-category {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            max-width: 1200px;
            width: 100%;
        }

        .add-category button {
            padding: 10px 20px;
            background-color: #00796b;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-category button:hover {
            background-color: #005f5b;
        }

        .categories-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            justify-content: center;
            padding: 2rem;
            height: auto;
        }

        .category {
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            max-width: 1200px;
            width: 100%;
        }

        .category img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .category h2 {
            flex-grow: 1;
            margin: 0 10px;
        }

        .category button {
            margin-left: 10px;
            padding: 10px 20px;
            color: white;
            background-color: #005f5b;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            width: 400px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-content input {
            width: 100%;
            margin-bottom: 1rem;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .modal-content button {
            padding: 10px 20px;
            background-color: #00796b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-content button:hover {
            background-color: #005f5b;
        }</style>
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
            <h1>Zarządzaj kategoriami</h1>
            <div class="menu">
                <a href="adminpanel.php">Zarządzaj ogłoszeniami</a>
                <a href="uzytkownicy.php">Zarządzaj użytkownikami</a>
                <a href="kategorie.php">Zarządzaj kategoriami</a>
                <a href="adminczat.php">Czat</a>
                <a href="zgloszenia.php">Zarządzaj zgłoszeniami</a>
            </div>
        </div>

        <div class="wrapper">
            <div class="add-category">
                <a href="dodaj_kategorie.php"><button>Dodaj kategorie</button></a>
            </div>
        </div>

        <div class="categories-container">
            <?php
            $sql = "SELECT kategoria_ID, nazwa, plik_sciezka FROM kategorie";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $filename = strtolower($row['plik_sciezka']);
                    echo "<div class='category'>";
                    echo "<img src='img/categories/" . htmlspecialchars($filename) . "' alt='" . htmlspecialchars($row['nazwa']) . "'>";
                    echo "<h2>" . htmlspecialchars($row['nazwa']) . "</h2>";
                    echo "<a href='edytuj_kategorie.php?id=" . htmlspecialchars($row['kategoria_ID']) . "'><button>Edytuj</button></a>";
                    echo "<a href='usun_kategorie.php?id=" . htmlspecialchars($row['kategoria_ID']) . "' onclick='return confirm(\"Czy na pewno chcesz usunąć tę kategorię?\")'><button>Usuń</button></a>";
                    echo "</div>";
                }
            } else {
                echo "Brak kategorii.";
            }
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Platforma ogłoszeniowa. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>