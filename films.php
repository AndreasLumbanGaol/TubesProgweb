<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Films</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { 
            background-color: #0d0606; 
            color: #ffffff; 
            font-family: sans-serif; 
            min-height: 100vh;
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
            text-decoration: none;
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
        
        /* Pembungkus posisi badge presale */
        .poster-wrapper {
            position: relative;
            width: 100%;
        }
        
        .movie-poster { 
            border-radius: 10px; 
            width: 100%; 
            aspect-ratio: 2/3; 
            object-fit: cover; 
            margin-bottom: 10px; 
        }
        
        /* Badge Presale Kuning */
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
        
        .poster-highlight { 
            border: 3px solid #00aaff; 
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="films.php">Films</a></li>
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

    <div class="container movies-section">
        <div class="section-header">
            <h2 class="section-title">NOW SHOWING</h2>
            <div class="location-badge">
                Bandung
            </div>
        </div>

        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 movie-grid">
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://upload.wikimedia.org/wikipedia/id/5/54/Avatar_The_Way_of_Water_poster.jpg" class="movie-poster" alt="Avatar">
                        <p class="movie-title">AVATAR: The Way of Water</p>
                        <p class="movie-duration">3h 17m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://upload.wikimedia.org/wikipedia/en/e/e1/Joker_%282019_film%29_poster.jpg" class="movie-poster" alt="Joker">
                        <p class="movie-title">JOKER: Put On A Happy Face</p>
                        <p class="movie-duration">60m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://image.tmdb.org/t/p/w500/qhb1qOilapbapxWQn9jtRCMwXJF.jpg" class="movie-poster" alt="Wonka">
                        <p class="movie-title">Wonka</p>
                        <p class="movie-duration">1h 56m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://image.tmdb.org/t/p/w500/r7XifzvtezNt31ypvsmb6Oqxw49.jpg" class="movie-poster" alt="Guardians">
                        <p class="movie-title">Guardians of The Galaxy</p>
                        <p class="movie-duration">2h 1m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://upload.wikimedia.org/wikipedia/en/8/8a/The_Avengers_%282012_film%29_poster.jpg" class="movie-poster" alt="Avengers">
                        <p class="movie-title">Avengers</p>
                        <p class="movie-duration">3h 1m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_5421e6fbe0a18094aa35cfacf23a23d3.jpg" class="movie-poster" alt="Ghost">
                        <p class="movie-title">Ghost In The Cell</p>
                        <p class="movie-duration">1h 46m</p>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_9cdf1156e0f7b0ee9a45f143ce11976e.jpg" class="movie-poster" alt="Jumbo">
                        <p class="movie-title">Jumbo</p>
                        <p class="movie-duration">1h 42m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_d4bc3b7b9035989df6277825cf5fefd5.jpg" class="movie-poster" alt="Mungkin Kita Perlu Waktu">
                        <p class="movie-title">Yang Lain Boleh Hilang, Asal Kau Jangan</p>
                        <p class="movie-duration">1h 53m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_38ea256af71e0767b204e1c300fc4f09.png" class="movie-poster" alt="13 Bom di Jakarta">
                        <p class="movie-title">2nd Miracle In Cell No 7</p>
                        <p class="movie-duration">2h 27m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_840a7c47b45221974b785876428c61f6.jpeg" class="movie-poster" alt="Sekawan Limo">
                        <p class="movie-title">2nd Miracle In Cell No 7</p>
                        <p class="movie-duration">1h 52m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_5cd2d563ce3516fc292cb495c3d666e2.jpg" class="movie-poster" alt="Petaka Gunung Gede">
                        <p class="movie-title">The Devil Wears Prada 2</p>
                        <p class="movie-duration">1h 59m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_3a0e2045bc02d6479c7fade242ce64ed.png" class="movie-poster" alt="Pangku">
                        <p class="movie-title">SALMOKJI: WHISPERING WATER</p>
                        <p class="movie-duration">1h 41m</p>
                    </div>
                </a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>