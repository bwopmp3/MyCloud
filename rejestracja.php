<?php
// Wymuszamy pokazywanie błędów
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_OFF);

if (isset($_POST['user'])) {
    $user = trim($_POST['user']);
    $pass = htmlentities($_POST['pass'], ENT_QUOTES, "UTF-8");
    $pass2 = htmlentities($_POST['pass2'], ENT_QUOTES, "UTF-8");
    $avatar = 'user.svg'; // Domyślna ikona z zadania

    if (!preg_match('/^[a-zA-Z0-9]+$/', $user)) {
        $error = "Login może zawierać wyłącznie litery i cyfry (wymagane do utworzenia katalogu)!";
    } elseif (strlen($pass) < 2) {
        $error = "Hasło musi składać się z minimum 2 znaków!";
    } elseif ($pass != $pass2) {
        $error = "Podane hasła nie są identyczne!";
    } else {
        $link = mysqli_connect("127.0.0.1:3308", "mymmksznnx_baza5", "Baza51234!", "mymmksznnx_baza5");
        if (!$link) { die("Błąd połączenia z bazą: " . mysqli_connect_error()); }
        mysqli_query($link, "SET NAMES 'utf8'");

        $check = mysqli_query($link, "SELECT id FROM users WHERE username='$user'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Taki login jest już zajęty!";
        } else {
            if (isset($_FILES['avatar']['name']) && $_FILES['avatar']['name'] != "") {
                if (!is_dir("avatary")) {
                    mkdir("avatary", 0777, true);
                }
                $avatar = basename($_FILES['avatar']['name']);
                move_uploaded_file($_FILES['avatar']['tmp_name'], "avatary/" . $avatar);
            }

            mysqli_query($link, "INSERT INTO users (username, password, avatar) VALUES ('$user', '$pass', '$avatar')");

            // Tworzenie katalogu macierzystego użytkownika na dysku MyCloud
            if (!is_dir($user)) {
                mkdir($user, 0777, true);
            }

            mysqli_close($link);
            header('Location: login.php?registered=1');
            exit();
        }
        mysqli_close($link);
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Rejestracja - MyCloud</title>
</head>
<body class="bg-light mt-5 pt-4">
    <div class="container d-flex justify-content-center">
        <div class="card shadow-lg p-4" style="width: 100%; max-width: 450px; border-radius: 15px;">
            <h2 class="text-center mb-4 text-primary fw-bold">Rejestracja</h2>
            
            <?php if(isset($error)) echo "<div class='alert alert-danger text-center'>$error</div>"; ?>
            
            <form method="post" enctype="multipart/form-data">
                
                <!-- Sekcja podglądu awatara -->
                <div class="text-center mb-4">
                    <img id="avatarPreview" src="avatary/user.svg" alt="Podgląd Awatara" class="rounded-circle border border-3 border-primary shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                </div>

                <!-- Pola tekstowe -->
                <div class="form-floating mb-3">
                    <input type="text" name="user" class="form-control" placeholder="Login" required>
                    <label>Login (tylko litery i cyfry)</label>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="password" name="pass" class="form-control" placeholder="Hasło" required>
                    <label>Hasło</label>
                </div>
                
                <div class="form-floating mb-4">
                    <input type="password" name="pass2" class="form-control" placeholder="Powtórz hasło" required>
                    <label>Powtórz hasło</label>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">Wybierz swój awatar (Opcjonalnie)</label>
                    <input type="file" name="avatar" class="form-control" accept="image/*" onchange="previewAvatar(event)">
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold fs-5 mb-3" style="border-radius: 10px;">Załóż konto</button>
                
                <div class="text-center">
                    <span class="text-muted">Masz już konto?</span> <a href="login.php" class="text-decoration-none fw-bold">Zaloguj się</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Skrypt JS do podglądu awatara w locie -->
    <script>
        function previewAvatar(event) {
            var input = event.target;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                document.getElementById('avatarPreview').src = 'avatary/user.svg';
            }
        }
    </script>
</body>
</html>