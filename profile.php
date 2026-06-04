<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php';

// Set default location to Bandung if not set
if (!isset($_SESSION['selected_location'])) {
    $_SESSION['selected_location'] = 'Bandung';
}

// Check if location is being changed via GET request
if (isset($_GET['set_location'])) {
    $_SESSION['selected_location'] = $_GET['set_location'];
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Default values in case user is not logged in (to prevent errors)
$userName = "Andreas Lumban";
$userEmail = "andreas@gmail.com";
$userPhone = "081572152613";
$role = "user";
$walletBalance = 500000;

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $query = "SELECT * FROM user WHERE UserID = '$userId'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $userName = $user['Nama'];
        $userEmail = $user['Email'];
        $userPhone = $user['Phone'] ? $user['Phone'] : "081572152613";
        $role = $user['Role'];
    }
}

// Fetch bought tickets dynamically
$tickets = [];
if (isset($_SESSION['user_id'])) {
    $query_tickets = "SELECT t.*, st.StartTime, st.PlayDate, m.Title, m.Duration, m.Genre, m.Rating, m.PosterURL, s.Name as StudioName, th.Name as TheaterName 
                      FROM ticket t
                      JOIN transaction tr ON t.TransactionID = tr.TransactionID
                      JOIN showtime st ON t.ShowtimeID = st.ShowtimeID
                      JOIN movie m ON st.MovieID = m.MovieID
                      JOIN studio s ON t.StudioID = s.StudioID
                      JOIN theater th ON s.TheaterID = th.TheaterID
                      WHERE tr.UserID = '$userId' AND t.Status = 'aktif'
                      ORDER BY tr.TransDate DESC";
    $result_tickets = mysqli_query($conn, $query_tickets);
    if ($result_tickets && mysqli_num_rows($result_tickets) > 0) {
        while ($row = mysqli_fetch_assoc($result_tickets)) {
            // Re-map seats dynamically if stored
            $row['Seats'] = 'C5, C6'; // Default representation or database column
            $tickets[] = $row;
        }
    }
}

