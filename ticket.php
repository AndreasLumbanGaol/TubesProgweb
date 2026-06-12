<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php'; // Menyambungkan ke database

// Redirect admin to admin dashboard
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin/index.php");
    exit();
}

// Ambil parameter dari URL (GET) yang dikirim dari payment.php
$movie = isset($_GET['movie']) ? $_GET['movie'] : 'Wonka';
$poster = isset($_GET['poster']) ? $_GET['poster'] : 'https://image.tmdb.org/t/p/w200/qhb1qOilapbapxWQn9jtRCMwXJF.jpg';
$duration = isset($_GET['duration']) ? $_GET['duration'] : '120m';
$cinema = isset($_GET['cinema']) ? $_GET['cinema'] : 'CGV Paskal 23';
$type = isset($_GET['type']) ? $_GET['type'] : 'Regular';
$price = isset($_GET['price']) ? intval($_GET['price']) : 50000;
$date = isset($_GET['date']) ? $_GET['date'] : 'Hari Ini';
$time = isset($_GET['time']) ? $_GET['time'] : '19:30';
$seats = isset($_GET['seats']) ? $_GET['seats'] : '';
$total = isset($_GET['total']) ? intval($_GET['total']) : 103000;
$method = isset($_GET['method']) ? $_GET['method'] : 'OVO';

$resell_ticket_id = isset($_GET['resell_ticket_id']) ? intval($_GET['resell_ticket_id']) : 0;

$seat_array = explode(',', $seats);
$seat_count = count($seat_array);
if ($seats === '' || empty($seats)) {
    $seat_count = 0;
}
$subtotal = $seat_count * $price;

// --- AMANKAN SESSION USER ID (Mendukung huruf besar & kecil) ---
$userId = null;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} elseif (isset($_SESSION['UserID'])) {
    $userId = $_SESSION['UserID'];
}

$payment_error = '';

