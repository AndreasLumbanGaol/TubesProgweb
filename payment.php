<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// Redirect admin to admin dashboard
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin/index.php");
    exit();
}

$movie = isset($_GET['movie']) ? $_GET['movie'] : 'Wonka';
$poster = isset($_GET['poster']) ? $_GET['poster'] : 'https://image.tmdb.org/t/p/w200/qhb1qOilapbapxWQn9jtRCMwXJF.jpg';
$duration = isset($_GET['duration']) ? $_GET['duration'] : '1h 56m';
$cinema = isset($_GET['cinema']) ? $_GET['cinema'] : 'CGV Paskal 23';
$type = isset($_GET['type']) ? $_GET['type'] : 'Regular';
$price = isset($_GET['price']) ? intval($_GET['price']) : 50000;
$date = isset($_GET['date']) ? $_GET['date'] : 'Hari Ini';
$time = isset($_GET['time']) ? $_GET['time'] : '19:30';

// Format jam untuk tampilan agar user-friendly
$time_display = date('H:i', strtotime($time)) . ' WIB';
$seats = isset($_GET['seats']) ? $_GET['seats'] : 'D7,D8';
$total = isset($_GET['total']) ? intval($_GET['total']) : 103000;

$seat_array = explode(',', $seats);
$seat_count = count($seat_array);
if ($seats === '' || empty($seats)) {
    $seat_count = 0;
}
$subtotal = $seat_count * $price;

$userId = null;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} elseif (isset($_SESSION['UserID'])) {
    $userId = $_SESSION['UserID'];
}

