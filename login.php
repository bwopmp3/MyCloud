<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_OFF);
session_start();

if (isset($_POST['user'])) {
    $user = htmlentities($_POST['user'], ENT_QUOTES, "UTF-8");
    $pass = htmlentities($_POST['pass'], ENT_QUOTES, "UTF-8");
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $link = mysqli_connect("127.0.0.1:3308", "mymmksznnx_baza5", "Baza51234!", "mymmksznnx_baza5");
    if (!$link) { die("Błąd połączenia z bazą: " . mysqli_connect_error()); }
    mysqli_query($link, "SET NAMES 'utf8'");
    
    $result = mysqli_query($link, "SELECT * FROM users WHERE username='$user'"); 
    $rekord = mysqli_fetch_array($result);
    
    if ($rekord && $rekord['password'] == $pass) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $rekord['username']; 
        $_SESSION['avatar'] = $rekord['avatar'];
        $_SESSION['user_id'] = $rekord['id'];
        $_SESSION['home_dir'] = $rekord['username'];

        // Próba zapisu udanego logowania do goscieportalu
        mysqli_query($link, "INSERT INTO goscieportalu (username, ip, status) VALUES ('$user', '$ip', 'OK')");

        mysqli_close($link);
        header('Location: index1.php'); 
        exit();
    } else {
        // Próba zapisu błędnego logowania do goscieportalu
        mysqli_query($link, "INSERT INTO goscieportalu (username, ip, status) VALUES ('$user', '$ip', 'Błąd')");
        $error = "Błędny login lub hasło!";
    }
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Logowanie - MyCloud</title>
</head>
<body class="text-center mt-5 bg-light">
    <main class="form-signin w-25 m-auto card p-4 shadow-sm">
        <form method="post">
            <h1 class="h3 mb-3 fw-normal">Zaloguj się do MyCloud</h1>
            <?php if(isset($_GET['registered'])) echo "<div class='alert alert-success'>Konto utworzone! Możesz się zalogować.</div>"; ?>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <div class="form-floating mb-2">
                <input type="text" name="user" class="form-control" placeholder="Login" required>
                <label>Login</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="pass" class="form-control" placeholder="Hasło" required>
                <label>Hasło</label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit">Zaloguj</button>
            <a href="rejestracja.php" class="mt-3 d-block">Zarejestruj się</a>
        </form>
    </main>
</body>
</html>