// Fallback to premium demo tickets if none are purchased yet (to keep the visual aesthetic wowing)
if (empty($tickets)) {
    $tickets = [
        [
            'Title' => 'Wonka',
            'Rating' => '6.9',
            'Genre' => 'Adventure, Comedy, Family',
            'Duration' => 116,
            'PosterURL' => 'https://image.tmdb.org/t/p/w500/qhb1qOilapbapxWQn9jtRCMwXJF.jpg',
            'TheaterName' => 'XXI Botanica Mall',
            'StudioName' => 'Gold Class',
            'PlayDate' => '2026-06-05',
            'StartTime' => '19:30:00',
            'Seats' => 'H9, H10',
            'TicketID' => 'TX-948201',
            'Status' => 'aktif'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Profile Premium</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
            color: #ffffff; 
            font-family: 'Outfit', sans-serif; 
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ================= NAVBAR STYLING ================= */
        .navbar { 
            border-bottom: 1px solid #3a2626;
            padding-top: 16px;
            padding-bottom: 16px;
            background-color: rgba(13, 6, 6, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
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
            width: 35px; 
            height: 35px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 2px solid #d4af37;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
        }

        /* ================= PROFILE LAYOUT STYLING ================= */
        .profile-wrapper {
            padding: 50px 0;
        }

        .glass-panel {
            background: var(--panel-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7), 0 0 20px rgba(212, 175, 55, 0.03);
            padding: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .glass-panel:hover {
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.8), 0 0 25px rgba(212, 175, 55, 0.06);
        }

        /* Profile Left Section Card */
        .profile-avatar-container {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 20px auto;
        }
        .profile-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #d4af37;
            padding: 5px;
            background: #110505;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
        }
        .user-name-display {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            text-align: center;
            margin-bottom: 25px;
        }

        .profile-details-list {
            margin-bottom: 30px;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 14px;
        }
        .detail-label {
            color: #888888;
            font-weight: 500;
        }
        .detail-value {
            color: #ffffff;
            font-weight: 600;
        }

        .profile-btn {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.3s ease;
            margin-bottom: 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .profile-btn-gold {
            background: var(--gold-gradient);
            color: #0d0606;
            border: none;
        }
        .profile-btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3);
            background: linear-gradient(135deg, #fcebb6 0%, #d4af37 100%);
            color: #0d0606;
        }
        .profile-btn-outline {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.15);
            color: #cccccc;
        }
        .profile-btn-outline:hover {
            background: rgba(255,255,255,0.05);
            color: #ffffff;
            border-color: rgba(255,255,255,0.3);
        }

        /* Profile Right Section - Wallet Card visual style */
        .section-headline {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.5px;
        }
        .section-headline::after {
            content: '';
            flex-grow: 1;
            height: 1px;
            background: linear-gradient(90deg, var(--glass-border) 0%, rgba(212, 175, 55, 0) 100%);
        }

        .credit-wallet-card {
            background: linear-gradient(135deg, #0d0505 0%, #20130d 50%, #0d0505 100%);
            border: 1px solid rgba(212, 175, 55, 0.35);
            border-radius: 20px;
            padding: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6), 0 0 20px rgba(212, 175, 55, 0.08);
            margin-bottom: 35px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .credit-wallet-card:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.7), 0 0 20px rgba(212, 175, 55, 0.15);
            border-color: #d4af37;
        }
        /* Geometric card decor */
        .credit-wallet-card::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -30%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .credit-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .card-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 16px;
            color: #d4af37;
            letter-spacing: 2px;
        }
        .card-chip {
            width: 45px;
            height: 33px;
            background: linear-gradient(135deg, #fae298 0%, #c59f2c 100%);
            border-radius: 6px;
            position: relative;
            box-shadow: inset 0 1px 3px rgba(255,255,255,0.4);
        }
        .card-chip::after {
            content: '';
            position: absolute;
            top: 6px; left: 6px; bottom: 6px; right: 6px;
            border: 1px solid rgba(0,0,0,0.15);
            border-radius: 3px;
        }
        .card-balance-section {
            margin-bottom: 24px;
        }
        .balance-label {
            font-size: 11px;
            color: #888888;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .balance-amount {
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            letter-spacing: 0.5px;
            text-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
        }
        .credit-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .card-holder {
            display: flex;
            flex-direction: column;
        }
        .holder-label {
            font-size: 9px;
            color: #666666;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .holder-name {
            font-size: 14px;
            font-weight: 700;
            color: #e5c060;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .btn-topup {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(212, 175, 55, 0.35);
            color: #d4af37;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 12px;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .btn-topup:hover {
            background: #d4af37;
            color: #000;
            border-color: #d4af37;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(212, 175, 55, 0.3);
        }

        /* ================= PREMIUM STUB TICKET STYLING ================= */
        .ticket-box {
            background: linear-gradient(135deg, #1b0a0a 0%, #0c0404 100%);
            border: 1px solid rgba(212, 175, 55, 0.25);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0,0,0,0.5);
            display: flex;
            position: relative;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }
        .ticket-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.6), 0 0 15px rgba(212, 175, 55, 0.08);
            border-color: rgba(212, 175, 55, 0.45);
        }

        /* Perforated ticket circles (aesthetic notches) */
        .ticket-box::before, .ticket-box::after {
            content: '';
            position: absolute;
            width: 24px;
            height: 24px;
            background-color: #0d0606; /* matches layout background to look cutout */
            border-radius: 50%;
            right: 178px; /* positioned right on the separator dashed line */
            z-index: 10;
            border: 1px solid rgba(212, 175, 55, 0.25);
        }
        .ticket-box::before {
            top: -12px;
            box-shadow: inset 0 -4px 6px rgba(0,0,0,0.4);
        }
        .ticket-box::after {
            bottom: -12px;
            box-shadow: inset 0 4px 6px rgba(0,0,0,0.4);
        }

        .ticket-main {
            padding: 24px;
            flex-grow: 1;
            display: flex;
            gap: 20px;
        }
        .ticket-poster-container {
            width: 100px;
            flex-shrink: 0;
        }
        .ticket-poster {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 16px rgba(0,0,0,0.6);
        }
        .ticket-content {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-grow: 1;
        }
        .ticket-header-group {
            margin-bottom: 10px;
        }
        .ticket-movie-title {
            font-size: 21px;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 4px 0;
            letter-spacing: 0.5px;
        }
        .ticket-rating {
            font-size: 13px;
            color: #d4af37;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .ticket-genre {
            font-size: 12px;
            color: #888888;
            margin-bottom: 12px;
        }
        .ticket-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px 16px;
            font-size: 12px;
            border-top: 1px solid rgba(255,255,255,0.05);
            padding-top: 10px;
        }
        .meta-cell-label {
            color: #666666;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 1px;
        }
        .meta-cell-value {
            color: #dddddd;
            font-weight: 600;
            font-size: 12px;
        }
        .meta-cell-value-highlight {
            color: #d4af37;
        }

        /* Ticket Right Side Stub */
        .ticket-stub {
            width: 190px;
            border-left: 2px dashed rgba(212, 175, 55, 0.25);
            background: rgba(212, 175, 55, 0.015);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 24px 20px;
            flex-shrink: 0;
            text-align: center;
        }
        .stub-brand-logo {
            font-family: serif;
            font-weight: 800;
            font-size: 16px;
            color: #d4af37;
            letter-spacing: 1px;
        }
        .stub-brand-logo span {
            font-style: italic;
            color: #c5a02d;
            font-weight: normal;
        }
        .stub-status-badge {
            background: rgba(46, 213, 115, 0.1);
            border: 1px solid rgba(46, 213, 115, 0.4);
            color: #2ed573;
            font-size: 10px;
            font-weight: 800;
            padding: 4px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 2px 8px rgba(46, 213, 115, 0.1);
            animation: pulse-badge 2s infinite alternate;
        }
        @keyframes pulse-badge {
            0% { box-shadow: 0 0 5px rgba(46, 213, 115, 0.1); }
            100% { box-shadow: 0 0 12px rgba(46, 213, 115, 0.35); }
        }

        .stub-barcode-container {
            width: 100%;
            background: #ffffff;
            padding: 8px;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .stub-barcode-img {
            width: 100%;
            height: 38px;
            object-fit: stretch;
            filter: contrast(200%);
        }
        .stub-ticket-id {
            font-family: monospace;
            font-size: 10px;
            color: #111111;
            margin-top: 4px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        /* Top-up modal or effect */
        .toast-topup {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #110505;
            border: 1px solid #d4af37;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            z-index: 1080;
            max-width: 320px;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid navbar-container">
            <a class="navbar-brand" href="index.php">Tixly<span>Cinema</span></a>
            
            <!-- Location Selector Dropdown next to Brand -->
            <div class="dropdown me-auto ms-3 d-none d-lg-block">
                <button class="btn btn-outline-warning btn-sm dropdown-toggle rounded-pill px-3" type="button" id="navbarLocationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: rgba(212, 175, 55, 0.4); color: #d4af37; background: rgba(212, 175, 55, 0.05); font-size: 13px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-geo-alt-fill me-1" viewBox="0 0 16 16">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                    <span><?php echo htmlspecialchars($_SESSION['selected_location']); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarLocationDropdown" style="background-color: #120707; border: 1px solid rgba(212, 175, 55, 0.3); z-index: 1050;">
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Bandung') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Bandung" style="<?php echo ($_SESSION['selected_location'] === 'Bandung') ? 'background-color: #d4af37;' : ''; ?>">Bandung</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Jakarta') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Jakarta" style="<?php echo ($_SESSION['selected_location'] === 'Jakarta') ? 'background-color: #d4af37;' : ''; ?>">Jakarta</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Surabaya') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Surabaya" style="<?php echo ($_SESSION['selected_location'] === 'Surabaya') ? 'background-color: #d4af37;' : ''; ?>">Surabaya</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Medan') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Medan" style="<?php echo ($_SESSION['selected_location'] === 'Medan') ? 'background-color: #d4af37;' : ''; ?>">Medan</a></li>
                    <li><a class="dropdown-item <?php echo ($_SESSION['selected_location'] === 'Yogyakarta') ? 'active text-dark' : 'text-white'; ?>" href="?set_location=Yogyakarta" style="<?php echo ($_SESSION['selected_location'] === 'Yogyakarta') ? 'background-color: #d4af37;' : ''; ?>">Yogyakarta</a></li>
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
                    <a href="profile.php">
                        <img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" 
                             alt="Profile Icon" 
                             class="profile-icon">
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- PROFILE SECTION -->
    <div class="container profile-wrapper">
        <div class="row g-4">
            
            <!-- LEFT PANEL: PROFILE CARD -->
            <div class="col-lg-4">
                <div class="glass-panel text-center">
                    
                    <div class="profile-avatar-container" style="margin-bottom: 25px;">
                        <img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" 
                             alt="User Avatar" class="profile-avatar">
                    </div>

                    <h2 class="user-name-display" style="margin-bottom: 25px;"><?php echo htmlspecialchars($userName); ?></h2>

                    <div class="profile-details-list">
                        <div class="detail-item">
                            <span class="detail-label">Status Akun</span>
                            <span class="detail-value text-success">Aktif</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value"><?php echo htmlspecialchars($userEmail); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">No. Telepon</span>
                            <span class="detail-value"><?php echo htmlspecialchars($userPhone); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tipe Akun</span>
                            <span class="detail-value text-capitalize"><?php echo htmlspecialchars($role); ?></span>
                        </div>
                    </div>

                    <a href="#" class="profile-btn profile-btn-gold" id="btn-edit-profile">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                        </svg>
                        Edit Profil
                    </a>
                    <a href="login.php" class="profile-btn profile-btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                        </svg>
                        Logout
                    </a>

                </div>
            </div>

            <!-- RIGHT PANEL: WALLET & BOUGHT TICKETS -->
            <div class="col-lg-8">
                <div class="glass-panel">
                    
                    <!-- WALLET SECTION -->
                    <div class="section-headline">Dompet Tixly</div>
                    
                    <div class="credit-wallet-card" id="tix-wallet-card">
                        <div class="credit-card-header">
                            <span class="card-brand">TIXLY pay</span>
                            <div class="card-chip"></div>
                        </div>
                        <div class="card-balance-section">
                            <div class="balance-label">Saldo Tersedia</div>
                            <div class="balance-amount" id="balance-value">Rp <?php echo number_format($walletBalance, 0, ',', '.'); ?></div>
                        </div>
                        <div class="credit-card-footer">
                            <div class="card-holder">
                                <span class="holder-label">Pemilik Kartu</span>
                                <span class="holder-name"><?php echo htmlspecialchars($userName); ?></span>
                            </div>
                            <button class="btn-topup" id="btn-topup-trigger">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-plus-circle-fill me-1" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                                </svg>
                                Top Up
                            </button>
                        </div>
                    </div>

                    <!-- BOUGHT TICKETS SECTION -->
                    <div class="section-headline">Tiket Aktif Anda</div>

                    <?php foreach ($tickets as $ticket): ?>
                        <div class="ticket-box">
                            <div class="ticket-main">
                                <div class="ticket-poster-container">
                                    <img src="<?php echo htmlspecialchars($ticket['PosterURL']); ?>" alt="<?php echo htmlspecialchars($ticket['Title']); ?> Poster" class="ticket-poster">
                                </div>
                                <div class="ticket-content">
                                    <div class="ticket-header-group">
                                        <h3 class="ticket-movie-title"><?php echo htmlspecialchars($ticket['Title']); ?></h3>
                                        <div class="ticket-rating">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-star-fill text-warning me-1" viewBox="0 0 16 16">
                                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                            </svg>
                                            ★ <?php echo htmlspecialchars($ticket['Rating']); ?>
                                        </div>
                                    </div>
                                    <div class="ticket-genre"><?php echo htmlspecialchars($ticket['Genre']); ?></div>
                                    
                                    <div class="ticket-meta-grid">
                                        <div>
                                            <div class="meta-cell-label">Bioskop / Studio</div>
                                            <div class="meta-cell-value"><?php echo htmlspecialchars($ticket['TheaterName']); ?></div>
                                            <div class="meta-cell-value meta-cell-value-highlight"><?php echo htmlspecialchars($ticket['StudioName']); ?></div>
                                        </div>
                                        <div>
                                            <div class="meta-cell-label">Waktu Tayang</div>
                                            <div class="meta-cell-value">
                                                <?php 
                                                    $formattedDate = date('D, d M Y', strtotime($ticket['PlayDate']));
                                                    echo htmlspecialchars($formattedDate);
                                                ?>
                                            </div>
                                            <div class="meta-cell-value"><?php echo date('H:i', strtotime($ticket['StartTime'])); ?> WIB</div>
                                        </div>
                                        <div>
                                            <div class="meta-cell-label">Kursi</div>
                                            <div class="meta-cell-value"><?php echo isset($ticket['Seats']) ? htmlspecialchars($ticket['Seats']) : 'A5, A6'; ?></div>
                                        </div>
                                        <div>
                                            <div class="meta-cell-label">Durasi Film</div>
                                            <div class="meta-cell-value"><?php echo htmlspecialchars($ticket['Duration']); ?> Menit</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="ticket-stub">
                                <div class="stub-brand-logo">Tixly<span>Cinema</span></div>
                                <span class="stub-status-badge">Tiket Aktif</span>
                                <div class="stub-barcode-container">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c9/Barcode-128.svg/640px-Barcode-128.svg.png" alt="Ticket Barcode" class="stub-barcode-img">
                                    <span class="stub-ticket-id"><?php echo isset($ticket['TicketID']) ? htmlspecialchars($ticket['TicketID']) : 'TX-892401'; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>

        </div>
    </div>

    <!-- TOAST POPUP FOR TOPUP AESTHETIC ACTION -->
    <div class="toast-topup p-3 d-none" id="topup-toast">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <strong class="text-warning">Top Up Saldo E-Wallet</strong>
            <button type="button" class="btn-close btn-close-white btn-sm" id="close-toast"></button>
        </div>
        <p class="text-white-50 small mb-3">Masukkan nominal Top Up yang Anda inginkan:</p>
        <div class="input-group input-group-sm mb-3">
            <span class="input-group-text bg-dark border-secondary text-warning">Rp</span>
            <input type="number" class="form-control bg-dark border-secondary text-white" id="topup-amount" value="100000" step="50000">
        </div>
        <button class="btn btn-warning btn-sm w-100 fw-bold" id="btn-confirm-topup" style="color:#000;">Konfirmasi Top Up</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logika fungsionalitas Top Up Interaktif
        document.addEventListener("DOMContentLoaded", function() {
            const walletCard = document.getElementById("tix-wallet-card");
            const topupTrigger = document.getElementById("btn-topup-trigger");
            const topupToast = document.getElementById("topup-toast");
            const closeToast = document.getElementById("close-toast");
            const btnConfirmTopup = document.getElementById("btn-confirm-topup");
            const topupAmountInput = document.getElementById("topup-amount");
            const balanceValue = document.getElementById("balance-value");

            // Open Top Up Dialog
            topupTrigger.addEventListener("click", function(e) {
                e.stopPropagation(); // prevent card click triggers
                topupToast.classList.remove("d-none");
            });

            // Close Top Up Dialog
            closeToast.addEventListener("click", function() {
                topupToast.classList.add("d-none");
            });

            // Handle Top Up Action
            btnConfirmTopup.addEventListener("click", function() {
                const addAmount = parseInt(topupAmountInput.value);
                if (addAmount > 0) {
                    // Current Balance
                    let currentBalance = 500000; // default state
                    const cleanBalanceText = balanceValue.textContent.replace("Rp ", "").replaceAll(".", "");
                    currentBalance = parseInt(cleanBalanceText);

                    // New Balance
                    const newBalance = currentBalance + addAmount;
                    balanceValue.textContent = "Rp " + newBalance.toLocaleString("id-ID");

                    // Show success notice
                    alert("Top Up Berhasil! Rp " + addAmount.toLocaleString("id-ID") + " ditambahkan ke E-Wallet Tixly Anda.");
                    topupToast.classList.add("d-none");
                }
            });



            // Edit Profile micro-action
            const btnEditProfile = document.getElementById("btn-edit-profile");
            if (btnEditProfile) {
                btnEditProfile.addEventListener("click", function(e) {
                    e.preventDefault();
                    alert("Fitur edit profil premium sedang disiapkan. Anda dapat mengganti foto, nama, dan data kontak segera!");
                });
            }
        });
    </script>
</body>
</html>