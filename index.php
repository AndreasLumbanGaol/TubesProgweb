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
        .carousel-img {
            height: 500px;
            object-fit: cover;
            filter: brightness(35%);
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
                    <a href="#">
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
                <img src="https://images.unsplash.com/photo-1440404653325-ab127d49abc1?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" class="d-block w-100 carousel-img" alt="Gala Premiere">
                <div class="carousel-caption hero-content">
                    <h1 class="hero-title">GALA PREMIERE</h1>
                    <p class="hero-subtitle">AMANKAN KURSIMU LEBIH AWAL DENGAN PRE-ORDER</p>
                    <button class="hero-button hero-button-gold">LIHAT JADWAL</button>
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
            <div class="location-badge">
                Bandung
            </div>
        </div>

        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 movie-grid">
            <div class="col">
                <div class="card movie-card">
                    <div class="poster-wrapper">
                        <img src="https://upload.wikimedia.org/wikipedia/id/5/54/Avatar_The_Way_of_Water_poster.jpg" class="movie-poster" alt="Avatar">
                    </div>
                    <p class="movie-title">AVATAR: The Way of Water</p>
                    <p class="movie-duration">3h 12m</p>
                </div>
            </div>
            <div class="col">
                <div class="card movie-card">
                    <div class="poster-wrapper">
                        <img src="https://upload.wikimedia.org/wikipedia/en/e/e1/Joker_%282019_film%29_poster.jpg" class="movie-poster" alt="Joker">
                    </div>
                    <p class="movie-title">JOKER: Put On A Happy Face</p>
                    <p class="movie-duration">60m</p>
                </div>
            </div>
            <div class="col">
                <div class="card movie-card">
                    <div class="poster-wrapper">
                        <img src="https://image.tmdb.org/t/p/w500/qhb1qOilapbapxWQn9jtRCMwXJF.jpg" class="movie-poster" alt="Wonka">
                    </div>
                    <p class="movie-title">Wonka</p>
                    <p class="movie-duration">1h 56m</p>
                </div>
            </div>
            <div class="col">
                <div class="card movie-card">
                    <div class="poster-wrapper">
                        <img src="https://image.tmdb.org/t/p/w500/r7XifzvtezNt31ypvsmb6Oqxw49.jpg" class="movie-poster" alt="Guardians">
                    </div>
                    <p class="movie-title">Guardians of The Galaxy</p>
                    <p class="movie-duration">2h 1m</p>
                </div>
            </div>
            <div class="col">
                <div class="card movie-card">
                    <div class="poster-wrapper">
                        <img src="https://upload.wikimedia.org/wikipedia/en/8/8a/The_Avengers_%282012_film%29_poster.jpg" class="movie-poster" alt="Avengers">
                    </div>
                    <p class="movie-title">Avengers</p>
                    <p class="movie-duration">3h 1m</p>
                </div>
            </div>
            <div class="col">
                <div class="card movie-card">
                    <div class="poster-wrapper">
                        <img src="https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_5421e6fbe0a18094aa35cfacf23a23d3.jpg" class="movie-poster" alt="Ghost">
                    </div>
                    <p class="movie-title">Ghost In The Cell</p>
                    <p class="movie-duration">1h 46m</p>
                </div>
            </div>
        </div>

        <div class="section-header" style="margin-top: 60px;">
            <h2 class="section-title">COMING SOON</h2>
            <div class="location-badge">
                Bandung
            </div>
        </div>

        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 movie-grid">
            <div class="col">
                <div class="card movie-card">
                    <div class="poster-wrapper">
                        <span class="badge-presale">Presale</span>
                        <img src="https://bit.ly/4v8IiMK" class="movie-poster" alt="Sonic 3">
                    </div>
                    <p class="movie-title">SONIC 4: The Hedgehog</p>
                </div>
            </div>
            <div class="col">
                <div class="card movie-card">
                    <div class="poster-wrapper">
                        <span class="badge-presale">Presale</span>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>