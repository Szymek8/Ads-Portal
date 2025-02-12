<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['uzytkownik_ID'])) {
    header("Location: login.php");
    exit();
}

$uzytkownik_ID = $_SESSION['uzytkownik_ID'];
$sql = "SELECT nazwa, imie, nazwisko, email, numer, data_dolaczenia
        FROM uzytkownicy
        WHERE uzytkownik_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uzytkownik_ID);
$stmt->execute();
$stmt->bind_result($nazwa_uzytkownika, $imie, $nazwisko, $email, $numer, $data_dolaczenia);
$stmt->fetch();
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
        
        .info {
            margin-bottom: 1rem;
        }

        .info h5 {
            display: inline;
            margin-right: 0.5rem;
            color: #00796b;
        }

        .info h4 {
            display: inline;
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
            <h1>Profil</h1>
            <div class="menu">
                <a href="konto.php">Ogłoszenia</a>
                <a href="czat.php">Czat</a>
                <a href="profil.php">Profil</a>
                <a href="wsparcie.php">Wsparcie</a>
            </div>
        </div>

        <div class="container">
            <h3>PODSTAWOWE INFORMACJE</h3>

            <div class="info">
                <h5>Nazwa:</h5>
                <h4><?php echo htmlspecialchars($nazwa_uzytkownika); ?></h4>
            </div>

            <div class="info">
                <h5>Imie i nazwisko:</h5>
                <h4><?php echo htmlspecialchars($imie . " " . $nazwisko); ?></h4>
            </div>

            <div class="info">
                <h5>E-mail:</h5>
                <h4><?php echo htmlspecialchars($email); ?></h4>
            </div>

            <div class="info">
                <h5>nr. tel:</h5>
                <h4><?php echo htmlspecialchars($numer); ?></h4>
            </div>

            <div class="info">
                <h5>Data dołączenia:</h5>
                <h4><?php echo htmlspecialchars($data_dolaczenia); ?></h4>
            </div> 
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Platforma ogłoszeniowa. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>