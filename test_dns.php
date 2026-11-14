<?php declare(strict_types=1); session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: login.php'); exit(); }
echo "<b>Rekord DNS (pbs.edu.pl):</b><br>";
$result = dns_get_record("pbs.edu.pl");
print_r($result);
echo "<br><br><b>IP serwera (pbs.edu.pl):</b><br>";
echo gethostbyname('pbs.edu.pl') . "<br>";
echo "<br><b>Twoje IP:</b><br>";
echo $_SERVER["REMOTE_ADDR"] . "<br>";
?>