// Jika user terdeteksi login, jalankan penyimpanan database
if($userId) {
    $trxKey = "trx_" . md5($movie . $date . $time . $seats . $resell_ticket_id);
    
    if(!isset($_SESSION[$trxKey])) {
        
        // 1. Cari ShowtimeID dan StudioID secara dinamis dari database
        $movie_clean_db = mysqli_real_escape_string($conn, $movie);
        $cinema_clean_db = mysqli_real_escape_string($conn, $cinema);
        $type_clean_db = mysqli_real_escape_string($conn, $type);
        $date_clean_db = mysqli_real_escape_string($conn, $date);
        $time_clean_db = mysqli_real_escape_string($conn, $time);

        $query_find_st = "SELECT st.ShowtimeID, st.StudioID 
                          FROM showtime st 
                          JOIN movie m ON st.MovieID = m.MovieID 
                          JOIN studio s ON st.StudioID = s.StudioID
                          JOIN theater t ON s.TheaterID = t.TheaterID
                          WHERE m.Title LIKE '%$movie_clean_db%'
                            AND t.Name = '$cinema_clean_db'
                            AND s.Type = '$type_clean_db'
                            AND st.PlayDate = '$date_clean_db'
                            AND st.StartTime LIKE '$time_clean_db%'
                          LIMIT 1";
        $res_find_st = mysqli_query($conn, $query_find_st);
        
        // Set nilai default aman jika showtime belum di-set di panel admin (Fallback)
        $showtime_id = 1; 
        $studio_id = 1;
        if ($res_find_st && mysqli_num_rows($res_find_st) > 0) {
            $row_st = mysqli_fetch_assoc($res_find_st);
            $showtime_id = $row_st['ShowtimeID'];
            $studio_id = $row_st['StudioID'];
        }

        $payment_ok = true;
        if ($method === 'Tixly Wallet') {
            $res_bal = mysqli_query($conn, "SELECT Saldo FROM user WHERE UserID = '$userId'");
            if ($res_bal && mysqli_num_rows($res_bal) > 0) {
                $row_bal = mysqli_fetch_assoc($res_bal);
                $current_saldo = $row_bal['Saldo'];
                if ($current_saldo >= $total) {
                    // Deduct user balance
                    mysqli_query($conn, "UPDATE user SET Saldo = Saldo - $total WHERE UserID = '$userId'");
                } else {
                    $payment_ok = false;
                }
            } else {
                $payment_ok = false;
            }
        }

        if ($payment_ok) {
            if ($resell_ticket_id > 0) {
                // ALUR A: PEMBELIAN RESELL
                
                // Cari ID Penjual, Harga Jual, dan info kursi tiket dari tiket sebelum kepemilikan diproses
                $query_seller = mysqli_query($conn, "
                    SELECT tr.UserID AS SellerID, tk.SecondPrice, tk.ShowtimeID, tk.StudioID, tk.SeatInfo 
                    FROM ticket tk
                    JOIN `transaction` tr ON tk.TransactionID = tr.TransactionID
                    WHERE tk.TicketID = $resell_ticket_id
                    LIMIT 1
                ");
                
                $seller_id = 0;
                $resell_price = 0;
                $showtime_id = 0;
                $studio_id = 0;
                $seat_info = '';

                if ($query_seller && mysqli_num_rows($query_seller) > 0) {
                    $row_seller = mysqli_fetch_assoc($query_seller);
                    $seller_id = intval($row_seller['SellerID']);
                    $resell_price = intval($row_seller['SecondPrice']);
                    $showtime_id = intval($row_seller['ShowtimeID']);
                    $studio_id = intval($row_seller['StudioID']);
                    $seat_info = $row_seller['SeatInfo'];
                }

                $insertTrx = mysqli_query($conn, "INSERT INTO `transaction` (TotalPrice, PaymentStatus, UserID, PaymentMethod) VALUES ($total, 'sukses', $userId, '" . mysqli_real_escape_string($conn, $method) . "')");
                $transactionId = mysqli_insert_id($conn);
                
                if($transactionId) {
                    // 1. Buat tiket baru untuk pembeli
                    $seat_info_escaped = mysqli_real_escape_string($conn, $seat_info);
                    $query_new_ticket = "INSERT INTO ticket (FirstPrice, Status, IsResale, TransactionID, ShowtimeID, StudioID, SeatInfo, SellerID) 
                                         VALUES ($resell_price, 'aktif', 0, $transactionId, $showtime_id, $studio_id, '$seat_info_escaped', $seller_id)";
                    mysqli_query($conn, $query_new_ticket);
                    
                    // 2. Ubah status tiket penjual menjadi 'terjual' dan nonaktifkan IsResale
                    $query_update_seller_ticket = "UPDATE ticket SET Status = 'terjual', IsResale = 0 WHERE TicketID = $resell_ticket_id";
                    mysqli_query($conn, $query_update_seller_ticket);
                    
                    // 3. Tambahkan uang penjualan tiket ke Saldo penjual
                    if ($seller_id > 0 && $resell_price > 0) {
                        mysqli_query($conn, "UPDATE user SET Saldo = Saldo + $resell_price WHERE UserID = $seller_id");
                    }
                }
            } else {
                // ALUR B: PEMBELIAN BARU DARI HOME / FILMS
                $insertTrx = mysqli_query($conn, "INSERT INTO `transaction` (TotalPrice, PaymentStatus, UserID, PaymentMethod) VALUES ($total, 'sukses', $userId, '" . mysqli_real_escape_string($conn, $method) . "')");
                $transactionId = mysqli_insert_id($conn);
                
                if($transactionId && $seat_count > 0) {
                    foreach($seat_array as $kursi) {
                        $kursi = trim($kursi);
                        if(!empty($kursi)) {
                            $query_ins_ticket = "INSERT INTO ticket (FirstPrice, Status, TransactionID, StudioID, SeatInfo, ShowtimeID, IsResale) 
                                                 VALUES ($price, 'aktif', $transactionId, $studio_id, '$kursi', $showtime_id, 0)";
                            mysqli_query($conn, $query_ins_ticket);
                        }
                    }
                }
            }
            // Kunci token transaksi agar anti-duplicate saat di-refresh (F5)
            $_SESSION[$trxKey] = true;
        } else {
            $payment_error = "Saldo Dompet Tixly Anda tidak mencukupi untuk melakukan transaksi ini. Silakan isi saldo Anda terlebih dahulu.";
        }
    }
}
// -----------------------------------------------------------------

$movie_clean = preg_replace('/[^A-Za-z0-9]/', '', $movie);
$movie_code = strtoupper(substr($movie_clean, 0, 3));
if (strlen($movie_code) < 3) {
    $movie_code = 'TIX';
}
$seed = $seats . $cinema . $time;
$random_num = (abs(crc32($seed)) % 9000) + 1000;
$booking_code = "TXL-2026-" . $movie_code . "-" . $random_num;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Tiket Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0d0606; font-family: sans-serif; min-height: 100vh; color: #ffffff; }
        .navbar { border-bottom: 1px solid #3a2626; padding-top: 16px; padding-bottom: 16px; }
        .navbar-brand { color: #d4af37 !important; font-family: serif; font-size: 24px; font-weight: bold; }
        .navbar-brand span { font-style: italic; color: #b8962e; font-weight: normal; }
        .nav-center-menu { margin: 0 auto; align-items: center; }
        .nav-link { color: #cccccc !important; font-weight: 500; margin: 0 10px; }
        .profile-icon { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 1px solid #d4af37; }
        .ticket-section { padding-top: 48px; padding-bottom: 48px; }
        .ticket-wrapper { max-width: 600px; margin: 0 auto; }
        .box-panel { background-color: #150808; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
        .border-ticket { border: 1px solid #d4af37; box-shadow: 0 0 15px rgba(212, 175, 55, 0.05); }
        .border-success-box { border: 1px solid #198754; background-color: rgba(25, 135, 84, 0.05); text-align: center; padding-top: 16px; padding-bottom: 16px; }
        .success-header { text-align: center; margin-bottom: 24px; }
        .success-circle { width: 90px; height: 90px; border: 1px solid #6c757d; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; }
        .success-title { font-weight: normal; letter-spacing: 1px; font-size: 28px; margin-bottom: 8px; margin-top: 0; }
        .success-title span { font-style: italic; color: #d4af37; font-weight: bold; }
        .success-subtitle { color: #888; font-size: 14px; margin: 0; }
        .movie-header { margin-bottom: 24px; }
        .movie-title { color: #ffffff; margin-bottom: 4px; font-family: monospace; font-size: 32px; margin-top: 0; }
        .movie-genre { color: #888; font-size: 14px; margin: 0; }
        .ticket-details-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 16px; }
        .ticket-label { font-size: 12px; color: #888; letter-spacing: 1px; margin-bottom: 5px; text-transform: uppercase; }
        .ticket-value { font-size: 16px; color: #fff; font-family: monospace; }
        .ticket-value-highlight { color: #d4af37; font-weight: bold; }
        .dashed-line { border-top: 1px dashed #3a2626; margin: 25px 0; }
        .qr-section { display: flex; align-items: center; gap: 24px; }
        .qr-img { width: 100px; height: 100px; object-fit: cover; border: 2px solid #d4af37; border-radius: 8px; }
        .booking-code { font-family: monospace; font-size: 18px; letter-spacing: 1px; color: #d4af37; font-weight: bold; margin-bottom: 8px; }
        .qr-desc { color: #888; margin: 0; font-size: 13px; line-height: 1.4; }
        .qr-desc span { color: #ffffff; font-weight: bold; }
        .summary-title { color: #d4af37; margin-bottom: 24px; text-decoration: underline; font-family: monospace; font-size: 16px; margin-top: 0; }
        .summary-row, .summary-total { display: flex; justify-content: space-between; align-items: center; }
        .summary-row { color: #888; font-size: 14px; margin-bottom: 16px; }
        .summary-total { border-top: 1px solid #6c757d; padding-top: 16px; }
        .total-label { color: #d4af37; font-weight: bold; font-size: 15px; }
        .badge-method { background-color: #3b3b3b; color: #aaa; font-size: 11px; padding: 4px 8px; border-radius: 4px; margin-right: 8px; }
        .total-amount { color: #d4af37; font-weight: bold; font-size: 16px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid navbar-container">
            <a class="navbar-brand" href="index.php">Tixly<span>Cinema</span></a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav nav-center-menu">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="films.php">Films</a></li>
                    <li class="nav-item"><a class="nav-link" href="resell.php">Resell Ticket</a></li>
                </ul>
                <div class="user-actions">
                    <a href="profile.php"><img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" class="profile-icon"></a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container ticket-section">
        <div class="ticket-wrapper">
            <?php if (!empty($payment_error)): ?>
                <div class="success-header">
                    <div class="success-circle" style="border-color: #dc3545; box-shadow: 0 0 15px rgba(220, 53, 69, 0.2); margin-top: 40px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#dc3545" class="bi x-lg" viewBox="0 0 16 16">
                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                        </svg>
                    </div>
                    <h2 class="success-title" style="color: #dc3545;">Pembayaran <span>Gagal</span></h2>
                    <p class="success-subtitle"><?php echo htmlspecialchars($payment_error); ?></p>
                </div>
                
                <div class="box-panel text-center py-5" style="border: 1px solid rgba(220, 53, 69, 0.3); background-color: rgba(220, 53, 69, 0.05); border-radius: 20px;">
                    <p class="mb-4 text-white-50">Silakan hubungi administrator atau gunakan metode pembayaran alternatif lainnya.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="profile.php" class="btn btn-warning px-4 py-2" style="font-weight: bold; border-radius: 8px; background: linear-gradient(135deg, #d4af37 0%, #b8962e 100%); border: none; color: #000;">Ke Profil</a>
                        <a href="javascript:history.back()" class="btn btn-outline-light px-4 py-2" style="font-weight: bold; border-radius: 8px;">Kembali ke Pembayaran</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="success-header">
                    <div class="success-circle" style="border-color: #20c997; box-shadow: 0 0 15px rgba(32, 201, 151, 0.2);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#20c997" class="bi bi-check-lg" viewBox="0 0 16 16"><path d="M12.736 1.4A1 1 0 0 1 13 2v7.184a1 1 0 0 1-.264.688L9.043 14H3.07l-3-3a1 1 0 0 1 0-1.414l1.414-1.414a1 1 0 0 1 1.414 0L5 10.243l6.322-6.322a1 1 0 0 1 1.414 0Z"/></svg>
                    </div>
                    <h2 class="success-title">Pembayaran <span>Berhasil</span></h2>
                    <p class="success-subtitle">Transaksi dikonfirmasi - <?php echo date("d M Y, H:i"); ?> WIB</p>
                </div>

                <div class="box-panel border-success-box">
                    <h6 style="color: #20c997; margin:0 0 4px 0; font-weight: bold;">Tiket Aktif & Siap Digunakan</h6>
                    <p style="margin: 0; color: #888; font-size: 14px;">Tunjukkan QR Code di bawah kepada petugas bioskop saat masuk</p>
                </div>

                <div class="box-panel border-ticket">
                    <div class="movie-header">
                        <h2 class="movie-title"><?php echo htmlspecialchars($movie); ?></h2>
                        <p class="movie-genre">Tixly Cinema Digital Pass</p>
                    </div>

                    <div class="ticket-details-grid">
                        <div><div class="ticket-label">TANGGAL</div><div class="ticket-value"><?php echo htmlspecialchars($date); ?></div></div>
                        <div><div class="ticket-label">JAM TAYANG</div><div class="ticket-value"><?php echo htmlspecialchars($time); ?></div></div>
                        <div><div class="ticket-label">DURASI</div><div class="ticket-value"><?php echo htmlspecialchars($duration); ?></div></div>
                        <div><div class="ticket-label">BIOSKOP</div><div class="ticket-value"><?php echo htmlspecialchars($cinema); ?></div></div>
                        <div><div class="ticket-label">STUDIO</div><div class="ticket-value"><?php echo htmlspecialchars($type); ?></div></div>
                        <div><div class="ticket-label">KURSI</div><div class="ticket-value ticket-value-highlight"><?php echo htmlspecialchars($seats); ?></div></div>
                    </div>

                    <div class="dashed-line"></div>

                    <div class="qr-section">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($booking_code); ?>&color=000000&bgcolor=ffffff" alt="QR Code" class="qr-img">
                        <div>
                            <div class="ticket-label">KODE BOOKING</div>
                            <div class="booking-code"><?php echo htmlspecialchars($booking_code); ?></div>
                            <p class="qr-desc">Scan QR Code ini di pintu masuk<br>studio <?php echo htmlspecialchars($cinema); ?>.<br>Berlaku untuk <span><?php echo $seat_count; ?> orang.</span></p>
                        </div>
                    </div>
                </div>

                <div class="box-panel border-ticket">
                    <h6 class="summary-title">Ringkasan Pembayaran</h6>
                    <div class="summary-row"><span><?php echo $seat_count; ?>x Tiket <?php echo htmlspecialchars($type); ?></span><span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span></div>
                    <div class="summary-row"><span>Biaya Layanan</span><span>Rp 3.000</span></div>
                    <div class="summary-total">
                        <span class="total-label">Total Dibayar</span>
                        <div><span class="badge-method"><?php echo htmlspecialchars($method); ?></span><span class="total-amount">Rp <?php echo number_format($total, 0, ',', '.'); ?></span></div>
                    </div>
                </div>
            <?php endif; ?>
        </div> 
    </div> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>