<?php
include 'db_connect.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: kategorie.php");
    exit();
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nazwa = $_POST['nazwa'];
    $opis = $_POST['opis'];
    $plik = $_FILES['zdjecie']['name'];

    if (!empty($plik)) {
        $sql = "SELECT plik_sciezka FROM kategorie WHERE kategoria_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($stary_plik_sciezka);
        $stmt->fetch();
        $stmt->close();

        if ($stary_plik_sciezka) {
            $file_path = "img/categories/" . $stary_plik_sciezka;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $target_dir = "img/categories/";
        $plik_sciezka = $nazwa . '.' . pathinfo($plik, PATHINFO_EXTENSION);
        $target_file = $target_dir . basename($plik_sciezka);
        move_uploaded_file($_FILES['zdjecie']['tmp_name'], $target_file);

        $sql = "UPDATE kategorie SET nazwa = ?, opis = ?, plik_sciezka = ? WHERE kategoria_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nazwa, $opis, $plik_sciezka, $id);
    } else {
        $sql = "UPDATE kategorie SET nazwa = ?, opis = ? WHERE kategoria_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nazwa, $opis, $id);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: kategorie.php");
    exit();
} else {
    $sql = "SELECT nazwa, opis FROM kategorie WHERE kategoria_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nazwa, $opis);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj kategorię - MMS</title>
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
            <h1>Edytuj kategorię</h1>
            <form action="edytuj_kategorie.php?id=<?php echo htmlspecialchars($id); ?>" method="post" enctype="multipart/form-data">
                <div>
                    <label for="nazwa">Nazwa kategorii:</label>
                    <input type="text" id="nazwa" name="nazwa" value="<?php echo htmlspecialchars($nazwa); ?>" required>
                </div>
                <div>
                    <label for="opis">Opis kategorii:</label>
                    <textarea id="opis" name="opis" required><?php echo htmlspecialchars($opis); ?></textarea>
                </div>
                <div>
                    <label for="zdjecie">Zdjęcie kategorii (pozostaw puste, jeśli nie chcesz zmieniać):</label>
                    <input type="file" id="zdjecie" name="zdjecie" accept="image/*">
                </div>
                <button type="submit">Zapisz zmiany</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Platforma ogłoszeniowa. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>