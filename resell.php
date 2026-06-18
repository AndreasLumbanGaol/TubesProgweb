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

// SINKRONISASI SESSION: Menyesuaikan dengan login.php yang menyimpan $_SESSION['user_id']
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = null;
if ($isLoggedIn) {
    // Type casting ke (int) untuk keamanan query SQL dari SQL Injection
    $user_id = (int)$_SESSION['user_id']; 
}

$msg_sukses = "";
$msg_error = "";

// Tangkap pesan sukses/gagal dari redirect URL (mencegah double submit / resubmit form saat refresh)
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'sukses_jual') {
        $msg_sukses = "Tiket berhasil dijual! Sekarang tiketmu ada di Pasar Resell.";
    } elseif ($_GET['pesan'] === 'sukses_batal') {
        $msg_sukses = "Berhasil menarik tiket! Tiket Anda kembali normal di akun profil.";
    } elseif ($_GET['pesan'] === 'error_sistem') {
        $msg_error = "Terjadi kesalahan sistem saat memproses tiket.";
    } elseif ($_GET['pesan'] === 'akses_ditolak') {
        $msg_error = "Akses ditolak! Ini bukan tiket Anda.";
    } elseif ($_GET['pesan'] === 'h_minus_1_error') {
        $msg_error = "Gagal menjual! Tiket hanya dapat dijual kembali maksimal H-1 sebelum hari tayang.";
    }
}

// ===================================================================
// LOGIKA PHP: PROSES JUAL (TRUE) ATAU BATAL JUAL (FALSE)
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $isLoggedIn) {
    $ticket_id_target = mysqli_real_escape_string($conn, $_POST['ticket_id']);
    
    // Validasi kepemilikan tiket demi keamanan
    $cek_kepemilikan = "SELECT t.TicketID FROM ticket t 
                        JOIN transaction tr ON t.TransactionID = tr.TransactionID 
                        WHERE t.TicketID = '$ticket_id_target' AND tr.UserID = '$user_id'";
    $hasil_cek = mysqli_query($conn, $cek_kepemilikan);

    if (mysqli_num_rows($hasil_cek) > 0) {
        if ($_POST['action'] == 'jual_tiket') {
            // Dapatkan FirstPrice dari tiket target di database demi keamanan
            $query_ticket_price = "SELECT FirstPrice FROM ticket WHERE TicketID = '$ticket_id_target' LIMIT 1";
            $res_ticket_price = mysqli_query($conn, $query_ticket_price);
            if ($res_ticket_price && mysqli_num_rows($res_ticket_price) > 0) {
                $row_ticket_price = mysqli_fetch_assoc($res_ticket_price);
                $first_price = intval($row_ticket_price['FirstPrice']);
                // Tentukan harga resell: 10% lebih mahal
                $second_price = $first_price + ($first_price * 0.10);
            } else {
                header("Location: resell.php?pesan=error_sistem");
                exit;
            }
            
            // Validasi H-1 sebelum hari tayang (minimal 24 jam dari sekarang)
            $query_showtime = "SELECT s.PlayDate, s.StartTime FROM ticket t 
                               JOIN showtime s ON t.ShowtimeID = s.ShowtimeID 
                               WHERE t.TicketID = '$ticket_id_target' LIMIT 1";
            $res_showtime = mysqli_query($conn, $query_showtime);
            if ($res_showtime && mysqli_num_rows($res_showtime) > 0) {
                $row_showtime = mysqli_fetch_assoc($res_showtime);
                $showtime_str = $row_showtime['PlayDate'] . ' ' . $row_showtime['StartTime'];
                $showtime_timestamp = strtotime($showtime_str);
                
                if (($showtime_timestamp - time()) < 86400) {
                    header("Location: resell.php?pesan=h_minus_1_error");
                    exit;
                }
            }
            // Aksi Jual: IsResale menjadi 1 (true)
            $query_action = "UPDATE ticket SET IsResale = 1, SecondPrice = '$second_price', SellerID = '$user_id' WHERE TicketID = '$ticket_id_target'";
            if (mysqli_query($conn, $query_action)) {
                // Post/Redirect/Get Pattern: Alihkan halaman agar form bersih kembali
                header("Location: resell.php?pesan=sukses_jual");
                exit;
            } else {
                header("Location: resell.php?pesan=error_sistem");
                exit;
            }
        } elseif ($_POST['action'] == 'batal_jual') {
            // Aksi Batal Jual: IsResale kembali menjadi 0 (false)
            $query_action = "UPDATE ticket SET IsResale = 0, SecondPrice = NULL, SellerID = NULL WHERE TicketID = '$ticket_id_target'";
            if (mysqli_query($conn, $query_action)) {
                header("Location: resell.php?pesan=sukses_batal");
                exit;
            } else {
                header("Location: resell.php?pesan=error_sistem");
                exit;
            }
        }
    } else {
        header("Location: resell.php?pesan=akses_ditolak");
        exit;
    }
}

