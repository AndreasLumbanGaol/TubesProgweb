<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default location to Bandung if not set
if (!isset($_SESSION['selected_location'])) {
    $_SESSION['selected_location'] = 'Bandung';
}

// Check if location is being changed via GET request
if (isset($_GET['set_location'])) {
    $_SESSION['selected_location'] = $_GET['set_location'];
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Home</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { 
            background-color: #0d0606; 
            color: #ffffff; 
            font-family: sans-serif; 
        }

        .navbar { 
            border-bottom: 1px solid #3a2626;
            padding-top: 16px;
            padding-bottom: 16px;
        }
        .navbar-container {
            padding-left: 24px;
            padding-right: 24px;
        }
        .navbar-brand { 
            color: #d4af37 !important; 
            font-family: serif; 
            font-size: 24px; 
            font-weight: bold;
        }
        .navbar-brand span {
            font-style: italic; 
            color: #b8962e; 
            font-weight: normal;
        }
        
        .nav-center-menu {
            margin: 0 auto;
            align-items: center;
        }
        .nav-link { 
            color: #cccccc !important; 
            font-weight: 500; 
            margin: 0 10px;
        }
        .nav-link.active {
            color: #d4af37 !important; 
            border: 1px solid rgba(212, 175, 55, 0.5); 
            border-radius: 8px; 
            background: rgba(212, 175, 55, 0.1);
            padding: 4px 16px;
        }

        .user-actions {
            display: flex;
            align-items: center;
        }
        .login-button {
            background-color: #d4af37; 
            color: #000; 
            font-weight: bold; 
            border-radius: 5px; 
            padding: 8px 16px;
            font-size: 14px;
            text-decoration: none;
            margin-right: 16px;
            transition: background-color 0.3s;
            border: none;
        }
        .login-button:hover {
            background-color: #b8962e;
            color: #000;
        }
        .profile-icon {
            width: 32px; 
            height: 32px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 1px solid #d4af37;
        }

        .hero-carousel {
            border-bottom: 1px solid #3a2626;
        }
        .hero-carousel .carousel-item {
            overflow: hidden;
        }
        .carousel-img {
            height: 500px;
            object-fit: cover;
            filter: brightness(35%);
            transition: transform 4s ease-out; /* Smooth transition when changing slide */
        }
        
        /* Ken Burns Cinematic Image Zooming Keyframe */
        @keyframes kenBurns {
            from {
                transform: scale(1.0);
            }
            to {
                transform: scale(1.12);
            }
        }
        
        /* Trigger Ken Burns effect on the active slide image */
        .hero-carousel .carousel-item.active .carousel-img {
            animation: kenBurns 6s ease-out forwards;
        }

        .hero-carousel .carousel-caption {
            bottom: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }
        
        /* Fade-In-Up Entry Keyframe */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(35px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-title, .hero-subtitle, .hero-button {
            opacity: 0; /* Hidden initially to allow animation entry */
        }

        /* Staggered Slide entries on active slide elements */
        .hero-carousel .carousel-item.active .hero-title {
            animation: fadeInUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
            animation-delay: 0.3s;
        }
        .hero-carousel .carousel-item.active .hero-subtitle {
            animation: fadeInUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
            animation-delay: 0.6s;
        }
        .hero-carousel .carousel-item.active .hero-button {
            animation: fadeInUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
            animation-delay: 0.9s;
            display: inline-block;
        }

        .hero-title { 
            font-size: 72px; 
            font-weight: bold; 
            letter-spacing: 2px; 
            margin-bottom: 10px; 
        }
        .hero-subtitle { 
            color: #d4af37; 
            font-size: 19px; 
            letter-spacing: 2px; 
            margin-bottom: 30px; 
        }
        
        .hero-button { 
            background-color: #b30000; 
            color: white; 
            padding: 10px 30px; 
            border-radius: 30px; 
            font-weight: bold; 
            border: none; 
            letter-spacing: 1px;
            transition: background-color 0.3s;
            text-decoration: none;
            cursor: pointer;
        }
        .hero-button:hover { 
            background-color: #ff1a1a; 
            color: white; 
        }
        .hero-button-gold {
            background-color: #d4af37; 
            color: #000;
        }
        .hero-button-gold:hover {
            background-color: #b8962e; 
            color: #000;
        }

        .movies-section {
            padding-bottom: 48px;
        }
        .section-header { 
            display: flex; 
            align-items: center; 
            margin-top: 40px; 
            margin-bottom: 25px; 
        }
        .section-title { 
            font-size: 21px; 
            font-weight: bold; 
            border-left: 3px solid #d4af37; 
            padding-left: 15px; 
            margin: 0; 
        }
        .location-badge { 
            background-color: #333333; 
            color: #d4af37; 
            padding: 5px 15px; 
            border-radius: 20px; 
            margin-left: 20px; 
            font-size: 14px; 
            display: flex; 
            align-items: center; 
            gap: 5px; 
        }

        .movie-grid {
            --bs-gutter-x: 24px;
            --bs-gutter-y: 24px;
        }
        .movie-link {
            text-decoration: none !important;
            color: inherit;
            display: block;
        }
        .movie-card { 
            background: transparent; 
            border: none; 
            text-align: center; 
            transition: transform 0.3s; 
            cursor: pointer; 
        }
        .movie-card:hover { transform: scale(1.05); }

        .poster-wrapper {
            position: relative;
            width: 100%;
            margin-bottom: 10px;
        }
        .movie-poster { 
            border-radius: 10px; 
            width: 100%; 
            aspect-ratio: 2/3; 
            object-fit: cover; 
            margin-bottom: 0; 
        }
        
        .badge-presale {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #c99a49;
            color: #000000;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 12px;
            border-radius: 20px;
            z-index: 2;
        }

        .movie-title { 
            color: #d4af37; 
            font-size: 14px; 
            margin: 0; 
            white-space: nowrap; 
            overflow: hidden; 
            text-overflow: ellipsis; 
        }
        .movie-duration { 
            color: #888; 
            font-size: 12px; 
            margin: 0; 
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid navbar-container">
            <a class="navbar-brand" href="index.php">Tixly<span>Cinema</span></a>
            
            <!-- Location Selector Dropdown next to Brand -->
            <div class="dropdown me-auto ms-3 d-none d-lg-block">
                <button class="btn btn-outline-warning btn-sm dropdown-toggle rounded-pill px-3" type="button" id="navbarLocationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: rgba(212, 175, 55, 0.4); color: #d4af37; background: rgba(212, 175, 55, 0.05); font-size: 13px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-geo-alt-fill me-1" viewBox="0 0 16 16">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                    <span><?php echo htmlspecialchars($_SESSION['selected_location']); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarLocationDropdown" style="background-color: #120707; border: 1px solid rgba(212, 175, 55, 0.3); z-index: 1050;">
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Bandung') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Bandung" style="<?php echo ($_SESSION['selected_location'] === 'Bandung') ? 'background-color: #d4af37;' : ''; ?>">Bandung</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Jakarta') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Jakarta" style="<?php echo ($_SESSION['selected_location'] === 'Jakarta') ? 'background-color: #d4af37;' : ''; ?>">Jakarta</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Surabaya') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Surabaya" style="<?php echo ($_SESSION['selected_location'] === 'Surabaya') ? 'background-color: #d4af37;' : ''; ?>">Surabaya</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Medan') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Medan" style="<?php echo ($_SESSION['selected_location'] === 'Medan') ? 'background-color: #d4af37;' : ''; ?>">Medan</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Yogyakarta') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Yogyakarta" style="<?php echo ($_SESSION['selected_location'] === 'Yogyakarta') ? 'background-color: #d4af37;' : ''; ?>">Yogyakarta</a></li>
                </ul>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav nav-center-menu">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="films.php">Films</a></li>
                    <li class="nav-item"><a class="nav-link" href="resell.php">Resell Ticket</a></li>
                </ul>
                <div class="user-actions">
                    <a href="login.php" class="login-button">
                        LOG IN / SIGN UP
                    </a>
                    <a href="profile.php">
                        <img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" 
                             alt="Profile Icon" 
                             class="profile-icon">
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div id="carouselExample" class="carousel slide hero-carousel" data-bs-ride="carousel">
        <div class="carousel-inner">

            <div class="carousel-item active" data-bs-interval="4000">
                <img src="https://cloud.jpnn.com/photo/arsip/normal/2025/12/07/teaser-poster-film-ghost-in-the-cell-foto-dok-come-and-see-n-ufoh.jpg" class="d-block w-100 carousel-img" alt="Ghost in the Cell">
                <div class="carousel-caption hero-content">
                    <h1 class="hero-title">GHOST IN THE CELL</h1>
                    <p class="hero-subtitle">BUY 1 GET 1 FREE TICKET</p>
                    <button class="hero-button">GET YOUR TICKET</button>
                </div>
            </div>

            <div class="carousel-item" data-bs-interval="4000">
                <img src="https://galalitescreens.com/wp-content/uploads/2017/11/cinema-theatre.webp" class="d-block w-100 carousel-img" alt="Diskon Tixly">
                <div class="carousel-caption hero-content">
                    <h1 class="hero-title">WEEKEND SPECIAL</h1>
                    <p class="hero-subtitle">CASHBACK 50% VIA GOPAY</p>
                    <button class="hero-button">CEK PROMO</button>
                </div>
            </div>

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container movies-section">
        
        <div class="section-header">
            <h2 class="section-title">NOW SHOWING</h2>
            <div class="dropdown ms-3">
                <button class="btn btn-secondary dropdown-toggle location-badge border-0" type="button" id="nowShowingLocationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #333333; color: #d4af37; border-radius: 20px; font-size: 14px; padding: 5px 15px; display: flex; align-items: center; gap: 5px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                    <span><?php echo htmlspecialchars($_SESSION['selected_location']); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="nowShowingLocationDropdown" style="background-color: #120707; border: 1px solid rgba(212, 175, 55, 0.4);">
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Bandung') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Bandung" style="<?php echo ($_SESSION['selected_location'] === 'Bandung') ? 'background-color: #d4af37;' : ''; ?>">Bandung</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Jakarta') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Jakarta" style="<?php echo ($_SESSION['selected_location'] === 'Jakarta') ? 'background-color: #d4af37;' : ''; ?>">Jakarta</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Surabaya') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Surabaya" style="<?php echo ($_SESSION['selected_location'] === 'Surabaya') ? 'background-color: #d4af37;' : ''; ?>">Surabaya</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Medan') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Medan" style="<?php echo ($_SESSION['selected_location'] === 'Medan') ? 'background-color: #d4af37;' : ''; ?>">Medan</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Yogyakarta') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Yogyakarta" style="<?php echo ($_SESSION['selected_location'] === 'Yogyakarta') ? 'background-color: #d4af37;' : ''; ?>">Yogyakarta</a></li>
                </ul>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 movie-grid">
            <div class="col">
                <a href="#" class="movie-link" data-bs-toggle="modal" data-bs-target="#bookingModal" data-title="AVATAR: The Way of Water" data-poster="https://upload.wikimedia.org/wikipedia/id/5/54/Avatar_The_Way_of_Water_poster.jpg" data-duration="3h 12m">
                    <div class="card movie-card">
                        <div class="poster-wrapper">
                            <img src="https://upload.wikimedia.org/wikipedia/id/5/54/Avatar_The_Way_of_Water_poster.jpg" class="movie-poster" alt="Avatar">
                        </div>
                        <p class="movie-title">AVATAR: The Way of Water</p>
                        <p class="movie-duration">3h 12m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="#" class="movie-link" data-bs-toggle="modal" data-bs-target="#bookingModal" data-title="JOKER: Put On A Happy Face" data-poster="https://upload.wikimedia.org/wikipedia/en/e/e1/Joker_%282019_film%29_poster.jpg" data-duration="60m">
                    <div class="card movie-card">
                        <div class="poster-wrapper">
                            <img src="https://upload.wikimedia.org/wikipedia/en/e/e1/Joker_%282019_film%29_poster.jpg" class="movie-poster" alt="Joker">
                        </div>
                        <p class="movie-title">JOKER: Put On A Happy Face</p>
                        <p class="movie-duration">60m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="#" class="movie-link" data-bs-toggle="modal" data-bs-target="#bookingModal" data-title="Wonka" data-poster="https://image.tmdb.org/t/p/w500/qhb1qOilapbapxWQn9jtRCMwXJF.jpg" data-duration="1h 56m">
                    <div class="card movie-card">
                        <div class="poster-wrapper">
                            <img src="https://image.tmdb.org/t/p/w500/qhb1qOilapbapxWQn9jtRCMwXJF.jpg" class="movie-poster" alt="Wonka">
                        </div>
                        <p class="movie-title">Wonka</p>
                        <p class="movie-duration">1h 56m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="#" class="movie-link" data-bs-toggle="modal" data-bs-target="#bookingModal" data-title="Guardians of The Galaxy" data-poster="https://image.tmdb.org/t/p/w500/r7XifzvtezNt31ypvsmb6Oqxw49.jpg" data-duration="2h 1m">
                    <div class="card movie-card">
                        <div class="poster-wrapper">
                            <img src="https://image.tmdb.org/t/p/w500/r7XifzvtezNt31ypvsmb6Oqxw49.jpg" class="movie-poster" alt="Guardians">
                        </div>
                        <p class="movie-title">Guardians of The Galaxy</p>
                        <p class="movie-duration">2h 1m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="#" class="movie-link" data-bs-toggle="modal" data-bs-target="#bookingModal" data-title="Avengers" data-poster="https://upload.wikimedia.org/wikipedia/en/8/8a/The_Avengers_%282012_film%29_poster.jpg" data-duration="3h 1m">
                    <div class="card movie-card">
                        <div class="poster-wrapper">
                            <img src="https://upload.wikimedia.org/wikipedia/en/8/8a/The_Avengers_%282012_film%29_poster.jpg" class="movie-poster" alt="Avengers">
                        </div>
                        <p class="movie-title">Avengers</p>
                        <p class="movie-duration">3h 1m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="#" class="movie-link" data-bs-toggle="modal" data-bs-target="#bookingModal" data-title="Ghost In The Cell" data-poster="https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_5421e6fbe0a18094aa35cfacf23a23d3.jpg" data-duration="1h 46m">
                    <div class="card movie-card">
                        <div class="poster-wrapper">
                            <img src="https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_5421e6fbe0a18094aa35cfacf23a23d3.jpg" class="movie-poster" alt="Ghost">
                        </div>
                        <p class="movie-title">Ghost In The Cell</p>
                        <p class="movie-duration">1h 46m</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="section-header" style="margin-top: 60px;">
            <h2 class="section-title">COMING SOON</h2>
            <div class="dropdown ms-3">
                <button class="btn btn-secondary dropdown-toggle location-badge border-0" type="button" id="comingSoonLocationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #333333; color: #d4af37; border-radius: 20px; font-size: 14px; padding: 5px 15px; display: flex; align-items: center; gap: 5px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                    <span><?php echo htmlspecialchars($_SESSION['selected_location']); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="comingSoonLocationDropdown" style="background-color: #120707; border: 1px solid rgba(212, 175, 55, 0.4);">
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Bandung') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Bandung" style="<?php echo ($_SESSION['selected_location'] === 'Bandung') ? 'background-color: #d4af37;' : ''; ?>">Bandung</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Jakarta') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Jakarta" style="<?php echo ($_SESSION['selected_location'] === 'Jakarta') ? 'background-color: #d4af37;' : ''; ?>">Jakarta</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Surabaya') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Surabaya" style="<?php echo ($_SESSION['selected_location'] === 'Surabaya') ? 'background-color: #d4af37;' : ''; ?>">Surabaya</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Medan') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Medan" style="<?php echo ($_SESSION['selected_location'] === 'Medan') ? 'background-color: #d4af37;' : ''; ?>">Medan</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Yogyakarta') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Yogyakarta" style="<?php echo ($_SESSION['selected_location'] === 'Yogyakarta') ? 'background-color: #d4af37;' : ''; ?>">Yogyakarta</a></li>
                </ul>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 movie-grid">
            <div class="col">
                <div class="card movie-card">
                    <div class="poster-wrapper">
                        <img src="https://bit.ly/4v8IiMK" class="movie-poster" alt="Sonic 3">
                    </div>
                    <p class="movie-title">SONIC 4: The Hedgehog</p>
                </div>
            </div>
            <div class="col">
                <div class="card movie-card">
                    <div class="poster-wrapper">
                        <img src="https://cinemags.org/?attachment_id=192228" class="movie-poster" alt="Spider-Man">
                    </div>
                    <p class="movie-title">Spider-man: Across The Spider Verse</p>
                </div>
            </div>
            <div class="col">
                <div class="card movie-card">
                    <div class="poster-wrapper">
                        <img src="https://posterspy.com/wp-content/uploads/2024/01/PosterSpy-Man-of-Tomorrow-Teaser-version-site.jpg" class="movie-poster" alt="Man of Tomorrow">
                    </div>
                    <p class="movie-title">Man Of Tomorrow</p>
                </div>
            </div>
            <div class="col">
                <div class="card movie-card">
                    <div class="poster-wrapper">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTaU3-BIeHwacBlx1pr2juR3RH-yhNN06rgdw&s" class="movie-poster" alt="Minion and Monster">
                    </div>
                    <p class="movie-title">Minion & Monsters</p>
                </div>
            </div>
            <div class="col">
                <div class="card movie-card">
                    <div class="poster-wrapper">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTBVpQ2G6WhTrJfC0i0_NiJjxh10EncCx3ujg&s" class="movie-poster" alt="Frozen II">
                    </div>
                    <p class="movie-title">Frozen III</p>
                </div>
            </div>
        </div>

    </div>

    <?php include 'booking_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>