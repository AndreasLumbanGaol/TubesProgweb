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

// Set default lokasi ke Bandung jika belum diatur
if (!isset($_SESSION['selected_location'])) {
    $_SESSION['selected_location'] = 'Bandung';
}

// Periksa apakah lokasi diubah melalui request GET
if (isset($_GET['set_location'])) {
    $_SESSION['selected_location'] = $_GET['set_location'];
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Nilai default data user
$userName = "User";
$userEmail = "";
$userPhone = "081572152613";
$role = "user";
$walletBalance = 0;

// Mengambil id dari session login sistem
$isLoggedIn = isset($_SESSION['UserID']) || isset($_SESSION['user_id']);
$userId = null;
if ($isLoggedIn) {
    $userId = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : $_SESSION['user_id'];
}

// ===================================================================
// LOGIKA PHP: PROSES UPDATE DATA PROFIL (KETIKA DISUBMIT)
// ===================================================================
$updateMessage = "";
$updateClass = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile' && $userId) {
        $newNama  = mysqli_real_escape_string($conn, $_POST['nama']);
        $newEmail = mysqli_real_escape_string($conn, $_POST['email']);
        $newPhone = mysqli_real_escape_string($conn, $_POST['phone']);
        $newPass  = $_POST['password'];

        // Ambil data user lama untuk mengecek jika email kembar milik orang lain
        $cekEmail = mysqli_query($conn, "SELECT * FROM user WHERE Email = '$newEmail' AND UserID != '$userId'");
        if (mysqli_num_rows($cekEmail) > 0) {
            $updateMessage = "Email sudah digunakan oleh akun lain!";
            $updateClass = "alert-danger";
        } else {
            // Cek apakah kolom password baru diisi atau dikosongkan
            if (!empty($newPass)) {
                $query_update = "UPDATE user SET Nama = '$newNama', Email = '$newEmail', Phone = '$newPhone', Password = '$newPass' WHERE UserID = '$userId'";
            } else {
                $query_update = "UPDATE user SET Nama = '$newNama', Email = '$newEmail', Phone = '$newPhone' WHERE UserID = '$userId'";
            }

            if (mysqli_query($conn, $query_update)) {
                // Sinkronisasi session nama agar sapaan di navbar langsung berubah
                $_SESSION['user_name'] = $newNama;
                
                // Refresh halaman agar data terbaru langsung tampil bersih
                header("Location: profile.php?status=success");
                exit;
            } else {
                $updateMessage = "Gagal memperbarui profil: " . mysqli_error($conn);
                $updateClass = "alert-danger";
            }
        }
    }
}

if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $updateMessage = "Profil Anda berhasil diperbarui!";
    $updateClass = "alert-success";
}

// Fetch data pengguna terbaru dari database
if ($userId) {
    $query = "SELECT * FROM user WHERE UserID = '$userId'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $userName = $user['Nama'];
        $userEmail = $user['Email'];
        $userPhone = $user['Phone'] ? $user['Phone'] : "081572152613";
        $role = $user['Role'];
        $walletBalance = $user['Saldo'];
    }
}

// Fetch Purchased Ticket History (Riwayat Pembelian)
$purchase_history = [];
if ($userId) {
    $query_purchases = "SELECT t.*, st.StartTime, st.PlayDate, m.Title, m.Duration, m.Genre, m.Rating, m.PosterURL, s.Name as StudioName, th.Name as TheaterName 
                    FROM ticket t
                    JOIN transaction tr ON t.TransactionID = tr.TransactionID
                    JOIN showtime st ON t.ShowtimeID = st.ShowtimeID
                    JOIN movie m ON st.MovieID = m.MovieID
                    LEFT JOIN studio s ON t.StudioID = s.StudioID
                    LEFT JOIN theater th ON s.TheaterID = th.TheaterID
                    WHERE tr.UserID = '$userId'
                    ORDER BY tr.TransactionID DESC";
    $result_purchases = mysqli_query($conn, $query_purchases);
    if ($result_purchases && mysqli_num_rows($result_purchases) > 0) {
        while ($row = mysqli_fetch_assoc($result_purchases)) {
            if(empty($row['StudioName'])) $row['StudioName'] = 'Regular Class';
            if(empty($row['TheaterName'])) $row['TheaterName'] = 'Tixly Cinema Center';
            
            // Calculate status dynamically: Selesai if PlayDate is in the past, otherwise check ticket status
            if ($row['Status'] === 'terjual') {
                $row['CalculatedStatus'] = 'Terjual';
            } elseif ($row['IsResale'] == 1) {
                $row['CalculatedStatus'] = 'Sedang Dijual';
            } else {
                $playdate = $row['PlayDate'];
                $today = date('Y-m-d');
                if ($playdate < $today) {
                    $row['CalculatedStatus'] = 'Selesai';
                } else {
                    $row['CalculatedStatus'] = ($row['Status'] == 'aktif') ? 'Aktif' : ucfirst($row['Status']);
                }
            }
            $purchase_history[] = $row;
        }
    }
}