// Set default lokasi bioskop
if (!isset($_SESSION['selected_location'])) {
    $_SESSION['selected_location'] = 'Bandung';
}
if (isset($_GET['set_location'])) {
    $_SESSION['selected_location'] = $_GET['set_location'];
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Resell Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0d0606; color: #ffffff; font-family: sans-serif; min-height: 100vh;}
        .navbar { border-bottom: 1px solid #3a2626; padding-top: 16px; padding-bottom: 16px; }
        .navbar-container { padding-left: 24px; padding-right: 24px; }
        .navbar-brand { color: #d4af37 !important; font-family: serif; font-size: 24px; font-weight: bold; }
        .navbar-brand span { font-style: italic; color: #b8962e; font-weight: normal; }
        .nav-center-menu { margin: 0 auto; align-items: center; }
        .nav-link { color: #cccccc !important; font-weight: 500; margin: 0 10px; }
        .nav-link.active { color: #d4af37 !important; border: 1px solid rgba(212, 175, 55, 0.5); border-radius: 8px; background: rgba(212, 175, 55, 0.1); padding: 4px 16px; }
        .user-actions { display: flex; align-items: center; }
        .login-button { background-color: #d4af37; color: #000; font-weight: bold; border-radius: 5px; padding: 8px 16px; font-size: 14px; text-decoration: none; margin-right: 16px; transition: background-color 0.3s; border: none; }
        .login-button:hover { background-color: #b8962e; color: #000; }
        .profile-icon { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #d4af37; }
        .resell-section { padding-top: 48px; padding-bottom: 48px; }
        .resell-header { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; }
        .resell-header-left { max-width: 500px; }
        .resell-header-right { display: flex; align-items: flex-end; margin-top: 24px; }
        .title-resell { font-size: 40px; letter-spacing: 1px; font-family: monospace; margin-bottom: 16px; margin-top: 0; }
        .title-resell span { font-style: italic; color: #d4af37; }
        .subtitle-resell { color: #888; font-size: 14px; margin-bottom: 24px; line-height: 1.5; }
        .btn-gold { background-color: #d4af37; color: #000; font-weight: bold; padding: 8px 24px; border-radius: 8px; border: none; cursor: pointer; transition: background-color 0.3s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-gold:hover { background-color: #b8962e; color: #000; }
        .btn-gold a { text-decoration: none; color: black; }
        .btn-add-icon { font-size: 20px; font-weight: normal; }
        .resell-grid { --bs-gutter-x: 24px; --bs-gutter-y: 24px; }
        .ticket-card { background-color: #150808; border: 1px solid #3a2626; border-radius: 15px; box-shadow: 0 0 15px rgba(212, 175, 55, 0.05); transition: transform 0.3s, border-color 0.3s; padding: 16px; height: 100%; display: flex; gap: 16px; position: relative; box-sizing: border-box; }
        .ticket-card:hover { transform: translateY(-5px); border-color: #d4af37; }
        .ticket-poster { width: 100px; height: 140px; object-fit: cover; border-radius: 8px; flex-shrink: 0; }
        .ticket-info { display: flex; flex-direction: column; flex-grow: 1; padding-bottom: 40px; }
        .ticket-title { color: #d4af37; margin-bottom: 8px; font-size: 20px; margin-top: 0; }
        .ticket-details { color: #888; font-size: 14px; margin-bottom: 16px; margin-top: 0; }
        .ticket-seat-badge { background-color: #212529; color: #888; border-radius: 50px; padding: 4px 16px; font-size: 12px; display: inline-block; width: fit-content; margin-bottom: 16px; }
        .ticket-price-container { margin-top: auto; }
        .ticket-price { color: #d4af37; font-size: 20px; margin: 0; font-weight: bold; }
        .btn-buy-ticket { position: absolute; bottom: 16px; right: 16px; background-color: #d4af37; border: none; border-radius: 8px; padding: 0; cursor: pointer; transition: background-color 0.3s; }
        .btn-buy-ticket:hover { background-color: #b8962e; }
        .btn-buy-ticket a { text-decoration: none; color: black; font-weight: bold; display: inline-block; padding: 8px 24px; }
        .tixly-modal .modal-content { background: linear-gradient(135deg, #120707 0%, #0a0303 100%); border: 1px solid rgba(212, 175, 55, 0.4); border-radius: 20px; color: #fff; }
        .tixly-modal .modal-header { border-bottom: 1px solid rgba(212, 175, 55, 0.15); }
        .tixly-modal .modal-title { color: #d4af37; font-family: serif; }
        .my-ticket-item { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 16px; transition: all 0.3s; margin-bottom: 15px;}
        .my-ticket-item:hover { border-color: #d4af37; background: rgba(212, 175, 55, 0.02); }
        .modal-price-calc { font-size: 12px; color: #888; margin-top: 4px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid navbar-container">
            <a class="navbar-brand" href="index.php">Tixly<span>Cinema</span></a>
            
            <div class="dropdown me-auto ms-3 d-none d-lg-block">
                <button class="btn btn-outline-warning btn-sm dropdown-toggle rounded-pill px-3" type="button" id="navbarLocationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: rgba(212, 175, 55, 0.4); color: #d4af37; background: rgba(212, 175, 55, 0.05); font-size: 13px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-geo-alt-fill me-1" viewBox="0 0 16 16">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                    <span><?php echo htmlspecialchars($_SESSION['selected_location']); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarLocationDropdown" style="background-color: #120707; border: 1px solid rgba(212, 175, 55, 0.3); z-index: 1050;">
                    <?php
                    $locs_query = mysqli_query($conn, "SELECT DISTINCT Location FROM theater ORDER BY Location ASC");
                    if ($locs_query && mysqli_num_rows($locs_query) > 0) {
                        while ($r = mysqli_fetch_assoc($locs_query)) {
                            $loc_name = $r['Location'];
                            echo '<li><a class="dropdown-item" href="?set_location=' . urlencode($loc_name) . '">' . htmlspecialchars($loc_name) . '</a></li>';
                        }
                    } else {
                        echo '<li><a class="dropdown-item" href="#">Tidak ada lokasi</a></li>';
                    }
                    ?>
                </ul>
            </div>
            
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
                    <?php if ($isLoggedIn): 
                        // Menyesuaikan penamaan dengan session yang di-set pada login.php Anda
                        $namaUser = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
                    ?>
                        <a href="profile.php" style="color: #d4af37; text-decoration: none; margin-right: 15px; font-weight: bold; font-family: monospace; font-size: 15px;">
                            Hi, <?php echo htmlspecialchars($namaUser); ?>!
                        </a>
                        <a href="profile.php"><img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" class="profile-icon"></a>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm ms-3" style="border-radius: 20px; font-weight: bold; padding: 5px 15px;">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="login-button">LOG IN / SIGN UP</a>
                        <a href="login.php"><img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" class="profile-icon" style="opacity: 0.4;"></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container resell-section">
        <?php if ($msg_sukses != ""): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #1a4d2e; color: #fff; border-color: #28a745;">
                <strong>Sukses!</strong> <?php echo $msg_sukses; ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($msg_error != ""): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: #4d1a1a; color: #fff; border-color: #dc3545;">
                <strong>Gagal!</strong> <?php echo $msg_error; ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="resell-header">
            <div class="resell-header-left">
                <h1 class="title-resell">Resell<span>Ticket</span></h1>
                <p class="subtitle-resell">Beli tiket dari member lain dengan harga lebih murah, atau jual tiketmu yang tidak terpakai.</p>
                <button class="btn-gold"><a href="films.php">Semua Film</a></button>
            </div>
            <div class="resell-header-right">
                <button class="btn-gold" data-bs-toggle="modal" data-bs-target="#jualTiketModal">
                    <span class="btn-add-icon">+</span> Kelola / Jual Tiketku
                </button>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-lg-2 resell-grid">
            <?php
            // Menyaring agar tiket milik sendiri tidak muncul di halaman pasar resell
            $filter_user = $isLoggedIn ? "AND tr.UserID != '$user_id'" : "";
            $query_resale = "SELECT t.TicketID, t.SeatInfo, t.SecondPrice, m.Title, m.PosterURL, m.Duration, s.PlayDate, s.StartTime, st.Name as StudioName, st.Type, th.Name as TheaterName 
                             FROM ticket t 
                             JOIN showtime s ON t.ShowtimeID = s.ShowtimeID 
                             JOIN movie m ON s.MovieID = m.MovieID
                             JOIN studio st ON s.StudioID = st.StudioID
                             JOIN theater th ON st.TheaterID = th.TheaterID
                             JOIN transaction tr ON t.TransactionID = tr.TransactionID
                             WHERE t.IsResale = 1 AND t.Status = 'aktif' $filter_user
                             ORDER BY t.TicketID DESC";
            $res_resale = mysqli_query($conn, $query_resale);

            if ($res_resale && mysqli_num_rows($res_resale) > 0) {
                while ($ticket = mysqli_fetch_assoc($res_resale)) {
                    $playDate = date('d M Y', strtotime($ticket['PlayDate']));
                    
                    $startTimeRaw = date('H:i', strtotime($ticket['StartTime']));
                    $startTime = $startTimeRaw . ' WIB';
                    
                    $theaterText = htmlspecialchars($ticket['TheaterName']);
                    $seatText = 'Kursi: ' . htmlspecialchars($ticket['SeatInfo']) . ' (1 Tiket)';
                    
                    $params = http_build_query([
                        'resell_ticket_id' => $ticket['TicketID'], 
                        'movie' => $ticket['Title'], 'poster' => $ticket['PosterURL'],
                        'duration' => $ticket['Duration'] . 'm', 'cinema' => $ticket['TheaterName'], 
                        'type' => $ticket['Type'], 'price' => $ticket['SecondPrice'], 
                        'date' => $playDate, 'time' => $startTimeRaw, 
                        'seats' => $ticket['SeatInfo'], 'total' => $ticket['SecondPrice'] + 3000
                    ]);
            ?>
                <div class="col">
                    <div class="ticket-card">
                        <img src="<?php echo htmlspecialchars($ticket['PosterURL']); ?>" class="ticket-poster" alt="<?php echo htmlspecialchars($ticket['Title']); ?>">
                        <div class="ticket-info">
                            <h5 class="ticket-title"><?php echo htmlspecialchars($ticket['Title']); ?></h5>
                            <p class="ticket-details"><?php echo "$playDate - $startTime - $theaterText"; ?></p>
                            <div class="ticket-seat-badge"><?php echo $seatText; ?></div>
                            <div class="ticket-price-container">
                                <p class="ticket-price">Rp <?php echo number_format($ticket['SecondPrice'], 0, ',', '.'); ?></p>
                            </div>
                        </div>
                        <button class="btn-buy-ticket"><a href="payment.php?<?php echo $params; ?>">Beli</a></button>
                    </div>
                </div>
            <?php 
                }
            } else {
            ?>
                <div class="col-12">
                    <div class="alert" style="background-color:rgba(255,255,255,0.05); color:#aaa; border:1px solid #3a2626; border-radius: 12px;">
                        Saat ini belum ada tiket yang dijual oleh pengguna lain di Pasar Resell.
                    </div>
                </div>
            <?php } ?>
        </div> 
    </div>

    <div class="modal fade tixly-modal" id="jualTiketModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kelola Tiket Saya</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="text-white-50 small mb-3">* Catatan: Sistem otomatis menetapkan harga tiket resell sebesar 10% lebih mahal untuk pembeli, dan penjual akan menerima dana 10% lebih murah dari harga beli langsung (tidak mendapatkan keuntungan).</p>
                    
                    <div>
                        <?php 
                        if($isLoggedIn) {
                            $query_mytickets = "SELECT t.TicketID, t.SeatInfo, t.FirstPrice, t.IsResale, m.Title, m.PosterURL, s.PlayDate, s.StartTime, st.Name as StudioName 
                                                FROM ticket t
                                                JOIN transaction tr ON t.TransactionID = tr.TransactionID
                                                JOIN showtime s ON t.ShowtimeID = s.ShowtimeID
                                                JOIN movie m ON s.MovieID = m.MovieID
                                                JOIN studio st ON t.StudioID = st.StudioID
                                                WHERE tr.UserID = '$user_id' AND t.Status = 'aktif'";
                            $res_mytickets = mysqli_query($conn, $query_mytickets);

                            if ($res_mytickets && mysqli_num_rows($res_mytickets) > 0) {
                                while ($myticket = mysqli_fetch_assoc($res_mytickets)) {
                                    $resell_price = $myticket['FirstPrice'] + ($myticket['FirstPrice'] * 0.10);
                                    $payout = $myticket['FirstPrice'] - ($myticket['FirstPrice'] * 0.10);
                                    
                                    // Pengecekan H-1 minimal 24 jam sebelum hari tayang
                                    $showtime_str = $myticket['PlayDate'] . ' ' . $myticket['StartTime'];
                                    $showtime_timestamp = strtotime($showtime_str);
                                    $is_resellable = (($showtime_timestamp - time()) >= 86400);
                        ?>
                                    <form action="resell.php" method="POST" class="m-0">
                                        <?php if($myticket['IsResale'] == 0): ?>
                                            <input type="hidden" name="action" value="jual_tiket">
                                        <?php else: ?>
                                            <input type="hidden" name="action" value="batal_jual">
                                        <?php endif; ?>
                                        <input type="hidden" name="ticket_id" value="<?php echo $myticket['TicketID']; ?>">
                                        <input type="hidden" name="second_price" value="<?php echo $resell_price; ?>">
                                        
                                        <div class="my-ticket-item d-flex align-items-center justify-content-between <?php echo ($myticket['IsResale'] == 1) ? 'border border-warning' : ''; ?>">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?php echo htmlspecialchars($myticket['PosterURL']); ?>" alt="Poster" style="width: 50px; aspect-ratio: 2/3; object-fit: cover; border-radius: 6px;">
                                                <div>
                                                    <h6 class="margin-0 text-white font-weight-bold" style="margin-bottom: 2px;"><?php echo htmlspecialchars($myticket['Title']); ?></h6>
                                                    <p class="text-white-50 small mb-0"><?php echo date('d M', strtotime($myticket['PlayDate'])) . ' - ' . date('H:i', strtotime($myticket['StartTime'])) . ' - ' . htmlspecialchars($myticket['StudioName']); ?></p>
                                                    <div class="badge bg-secondary" style="font-size: 10px; padding: 2px 8px; margin-top: 4px;">Kursi: <?php echo htmlspecialchars($myticket['SeatInfo']); ?></div>
                                                    
                                                    <?php if($myticket['IsResale'] == 1): ?>
                                                        <span class="badge bg-warning text-dark ms-2" style="font-size: 10px;">Sedang Dijual</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div style="color: #d4af37; font-weight: bold; font-size: 14px;" title="Harga Jual">Harga Jual: Rp <?php echo number_format($resell_price, 0, ',', '.'); ?></div>
                                                <div style="color: #ff4d4d; font-weight: bold; font-size: 12px;" title="Dana Diterima">Dana Diterima: Rp <?php echo number_format($payout, 0, ',', '.'); ?></div>
                                                <div class="modal-price-calc">Asli: Rp <?php echo number_format($myticket['FirstPrice'], 0, ',', '.'); ?></div>
                                                
                                                <?php if($myticket['IsResale'] == 0): ?>
                                                    <?php if($is_resellable): ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-warning mt-2 px-3" style="font-size: 11px; font-weight: bold; border-radius: 6px;">Jual Sekarang</button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 px-3" disabled style="font-size: 11px; font-weight: bold; border-radius: 6px;" title="Tiket hanya dapat dijual kembali maksimal H-1 sebelum hari tayang">H-1 Lewat</button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger mt-2 px-3" style="font-size: 11px; font-weight: bold; border-radius: 6px;">Batal Jual</button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </form>
                        <?php
                                }
                            } else {
                                echo "<p class='text-white-50 text-center my-3'>Anda belum memiliki tiket aktif.</p>";
                            }
                        } else {
                            echo "<div class='text-center py-4'>
                                    <p class='text-white-50 mb-3'>Silakan login terlebih dahulu untuk menjual tiket Anda.</p>
                                    <a href='login.php' class='btn-gold px-4' style='font-size: 13px;'>Login Sekarang</a>
                                  </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>