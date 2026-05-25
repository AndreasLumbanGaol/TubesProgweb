<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Profile</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { 
            background-color: #0d0606; 
            color: #ffffff; 
            font-family: sans-serif; 
            min-height: 100vh;
        }

        /* ================= NAVBAR STYLING ================= */
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

        /* ================= PROFILE STYLING ================= */
        .profile-section {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 15px;
        }

        .page-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            letter-spacing: 0.5px;
        }

        .user-info-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .greeting-text {
            font-size: 14px;
            color: #aaaaaa;
            margin-bottom: 8px;
        }

        .user-name-badge {
            background: linear-gradient(135deg, #e5c060, #b28d32);
            color: #0d0606;
            font-size: 22px;
            font-weight: bold;
            padding: 8px 45px;
            border-radius: 8px;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
            margin-bottom: 10px;
        }

        .user-phone {
            font-size: 15px;
            color: #bbbbbb;
            letter-spacing: 0.5px;
        }

        /* Container Card Utama */
        .info-card {
            background-color: #210d0b;
            border: 1px solid #d4af37;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.15);
            margin-bottom: 30px;
        }

        /* Header Bar di Atas Card */
        .card-header-bar {
            background: #e1b44c;
            color: #0d0606;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            padding: 10px 0;
            border-radius: 16px 16px 0 0;
        }

        .card-body-content {
            padding: 24px 30px;
        }

        /* Bagian Wallet */
        .wallet-text {
            font-size: 16px;
            color: #ffffff;
            margin: 0;
        }

        .wallet-amount {
            font-size: 20px;
            font-weight: bold;
            font-family: monospace, sans-serif;
            margin-left: 5px;
        }

        /* List Film */
        .movie-item {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        .movie-poster-thumb {
            width: 100px;
            aspect-ratio: 2/3;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.5);
        }

        .movie-details h3 {
            color: #ffffff;
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }

        .movie-rating {
            color: #e5c060;
            font-size: 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .movie-meta {
            color: #888888;
            font-size: 12px;
            margin: 0;
            line-height: 1.5;
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
                    <li class="nav-item"><a class="nav-link" href="films.php">Films</a></li>
                    <li class="nav-item"><a class="nav-link" href="resell.php">Resell Ticket</a></li>
                </ul>
                <div class="user-actions">
                    <a href="login.php" class="login-button">LOG IN / SIGN UP</a>
                    <a href="profile.php">
                        <img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" 
                             alt="Profile Icon" 
                             class="profile-icon" style="border-color: #d4af37;">
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container profile-section">
        <h1 class="page-title">Akun Saya</h1>

        <div class="user-info-header">
            <div class="greeting-text">Halo,</div>
            <div class="user-name-badge">Nama User</div>
            <div class="user-phone">081572152613</div>
        </div>

        <div class="info-card">
            <div class="card-header-bar">Dompet User</div>
            <div class="card-body-content">
                <p class="wallet-text">
                    Saldo Utama : <span class="wallet-amount">Rp500.000</span>
                </p>
            </div>
        </div>

        <div class="info-card">
            <div class="card-header-bar">Film yang dibeli</div>
            <div class="card-body-content">
                <div class="movie-item">
                    <img src="https://image.tmdb.org/t/p/w500/qhb1qOilapbapxWQn9jtRCMwXJF.jpg" alt="Wonka Poster" class="movie-poster-thumb">
                    
                    <div class="movie-details">
                        <h3>Wonka</h3>
                        <div class="movie-rating">
                            ★ 6.9
                        </div>
                        <p class="movie-meta">
                            Genre: Adventure, Comedy, Family<br>
                            Durasi: 1j 56m
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>