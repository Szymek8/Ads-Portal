<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nazwa = $_POST['nazwa'];
    $opis = $_POST['opis'];
    $plik = $_FILES['zdjecie']['name'];

    $target_dir = "img/categories/";
    $plik_sciezka = $nazwa . '.' . pathinfo($plik, PATHINFO_EXTENSION);
    $target_file = $target_dir . basename($plik_sciezka);

    if (move_uploaded_file($_FILES['zdjecie']['tmp_name'], $target_file)) {
        $sql = "INSERT INTO kategorie (nazwa, opis, plik_sciezka) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nazwa, $opis, $plik_sciezka);
        $stmt->execute();
        $stmt->close();
        header("Location: kategorie.php");
        exit();
    } else {
        echo "Błąd podczas przesyłania pliku.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj kategorię - MMS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style1.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }
        input[type="text"],
        textarea,
        input[type="file"],
        button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
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
        <div class="container">
            <h1>Dodaj nową kategorię</h1>
            <form action="dodaj_kategorie.php" method="post" enctype="multipart/form-data">
                <div>
                    <label for="nazwa">Nazwa kategorii:</label>
                    <input type="text" id="nazwa" name="nazwa" required>
                </div>
                <div>
                    <label for="opis">Opis kategorii:</label>
                    <textarea id="opis" name="opis" required></textarea>
                </div>
                <div>
                    <label for="zdjecie">Zdjęcie kategorii:</label>
                    <input type="file" id="zdjecie" name="zdjecie" accept="image/*" required>
                </div>
                <button type="submit">Dodaj kategorię</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Platforma ogłoszeniowa. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>