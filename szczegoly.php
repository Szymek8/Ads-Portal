<?php
include 'db_connect.php';
session_start();

if (!isset($_GET['id'])) {
    echo "Błąd: brak identyfikatora ogłoszenia.";
    exit;
}

$id = intval($_GET['id']);

$sql = "SELECT ogloszenia.tytul, ogloszenia.opis, ogloszenia.cena, ogloszenia.miasto, uzytkownicy.email, uzytkownicy.imie, uzytkownicy.data_dolaczenia, uzytkownicy.numer, uzytkownicy.uzytkownik_ID AS owner_id
        FROM ogloszenia 
        JOIN uzytkownicy ON ogloszenia.uzytkownik_ID = uzytkownicy.uzytkownik_ID 
        WHERE ogloszenia.ogloszenie_ID = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Ogłoszenie nie zostało znalezione.";
    exit;
}

$ogloszenie = $result->fetch_assoc();

$sql = "SELECT plik_sciezka FROM ogloszenie_zdjecia WHERE ogloszenie_ID = $id";
$imagesResult = $conn->query($sql);
$images = [];
if ($imagesResult->num_rows > 0) {
    while ($row = $imagesResult->fetch_assoc()) {
        $images[] = $row['plik_sciezka'];
    }
}

$data_dolaczenia = date("F Y", strtotime($ogloszenie['data_dolaczenia']));
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.style.display = 'none';
            });
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
    <style>
        .grid-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: repeat(3, 1fr);
    gap: 10px;
    width: 100%;
    max-width: 1200px;
    margin: 3rem auto;
    height: 100vh;
}

.grid-item {
    background-color: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.images {
    grid-column: span 2;
    grid-row: span 2;
    position: relative;
}

.description {
    grid-column: span 2;
    grid-row: span 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    position: relative;
    word-wrap: break-word;
}

.user-information {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}

.more-details {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}

.more-details h2 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.more-details .price {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 1rem;
}

.more-details .buttons {
    margin-top: auto;
}

.more-details button {
    display: block;
    width: 100%;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    background-color: #004d40;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-size: 1.5rem;
}

.more-details button:hover {
    background-color: #00332c;
}

.image-carousel img {
    width: 100%;
    height: 550px;
    object-fit: cover;
    display: block;
}

.slick-prev, .slick-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.2);
    border: none;
    width: 40px;
    height: 40px;
    color: white;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    border-radius: 0;
}

.slick-prev:hover, .slick-next:hover,
.slick-prev:focus, .slick-next:focus,
.slick-prev:active, .slick-next:active {
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
}

.slick-prev {
    left: 10px;
}

.slick-next {
    right: 10px;
}

.report-button {
    position: absolute;
    bottom: 10px;
    right: 10px;
    padding: 0.5rem 1rem;
    background-color: white;
    color: red;
    border: 2px solid;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s ease;
}