$userBalance = 0;
if ($userId) {
    $res_bal = mysqli_query($conn, "SELECT Saldo FROM user WHERE UserID = '$userId'");
    if ($res_bal && mysqli_num_rows($res_bal) > 0) {
        $userBalance = mysqli_fetch_assoc($res_bal)['Saldo'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Pembayaran Premium</title>
    
    <!-- Google Fonts - Inter & Playfair Display -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #080303;
            --card-bg: rgba(22, 10, 10, 0.6);
            --gold: #d4af37;
            --gold-light: rgba(212, 175, 55, 0.15);
            --gold-glow: rgba(212, 175, 55, 0.4);
            --text-muted: #a09292;
        }

        body { 
            background-color: var(--bg-color); 
            color: #ffffff;
            font-family: 'Inter', sans-serif; 
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(212, 175, 55, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(179, 0, 0, 0.05) 0%, transparent 40%);
            background-attachment: fixed;
        }

        /* Navbar Styling */
        .navbar { 
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            padding-top: 18px;
            padding-bottom: 18px;
            background-color: rgba(8, 3, 3, 0.85);
            backdrop-filter: blur(12px);
        }
        .navbar-container {
            padding-left: 24px;
            padding-right: 24px;
        }
        .navbar-brand { 
            color: var(--gold) !important; 
            font-family: 'Playfair Display', serif; 
            font-size: 26px; 
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
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: var(--gold) !important;
        }
        .nav-link.active {
            color: var(--gold) !important; 
            border: 1px solid rgba(212, 175, 55, 0.5); 
            border-radius: 8px; 
            background: rgba(212, 175, 55, 0.1);
            padding: 4px 16px;
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
            border: 1px solid var(--gold);
            transition: transform 0.3s;
        }
        .profile-icon:hover {
            transform: scale(1.1);
        }

        /* Payment Section */
        .payment-section {
            padding-top: 50px;
            padding-bottom: 80px;
        }
        .page-header {
            margin-bottom: 35px;
        }
        .breadcrumb-text {
            color: var(--gold);
            text-transform: uppercase;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }
        .page-title { 
            font-size: 38px; 
            font-family: 'Playfair Display', serif; 
            font-weight: 700;
            margin: 0;
        }
        .page-title span {
            font-style: italic;
            color: var(--gold);
        }

        .payment-grid {
            --bs-gutter-x: 30px;
            --bs-gutter-y: 30px;
        }

        /* Glassmorphic Box Panel */
        .box-panel { 
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.06); 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            padding: 30px;
            height: 100%;
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(15px);
        }
        
        .panel-title {
            color: #ffffff;
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .panel-title::after {
            content: '';
            flex-grow: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.4) 0%, rgba(212, 175, 55, 0) 100%);
        }

        /* Payment Methods Grid */
        .payment-category-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .payment-methods-grid {
            --bs-gutter-x: 16px;
            --bs-gutter-y: 16px;
            margin-bottom: 25px;
        }

        /* Modern Payment Card Button */
        .payment-method-card {
            position: relative;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 20px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            height: 100px;
            width: 100%;
            overflow: hidden;
        }

        .payment-method-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(212, 175, 55, 0.3);
        }

        /* Brand Colors & Glowing Hover Effects */
        .payment-method-card.gopay:hover { box-shadow: 0 0 20px rgba(0, 161, 222, 0.2); border-color: #00a1de; }
        .payment-method-card.qris:hover { box-shadow: 0 0 20px rgba(255, 165, 0, 0.2); border-color: orange; }
        .payment-method-card.bca:hover { box-shadow: 0 0 20px rgba(0, 90, 170, 0.2); border-color: #005aaa; }
        .payment-method-card.ovo:hover { box-shadow: 0 0 20px rgba(119, 44, 232, 0.2); border-color: #772ce8; }
        .payment-method-card.dana:hover { box-shadow: 0 0 20px rgba(19, 142, 232, 0.2); border-color: #138ee8; }
        .payment-method-card.card:hover { box-shadow: 0 0 20px rgba(255, 255, 255, 0.1); border-color: #ffffff; }

        .payment-method-card.active {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.03) 100%) !important;
            border-color: var(--gold) !important;
            box-shadow: 0 0 25px rgba(212, 175, 55, 0.35) !important;
        }

        .payment-method-logo {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            transition: transform 0.3s;
        }
        
        .payment-method-card:hover .payment-method-logo {
            transform: scale(1.08);
        }

        .logo-gopay { color: #00a1de; }
        .logo-qris { color: #f3ca44; }
        .logo-bca { color: #005aaa; }
        .logo-ovo { color: #a55eea; }
        .logo-dana { color: #2980b9; }
        .logo-card { color: #d1d8e0; }

        .payment-method-desc {
            font-size: 11px;
            font-weight: 500;
            color: var(--text-muted);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Selected Indicator Checkmark */
        .selected-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: var(--gold);
            color: #000;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .payment-method-card.active .selected-badge {
            opacity: 1;
            transform: scale(1);
        }

        /* Digital Struk/Receipt Receipt Styling */
        .receipt-card {
            background: linear-gradient(180deg, rgba(28, 14, 14, 0.85) 0%, rgba(18, 9, 9, 0.95) 100%);
            border: 1px solid rgba(212, 175, 55, 0.25);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            padding: 30px;
            position: relative;
        }
        
        /* Zigzag Tear Visual effect */
        .receipt-card::before {
            content: '';
            position: absolute;
            top: -6px;
            left: 0;
            width: 100%;
            height: 6px;
            background-image: linear-gradient(135deg, transparent 4px, rgba(28, 14, 14, 0.85) 4px), linear-gradient(-135deg, transparent 4px, rgba(28, 14, 14, 0.85) 4px);
            background-size: 12px 6px;
            background-repeat: repeat-x;
        }

        .movie-info {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px dashed rgba(212, 175, 55, 0.2);
        }
        .movie-poster {
            border-radius: 8px;
            width: 80px;
            height: 115px;
            object-fit: cover;
            border: 1px solid rgba(212, 175, 55, 0.3);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        }
        .movie-details h6 {
            color: #ffffff;
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
            margin-top: 0;
        }
        .movie-details p {
            color: var(--text-muted);
            margin: 0;
            font-size: 13px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .movie-details p strong {
            color: var(--gold);
        }

        .price-section {
            margin-top: 5px;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 14px;
        }
        .price-row span:last-child {
            color: #ffffff;
            font-family: monospace;
            font-size: 15px;
            font-weight: 600;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px dashed rgba(212, 175, 55, 0.2);
            padding-top: 20px;
            margin-bottom: 30px;
        }
        .total-label { 
            color: #ffffff; 
            font-weight: bold; 
            font-size: 15px;
        }
        .total-price { 
            color: var(--gold); 
            font-weight: 800; 
            font-size: 24px; 
            font-family: monospace;
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.2);
        }

        /* Premium Pulsing CTA Button */
        .btn-continue-container {
            margin-top: auto;
        }
        .btn-continue {
            background: linear-gradient(135deg, #d4af37 0%, #b8962e 100%);
            color: #000 !important;
            font-weight: 800;
            font-size: 16px;
            padding: 14px 24px;
            border-radius: 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.25);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            width: 100%;
        }
        .btn-continue:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.45);
            background: linear-gradient(135deg, #f3ca44 0%, #d4af37 100%);
        }
        .btn-continue:active {
            transform: translateY(0);
        }

        .btn-back {
            background-color: transparent;
            color: var(--text-muted);
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            margin-bottom: 25px;
        }
        .btn-back:hover {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.3);
            background-color: rgba(255, 255, 255, 0.02);
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

    <div class="container payment-section">
        
        <!-- Tombol Kembali -->
        <a href="javascript:history.back()" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Kembali
        </a>

        <div class="page-header">
            <p class="breadcrumb-text">Secure Checkout</p>
            <h1 class="page-title">Selesaikan <span>Pembayaran</span></h1>
        </div>

        <div class="row payment-grid">
            
            <!-- PANEL KIRI: METODE PEMBAYARAN -->
            <div class="col-lg-7">
                <div class="box-panel">
                     <h5 class="panel-title">Pilih Metode Pembayaran</h5>
                     
                     <!-- Kategori Dompet Utama -->
                     <div class="payment-category-title">Metode Utama</div>
                     <div class="row row-cols-1 g-3 payment-methods-grid mb-4">
                         <div class="col-12">
                             <button class="payment-method-card active w-100 d-flex flex-row justify-content-between px-4 py-3 align-items-center" onclick="selectPayment(this, 'Tixly Wallet')" style="height: auto; min-height: 80px;">
                                 <span class="selected-badge" style="top: 50%; transform: translateY(-50%) scale(1); right: 24px;">&#10003;</span>
                                 <div class="text-start">
                                     <span class="payment-method-logo d-block" style="color: var(--gold); font-size: 20px; font-weight: 800; margin-bottom: 0;">Dompet Tixly</span>
                                     <span class="payment-method-desc" style="font-size: 13px; color: #fff; text-transform: none; letter-spacing: 0;">Saldo: <strong>Rp <?php echo number_format($userBalance, 0, ',', '.'); ?></strong></span>
                                 </div>
                                 <?php if ($userBalance < $total): ?>
                                     <span class="badge bg-danger p-2" style="font-size: 11px;">Saldo Kurang</span>
                                 <?php else: ?>
                                     <span class="badge bg-success p-2" style="font-size: 11px;">Saldo Cukup</span>
                                 <?php endif; ?>
                             </button>
                         </div>
                     </div>

                     <!-- Kategori E-Wallet -->
                     <div class="payment-category-title">E-Wallet & Instan</div>
                     <div class="row row-cols-2 row-cols-md-3 g-3 payment-methods-grid">
                         <div class="col">
                             <button class="payment-method-card gopay" onclick="selectPayment(this, 'GoPay')">
                                 <span class="selected-badge">&#10003;</span>
                                 <span class="payment-method-logo logo-gopay">GoPay</span>
                                 <span class="payment-method-desc">Instant Pay</span>
                             </button>
                         </div>
                        <div class="col">
                            <button class="payment-method-card qris" onclick="selectPayment(this, 'QRIS')">
                                <span class="selected-badge">&#10003;</span>
                                <span class="payment-method-logo logo-qris">QRIS</span>
                                <span class="payment-method-desc">Scan Code</span>
                            </button>
                        </div>
                        <div class="col">
                            <button class="payment-method-card ovo" onclick="selectPayment(this, 'OVO')">
                                <span class="selected-badge">&#10003;</span>
                                <span class="payment-method-logo logo-ovo">OVO</span>
                                <span class="payment-method-desc">E-Money</span>
                            </button>
                        </div>
                        <div class="col">
                            <button class="payment-method-card dana" onclick="selectPayment(this, 'DANA')">
                                <span class="selected-badge">&#10003;</span>
                                <span class="payment-method-logo logo-dana">DANA</span>
                                <span class="payment-method-desc">E-Wallet</span>
                            </button>
                        </div>
                    </div>

                    <!-- Kategori Transfer VA -->
                    <div class="payment-category-title">Virtual Account (Transfer Bank)</div>
                    <div class="row row-cols-2 row-cols-md-3 g-3 payment-methods-grid">
                        <div class="col">
                            <button class="payment-method-card bca" onclick="selectPayment(this, 'BCA Virtual')">
                                <span class="selected-badge">&#10003;</span>
                                <span class="payment-method-logo logo-bca">BCA VA</span>
                                <span class="payment-method-desc">Auto-Check</span>
                            </button>
                        </div>
                    </div>

                    <!-- Kategori Lainnya -->
                    <div class="payment-category-title">Kartu & Kredit</div>
                    <div class="row row-cols-2 row-cols-md-3 g-3 payment-methods-grid">
                        <div class="col">
                            <button class="payment-method-card card" onclick="selectPayment(this, 'Kartu Kredit')">
                                <span class="selected-badge">&#10003;</span>
                                <span class="payment-method-logo logo-card">Credit Card</span>
                                <span class="payment-method-desc">Visa / Mastercard</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANEL KANAN: DETAIL STRUK PESANAN -->
            <div class="col-lg-5">
                <div class="receipt-card">
                    <h5 class="panel-title">Ringkasan Struk</h5>
                    
                    <div class="movie-info">
                        <img src="<?php echo htmlspecialchars($poster); ?>" class="movie-poster" alt="<?php echo htmlspecialchars($movie); ?>">
                        <div class="movie-details">
                            <h6><?php echo htmlspecialchars($movie); ?></h6>
                            <p>
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                </svg>
                                <?php echo htmlspecialchars($duration); ?>
                            </p>
                            <p>
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                </svg>
                                <strong><?php echo htmlspecialchars($cinema); ?></strong>
                            </p>
                            <p>
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-calendar-check-fill" viewBox="0 0 16 16">
                                    <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V5h16v9zm-3-3.855c.749-.19 1.42-.553 2-1.041v.444c0 .496-.2.952-.524 1.282A1.996 1.996 0 0 1 13 11.145V10.145z"/>
                                </svg>
                                <?php echo htmlspecialchars($date); ?> - <?php echo htmlspecialchars($time_display); ?>
                            </p>
                        </div>
                    </div>

                    <div class="price-section">
                        <div class="price-row">
                            <span>Jenis Tiket</span>
                            <span>Studio <?php echo htmlspecialchars($type); ?></span>
                        </div>
                        <div class="price-row">
                            <span>Kursi Terpilih</span>
                            <span><?php echo htmlspecialchars($seats); ?></span>
                        </div>
                        <div class="price-row">
                            <span>Jumlah Tiket</span>
                            <span><?php echo $seat_count; ?>x</span>
                        </div>
                        <div class="price-row">
                            <span>Harga Satuan</span>
                            <span>Rp <?php echo number_format($price, 0, ',', '.'); ?></span>
                        </div>
                        <div class="price-row">
                            <span>Subtotal Tiket</span>
                            <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                        </div>
                        <div class="price-row">
                            <span>Biaya Layanan</span>
                            <span>Rp 3.000</span>
                        </div>
                        
                        <div class="total-row">
                            <span class="total-label">Total Pembayaran</span>
                            <span class="total-price">Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                        </div>
                    </div>

                    <div class="btn-continue-container">
                        <a href="ticket.php" class="btn-continue" id="continue-checkout-btn">
                            Bayar Sekarang &rarr;
                        </a>
                    </div>

                </div>
            </div>

        </div> 
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let selectedMethod = 'Tixly Wallet'; // default active
        const userBalance = <?php echo $userBalance; ?>;
        const totalAmount = <?php echo $total; ?>;

        function selectPayment(clickedBtn, methodName) {
            // Hapus kelas active dari semua metode pembayaran
            const buttons = document.querySelectorAll('.payment-method-card');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // Tambahkan kelas active pada tombol yang diklik
            clickedBtn.classList.add('active');
            
            // Simpan metode pembayaran terpilih
            selectedMethod = methodName;
            
            // Perbarui link checkout
            updateContinueUrl();

            // Validasi saldo
            validateBalance();
        }

        function validateBalance() {
            const continueBtn = document.getElementById('continue-checkout-btn');
            if (!continueBtn) return;

            if (selectedMethod === 'Tixly Wallet' && userBalance < totalAmount) {
                continueBtn.classList.add('disabled');
                continueBtn.style.pointerEvents = 'none';
                continueBtn.style.opacity = '0.5';
                continueBtn.innerHTML = 'Saldo Tidak Cukup &rarr;';
            } else {
                continueBtn.classList.remove('disabled');
                continueBtn.style.pointerEvents = 'auto';
                continueBtn.style.opacity = '1';
                continueBtn.innerHTML = 'Bayar Sekarang &rarr;';
            }
        }

        function updateContinueUrl() {
            const continueBtn = document.getElementById('continue-checkout-btn');
            if (continueBtn) {
                // Salin seluruh query parameter saat ini
                const params = new URLSearchParams(window.location.search);
                
                // Tambahkan metode pembayaran terpilih
                params.set('method', selectedMethod);
                
                // Set link checkout dengan query parameter baru
                continueBtn.href = 'ticket.php?' + params.toString();
            }
        }

        // Jalankan saat halaman dimuat
        document.addEventListener("DOMContentLoaded", function() {
            updateContinueUrl();
            validateBalance();
        });
    </script>
</body>
</html>