<?php
include 'db_connect.php';
session_start();


$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
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

        .search-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .search {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            max-width: 1200px;
            width: 100%;
        }

        .search input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search button {
            padding: 10px 20px;
            background-color: #00796b;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search button:hover {
            background-color: #005f5b;
        }

        .user-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            justify-content: center;
            padding: 2rem;
            height: auto;
        }

        .user {
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

        .user-header {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-info {
            margin-top: 10px;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }

        .buttons button {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .delete-button {
            background-color: #e74c3c;
        }

        .delete-button:hover {
            background-color: #c0392b;
        }

        .permissions-button {
            background-color: #00796b;
        }

        .permissions-button:hover {
            background-color: #005f5b;
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
            <h1>Zarządzaj użytkownikami</h1>
            <div class="menu">
                <a href="adminpanel.php">Zarządzaj ogłoszeniami</a>
                <a href="uzytkownicy.php">Zarządzaj użytkownikami</a>
                <a href="kategorie.php">Zarządzaj kategoriami</a>
                <a href="adminczat.php">Czat</a>
                <a href="zgloszenia.php">Zarządzaj zgłoszeniami</a>
            </div>

            <div class="search-wrapper">
                <div class="search">
                    <input type="text" id="search-input" placeholder="Wpisz nazwę użytkownika" value="<?php echo htmlspecialchars($search); ?>">
                    <button id="search-button">Wyszukaj</button>
                    <button id="clear-button">Wyczyść</button>
                </div>
            </div>

            <div class="user-container">
                <?php
                $sql = "SELECT uzytkownik_ID, nazwa, data_dolaczenia, rodzaj_konta 
                        FROM uzytkownicy 
                        WHERE nazwa LIKE '%$search%'
                        ORDER BY FIELD(rodzaj_konta, 'admin', 'uzytkownik'), nazwa";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='user'>";
                        echo "<div class='user-header'>";
                        echo '<img src="img/user.png" alt="Użytkownik">';
                        echo "<div class='user-details'>";
                        echo "<div class='user-info'>Nazwa użytkownika: " . htmlspecialchars($row['nazwa']) . "</div>";
                        echo "<div class='user-info'>Data dołączenia: " . htmlspecialchars($row['data_dolaczenia']) . "</div>";
                        echo "<div class='user-info'>Ranga: " . htmlspecialchars($row['rodzaj_konta']) . "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "<div class='buttons'>";
                        echo "<button class='permissions-button' data-id='" . htmlspecialchars($row['uzytkownik_ID']) . "'>Zmień uprawnienia</button>";
                        echo "<button class='delete-button' data-id='" . htmlspecialchars($row['uzytkownik_ID']) . "'>Usuń</button>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "Brak użytkowników.";
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
            $('#search-button').on('click', function() {
                var searchQuery = $('#search-input').val();
                window.location.href = 'uzytkownicy.php?search=' + searchQuery;
            });

            $('#clear-button').on('click', function() {
                window.location.href = 'uzytkownicy.php';
            });

            $('.permissions-button').on('click', function() {
                var userId = $(this).data('id');
                if (confirm('Czy na pewno chcesz zmienić uprawnienia tego użytkownika?')) {
                    $.post('update_permissions.php', { id: userId }, function(response) {
                        alert(response.message);
                        location.reload();
                    }, 'json');
                }
            });

            $('.delete-button').on('click', function() {
                var userId = $(this).data('id');
                if (confirm('Czy na pewno chcesz usunąć tego użytkownika?')) {
                    $.post('delete_user.php', { id: userId }, function(response) {
                        alert(response.message);
                        location.reload();
                    }, 'json');
                }
            });
        });
    </script>
</body>
</html>