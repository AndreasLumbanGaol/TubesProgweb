<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Tiket Online</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { 
            background-color: #0d0606; 
            font-family: sans-serif; 
            min-height: 100vh;
            color: #ffffff;
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

        .user-actions {
            display: flex;
            align-items: center;
        }
        .profile-icon {
            width: 35px; 
            height: 35px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 1px solid #d4af37;
        }

        .ticket-section {
            padding-top: 48px;
            padding-bottom: 48px;
        }
        .ticket-wrapper {
            max-width: 600px;
            margin: 0 auto;
        }

        .box-panel { 
            background-color: #150808; 
            border-radius: 12px; 
            padding: 24px;
            margin-bottom: 24px;
        }
        .border-ticket { 
            border: 1px solid #d4af37; 
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.05); 
        }
        .border-success-box { 
            border: 1px solid #198754; 
            background-color: rgba(25, 135, 84, 0.05); 
            text-align: center;
            padding-top: 16px;
            padding-bottom: 16px;
        }

        .success-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .success-circle {
            width: 90px;
            height: 90px;
            border: 1px solid #6c757d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
        }
        .success-title {
            font-weight: normal;
            letter-spacing: 1px;
            font-size: 28px;
            margin-bottom: 8px;
            margin-top: 0;
        }
        .success-title span {
            font-style: italic;
            color: #d4af37;
            font-weight: bold;
        }
        .success-subtitle {
            color: #888;
            font-size: 14px;
            margin: 0;
        }

        .status-title {
            color: #20c997;
            letter-spacing: 1px;
            margin-bottom: 4px;
            margin-top: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .status-desc {
            margin: 0;
            color: #888;
            font-size: 14px;
        }

        .movie-header {
            margin-bottom: 24px;
        }
        .movie-title {
            color: #ffffff;
            margin-bottom: 4px;
            font-family: monospace;
            font-size: 32px;
            margin-top: 0;
        }
        .movie-genre {
            color: #888;
            font-size: 14px;
            margin: 0;
        }

        .ticket-details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 16px;
        }
        .ticket-label { 
            font-size: 12px; 
            color: #888; 
            letter-spacing: 1px; 
            margin-bottom: 5px; 
            text-transform: uppercase; 
        }
        .ticket-value { 
            font-size: 16px; 
            color: #fff; 
            font-family: monospace; 
        }
        .ticket-value-highlight {
            color: #d4af37;
            font-weight: bold;
        }

        .dashed-line { 
            border-top: 1px dashed #3a2626; 
            margin: 25px 0; 
        }

        .qr-section {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        .qr-img {
            width: 100px;
            height: 100px;
            border: 4px solid #fff;
            border-radius: 4px;
        }
        .booking-code { 
            font-family: monospace; 
            font-size: 18px; 
            letter-spacing: 1px; 
            color: #d4af37;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .qr-desc {
            color: #888;
            margin: 0;
            font-size: 13px;
            line-height: 1.4;
        }
        .qr-desc span {
            color: #ffffff;
            font-weight: bold;
        }

        .summary-title {
            color: #d4af37;
            margin-bottom: 24px;
            text-decoration: underline;
            font-family: monospace;
            font-size: 16px;
            margin-top: 0;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            color: #888;
            font-size: 14px;
            margin-bottom: 16px;
        }
        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #6c757d;
            padding-top: 16px;
        }
        .total-label {
            color: #d4af37;
            font-weight: bold;
            font-size: 15px;
        }
        .total-value-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .badge-method {
            background-color: #3b3b3b;
            color: #aaa;
            font-size: 11px;
            letter-spacing: 1px;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .total-amount {
            color: #d4af37;
            font-weight: bold;
            font-size: 16px;
        }

        .btn-download {
            background-color: #d4af37;
            color: #000;
            width: 100%;
            font-weight: bold;
            padding: 16px;
            text-align: center;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-bottom: 48px;
            font-size: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .btn-download:hover {
            background-color: #b8962e;
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
                    <a href="#">
                        <img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" 
                             alt="Profile" 
                             class="profile-icon">
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container ticket-section">
        <div class="ticket-wrapper">
            
            <div class="success-header">
                <div class="success-circle">
                </div>
                <h2 class="success-title">Pembayaran <span>Berhasil</span></h2>
                <p class="success-subtitle">Transaksi dikonfirmasi - Senin, 4 Mei 2026 - 14:32 WIB</p>
            </div>

            <div class="box-panel border-success-box">
                <h6 class="status-title">Tiket Aktif & Siap Digunakan</h6>
                <p class="status-desc">Tunjukkan QR Code di bawah kepada petugas bioskop saat masuk</p>
            </div>

            <div class="box-panel border-ticket">
                
                <div class="movie-header">
                    <h2 class="movie-title">Wonka</h2>
                    <p class="movie-genre">Fantasy - Comedy - PG-13</p>
                </div>

                <div class="ticket-details-grid">
                    <div>
                        <div class="ticket-label">TANGGAL</div>
                        <div class="ticket-value">Senin, 4 Mei</div>
                    </div>
                    <div>
                        <div class="ticket-label">JAM TAYANG</div>
                        <div class="ticket-value">19:30 WIB</div>
                    </div>
                    <div>
                        <div class="ticket-label">DURASI</div>
                        <div class="ticket-value">116 menit</div>
                    </div>
                    <div>
                        <div class="ticket-label">BIOSKOP</div>
                        <div class="ticket-value">CGV Paskal 23</div>
                    </div>
                    <div>
                        <div class="ticket-label">STUDIO</div>
                        <div class="ticket-value">Studio 4</div>
                    </div>
                    <div>
                        <div class="ticket-label">KURSI</div>
                        <div class="ticket-value ticket-value-highlight">D7, D8</div>
                    </div>
                </div>

                <div class="dashed-line"></div>

                <div class="qr-section">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=TXL-2026-WNK-7834&color=ffffff&bgcolor=150808" alt="QR Code" class="qr-img">
                    
                    <div>
                        <div class="ticket-label">KODE BOOKING</div>
                        <div class="booking-code">TXL-2026-WNK-7834</div>
                        <p class="qr-desc">
                            Scan QR Code ini di pintu masuk<br>studio CGV Paskal 23.<br>
                            Berlaku untuk <span>2 orang.</span>
                        </p>
                    </div>
                </div>

            </div>

            <div class="box-panel border-ticket">
                <h6 class="summary-title">Ringkasan Pembayaran</h6>
                
                <div class="summary-row">
                    <span>2x Tiket Regular</span>
                    <span>Rp 120.000</span>
                </div>
                <div class="summary-row">
                    <span>Biaya Layanan</span>
                    <span>Rp 3.000</span>
                </div>
                
                <div class="summary-total">
                    <span class="total-label">Total Dibayar</span>
                    <div class="total-value-wrapper">
                        <span class="badge-method">OVO</span>
                        <span class="total-amount">Rp 123.000</span>
                    </div>
                </div>
            </div>

            <button class="btn-download">
                Unduh Tiket
            </button>

        </div> 
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>