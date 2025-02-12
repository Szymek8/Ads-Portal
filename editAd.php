<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['uzytkownik_ID'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['uzytkownik_ID'];

if (!isset($_GET['id'])) {
    echo "Błąd: brak identyfikatora ogłoszenia.";
    exit;
}

$ad_id = intval($_GET['id']);
$sql = "SELECT * FROM ogloszenia WHERE ogloszenie_ID = $ad_id AND uzytkownik_ID = $user_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Ogłoszenie nie zostało znalezione.";
    exit;
}

$ad = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $location = $_POST['location'];

    // Aktualizacja ogłoszenia w bazie danych
    $update_sql = "UPDATE ogloszenia SET tytul = ?, opis = ?, cena = ?, miasto = ?, status = 'oczekujace' WHERE ogloszenie_ID = ? AND uzytkownik_ID = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssdssi", $title, $description, $price, $location, $ad_id, $user_id);

    if ($stmt->execute()) {
        echo "Ogłoszenie zostało zaktualizowane.";
    } else {
        echo "Błąd podczas aktualizacji ogłoszenia: " . $conn->error;
    }

    // Handle image deletions
    if (isset($_POST['delete_images'])) {
        $delete_images = rtrim($_POST['delete_images'], ',');
        $image_ids = explode(',', $delete_images);

        foreach ($image_ids as $image_id) {
            // Get the image path
            $path_sql = "SELECT plik_sciezka FROM ogloszenie_zdjecia WHERE zdjecie_ID = ? AND ogloszenie_ID = ?";
            $stmt = $conn->prepare($path_sql);
            $stmt->bind_param("ii", $image_id, $ad_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $image_path = $row['plik_sciezka'];

            // Delete the image file
            if (file_exists($image_path)) {
                unlink($image_path);
            }

            // Delete the image record from the database
            $delete_sql = "DELETE FROM ogloszenie_zdjecia WHERE zdjecie_ID = ? AND ogloszenie_ID = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("ii", $image_id, $ad_id);
            $stmt->execute();
        }
    }

    // Handle new image uploads
    if (isset($_FILES['new_images'])) {
        $uploadDir = 'img/' . $ad_id . '/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $image_count = count($_FILES['new_images']['name']);
        $max_images = 5;

        // Check current number of images
        $count_sql = "SELECT COUNT(*) as count FROM ogloszenie_zdjecia WHERE ogloszenie_ID = ?";
        $stmt = $conn->prepare($count_sql);
        $stmt->bind_param("i", $ad_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $current_image_count = $row['count'];

        if ($current_image_count + $image_count <= $max_images) {
            for ($i = 0; $i < $image_count; $i++) {
                $fileName = basename($_FILES['new_images']['name'][$i]);
                $target_file = $uploadDir . $fileName;
                $fileType = pathinfo($target_file, PATHINFO_EXTENSION);

                if (in_array(strtolower($fileType), $allowedTypes)) {
                    if (move_uploaded_file($_FILES['new_images']['tmp_name'][$i], $target_file)) {
                        $insert_sql = "INSERT INTO ogloszenie_zdjecia (ogloszenie_ID, plik_sciezka) VALUES (?, ?)";
                        $stmt = $conn->prepare($insert_sql);
                        $stmt->bind_param("is", $ad_id, $target_file);
                        $stmt->execute();
                    } else {
                        echo "Przepraszamy, wystąpił błąd podczas przesyłania pliku $fileName.";
                        break;
                    }
                } else {
                    echo "Niedozwolony typ pliku: $fileName.";
                    break;
                }
            }
        } else {
            echo "Nie można dodać więcej niż 5 zdjęć.";
        }
    }

    $stmt->close();
    $conn->close();
    header("Location: konto.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj ogłoszenie</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
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

        .container-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
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

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 16px;
            color: #555;
        }

        input, textarea {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #00796b;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }

        button:hover {
            background-color: #004d40;
        }

        .image-preview {
            display: flex;
            gap: 10px;
        }

        .image-preview img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            position: relative;
        }

        .delete-icon {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 255, 255, 0.7);
            border: none;
            cursor: pointer;
        }

        .image-container {
            position: relative;
        }
    </style>
    <script>
        function confirmUpdate() {
            return confirm("Czy na pewno chcesz zaktualizować to ogłoszenie?");
        }

        function removeImage(imageId) {
            var imageElement = document.getElementById('image-' + imageId);
            if (imageElement) {
                imageElement.style.display = 'none';
                var deleteInput = document.getElementById('delete-images-input');
                deleteInput.value += imageId + ',';
            }
        }
    </script>
</head>
<body>
    <header>
        <div class="container-nav">
            <div class="logo"><a href="index.php">MMS</a></div>
            <div class="user-options">
                <a href="konto.php"><button>Moje konto</button></a>
                <a href="logout.php"><button>Wyloguj się</button></a>
            </div>
        </div>
    </header>
    <main>
        <div class="container">
            <h1>Edytuj ogłoszenie</h1>
            <form action="editAd.php?id=<?php echo $ad_id; ?>" method="post" enctype="multipart/form-data" onsubmit="return confirmUpdate();">
                <label for="title">Tytuł ogłoszenia:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($ad['tytul']); ?>" required>
                
                <label for="description">Opis ogłoszenia:</label>
                <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($ad['opis']); ?></textarea>
                
                <label for="price">Cena (PLN):</label>
                <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($ad['cena']); ?>" step="0.01" required>
                
                <label for="location">Lokalizacja:</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($ad['miasto']); ?>" required>
                
                <h3>Zdjęcia ogłoszenia:</h3>
                <div class="image-preview">
                    <?php
                    $img_sql = "SELECT * FROM ogloszenie_zdjecia WHERE ogloszenie_ID = $ad_id";
                    $img_result = $conn->query($img_sql);

                    if ($img_result->num_rows > 0) {
                        while ($img = $img_result->fetch_assoc()) {
                            echo '<div class="image-container" id="image-' . $img['zdjecie_ID'] . '">';
                            echo '<img src="' . htmlspecialchars($img['plik_sciezka']) . '" alt="Zdjęcie ogłoszenia">';
                            echo '<button type="button" class="delete-icon" onclick="removeImage(' . $img['zdjecie_ID'] . ')">x</button>';
                            echo '</div>';
                        }
                    } else {
                        echo "Brak zdjęć do tego ogłoszenia.";
                    }
                    ?>
                </div>

                <input type="hidden" id="delete-images-input" name="delete_images" value="">

                <label for="new_images">Dodaj nowe zdjęcia (maksymalnie 5):</label>
                <input type="file" id="new_images" name="new_images[]" multiple accept="image/*">

                <button type="submit">Zaktualizuj ogłoszenie</button>
            </form>
        </div>
    </main>
</body>
</html>
<?php
$conn->close();
?>