.report-button:hover {
    background-color: red;
    color: white;
    border-color: white;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999;
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.modal-content h3 {
    margin-bottom: 20px;
}

.modal-content .reason {
    margin: 10px 0;
    padding: 10px;
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    border-radius: 5px;
    cursor: pointer;
}

.modal-content .close-btn {
    margin-top: 20px;
    padding: 10px 20px;
    background-color: red;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.modal-content .close-btn:hover {
    background-color: darkred;
}

.modal {
   display: none;
   position: fixed;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   background-color: rgba(0, 0, 0, 0.5);
   display: flex;
   justify-content: center;
   align-items: center;
   z-index: 999;
}

.modal-content {
    background-color: #ffffff;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.modal-content h3 {
    margin-bottom: 20px;
    font-size: 1.8rem;
    color: #333333;
}

.modal-content textarea {
    width: 100%;
    height: 150px;
    border: 1px solid #cccccc;
    border-radius: 8px;
    font-size: 1rem;
    resize: none;
    margin-bottom: 20px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: border-color 0.3s ease;
}

.modal-content textarea:focus {
    outline: none;
    border-color: #00796b;
}

.modal-content button {
    width: 100%;
    padding: 15px;
    margin-bottom: 10px;
    background-color: #00796b;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-size: 1.2rem;
}

.modal-content button:hover {
    background-color: #004d40;
}

.modal-content .close-btn {
    background-color: #e53935;
}

.modal-content .close-btn:hover {
    background-color: #b71c1c;
}
.success-message {
    color: green;
    font-size: 1.5rem;
    text-align: center;
    margin-top: 20px;
}
    </style>
    <main>
        <div class="container-details grid-container">
            <div class="grid-item images">
                <div class="image-carousel">
                    <?php foreach ($images as $image): ?>
                        <a href="<?php echo htmlspecialchars($image); ?>" data-lightbox="carousel-images"><img src="<?php echo htmlspecialchars($image); ?>" alt="Image"></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="grid-item more-details">
                <h1><?php echo htmlspecialchars($ogloszenie['tytul']); ?></h1>
                <p class="price"><?php echo htmlspecialchars($ogloszenie['cena']); ?> zł</p>
                <div class="buttons">
                    <button class="message-btn" data-owner-id="<?php echo htmlspecialchars($ogloszenie['owner_id']); ?>">Wyślij wiadomość</button>
                    <button class="call-btn" data-phone="<?php echo htmlspecialchars($ogloszenie['numer']); ?>">Zadzwoń</button>
                </div>
            </div>

            <div class="grid-item user-information">
                <h2>Informacje o użytkowniku</h2>
                <p>Imię: <?php echo htmlspecialchars($ogloszenie['imie']); ?></p>
                <p>Data dołączenia: <?php echo htmlspecialchars($data_dolaczenia); ?></p>
            </div>

            <div class="grid-item description">
                <h2>Opis</h2>
                <p><?php echo htmlspecialchars($ogloszenie['opis']); ?></p>
                <button class="report-button">Zgłoś</button>
                <div id="reportModal" class="modal">
                    <div class="modal-content">
                        <h3>Zgłoś naruszenie</h3>
                        <div class="reason" data-reason="Spam">Spam</div>
                        <div class="reason" data-reason="Nieprawidłowe dane sprzedającego">Nieprawidłowe dane sprzedającego</div>
                        <div class="reason" data-reason="Oszustwo">Oszustwo</div>
                        <div class="reason" data-reason="Szkodliwe Treści">Szkodliwe Treści</div>
                        <div class="reason" data-reason="Inne">Inne</div>
                        <button class="close-btn">Zamknij</button>
                    </div>
                </div>
            </div>

            <div class="grid-item location">
                <h2>Lokalizacja</h2>
                <p><?php echo htmlspecialchars($ogloszenie['miasto']); ?></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Platforma ogłoszeniowa. Wszystkie prawa zastrzeżone.</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/@fortawesome/fontawesome-free/5.15.4/js/all.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.image-carousel').slick({
                dots: true,
                infinite: true,
                speed: 300,
                slidesToShow: 1,
                adaptiveHeight: true,
                prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>'
            });

            $('.call-btn').on('click', function() {
                const phoneNumber = $(this).data('phone');
                $(this).html(` ${phoneNumber}`);
            });

            $('.message-btn').on('click', function() {
                <?php if (isset($_SESSION['uzytkownik_ID'])): ?>
                    $('#messageModal').fadeIn();
                <?php else: ?>
                    window.location.href = 'login.php';
                <?php endif; ?>
            });

            $('#sendMessageBtn').on('click', function() {
                const message = $('#messageText').val();
                const ownerId = $('.message-btn').data('owner-id');
                
                if (message.trim().length > 0) {
                    $.post('send_message.php', { 
                        nadawca_ID: <?php echo $_SESSION['uzytkownik_ID']; ?>,
                        odbiorca_ID: ownerId,
                        tresc: message 
                    }, function(response) {
                        $('#messageText').hide();
                        $('#sendMessageBtn').hide();
                        $('#messageModal .modal-content').append('<p id="successMessage" class="success-message">Wiadomość wysłana</p>');
                        setTimeout(function() {
                            $('#messageModal').fadeOut();
                            $('#successMessage').remove();
                            $('#messageText').val('').show();
                            $('#sendMessageBtn').prop('disabled', false).show();
                        }, 2000);
                    }, 'json');
                } else {
                    alert('Wiadomość nie może być pusta.');
                }
            });

            $('#messageModal .close-btn').on('click', function() {
                $('#messageModal').fadeOut();
            });

            let selectedReason = '';

            $('.report-button').on('click', function () {
                $('#reportModal').fadeIn();
            });

            $(document).on('click', '.reason', function () {
                selectedReason = $(this).data('reason');
                $('#reportModal .modal-content').html(`
                    <h3>Podaj więcej szczegółów</h3>
                    <textarea id="reportDetails" placeholder="Napisz więcej szczegółów tutaj..."></textarea>
                    <button id="submitReportBtn">Wyślij zgłoszenie</button>
                    <button id="backToReasons">Wróć</button>
                    <button class="close-btn">Zamknij</button>
                `);
            });

            $(document).on('click', '#backToReasons', function() {
                $('#reportModal .modal-content').html(`
                    <h3>Zgłoś naruszenie</h3>
                    <div class="reason" data-reason="Spam">Spam</div>
                    <div class="reason" data-reason="Nieprawidłowe dane sprzedającego">Nieprawidłowe dane sprzedającego</div>
                    <div class="reason" data-reason="Oszustwo">Oszustwo</div>
                    <div class="reason" data-reason="Szkodliwe Treści">Szkodliwe Treści</div>
                    <div class="reason" data-reason="Inne">Inne</div>
                    <button class="close-btn">Zamknij</button>
                `);
            });

            $(document).on('click', '#submitReportBtn', function() {
                const details = $('#reportDetails').val();
                const userId = <?php echo $_SESSION['uzytkownik_ID']; ?>;
                const ogloszenieId = <?php echo $id; ?>;

                if (details.trim().length > 0) {
                    $.post('submit_report.php', { 
                        uzytkownik_ID: userId,
                        ogloszenie_ID: ogloszenieId,
                        rodzaj: selectedReason,
                        opis: details 
                    }, function(response) {
                        $('#reportDetails').hide();
                        $('#submitReportBtn').hide();
                        $('#backToReasons').hide();
                        $('#reportModal .modal-content').append('<p id="successReportMessage" class="success-message">Zgłoszenie wysłane</p>');
                        setTimeout(function() {
                            $('#reportModal').fadeOut();
                            $('#successReportMessage').remove();
                            $('#reportDetails').val('').show();
                            $('#submitReportBtn').prop('disabled', false).show();
                            $('#backToReasons').show();
                        }, 2000);
                    }, 'json');
                } else {
                    alert('Opis nie może być pusty.');
                }
            });

            $(document).on('click', '.close-btn', function() {
                $(this).closest('.modal').fadeOut();
            });

            $(window).on('click', function (event) {
                if ($(event.target).is('.modal')) {
                    $(event.target).fadeOut();
                }
            });
        });
    </script>
</body>
</html>

<div id="messageModal" class="modal">
    <div class="modal-content">
        <h3>Napisz wiadomość</h3>
        <textarea id="messageText" placeholder="Napisz swoją wiadomość tutaj..."></textarea>
        <button id="sendMessageBtn">Wyślij</button>
        <button class="close-btn">Zamknij</button>
    </div>
</div>

<?php
$conn->close();
?>