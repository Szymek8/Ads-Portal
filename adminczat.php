<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['uzytkownik_ID'])) {
    header("Location: login.php");
    exit();
}

$admin_ID = 22;

$query_users = "
    SELECT DISTINCT u.nazwa, w.nadawca_ID, MAX(w.data_wyslania) AS last_message_time
    FROM wiadomosci w 
    JOIN uzytkownicy u ON w.nadawca_ID = u.uzytkownik_ID 
    WHERE w.odbiorca_ID = $admin_ID
    GROUP BY u.nazwa, w.nadawca_ID
    ORDER BY last_message_time DESC";
$result_users = mysqli_query($conn, $query_users);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMS</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            let pollingInterval;
            
            function loadMessages(odbiorca_ID) {
                $.ajax({
                    url: 'load_admins_messages.php',
                    method: 'POST',
                    data: { odbiorca_ID: odbiorca_ID },
                    success: function(response) {
                        $('.details-message').html(response);
                        $(".details-message").scrollTop($(".details-message")[0].scrollHeight);
                    }
                });
            }

            function startMessagePolling(odbiorca_ID) {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                }
                pollingInterval = setInterval(function() {
                    loadMessages(odbiorca_ID);
                }, 2000);
            }

            var firstUser = $('.msg').first();
            if (firstUser.length) {
                var firstUserId = firstUser.data('id');
                var firstUserName = firstUser.find('.nickname').text();
                $('.details-user-info p').text(firstUserName);
                $('.details-user-info p').data('id', firstUserId);
                loadMessages(firstUserId);
                startMessagePolling(firstUserId);
            }

            $('.msg').click(function(){
                $('.msg').removeClass('active');
                $(this).addClass('active');
                var odbiorca_ID = $(this).data('id');
                var userName = $(this).find('.nickname').text();
                $('.details-user-info p').text(userName);
                $('.details-user-info p').data('id', odbiorca_ID);
                loadMessages(odbiorca_ID);
                startMessagePolling(odbiorca_ID);
            });

            $('.details-input img').click(function(){
                var odbiorca_ID = $('.details-user-info p').data('id');
                var message = $('textarea').val();
                if (message.trim().length > 0) {
                    $.ajax({
                        url: 'send_message_to_users.php',
                        method: 'POST',
                        data: {
                            odbiorca_ID: odbiorca_ID,
                            tresc: message
                        },
                        success: function(response) {
                            $('textarea').val('');
                            loadMessages(odbiorca_ID);
                        }
                    });
                }
            });
        });
    </script>
    <style>
        .msg.active {
            background-color: #e9ecef;
        }
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

        .chat-container {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            gap: 1rem;
        }

        .messages {
            width: 400px;
            height: 1000px;
            border-radius: 1rem;
            background-color: white;
            display: flex;
            flex-direction: column;
            padding: 1rem;
        }

        .msg {
            height: 100px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #00796b;
            padding: 0.5rem;
        }

        .msg:hover {
            background-color: #e9ecef;
        }

        .msg img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 1rem;
        }

        .msg .user-info {
            display: flex;
            flex-direction: column;
        }

        .msg .user-info p {
            margin: 0;
        }

        .user-info p.nickname {
            font-weight: bold;
        }

        .messages-details {
            width: 800px;
            height: 1000px;
            border-radius: 1rem;
            background-color: white;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .details-user-info {
            height: 50px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #00796b;
        }

        .details-user-info img {
            width: 30px;
            height: 30px;
            margin: 0 1rem;
        }

        .details-user-info p {
            font-weight: bold;
        }

        .details-input {
            height: 50px;
            display: flex;
            align-items: center;
        }

        .details-input textarea {
            width: 90%;
            height: 100%;
            border: solid 1px #00796b;
            padding: 0.5rem;
            resize: none;
        }

        .details-input textarea:focus {
            outline: none;
        }

        .details-input img {
            margin-left: 1rem;
            align-self: center;
        }

        .details-input img:hover {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            cursor: pointer;
        }

        .details-message {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 20px;
            overflow-y: auto;
            height: calc(100% - 120px);
        }

        .message {
            display: flex;
            align-items: center;
            padding: 0 1rem;
            border-radius: 10px;
            max-width: 70%;
        }

        .message.user {
            align-self: flex-end;
            background-color: #00796b;
            color: white;
        }

        .message.receiver {
            align-self: flex-start;
            background-color: #e0e0e0;
            color: black;
        }
        .view-button {
            background-color: #00796b;
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
            <h1>Czat</h1>
            <div class="menu">
                <a href="adminpanel.php">Zarządzaj ogłoszeniami</a>
                <a href="uzytkownicy.php">Zarządzaj użytkownikami</a>
                <a href="kategorie.php">Zarządzaj kategoriami</a>
                <a href="adminczat.php">Czat</a>
                <a href="zgloszenia.php">Zarządzaj zgłoszeniami</a>
            </div>
        </div>
        <div class="chat-container">
            <div class="messages">
                <?php while ($row_user = mysqli_fetch_assoc($result_users)): ?>
                <div class="msg" data-id="<?php echo $row_user['nadawca_ID']; ?>">
                    <img src="img/messageAvatar.png" alt="Avatar">
                    <div class="user-info">
                        <p class="nickname"><?php echo htmlspecialchars($row_user['nazwa'], ENT_QUOTES); ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="messages-details">
                <div class="details-user-info">
                    <img src="img/messageAvatar.png" alt="Avatar">
                    <p data-id="">Nazwa użytkownika</p>
                </div>
                <div class="details-message">
                </div>
                <div class="details-input">
                    <textarea placeholder="Napisz wiadomość..." maxlength="500"></textarea>
                    <img src="img/sendmessage.png" alt="Wyślij wiadomość">
                </div>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Platforma ogłoszeniowa. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>