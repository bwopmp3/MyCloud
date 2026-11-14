<?php declare(strict_types=1); session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: login.php'); exit(); }
exec('TERM=xterm /usr/bin/top -n 1 -b -i', $top, $error);
echo nl2br(implode("\n", $top));
if ($error){ echo "Błąd lub brak uprawnień"; }
?>