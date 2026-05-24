<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Booking Seat</title>
    
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
            gap: 16px;
        }
        .nav-link { 
            color: #cccccc !important; 
            font-weight: 500; 
            padding-left: 16px !important;
            padding-right: 16px !important;
        }
        .nav-link.active {
            color: #d4af37 !important; 
            border: 1px solid rgba(212, 175, 55, 0.5); 
            border-radius: 8px; 
            background: rgba(212, 175, 55, 0.1);
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .login-button {
            background-color: #d4af37; 
            color: #000; 
            font-weight: bold; 
            border-radius: 5px; 
            padding: 4px 16px;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.3s;
            border: none;
        }
        .login-button:hover {
            background-color: #b8962e;
            color: #000;
        }
        .profile-icon {
            width: 35px; 
            height: 35px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 1px solid #d4af37;
        }

        /* Booking*/
        .booking-section {
            padding-top: 48px;
            padding-bottom: 48px;
        }
        .booking-grid {
            --bs-gutter-x: 24px;
            --bs-gutter-y: 24px;
        }
        .box-panel { 
            background-color: #150808; 
            border: 1px solid #d4af37; 
            border-radius: 12px; 
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.1);
            padding: 24px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .screen-container {
            text-align: center;
            margin-top: 16px;
            margin-bottom: 48px;
        }
        .screen-line { 
            height: 4px; 
            background: #d4af37; 
            box-shadow: 0 0 12px #d4af37; 
            width: 50%; 
            margin: 0 auto 8px auto; 
            border-radius: 2px; 
        }
        .screen-text {
            color: #d4af37;
            font-weight: bold;
            letter-spacing: 3px;
            font-size: 14px;
        }
        .seating-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 24px;
        }
        .seat-row { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            margin-bottom: 8px; 
        }
        .seat-label { 
            color: #d4af37; 
            width: 25px; 
            font-weight: bold; 
            text-align: right; 
            margin-right: 15px; 
            font-size: 14px;
        }
        .seat-group { 
            display: flex; 
            gap: 8px; 
        }
        .seat-gap { 
            width: 45px; 
        } 
        
        .seat { 
            width: 32px; 
            height: 32px; 
            border: 1px solid #d4af37; 
            border-radius: 6px 6px 4px 4px; 
            cursor: pointer; 
            transition: 0.2s; 
        }
        .seat:hover:not(.occupied) { background-color: rgba(212, 175, 55, 0.3); }
        .seat.selected { background-color: #d4af37; border-color: #d4af37; }
        .seat.occupied { background-color: #1a1a1a; border-color: #1a1a1a; cursor: not-allowed; }
        
        .legend-container {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-top: 48px;
            color: #888;
            font-size: 14px;
        }
        .legend-box { 
            width: 16px; 
            height: 16px; 
            border-radius: 4px; 
            display: inline-block; 
            vertical-align: middle; 
            margin-right: 5px;
        }
        .legend-available { border: 1px solid #d4af37; }
        .legend-selected { background-color: #d4af37; }
        .legend-occupied { background-color: #1a1a1a; }

        .summary-title {
            color: #ffffff;
            border-bottom: 1px solid #6c757d;
            padding-bottom: 16px;
            margin-bottom: 24px;
            font-family: monospace;
            font-size: 20px;
            margin-top: 0;
        }
        .movie-info {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
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
        .ticket-section { margin-top: 8px; }
        .ticket-label {
            color: #888;
            margin-bottom: 4px;
            font-size: 14px;
        }
        .ticket-seats {
            color: #d4af37;
            font-size: 20px;
            margin-bottom: 24px;
            margin-top: 0;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            color: #888;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .price-row-last { margin-bottom: 24px; }
        .total-row {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #6c757d;
            padding-top: 16px;
            margin-bottom: 24px;
        }
        .total-label { color: #ffffff; font-weight: bold; }
        .total-price { color: #d4af37; font-weight: bold; font-size: 20px; }
        
        .btn-checkout {
            background-color: #d4af37;
            color: #000;
            width: 100%;
            font-weight: bold;
            padding: 8px 16px;
            margin-top: auto;
            text-decoration: none;
            text-align: center;
            border-radius: 4px;
            border: none;
        }
        .btn-checkout:hover {
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
                    <li class="nav-item"><a class="nav-link active" href="#">Resell Ticket</a></li>
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

    <div class="container booking-section">
        <div class="row booking-grid">
            
            <!-- Panel Kursi -->
            <div class="col-lg-8">
                <div class="box-panel">
                    
                    <div class="screen-container">
                        <div class="screen-line"></div>
                        <span class="screen-text">LAYAR</span>
                    </div>

                    <div class="seating-area">
                        
                        <div class="seat-row"><div class="seat-label">A</div>
                            <div class="seat-group">
                                <div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div>
                            </div><div class="seat-gap"></div>
                            <div class="seat-group">
                                <div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div>
                            </div>
                        </div>
                        
                        <div class="seat-row"><div class="seat-label">B</div>
                            <div class="seat-group"><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div></div><div class="seat-gap"></div>
                            <div class="seat-group"><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div></div>
                        </div>

                        <div class="seat-row"><div class="seat-label">C</div>
                            <div class="seat-group"><div class="seat"></div><div class="seat occupied"></div><div class="seat occupied"></div><div class="seat occupied"></div><div class="seat occupied"></div><div class="seat"></div></div><div class="seat-gap"></div>
                            <div class="seat-group"><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div></div>
                        </div>

                        <div class="seat-row"><div class="seat-label">D</div>
                            <div class="seat-group"><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div></div><div class="seat-gap"></div>
                            <div class="seat-group"><div class="seat selected"></div><div class="seat selected"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div></div>
                        </div>

                        <div class="seat-row"><div class="seat-label">E</div>
                            <div class="seat-group"><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div></div><div class="seat-gap"></div>
                            <div class="seat-group"><div class="seat occupied"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div></div>
                        </div>

                        <div class="seat-row"><div class="seat-label">F</div>
                            <div class="seat-group"><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat occupied"></div><div class="seat"></div><div class="seat occupied"></div></div><div class="seat-gap"></div>
                            <div class="seat-group"><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div></div>
                        </div>

                        <div class="seat-row"><div class="seat-label">G</div>
                            <div class="seat-group"><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat occupied"></div><div class="seat"></div></div><div class="seat-gap"></div>
                            <div class="seat-group"><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div></div>
                        </div>

                        <div class="seat-row"><div class="seat-label">H</div>
                            <div class="seat-group"><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div></div><div class="seat-gap"></div>
                            <div class="seat-group"><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat occupied"></div><div class="seat occupied"></div></div>
                        </div>
                    </div>

                    <div class="legend-container">
                        <div><span class="legend-box legend-available"></span> Tersedia</div>
                        <div><span class="legend-box legend-selected"></span> Dipilih</div>
                        <div><span class="legend-box legend-occupied"></span> Terisi</div>
                    </div>

                </div>
            </div>

            <div class="col-lg-4">
                <div class="box-panel">
                    
                    <h5 class="summary-title">Ringkasan Pesanan</h5>
                    
                    <div class="movie-info">
                        <img src="https://image.tmdb.org/t/p/w200/qhb1qOilapbapxWQn9jtRCMwXJF.jpg" class="movie-poster" alt="Wonka">
                        <div class="movie-details">
                            <h6>Wonka</h6>
                            <p>Hari Ini - 19:30</p>
                            <p>CGV Paskal 23 - D7,D8</p>
                        </div>
                    </div>

                    <div class="ticket-section">
                        <p class="ticket-label">Kursi Dipilih</p>
                        <h5 class="ticket-seats">D7, D8</h5>

                        <div class="price-row">
                            <span>2x Tiket Regular</span>
                            <span>Rp 120.000</span>
                        </div>
                        <div class="price-row price-row-last">
                            <span>Biaya Layanan</span>
                            <span>Rp 3.000</span>
                        </div>
                        
                        <div class="total-row">
                            <span class="total-label">Total Bayar</span>
                            <span class="total-price">Rp 123.000</span>
                        </div>
                    </div>

                    <a href="payment.php" class="btn-checkout">
                        Lanjutkan ke Pembayaran &rarr;
                    </a>

                </div>
            </div>

        </div> 
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const seats = document.querySelectorAll('.seat:not(.occupied)');
        seats.forEach(seat => {
            seat.addEventListener('click', () => {
                seat.classList.toggle('selected');
            });
        });
    </script>
</body>
</html>