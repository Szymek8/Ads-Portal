<?php
include 'db_connect.php';
session_start();

$ad_success = false;
$ad_error = '';

$categories = [];
$sql = "SELECT nazwa FROM kategorie";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['nazwa'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['uzytkownik_ID'])) {
        $ad_error = "Musisz być zalogowany, aby dodać ogłoszenie.";
    } else {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $location = $_POST['location'];
        $user_id = $_SESSION['uzytkownik_ID'];

        $stmt = $conn->prepare("INSERT INTO ogloszenia (tytul, opis, cena, kategoria_ID, uzytkownik_ID, data_zalozenia, miasto) VALUES (?, ?, ?, (SELECT kategoria_ID FROM kategorie WHERE nazwa = ?), ?, NOW(), ?)");
        $stmt->bind_param("ssdsis", $title, $description, $price, $category, $user_id, $location);
        if ($stmt->execute()) {
            $ad_id = $stmt->insert_id;
            $stmt->close();

            $uploadDir = 'img/' . $ad_id . '/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $fileCount = count($_FILES['images']['name']);

            if ($fileCount > 5) {
                $ad_error = "Możesz dodać maksymalnie 1 zdjęcie.";
            } else {
                for ($i = 0; $i < $fileCount; $i++) {
                    $fileName = basename($_FILES['images']['name'][$i]);
                    $targetFilePath = $uploadDir . $fileName;
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

                    if (in_array(strtolower($fileType), $allowedTypes)) {
                        if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $targetFilePath)) {
                            $stmt = $conn->prepare("INSERT INTO ogloszenie_zdjecia (ogloszenie_ID, plik_sciezka) VALUES (?, ?)");
                            $stmt->bind_param("is", $ad_id, $targetFilePath);
                            $stmt->execute();
                            $stmt->close();
                        } else {
                            $ad_error = "Przepraszamy, wystąpił błąd podczas przesyłania pliku $fileName.";
                            break;
                        }
                    } else {
                        $ad_error = "Niedozwolony typ pliku: $fileName.";
                        break;
                    }
                }

                if (empty($ad_error)) {
                    $ad_success = true;
                }
            }
        } else {
            $ad_error = "Błąd dodawania ogłoszenia: " . $conn->error;
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMS - Dodaj Ogłoszenie</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        main {
            min-height: 100vh;
        }

        header {
            background-color: #00796b;
            color: #fff;
            padding: 15px 0;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 2rem;
        }

        h1 {
            text-align: center;
            color: #333;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .add-ad-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .add-ad-form label {
            font-size: 16px;
            color: #555;
            margin-top: 10px;
            width: 100%;
            max-width: 800px;
        }

        .add-ad-form input,
        .add-ad-form select,
        .add-ad-form textarea,
        .add-ad-form button {
            width: 100%;
            max-width: 800px;
            padding: 10px;
            margin: 5px 0px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .add-ad-form button {
            background-color: #00796b;
            color: #fff;
            border: none;
            padding: 15px;
            cursor: pointer;
            font-size: 18px;
        }

        .add-ad-form button:hover {
            background-color: #004d40;
        }

        .message {
            font-size: 1rem;
            margin-top: 1rem;
            color: red;
        }

        .success {
            color: green;
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
            <h1>Dodaj nowe ogłoszenie</h1>
            <?php if ($ad_success): ?>
                <p class="message success">Ogłoszenie zostało dodane pomyślnie. Przekierowanie...</p>
                <script>
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 2000);
                </script>
            <?php else: ?>
                <form action="addAd.php" method="post" enctype="multipart/form-data" class="add-ad-form">
                    <label for="title">Tytuł ogłoszenia:</label>
                    <input type="text" id="title" name="title" required>
                    <label for="description">Opis ogłoszenia:</label>
                    <textarea id="description" name="description" rows="5" required></textarea>
                    <label for="price">Cena (PLN):</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                    <label for="category">Kategoria:</label>
                    <select id="category" name="category" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="location">Lokalizacja:</label>
                    <input type="text" id="location" name="location" required>
                    <label for="images">Dodaj zdjęcie (maks. 5):</label>
                    <input type="file" id="images" name="images[]" accept="image/*" multiple required>
                    <?php if ($ad_error): ?>
                        <p class="message"><?php echo $ad_error; ?></p>
                    <?php endif; ?>
                    <button type="submit">Dodaj ogłoszenie</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Platforma ogłoszeniowa. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>