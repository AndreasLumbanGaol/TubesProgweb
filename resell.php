<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Resell Ticket</title>
    
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
        
        /* Navigation Links */
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

        .resell-section {
            padding-top: 48px;
            padding-bottom: 48px;
        }
        .resell-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 48px;
        }
        .resell-header-left {
            max-width: 500px;
        }
        .resell-header-right {
            display: flex;
            align-items: flex-end;
            margin-top: 24px;
        }

        .title-resell { 
            font-size: 40px; 
            letter-spacing: 1px; 
            font-family: monospace; 
            margin-bottom: 16px;
            margin-top: 0;
        }
        .title-resell span {
            font-style: italic;
            color: #d4af37;
        }
        .subtitle-resell {
            color: #888;
            font-size: 14px;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .btn-gold {
            background-color: #d4af37;
            color: #000;
            font-weight: bold;
            padding: 8px 24px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-gold:hover {
            background-color: #b8962e;
            color: #000;
        }
        .btn-add-icon {
            font-size: 20px;
            font-weight: normal;
        }

        .resell-grid {
            --bs-gutter-x: 24px;
            --bs-gutter-y: 24px;
        }

        .ticket-card { 
            background-color: #150808; 
            border: 1px solid #3a2626; 
            border-radius: 15px; 
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.05);
            transition: transform 0.3s, border-color 0.3s;
            padding: 16px;
            height: 100%;
            display: flex;
            gap: 16px;
            position: relative;
            box-sizing: border-box;
        }
        .ticket-card:hover {
            transform: translateY(-5px);
            border-color: #d4af37;
        }

        .ticket-poster {
            width: 100px;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .ticket-info {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            padding-bottom: 40px; /* Space for absolute button */
        }
        .ticket-title {
            color: #d4af37;
            margin-bottom: 8px;
            font-size: 20px;
            margin-top: 0;
        }
        .ticket-details {
            color: #888;
            font-size: 14px;
            margin-bottom: 16px;
            margin-top: 0;
        }
        .ticket-seat-badge {
            background-color: #212529;
            color: #888;
            border-radius: 50px;
            padding: 4px 16px;
            font-size: 12px;
            display: inline-block;
            width: fit-content;
            margin-bottom: 16px;
        }
        
        .ticket-price-container {
            margin-top: auto;
        }
        .ticket-price {
            color: #d4af37;
            font-size: 20px;
            margin: 0;
            font-weight: bold;
        }

        .btn-buy-ticket {
            position: absolute;
            bottom: 16px;
            right: 16px;
            background-color: #d4af37;
            color: #000;
            font-weight: bold;
            padding: 8px 24px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-buy-ticket:hover {
            background-color: #b8962e;
        }
        .btn-buy-ticket a{
            text-decoration: none;
            color: black;
        }
        .btn-gold a {
            text-decoration: none;
            color: black;
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
                    <li class="nav-item"><a class="nav-link active" href="resell.php">Resell Ticket</a></li>
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

    <div class="container resell-section">
        
        <div class="resell-header">
            <div class="resell-header-left">
                <h1 class="title-resell">Resell<span>Ticket</span></h1>
                <p class="subtitle-resell">
                    Beli tiket dari member lain dengan harga lebih murah, atau jual tiketmu yang tidak terpakai.
                </p>
                <button class="btn-gold"><a href="films.php">Semua Film</a></button>
            </div>
            
            <div class="resell-header-right">
                <button class="btn-gold">
                    <span class="btn-add-icon">+</span> Jual Tiketku
                </button>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-lg-2 resell-grid">
            
            <div class="col">
                <div class="ticket-card">
                    <img src="https://image.tmdb.org/t/p/w200/qhb1qOilapbapxWQn9jtRCMwXJF.jpg" class="ticket-poster" alt="Wonka">
                    
                    <div class="ticket-info">
                        <h5 class="ticket-title">Wonka</h5>
                        <p class="ticket-details">Hari Ini - 19.30 - CGV Paskal 23</p>
                        
                        <div class="ticket-seat-badge">
                            Baris D - Kursi 7 & 8 (2 tiket)
                        </div>
                        
                        <div class="ticket-price-container">
                            <p class="ticket-price">Rp 110.000</p>
                        </div>
                    </div>

                    <button class="btn-buy-ticket"><a href="payment.php">Beli</a></button>
                </div>
            </div>

            <div class="col">
                <div class="ticket-card">
                    <img src="https://via.placeholder.com/200x300/444444/d4af37?text=Ghost+In+The+Cell" class="ticket-poster" alt="Ghost">
                    
                    <div class="ticket-info">
                        <h5 class="ticket-title">Ghost In The Cell</h5>
                        <p class="ticket-details">Sabtu, 10 Mei - 20.00 - XXI Botanica Mall</p>
                        
                        <div class="ticket-seat-badge">
                            Baris F - Kursi 12 (1 tiket)
                        </div>
                        
                        <div class="ticket-price-container">
                            <p class="ticket-price">Rp 30.000</p>
                        </div>
                    </div>

                     <button class="btn-buy-ticket"><a href="payment.php">Beli</a></button>
                </div>
            </div>

        </div> 
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>