<?php declare(strict_types=1); session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: login.php'); exit(); }
$output = shell_exec('ls -al');
echo "<pre>$output</pre>";
?>