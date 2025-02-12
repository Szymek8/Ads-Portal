<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['uzytkownik_ID'])) {
    header("Location: login.php");
    exit();
}

$uzytkownik_ID = $_SESSION['uzytkownik_ID'];

// Fetch all messages between the user and any admin
$query = "
    SELECT w.*, IF(w.nadawca_ID = $uzytkownik_ID, 'Ty', 'Administrator') AS user_nickname
    FROM wiadomosci w
    WHERE (w.nadawca_ID = $uzytkownik_ID AND w.odbiorca_ID IN (SELECT uzytkownik_ID FROM uzytkownicy WHERE rodzaj_konta = 'admin'))
       OR (w.odbiorca_ID = $uzytkownik_ID AND w.nadawca_ID IN (SELECT uzytkownik_ID FROM uzytkownicy WHERE rodzaj_konta = 'admin'))
    ORDER BY w.data_wyslania ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
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
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMS</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            function loadMessages() {
                $.ajax({
                    url: 'load_support_messages.php',
                    method: 'POST',
                    data: { uzytkownik_ID: <?php echo $uzytkownik_ID; ?> },
                    success: function(response) {
                        $('.details-message').html(response);
                        $(".details-message").scrollTop($(".details-message")[0].scrollHeight);
                    }
                });
            }

            setInterval(loadMessages, 2000);

            $('.details-input img').click(function(){
                var message = $('textarea').val();
                if (message.trim().length > 0) {
                    $.ajax({
                        url: 'send_message_to_admins.php',
                        method: 'POST',
                        data: {
                            nadawca_ID: <?php echo $uzytkownik_ID; ?>,
                            tresc: message
                        },
                        success: function(response) {
                            $('textarea').val('');
                            loadMessages();
                        }
                    });
                }
            });

            loadMessages();
        });
    </script>
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
            <h1>Wsparcie</h1>
            <div class="menu">
                <a href="konto.php">Ogłoszenia</a>
                <a href="czat.php">Czat</a>
                <a href="profil.php">Profil</a>
                <a href="wsparcie.php">Wsparcie</a>
            </div>
        </div>
             
        <div class="chat-container">
            <div class="messages">
                <div class="msg">
                    <img src="img/messageAvatar.png" alt="Avatar">
                    <div class="user-info">
                        <p class="nickname">Administrator</p>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>

                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <div class="messages-details">
                <div class="details-user-info">
                    <img src="img/messageAvatar.png" alt="Avatar">
                    <p>Administrator</p>
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