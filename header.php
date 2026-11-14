<?php declare(strict_types=1); session_start(); ?>
<header>
<div class="container">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top mt-0 mb-0 ms-0 me-0 pt-0 pb-0 ps-0 pe-0">
	<div class="container-fluid">
	
        <!-- Przycisk menu dla urządzeń mobilnych -->
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main_nav"  aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="main_nav">
            <!-- Lewa strona menu -->
			<ul class="navbar-nav">
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Polecenia 1.x</a>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" href="polecenie1_1.php"> <img src="media/menu_icons/info.svg" height="18"> Polecenie 1.1 </a></li>
						<li><a class="dropdown-item" href="polecenie1_2.php"> <img src="media/menu_icons/help.svg" height="18"> Polecenie 1.2 </a></li>
					</ul>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Polecenia 2.x</a>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" href="polecenie2_1.php"> Polecenie 2.1 </a></li>
						<li><a class="dropdown-item" href="polecenie2_2.php"> Polecenie 2.2 </a></li>
					</ul>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Polecenia 3.x</a>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" href="polecenie3_1.php"> Polecenie 3.1 </a></li>
					    <li><a class="dropdown-item" href="polecenie3_2.php"> Polecenie 3.2 </a></li>
                        <li><a class="dropdown-item" href="polecenie3_3.php"> Polecenie 3.3 </a></li>
					</ul>
				</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Skrypty Sieciowe</a>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="test_phpinfo.php"> PHP Info </a></li>
        <li><a class="dropdown-item" href="test_whoami.php"> Whoami </a></li>
        <li><a class="dropdown-item" href="test_top.php"> Procesy (Top) </a></li>
        <li><a class="dropdown-item" href="test_ls.php"> Pliki (ls -al) </a></li>
        <li><a class="dropdown-item" href="test_dns.php"> Testy DNS </a></li>
    </ul>
</li>
                <!-- Logi jako osobny element menu po lewej -->
                <li class="nav-item"><a class="nav-link" href="logi.php">Logi Geolokalizacji</a></li>
<li class="nav-item"><a class="nav-link" href="index1.php">MyCloud</a></li>
			</ul> 

          
            
            <!-- Prawa strona menu (Profil i Wyloguj) -->
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <li class="nav-item d-flex align-items-center me-3 text-light">
                        <img src="avatary/<?php echo htmlspecialchars($_SESSION['avatar']); ?>" alt="Avatar" width="32" height="32" class="rounded-circle border border-light me-2" style="object-fit: cover;">
                        <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-sm btn-outline-light" href="logout.php">Wyloguj</a>
                    </li>
                <?php endif; ?>
            </ul>
		</div> 
	</div> 
</nav>		
</div>
</header>