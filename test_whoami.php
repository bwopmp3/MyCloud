<?php declare(strict_types=1); session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: login.php'); exit(); }
echo "Zalogowany użytkownik hostingu: " . exec('whoami');
?>