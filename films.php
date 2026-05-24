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
        .movie-poster { 
            border-radius: 10px; 
            width: 100%; 
            aspect-ratio: 2/3; 
            object-fit: cover; 
            margin-bottom: 10px; 
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
                        <img src="https://image.tmdb.org/t/p/w500/t6HIqrHe1b0aV780P7213I6B1l.jpg" class="movie-poster" alt="Avatar">
                        <p class="movie-title">AVATAR: The Way of Water</p>
                        <p class="movie-duration">3h 17m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://image.tmdb.org/t/p/w500/udDclJoHjfpt8PnF8X9w1sNl3G.jpg" class="movie-poster" alt="Joker">
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
                        <img src="https://image.tmdb.org/t/p/w500/RYMX2wcKCBAr24UyPD7xrmstKX.jpg" class="movie-poster" alt="Avengers">
                        <p class="movie-title">Avengers</p>
                        <p class="movie-duration">3h 1m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://via.placeholder.com/300x450/444444/d4af37?text=Ghost+In+The+Cell" class="movie-poster" alt="Ghost">
                        <p class="movie-title">Ghost In The Cell</p>
                        <p class="movie-duration">1h 46m</p>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://via.placeholder.com/300x450/1a1a1a/ffffff?text=Jumbo" class="movie-poster" alt="Jumbo">
                        <p class="movie-title">Jumbo</p>
                        <p class="movie-duration">1h 42m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://via.placeholder.com/300x450/1a1a1a/ffffff?text=Mungkin+Kita" class="movie-poster" alt="Mungkin Kita Perlu Waktu">
                        <p class="movie-title">Mungkin Kita Perlu Waktu</p>
                        <p class="movie-duration">1h 35m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://via.placeholder.com/300x450/1a1a1a/ffffff?text=13+Bom+di+Jakarta" class="movie-poster" alt="13 Bom di Jakarta">
                        <p class="movie-title">13 Bom di Jakarta</p>
                        <p class="movie-duration">2h 23m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://via.placeholder.com/300x450/1a1a1a/ffffff?text=Sekawan+Limo" class="movie-poster" alt="Sekawan Limo">
                        <p class="movie-title">Sekawan Limo</p>
                        <p class="movie-duration">1h 52m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://via.placeholder.com/300x450/1a1a1a/ffffff?text=Petaka+Gunung" class="movie-poster" alt="Petaka Gunung Gede">
                        <p class="movie-title">Petaka Gunung Gede</p>
                        <p class="movie-duration">1h 38m</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="booking.php" class="movie-link">
                    <div class="card movie-card">
                        <img src="https://via.placeholder.com/300x450/1a1a1a/ffffff?text=Pangku" class="movie-poster" alt="Pangku">
                        <p class="movie-title">Pangku</p>
                        <p class="movie-duration">1h 41m</p>
                    </div>
                </a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>