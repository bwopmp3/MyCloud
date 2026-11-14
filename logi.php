<?php declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: login.php'); exit(); }

$link = mysqli_connect("127.0.0.1:3308", "mymmksznnx_baza5", "Baza51234!", "mymmksznnx_baza5");
mysqli_query($link, "SET NAMES 'utf8'");

// 1. Zapisanie wizyty bieżącego użytkownika (tylko IP, data wstawia się sama)
$current_ip = $_SERVER['REMOTE_ADDR'];
mysqli_query($link, "INSERT INTO goscieportalu (ipaddress) VALUES ('$current_ip')");

// 2. Zapytanie SQL dla Teleinformatyki: Grupowanie po IP, zliczanie wizyt, sortowanie malejąco
$query = "SELECT ipaddress, COUNT(*) as ilosc_wejsc FROM goscieportalu GROUP BY ipaddress ORDER BY ilosc_wejsc DESC";
$result = mysqli_query($link, $query);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logi i Geolokalizacja</title>
    <!-- Biblioteki szablonu (Bootstrap + DataTables do filtrowania i paginacji) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="twoj_js.js"></script>
</head>
<body onload="myLoadHeader()">
    <div id='myHeader'></div>
    <main class="container mt-5 pt-5">
        <h2>Logi i lokalizacja wizyt strony</h2>
        <table id="tabelaLogow" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Adres IP</th>
                    <th>Ilość wejść</th>
                    <th>Kraj</th>
                    <th>Miasto</th>
                    <th>Lokalizacja (Mapy Google)</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // 3. Wyświetlanie danych i pobieranie geolokalizacji JSON w locie
            while($row = mysqli_fetch_array($result)) {
                $ip = $row['ipaddress'];
                $count = $row['ilosc_wejsc'];
                
                // Pobieranie danych z API
                $json = @file_get_contents("http://ipinfo.io/{$ip}/geo");
                $details = json_decode($json);
                
                $country = $details->country ?? 'Brak danych';
                $city = $details->city ?? 'Brak danych';
                $loc = $details->loc ?? null;
                
                // Generowanie linku do Google Maps na podstawie zmiennej $loc
                $mapLink = $loc ? "<a href='https://www.google.pl/maps/place/{$loc}' target='_blank'>LINK</a>" : "Brak";

                echo "<tr>
                        <td>{$ip}</td>
                        <td><b>{$count}</b></td>
                        <td>{$country}</td>
                        <td>{$city}</td>
                        <td>{$mapLink}</td>
                      </tr>";
            }
            mysqli_close($link);
            ?>
            </tbody>
        </table>
    </main>

    <!-- Inicjalizacja skryptu DataTables (Sortowanie, Wyszukiwanie, Paginacja) -->
    <script>
        $(document).ready(function() {
            $('#tabelaLogow').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Polish.json"
                }
            });
        });
    </script>
</body>
</html>