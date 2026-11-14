<?php
declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$user_login = $_SESSION['username'];
$base_dir = $user_login;

// Zabezpieczenie: utwórz katalog macierzysty, jeśli konto istniało przed migracją bazy
if (!is_dir($base_dir)) {
    mkdir($base_dir, 0777, true);
}

// Obsługa aktualnego podkatalogu (tylko 1 poziom zagłębienia, zabezpieczenie przed Path Traversal)
$sub = "";
if (isset($_GET['dir']) && $_GET['dir'] !== "") {
    $requested_dir = basename($_GET['dir']);
    if (preg_match('/^[a-zA-Z0-9_-]+$/', $requested_dir) && is_dir($base_dir . "/" . $requested_dir)) {
        $sub = $requested_dir;
    }
}

$current_dir = ($sub !== "") ? ($base_dir . "/" . $sub) : $base_dir;
$redirect_url = "index1.php" . ($sub !== "" ? "?dir=" . urlencode($sub) : "");
$msg = "";

// 1. TWORZENIE PODKATALOGU (dozwolone tylko w katalogu głównym użytkownika)
if (isset($_POST['new_folder']) && $sub === "") {
    $folder_name = trim($_POST['folder_name']);
    if (preg_match('/^[a-zA-Z0-9_-]+$/', $folder_name)) {
        $new_path = $base_dir . "/" . $folder_name;
        if (!file_exists($new_path)) {
            mkdir($new_path, 0777, true);
            header("Location: index1.php");
            exit();
        } else {
            $msg = "<div class='alert alert-warning'>Folder o tej nazwie już istnieje.</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Nazwa folderu może zawierać tylko litery, cyfry, _ oraz -.</div>";
    }
}

// 2. PRZESYŁANIE PLIKU (UPLOAD)
if (isset($_POST['upload_submit']) && isset($_FILES['fileToUpload'])) {
    if ($_FILES['fileToUpload']['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES['fileToUpload']['name']);
        $target_file = $current_dir . "/" . $filename;
        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file)) {
            header("Location: " . $redirect_url);
            exit();
        } else {
            $msg = "<div class='alert alert-danger'>Błąd zapisu pliku na serwerze.</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Nie wybrano pliku lub wystąpił błąd przesyłania.</div>";
    }
}

// 3. USUWANIE PLIKU LUB PODKATALOGU
if (isset($_GET['delete'])) {
    $item_to_delete = basename($_GET['delete']);
    $target_path = $current_dir . "/" . $item_to_delete;

    if (file_exists($target_path)) {
        if (is_file($target_path)) {
            unlink($target_path);
        } elseif (is_dir($target_path) && $sub === "") {
            // Usuń najpierw wszystkie pliki wewnątrz podkatalogu, potem sam katalog
            $files_inside = array_diff(scandir($target_path), ['.', '..']);
            foreach ($files_inside as $f) {
                unlink($target_path . "/" . $f);
            }
            rmdir($target_path);
        }
        header("Location: " . $redirect_url);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>MyCloud - Dysk Sieciowy</title>
    <style>
        .thumb-img { max-width: 80px; max-height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6; }
        .media-player { max-width: 240px; height: 36px; }
        .video-player { max-width: 220px; max-height: 125px; border-radius: 4px; }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index1.php">☁️ MyCloud</a>
            <div class="d-flex align-items-center text-white gap-3">
                <span>Zalogowany: <strong><?php echo htmlspecialchars($user_login); ?></strong></span>
                <a href="logi.php" class="btn btn-outline-info btn-sm">Logi wizyt</a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Wyloguj</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <?php echo $msg; ?>

        <!-- Pasek ścieżki i nawigacji -->
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm mb-4">
            <div>
                <span class="text-muted">Lokalizacja:</span>
                <strong>/<?php echo htmlspecialchars($current_dir); ?></strong>
            </div>
            <?php if ($sub !== ""): ?>
                <a href="index1.php" class="btn btn-warning fw-bold">⬆️ Poziom wyżej (Level Up)</a>
            <?php endif; ?>
        </div>

        <!-- Panele akcji: Upload oraz Tworzenie folderu -->
        <div class="row g-3 mb-4">
            <div class="<?php echo ($sub === "") ? "col-md-6" : "col-12"; ?>">
                <div class="card shadow-sm p-3 h-100">
                    <h6 class="card-title text-primary fw-bold">📤 Wyślij plik na dysk</h6>
                    <form method="post" enctype="multipart/form-data" class="d-flex gap-2 mt-2">
                        <input type="file" name="fileToUpload" class="form-control" required>
                        <button type="submit" name="upload_submit" class="btn btn-primary">Wyślij</button>
                    </form>
                </div>
            </div>

            <?php if ($sub === ""): ?>
            <div class="col-md-6">
                <div class="card shadow-sm p-3 h-100">
                    <h6 class="card-title text-success fw-bold">📁 Nowy podkatalog</h6>
                    <form method="post" class="d-flex gap-2 mt-2">
                        <input type="text" name="folder_name" placeholder="Nazwa folderu (litery/cyfry)" class="form-control" required>
                        <button type="submit" name="new_folder" class="btn btn-success">Utwórz</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Lista plików i folderów -->
        <div class="card shadow-sm">
            <table class="table table-hover align-middle mb-0 bg-white">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 60px;">Typ</th>
                        <th>Nazwa</th>
                        <th>Podgląd / Odtwarzacz</th>
                        <th>Rozmiar</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
<?php
$items = array_diff(scandir($current_dir), ['.', '..']);
$folders = [];
$files = [];

foreach ($items as $item) {
    if (is_dir($current_dir . "/" . $item)) {
        $folders[] = $item;
    } else {
        $files[] = $item;
    }
}

if (empty($folders) && empty($files)) {
    echo "<tr><td colspan='5' class='text-center text-muted py-4'>Ten katalog jest pusty.</td></tr>";
}

// Najpierw wyświetlamy podkatalogi
foreach ($folders as $folder) {
    $folder_url = "index1.php?dir=" . urlencode($folder);
    $del_url = "index1.php?delete=" . urlencode($folder);
    echo "<tr>
            <td class='fs-4'>📁</td>
            <td><a href='$folder_url' class='fw-bold text-decoration-none'>" . htmlspecialchars($folder) . "</a></td>
            <td><span class='badge bg-secondary'>Katalog</span></td>
            <td>-</td>
            <td class='text-end'>
                <a href='$del_url' class='btn btn-outline-danger btn-sm' onclick=\"return confirm('Usunąć folder wraz z całą zawartością?');\">🗑️ Usuń</a>
            </td>
          </tr>";
}

// Następnie wyświetlamy pliki
foreach ($files as $file) {
    $file_path = $current_dir . "/" . $file;
    // Kodowanie segmentów URL dla spacji i polskich znaków
    $encoded_path = rawurlencode($base_dir) . "/" . ($sub !== "" ? rawurlencode($sub) . "/" : "") . rawurlencode($file);
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $size_kb = round(filesize($file_path) / 1024, 1) . " KB";
    $del_url = $redirect_url . ($sub !== "" ? "&" : "?") . "delete=" . urlencode($file);

    $icon = "📄";
    $preview = "<span class='text-muted small'>Kliknij nazwę, aby otworzyć/pobrać</span>";
    $link_attr = "download";

    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        $icon = "🖼️";
        $preview = "<a href='$encoded_path' target='_blank'><img src='$encoded_path' class='thumb-img' alt='podgląd'></a>";
        $link_attr = "target='_blank'";
    } elseif (in_array($ext, ['mp3', 'wav', 'ogg'])) {
        $icon = "🎵";
        $preview = "<audio controls preload='none' class='media-player'><source src='$encoded_path'></audio>";
        $link_attr = "download";
    } elseif (in_array($ext, ['mp4', 'webm'])) {
        $icon = "🎬";
        $preview = "<video controls preload='none' class='video-player'><source src='$encoded_path'></video>";
        $link_attr = "download";
    } elseif ($ext === 'pdf') {
        $icon = "📕";
        $link_attr = "target='_blank'";
    }

    echo "<tr>
            <td class='fs-4'>$icon</td>
            <td><a href='$encoded_path' $link_attr class='text-decoration-none fw-semibold'>" . htmlspecialchars($file) . "</a></td>
            <td>$preview</td>
            <td>$size_kb</td>
            <td class='text-end'>
                <a href='$encoded_path' download class='btn btn-outline-primary btn-sm me-1'>⬇️ Pobierz</a>
                <a href='$del_url' class='btn btn-outline-danger btn-sm' onclick=\"return confirm('Na pewno usunąć ten plik?');\">🗑️ Usuń</a>
            </td>
          </tr>";
}
?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>