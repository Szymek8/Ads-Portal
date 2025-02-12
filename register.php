<?php
include 'db_connect.php';

$registration_success = false;
$registration_error = '';
$username_error = '';
$email_error = '';
$phone_error = '';

$username = '';
$first_name = '';
$last_name = '';
$email = '';
$phone_number = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone_number = $_POST['phone_number'];


    $phone_number = preg_replace('/\D/', '', $phone_number); 

    if ($password != $confirm_password) {
        $registration_error = "Hasła nie są zgodne.";
    } else {
        $user_check_sql = "SELECT * FROM uzytkownicy WHERE nazwa = '$username'";
        $email_check_sql = "SELECT * FROM uzytkownicy WHERE email = '$email'";
        $phone_check_sql = "SELECT * FROM uzytkownicy WHERE numer = '$phone_number'";

        $user_check_result = $conn->query($user_check_sql);
        $email_check_result = $conn->query($email_check_sql);
        $phone_check_result = $conn->query($phone_check_sql);

        if ($user_check_result->num_rows > 0) {
            $username_error = "Nazwa użytkownika jest już zajęta.";
        }
        if ($email_check_result->num_rows > 0) {
            $email_error = "Email jest już zajęty.";
        }
        if ($phone_check_result->num_rows > 0) {
            $phone_error = "Numer telefonu jest już zajęty.";
        }

        // Jeśli nie ma błędów, dodaj użytkownika do bazy danych
        if (empty($username_error) && empty($email_error) && empty($phone_error)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO uzytkownicy (nazwa, imie, nazwisko, email, haslo, numer, data_dolaczenia) 
                    VALUES ('$username', '$first_name', '$last_name', '$email', '$hashed_password', '$phone_number', NOW())";

            if ($conn->query($sql) === TRUE) {
                $registration_success = true;
            } else {
                $registration_error = "Error: " . $sql . "<br>" . $conn->error;
            }

            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMS - Rejestracja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url("img/background.jpg");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
        }

        .form-container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .form-container h1 {
            margin-bottom: 1rem;
            color: #00796b;
        }

        .form-container label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .form-container input {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
        }

        .form-container button {
            width: 100%;
            padding: 0.75rem;
            background-color: #00796b;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        .form-container button:hover {
            background-color: #004d40;
        }

        .form-container a {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #00796b;
            text-decoration: none;
        }

        .form-container a:hover {
            text-decoration: underline;
        }

        .message {
            font-size: 1rem;
            color: red;
        }

        .success {
            color: green;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const phoneNumberInput = document.getElementById('phone_number');

            phoneNumberInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 9) {
                    value = value.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1-$2-$3').replace(/-$/, ''); 
                }
                e.target.value = value;
            });

            phoneNumberInput.addEventListener('blur', function(e) {
                let value = e.target.value.replace(/\D/g, ''); 
                if (value.length > 9) {
                    value = value.slice(0, 9); 
                }
                e.target.value = value.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1-$2-$3').replace(/-$/, ''); 
            });
        });
    </script>
</head>
<body>
    <div class="form-container">
        <?php if ($registration_success): ?>
            <h1 class="success">Rejestracja zakończona sukcesem!</h1>
            <p class="success">Przekierowanie na stronę główną...</p>
            <script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 2000);
            </script>
        <?php else: ?>
            <h1>Zarejestruj się</h1>
            <form action="register.php" method="POST">
                <label for="username">Nazwa użytkownika</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                <?php if ($username_error): ?>
                    <p class="message"><?php echo $username_error; ?></p>
                <?php endif; ?>
                <label for="first_name">Imię</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                <label for="last_name">Nazwisko</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <?php if ($email_error): ?>
                    <p class="message"><?php echo $email_error; ?></p>
                <?php endif; ?>
                <label for="password">Hasło</label>
                <input type="password" id="password" name="password" required>
                <label for="confirm_password">Potwierdź hasło</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <?php if ($registration_error && !$username_error && !$email_error && !$phone_error): ?>
                    <p class="message"><?php echo $registration_error; ?></p>
                <?php endif; ?>
                <label for="phone_number">Numer telefonu</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
                <?php if ($phone_error): ?>
                    <p class="message"><?php echo $phone_error; ?></p>
                <?php endif; ?>
                <button type="submit">Zarejestruj się</button>
            </form>
            <a href="login.php">Masz już konto? Zaloguj się</a>
        <?php endif; ?>
    </div>
</body>
</html>