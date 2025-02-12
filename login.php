<?php
include 'db_connect.php';
session_start();

$login_success = false;
$login_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $_SESSION['email'] = $email;

    $sql = "SELECT * FROM uzytkownicy WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['haslo'])) {
            $_SESSION['uzytkownik_ID'] = $row['uzytkownik_ID'];
            $login_success = true;
            unset($_SESSION['email']);
        } else {
            $login_error = "Nieprawidłowe hasło.";
        }
    } else {
        $login_error = "Nie znaleziono użytkownika.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMS - Logowanie</title>
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
            margin-top: 1rem;
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Logowanie</h1>
        <?php if ($login_success): ?>
            <p class="message success">Zalogowano pomyślnie. Przekierowanie...</p>
            <script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 2000);
            </script>
        <?php else: ?>
            <form action="login.php" method="POST">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" required>
                <label for="password">Hasło</label>
                <input type="password" id="password" name="password" required>
                <?php if ($login_error): ?>
                    <p class="message"><?php echo $login_error; ?></p>
                <?php endif; ?>
                <button type="submit">Zaloguj się</button>
            </form>
            <a href="register.php">Nie masz jeszcze konta? Zarejestruj się</a>
        <?php endif; ?>
    </div>
</body>
</html>