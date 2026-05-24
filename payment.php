<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Pembayaran</title>
    
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

        .payment-section {
            padding-top: 48px;
            padding-bottom: 48px;
        }
        .page-header {
            margin-bottom: 24px;
        }
        .breadcrumb-text {
            color: #d4af37;
            margin-bottom: 4px;
            font-size: 14px;
        }
        .page-title { 
            font-size: 40px; 
            letter-spacing: 1px; 
            font-family: monospace; 
            margin: 0;
        }
        .page-title span {
            font-style: italic;
            color: #d4af37;
        }

        .payment-grid {
            --bs-gutter-x: 24px;
            --bs-gutter-y: 24px;
        }

        .box-panel { 
            background-color: #150808; 
            border: 1px solid #3a2626; 
            border-radius: 15px; 
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.05);
            padding: 24px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .panel-title {
            color: #ffffff;
            border-bottom: 1px solid #d4af37;
            padding-bottom: 16px;
            margin-bottom: 24px;
            font-family: monospace;
            font-size: 20px;
            margin-top: 0;
        }

        .payment-methods-grid {
            --bs-gutter-x: 16px;
            --bs-gutter-y: 16px;
        }
        .btn-payment-method {
            color: #d4af37;
            border: 1px solid #d4af37;
            background-color: transparent;
            font-family: monospace;
            font-size: 18px;
            transition: all 0.3s;
            width: 100%;
            padding: 16px;
            border-radius: 8px;
            cursor: pointer;
        }
        .btn-payment-method:hover {
            background-color: rgba(212, 175, 55, 0.1);
            color: #d4af37;
        }
        .btn-payment-method.active {
            background-color: rgba(212, 175, 55, 0.2);
            box-shadow: inset 0 0 10px rgba(212, 175, 55, 0.5);
        }
        .divider-bottom {
            border-bottom: 1px solid #6c757d;
            margin-top: 48px;
        }

        .movie-info {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            border-bottom: 1px solid #6c757d;
            padding-bottom: 24px;
        }
        .movie-poster {
            border-radius: 4px;
            width: 70px;
            height: 100px;
            object-fit: cover;
        }
        .movie-details h6 {
            color: #d4af37;
            margin-bottom: 8px;
            font-size: 16px;
            margin-top: 0;
        }
        .movie-details p {
            color: #888;
            margin: 0;
            font-size: 14px;
        }
        .price-section {
            margin-top: 8px;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            color: #888;
            font-size: 14px;
            margin-bottom: 16px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #6c757d;
            padding-top: 24px;
            margin-bottom: 24px;
        }
        .total-label { color: #ffffff; font-weight: bold; }
        .total-price { color: #d4af37; font-weight: bold; font-size: 20px; }

        .btn-continue-container {
            display: flex;
            justify-content: flex-end;
            margin-top: auto;
        }
        .btn-continue {
            background-color: #d4af37;
            color: #000;
            font-weight: bold;
            padding: 8px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .btn-continue:hover {
            background-color: #b8962e;
            color: #000;
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
                    <li class="nav-item"><a class="nav-link" href="#">Resell Ticket</a></li>
                </ul>
                <div class="user-actions">
                    <a href="login.php" class="login-button">
                        LOG IN / SIGN UP
                    </a>
                    <a href="#">
                        <img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" 
                             alt="Profile" 
                             class="profile-icon">
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container payment-section">
        
        <div class="page-header">
            <p class="breadcrumb-text">Pembayaran</p>
            <h1 class="page-title">Selesaikan <span>Pembayaran</span></h1>
        </div>

        <div class="row payment-grid">
            
            <div class="col-lg-7">
                <div class="box-panel">
                    <h5 class="panel-title">Metode Pembayaran</h5>
                    
                    <div class="row row-cols-2 row-cols-md-3 payment-methods-grid">
                        <div class="col">
                            <button class="btn-payment-method" onclick="selectPayment(this)">GoPay</button>
                        </div>
                        <div class="col">
                            <button class="btn-payment-method" onclick="selectPayment(this)">QRIS</button>
                        </div>
                        <div class="col">
                            <button class="btn-payment-method" onclick="selectPayment(this)">BCA Virtual</button>
                        </div>
                        <div class="col">
                            <button class="btn-payment-method" onclick="selectPayment(this)">OVO</button>
                        </div>
                        <div class="col">
                            <button class="btn-payment-method" onclick="selectPayment(this)">DANA</button>
                        </div>
                        <div class="col">
                            <button class="btn-payment-method" onclick="selectPayment(this)">Kartu Kredit</button>
                        </div>
                    </div>

                    <div class="divider-bottom"></div>
                </div>
            </div>

            <!-- Panel Detail Pesanan -->
            <div class="col-lg-5">
                <div class="box-panel">
                    
                    <h5 class="panel-title">Detail Pesanan</h5>
                    
                    <div class="movie-info">
                        <img src="https://image.tmdb.org/t/p/w200/qhb1qOilapbapxWQn9jtRCMwXJF.jpg" class="movie-poster" alt="Wonka">
                        <div class="movie-details">
                            <h6>Wonka</h6>
                            <p>Hari Ini - 19:30</p>
                            <p>CGV Paskal 23 - D7,D8</p>
                        </div>
                    </div>

                    <div class="price-section">
                        <div class="price-row">
                            <span>2x Tiket Regular</span>
                            <span>Rp 120.000</span>
                        </div>
                        <div class="price-row">
                            <span>Biaya Layanan</span>
                            <span>Rp 3.000</span>
                        </div>
                        
                        <div class="total-row">
                            <span class="total-label">Total Bayar</span>
                            <span class="total-price">Rp 123.000</span>
                        </div>
                    </div>

                    <div class="btn-continue-container">
                        <a href="ticket.php" class="btn-continue">
                            Lanjutkan &rarr;
                        </a>
                    </div>

                </div>
            </div>

        </div> 
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function selectPayment(clickedBtn) {
            const buttons = document.querySelectorAll('.btn-payment-method');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            clickedBtn.classList.add('active');
        }
    </script>
</body>
</html>