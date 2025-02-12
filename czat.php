<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['uzytkownik_ID'])) {
    header("Location: login.php");
    exit();
}

$uzytkownik_ID = $_SESSION['uzytkownik_ID'];
$odbiorca_ID = isset($_GET['odbiorca_ID']) ? $_GET['odbiorca_ID'] : null;

$query = "
    SELECT 
        w1.*, 
        u.nazwa AS user_nickname 
    FROM 
        wiadomosci w1 
    INNER JOIN 
        (
            SELECT 
                LEAST(nadawca_ID, odbiorca_ID) AS user1, 
                GREATEST(nadawca_ID, odbiorca_ID) AS user2, 
                MAX(wiadomosc_ID) AS max_id 
            FROM 
                wiadomosci 
            WHERE 
                nadawca_ID = $uzytkownik_ID OR odbiorca_ID = $uzytkownik_ID 
            GROUP BY 
                LEAST(nadawca_ID, odbiorca_ID), GREATEST(nadawca_ID, odbiorca_ID)
        ) w2 
        ON 
            w1.wiadomosc_ID = w2.max_id 
    JOIN 
        uzytkownicy u 
        ON 
            (u.uzytkownik_ID = w1.nadawca_ID AND w1.nadawca_ID != $uzytkownik_ID) 
            OR 
            (u.uzytkownik_ID = w1.odbiorca_ID AND w1.odbiorca_ID != $uzytkownik_ID)
    ORDER BY 
        w1.data_wyslania DESC";
$result = mysqli_query($conn, $query);

if (!$odbiorca_ID && $row = mysqli_fetch_assoc($result)) {
    $odbiorca_ID = ($row['nadawca_ID'] == $uzytkownik_ID) ? $row['odbiorca_ID'] : $row['nadawca_ID'];
    header("Location: czat.php?odbiorca_ID=$odbiorca_ID");
    exit();
}
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
            function loadMessages() {
                $.ajax({
                    url: 'load_messages.php',
                    method: 'POST',
                    data: { odbiorca_ID: <?php echo $odbiorca_ID; ?> },
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
                        url: 'send_message.php',
                        method: 'POST',
                        data: {
                            nadawca_ID: <?php echo $uzytkownik_ID; ?>,
                            odbiorca_ID: <?php echo $odbiorca_ID; ?>,
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
                <a href="konto.php">Ogłoszenia</a>
                <a href="czat.php">Czat</a>
                <a href="profil.php">Profil</a>
                <a href="wsparcie.php">Wsparcie</a>
            </div>
        </div>
             
        <div class="chat-container">
            <div class="messages">
                <?php
                mysqli_data_seek($result, 0); 
                while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="msg" onclick="window.location.href='czat.php?odbiorca_ID=<?php echo ($row['nadawca_ID'] == $uzytkownik_ID) ? $row['odbiorca_ID'] : $row['nadawca_ID']; ?>'">
                    <img src="img/messageAvatar.png" alt="Avatar">
                    <div class="user-info">
                        <p class="nickname"><?php echo $row['user_nickname']; ?></p>
                        <p><?php echo htmlspecialchars($row['tresc'], ENT_QUOTES); ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="messages-details">
                <div class="details-user-info">
                    <img src="img/messageAvatar.png" alt="Avatar">
                    <p><?php echo htmlspecialchars(mysqli_fetch_assoc(mysqli_query($conn, "SELECT nazwa FROM uzytkownicy WHERE uzytkownik_ID = $odbiorca_ID"))['nazwa'], ENT_QUOTES); ?></p>
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