// Fetch Resold Ticket History (Riwayat Penjualan)
$sales_history = [];
if ($userId) {
    $query_sales = "SELECT t.*, m.Title, m.PosterURL, s.PlayDate, s.StartTime, st.Name as StudioName, th.Name as TheaterName
                    FROM ticket t
                    JOIN showtime s ON t.ShowtimeID = s.ShowtimeID
                    JOIN movie m ON s.MovieID = m.MovieID
                    LEFT JOIN studio st ON t.StudioID = st.StudioID
                    LEFT JOIN theater th ON st.TheaterID = th.TheaterID
                    WHERE t.SellerID = '$userId' AND (t.Status = 'terjual' OR t.IsResale = 1)
                    ORDER BY t.TicketID DESC";
    $result_sales = mysqli_query($conn, $query_sales);
    if ($result_sales && mysqli_num_rows($result_sales) > 0) {
        while ($row = mysqli_fetch_assoc($result_sales)) {
            if(empty($row['StudioName'])) $row['StudioName'] = 'Regular Class';
            if(empty($row['TheaterName'])) $row['TheaterName'] = 'Tixly Cinema Center';
            $sales_history[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Profile Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --gold-primary: #d4af37;
            --gold-gradient: linear-gradient(135deg, #e5c060, #b28d32);
            --dark-bg: #0d0606;
            --panel-bg: rgba(26, 12, 11, 0.45);
            --glass-border: rgba(212, 175, 55, 0.2);
        }
        body { 
            background: radial-gradient(circle at top right, #2c0e0c 0%, #0d0606 60%);
            color: #ffffff; font-family: 'Outfit', sans-serif; min-height: 100vh; overflow-x: hidden;
        }
        .navbar { 
            border-bottom: 1px solid #3a2626; padding-top: 16px; padding-bottom: 16px;
            background-color: rgba(13, 6, 6, 0.95); backdrop-filter: blur(10px); z-index: 1000;
        }
        .navbar-brand { color: #d4af37 !important; font-family: serif; font-size: 24px; font-weight: bold; text-decoration: none; }
        .navbar-brand span { font-style: italic; color: #b8962e; font-weight: normal; }
        .nav-center-menu { margin: 0 auto; align-items: center; }
        .nav-link { color: #cccccc !important; font-weight: 500; margin: 0 10px; }
        .profile-icon { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #d4af37; box-shadow: 0 0 10px rgba(212, 175, 55, 0.3); }
        .profile-wrapper { padding: 50px 0; }
        .glass-panel {
            background: var(--panel-bg); backdrop-filter: blur(16px); border: 1px solid var(--glass-border);
            border-radius: 24px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7); padding: 30px;
        }
        .profile-avatar { width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 3px solid #d4af37; padding: 5px; background: #110505; }
        .user-name-display { font-size: 24px; font-weight: 800; color: #ffffff; text-align: center; margin-bottom: 25px; }
        .detail-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.05); font-size: 14px; }
        .detail-label { color: #888888; }
        .detail-value { color: #ffffff; font-weight: 600; }
        .profile-btn { width: 100%; padding: 12px; border-radius: 12px; font-weight: 700; font-size: 14px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 12px; transition: all 0.3s ease; }
        .profile-btn-gold { background: var(--gold-gradient); color: #0d0606; border: none; }
        .profile-btn-gold:hover { opacity: 0.9; transform: translateY(-2px); color: #0d0606; }
        .profile-btn-outline { background: transparent; border: 1px solid rgba(255,255,255,0.15); color: #cccccc; }
        .profile-btn-outline:hover { background: rgba(255, 255, 255, 0.05); color: #fff; }
        .section-headline { font-size: 20px; font-weight: 800; color: #ffffff; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .section-headline::after { content: ''; flex-grow: 1; height: 1px; background: linear-gradient(90deg, var(--glass-border) 0%, rgba(212, 175, 55, 0) 100%); }
        .credit-wallet-card {
            background: linear-gradient(135deg, #0d0505 0%, #20130d 50%, #0d0505 100%);
            border: 1px solid rgba(212, 175, 55, 0.35); border-radius: 20px; padding: 24px; position: relative; margin-bottom: 35px;
        }
        .balance-amount { font-size: 32px; font-weight: 800; color: #ffffff; }
        .ticket-box {
            background: linear-gradient(135deg, #1b0a0a 0%, #0c0404 100%); border: 1px solid rgba(212, 175, 55, 0.25);
            border-radius: 20px; overflow: hidden; display: flex; position: relative; margin-bottom: 25px;
        }
        .ticket-box::before, .ticket-box::after {
            content: ''; position: absolute; width: 24px; height: 24px; background-color: #0d0606; border-radius: 50%; right: 178px; z-index: 10; border: 1px solid rgba(212, 175, 55, 0.25);
        }
        .ticket-box::before { top: -12px; } .ticket-box::after { bottom: -12px; }
        .ticket-main { padding: 24px; flex-grow: 1; display: flex; gap: 20px; }
        .ticket-poster { width: 100px; object-fit: cover; border-radius: 12px; }
        .ticket-movie-title { font-size: 21px; font-weight: 800; color: #ffffff; margin: 0; }
        .ticket-meta-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px 16px; font-size: 12px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 10px; margin-top: 10px; }
        .meta-cell-label { color: #666666; font-size: 9px; text-transform: uppercase; }
        .meta-cell-value { color: #dddddd; font-weight: 600; }
        .ticket-stub { width: 190px; border-left: 2px dashed rgba(212, 175, 55, 0.25); display: flex; flex-direction: column; align-items: center; justify-content: space-between; padding: 24px 20px; text-align: center; }
        .stub-status-badge { background: rgba(46, 213, 115, 0.1); border: 1px solid rgba(46, 213, 115, 0.4); color: #2ed573; font-size: 10px; font-weight: 800; padding: 4px 14px; border-radius: 20px; }
        .stub-barcode-img { width: 100%; height: 38px; object-fit: stretch; background: #fff; padding: 2px; }

        /* History Tabs Styles */
        .history-tabs { display: flex; gap: 15px; border-bottom: 2px solid rgba(212, 175, 55, 0.15); margin-bottom: 25px; padding-bottom: 10px; }
        .history-tab-btn { background: transparent; border: none; color: #888; font-size: 16px; font-weight: 700; padding: 8px 16px; cursor: pointer; transition: all 0.3s ease; position: relative; }
        .history-tab-btn:hover { color: #fff; }
        .history-tab-btn.active { color: #d4af37; }
        .history-tab-btn.active::after { content: ''; position: absolute; bottom: -12px; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #d4af37 0%, #b30000 100%); border-radius: 3px; }
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
                    <li class="nav-item"><a class="nav-link" href="resell.php">Resell Ticket</a></li>
                </ul>
                <div class="user-actions">
                    <span class="text-white-50 me-3 d-none d-lg-inline" style="font-size: 14px;">Hai, <strong><?php echo htmlspecialchars($userName); ?></strong></span>
                    <a href="profile.php"><img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" class="profile-icon" alt="Avatar"></a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container profile-wrapper">
        
        <?php if(!empty($updateMessage)): ?>
            <div class="alert <?php echo $updateClass; ?> alert-dismissible fade show text-center mb-4 mx-auto" style="max-width: 800px; border-radius: 12px;" role="alert">
                <?php echo $updateMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="close" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            
            <div class="col-lg-4">
                <div class="glass-panel text-center">
                    <div style="margin-bottom: 25px;">
                        <img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" class="profile-avatar" alt="Avatar Utama">
                    </div>
                    <h2 class="user-name-display"><?php echo htmlspecialchars($userName); ?></h2>
                    <div class="profile-details-list mb-4">
                        <div class="detail-item"><span class="detail-label">Status Akun</span><span class="detail-value text-success">Aktif</span></div>
                        <div class="detail-item"><span class="detail-label">Email</span><span class="detail-value"><?php echo htmlspecialchars($userEmail); ?></span></div>
                        <div class="detail-item"><span class="detail-label">No. Telepon</span><span class="detail-value"><?php echo htmlspecialchars($userPhone); ?></span></div>
                        <div class="detail-item"><span class="detail-label">Tipe Akun</span><span class="detail-value text-capitalize"><?php echo htmlspecialchars($role); ?></span></div>
                    </div>
                    <a href="#" class="profile-btn profile-btn-gold" id="btn-edit-profile">Edit Profil</a>
                    <a href="logout.php" class="profile-btn profile-btn-outline">Logout</a>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="glass-panel">
                    <div class="section-headline">Dompet Tixly</div>
                    <div class="credit-wallet-card">
                        <div class="balance-amount">Rp <?php echo number_format($walletBalance, 0, ',', '.'); ?></div>
                        <div class="text-white-50 small mt-1">Saldo Tersedia untuk Pembelian Tiket</div>
                    </div>

                    <div class="section-headline">Riwayat Tiket</div>
                    
                    <div class="history-tabs">
                        <button class="history-tab-btn active" id="pembelian-tab-btn" onclick="showTab('pembelian')">Riwayat Pembelian</button>
                        <button class="history-tab-btn" id="penjualan-tab-btn" onclick="showTab('penjualan')">Riwayat Penjualan</button>
                    </div>

                    <!-- Purchases History Content -->
                    <div id="pembelian-tab-content" class="history-tab-content">
                        <?php if(empty($purchase_history)): ?>
                            <div class="alert text-center py-4 text-white-50" style="background: rgba(255,255,255,0.02); border: 1px dashed #3a2626; border-radius: 12px;">
                                Anda belum memiliki riwayat pembelian tiket film.
                            </div>
                        <?php else: ?>
                            <?php foreach ($purchase_history as $ticket): 
                                $isCompleted = ($ticket['CalculatedStatus'] === 'Selesai' || $ticket['CalculatedStatus'] === 'Terjual');
                                if ($ticket['CalculatedStatus'] === 'Terjual') {
                                    $badgeClass = 'bg-danger text-white border-0';
                                } elseif ($ticket['CalculatedStatus'] === 'Sedang Dijual') {
                                    $badgeClass = 'bg-warning text-dark border-0';
                                } else {
                                    $badgeClass = $isCompleted ? 'bg-secondary text-white border-0' : '';
                                }
                                $barcodeStyle = $isCompleted ? 'opacity: 0.3; filter: grayscale(1);' : '';
                                $statusLabel = $ticket['CalculatedStatus'];
                            ?>
                                <div class="ticket-box" style="<?php echo $isCompleted ? 'border-color: rgba(255,255,255,0.08);' : ''; ?>">
                                    <div class="ticket-main">
                                        <div class="ticket-poster-container">
                                            <img src="<?php echo htmlspecialchars($ticket['PosterURL']); ?>" alt="Poster" class="ticket-poster" style="<?php echo $isCompleted ? 'filter: grayscale(0.5); opacity: 0.6;' : ''; ?>">
                                        </div>
                                        <div class="ticket-content">
                                            <h3 class="ticket-movie-title" style="<?php echo $isCompleted ? 'color: #888;' : ''; ?>"><?php echo htmlspecialchars($ticket['Title']); ?></h3>
                                            <div class="text-warning small">★ <?php echo htmlspecialchars($ticket['Rating']); ?> | <?php echo htmlspecialchars($ticket['Genre']); ?></div>
                                            
                                            <div class="ticket-meta-grid">
                                                <div>
                                                    <div class="meta-cell-label">Bioskop / Studio</div>
                                                    <div class="meta-cell-value" style="<?php echo $isCompleted ? 'color: #777;' : ''; ?>"><?php echo htmlspecialchars($ticket['TheaterName']); ?></div>
                                                    <div class="meta-cell-value text-warning" style="<?php echo $isCompleted ? 'color: rgba(212,175,55,0.4) !important;' : ''; ?>"><?php echo htmlspecialchars($ticket['StudioName']); ?></div>
                                                </div>
                                                <div>
                                                    <div class="meta-cell-label">Waktu Tayang</div>
                                                    <div class="meta-cell-value" style="<?php echo $isCompleted ? 'color: #777;' : ''; ?>"><?php echo date('d M Y', strtotime($ticket['PlayDate'])); ?></div>
                                                    <div class="meta-cell-value" style="<?php echo $isCompleted ? 'color: #777;' : ''; ?>"><?php echo date('H:i', strtotime($ticket['StartTime'])); ?> WIB</div>
                                                </div>
                                                <div>
                                                    <div class="meta-cell-label">Kursi</div>
                                                    <div class="meta-cell-value text-warning" style="<?php echo $isCompleted ? 'color: rgba(212,175,55,0.4) !important;' : ''; ?>"><?php echo htmlspecialchars($ticket['SeatInfo']); ?></div>
                                                </div>
                                                <div>
                                                    <div class="meta-cell-label">Durasi</div>
                                                    <div class="meta-cell-value" style="<?php echo $isCompleted ? 'color: #777;' : ''; ?>"><?php echo htmlspecialchars($ticket['Duration']); ?> Menit</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ticket-stub" style="<?php echo $isCompleted ? 'border-left-color: rgba(255,255,255,0.08);' : ''; ?>">
                                        <div style="font-family: serif; font-weight:800; color:<?php echo $isCompleted ? '#666' : '#d4af37'; ?>;">Tixly<span>Cinema</span></div>
                                        <span class="stub-status-badge <?php echo $badgeClass; ?>"><?php echo ($statusLabel === 'Aktif') ? 'Tiket Aktif' : $statusLabel; ?></span>
                                        <div style="width:100%;">
                                            <img src="https://bwipjs-api.metafloor.com/?bcid=code128&text=TX-<?php echo $ticket['TicketID']; ?>&scale=2&rotate=N&includeheader=false" alt="Barcode" class="stub-barcode-img" style="background: #ffffff; padding: 4px; object-fit: contain; <?php echo $barcodeStyle; ?>">
                                            <span class="small monospace d-block text-dark bg-white" style="<?php echo $barcodeStyle; ?>">TX-<?php echo $ticket['TicketID']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Sales History Content -->
                    <div id="penjualan-tab-content" class="history-tab-content" style="display: none;">
                        <?php if(empty($sales_history)): ?>
                            <div class="alert text-center py-4 text-white-50" style="background: rgba(255,255,255,0.02); border: 1px dashed #3a2626; border-radius: 12px;">
                                Anda belum pernah menjual tiket di pasar resell.
                            </div>
                        <?php else: ?>
                            <?php foreach ($sales_history as $sale): 
                                $isSold = ($sale['IsResale'] == 0 && $sale['Status'] == 'terjual');
                                $statusBadge = $isSold ? '<span class="badge bg-success text-white px-3 py-2 rounded-pill">Terjual</span>' : '<span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending</span>';
                                $sellPrice = $sale['SecondPrice'] ? $sale['SecondPrice'] : ($sale['FirstPrice'] * 1.10);
                                $earned = $sale['FirstPrice'] - ($sale['FirstPrice'] * 0.10);
                            ?>
                                <div class="resell-history-item p-3 mb-3 d-flex align-items-center justify-content-between" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(212,175,55,0.15); border-radius: 16px;">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?php echo htmlspecialchars($sale['PosterURL']); ?>" alt="Poster" style="width: 50px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(212,175,55,0.3);">
                                        <div>
                                            <h5 class="text-white mb-1" style="font-size: 16px; font-weight: 700;"><?php echo htmlspecialchars($sale['Title']); ?></h5>
                                            <span class="small text-white-50 d-block">Bioskop: <strong><?php echo htmlspecialchars($sale['TheaterName']); ?> (<?php echo htmlspecialchars($sale['StudioName']); ?>)</strong></span>
                                            <span class="small text-white-50 d-block">Kursi: <strong class="text-warning"><?php echo htmlspecialchars($sale['SeatInfo']); ?></strong> | Jadwal: <?php echo date('d M Y', strtotime($sale['PlayDate'])); ?> - <?php echo date('H:i', strtotime($sale['StartTime'])); ?></span>
                                        </div>
                                    </div>
                                    <div class="text-end d-flex flex-column align-items-end gap-2">
                                        <div style="text-align: right;">
                                            <span class="small text-muted d-block" style="font-size: 10px;">HARGA JUAL</span>
                                            <strong class="text-warning d-block" style="font-size: 14px;">Rp <?php echo number_format($sellPrice, 0, ',', '.'); ?></strong>
                                            
                                            <span class="small text-muted d-block mt-1" style="font-size: 10px;"><?php echo $isSold ? 'DANA DITERIMA' : 'ESTIMASI TERIMA'; ?></span>
                                            <strong class="<?php echo $isSold ? 'text-success' : 'text-danger'; ?> d-block" style="font-size: 14px;">Rp <?php echo number_format($earned, 0, ',', '.'); ?></strong>
                                        </div>
                                        <div>
                                            <?php echo $statusBadge; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-white" style="background: linear-gradient(135deg, #190a09 0%, #0d0606 100%); border: 1px solid rgba(212, 175, 55, 0.4); border-radius: 20px;">
                <div class="modal-header" style="border-bottom: 1px solid rgba(212, 175, 55, 0.15);">
                    <h5 class="modal-title" id="editProfileModalLabel" style="color: #d4af37; font-weight: 700;">Ubah Data Profil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="profile.php" method="POST">
                    <div class="modal-body" style="padding: 25px;">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="mb-3">
                            <label for="inputNama" class="form-label small text-white-50">Nama Lengkap</label>
                            <input type="text" class="form-control bg-dark text-white border-secondary" id="inputNama" name="nama" value="<?php echo htmlspecialchars($userName); ?>" required style="border-radius: 10px;">
                        </div>
                        <div class="mb-3">
                            <label for="inputEmail" class="form-label small text-white-50">Email Akun</label>
                            <input type="email" class="form-control bg-dark text-white border-secondary" id="inputEmail" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" required style="border-radius: 10px;">
                        </div>
                        <div class="mb-3">
                            <label for="inputPhone" class="form-label small text-white-50">No. Handphone</label>
                            <input type="tel" class="form-control bg-dark text-white border-secondary" id="inputPhone" name="phone" value="<?php echo htmlspecialchars($userPhone); ?>" required style="border-radius: 10px;">
                        </div>
                        <div class="mb-3">
                            <label for="inputPassword" class="form-label small text-white-50">Password Baru (Biarkan kosong jika tidak diganti)</label>
                            <input type="password" class="form-control bg-dark text-white border-secondary" id="inputPassword" name="password" placeholder="Masukkan password baru" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid rgba(212, 175, 55, 0.15);">
                        <button type="button" class="btn btn-sm text-white-50" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm px-4" style="background: var(--gold-gradient); color: #0d0606; font-weight: 700; border-radius: 10px;">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Edit Profile Modal
            const btnEditProfile = document.getElementById('btn-edit-profile');
            if (btnEditProfile) {
                btnEditProfile.addEventListener('click', function(e) {
                    e.preventDefault();
                    const editModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
                    editModal.show();
                });
            }
        });

        function showTab(tabName) {
            document.querySelectorAll('.history-tab-content').forEach(content => {
                content.style.display = 'none';
            });
            document.querySelectorAll('.history-tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById(tabName + '-tab-content').style.display = 'block';
            document.getElementById(tabName + '-tab-btn').classList.add('active');
        }
    </script>
</body>
</html>