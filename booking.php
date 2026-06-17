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
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$time = isset($_GET['time']) ? $_GET['time'] : '19:30';

// Format jam untuk tampilan agar user-friendly
$time_display = date('H:i', strtotime($time)) . ' WIB';

// Format tanggal untuk tampilan agar user-friendly
$date_display = date('d M Y', strtotime($date));
$hari = date('Y-m-d', strtotime($date));
if ($hari == date('Y-m-d')) {
    $date_display = "Hari Ini (" . date('d M', strtotime($date)) . ")";
} elseif ($hari == date('Y-m-d', strtotime('+1 day'))) {
    $date_display = "Besok (" . date('d M', strtotime($date)) . ")";
}

// Cari ShowtimeID berdasarkan parameter pencarian
$showtime_id = 0;
$movie_escaped = mysqli_real_escape_string($conn, $movie);
$cinema_escaped = mysqli_real_escape_string($conn, $cinema);
$type_escaped = mysqli_real_escape_string($conn, $type);
$date_escaped = mysqli_real_escape_string($conn, $date);
$time_escaped = mysqli_real_escape_string($conn, $time);

$query_showtime = "SELECT st.ShowtimeID 
                   FROM showtime st
                   JOIN movie m ON st.MovieID = m.MovieID
                   JOIN studio s ON st.StudioID = s.StudioID
                   JOIN theater t ON s.TheaterID = t.TheaterID
                   WHERE m.Title = '$movie_escaped' 
                     AND t.Name = '$cinema_escaped' 
                     AND s.Type = '$type_escaped'
                     AND st.PlayDate = '$date_escaped' 
                     AND st.StartTime LIKE '$time_escaped%'
                   LIMIT 1";
$res_showtime = mysqli_query($conn, $query_showtime);
if ($res_showtime && mysqli_num_rows($res_showtime) > 0) {
    $row_showtime = mysqli_fetch_assoc($res_showtime);
    $showtime_id = $row_showtime['ShowtimeID'];
}

// Ambil daftar kursi yang sudah dipesan untuk jadwal ini
$occupied_seats = [];
if ($showtime_id > 0) {
    $query_seats = "SELECT SeatInfo FROM ticket WHERE ShowtimeID = $showtime_id AND Status = 'aktif'";
    $res_seats = mysqli_query($conn, $query_seats);
    if ($res_seats) {
        while ($row_seat = mysqli_fetch_assoc($res_seats)) {
            $occupied_seats[] = trim($row_seat['SeatInfo']);
        }
    }
}
?>
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
                    <li class="nav-item"><a class="nav-link active" href="films.php">Films</a></li>
                    <li class="nav-item"><a class="nav-link" href="resell.php">Resell Ticket</a></li>
                </ul>
                <div class="user-actions">
                    <?php 
                    $isLoggedIn = isset($_SESSION['UserID']) || isset($_SESSION['user_id']);
                    if ($isLoggedIn): 
                        $namaUser = isset($_SESSION['Nama']) ? $_SESSION['Nama'] : (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User');
                    ?>
                        <a href="profile.php" style="color: #d4af37; text-decoration: none; margin-right: 15px; font-weight: bold; font-family: monospace; font-size: 15px;">
                            Hi, <?php echo htmlspecialchars($namaUser); ?>!
                        </a>
                        <a href="profile.php">
                            <img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" alt="Profile" class="profile-icon">
                        </a>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm ms-3" style="border-radius: 20px; font-weight: bold; padding: 5px 15px;">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="login-button">LOG IN / SIGN UP</a>
                        <a href="login.php">
                            <img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" alt="Profile" class="profile-icon" style="opacity: 0.4;">
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <?php if ($showtime_id <= 0): ?>
        <div class="container my-5 text-center">
            <div class="card bg-dark border-secondary p-5" style="max-width: 600px; margin: 2rem auto; border-radius: 10px; border: 1px solid #3a2626; background-color: #150808 !important;">
                <h3 class="text-warning mb-4">Jadwal Tayang Tidak Ditemukan</h3>
                <p class="text-light mb-4" style="line-height: 1.6;">Maaf, jadwal tayang untuk film <strong><?php echo htmlspecialchars($movie); ?></strong> pada bioskop <strong><?php echo htmlspecialchars($cinema); ?></strong> (<?php echo htmlspecialchars($type); ?>) tanggal <strong><?php echo htmlspecialchars($date_display); ?></strong> pukul <strong><?php echo htmlspecialchars($time_display); ?></strong> tidak ditemukan di database. Silakan pilih jadwal lain.</p>
                <div>
                    <a href="index.php" class="btn px-4 py-2 me-3" style="font-weight: bold; border-radius: 20px; background-color: #d4af37; color: #000; text-decoration: none;">Kembali ke Beranda</a>
                    <a href="films.php" class="btn px-4 py-2" style="font-weight: bold; border-radius: 20px; color: #d4af37; border: 1px solid #d4af37; text-decoration: none;">Lihat Film Lain</a>
                </div>
            </div>
        </div>
    <?php else: ?>
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
                        <?php
                        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                        foreach ($rows as $rowLetter) {
                            ?>
                            <div class="seat-row">
                                <div class="seat-label"><?php echo $rowLetter; ?></div>
                                <div class="seat-group">
                                    <?php
                                    for ($i = 1; $i <= 6; $i++) {
                                        $seatName = $rowLetter . $i;
                                        $occupiedClass = in_array($seatName, $occupied_seats) ? 'occupied' : '';
                                        echo "<div class='seat $occupiedClass' data-seat-name='$seatName'></div>";
                                    }
                                    ?>
                                </div>
                                <div class="seat-gap"></div>
                                <div class="seat-group">
                                    <?php
                                    for ($i = 7; $i <= 12; $i++) {
                                        $seatName = $rowLetter . $i;
                                        $occupiedClass = in_array($seatName, $occupied_seats) ? 'occupied' : '';
                                        echo "<div class='seat $occupiedClass' data-seat-name='$seatName'></div>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
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
                        <img src="<?php echo htmlspecialchars($poster); ?>" class="movie-poster" alt="<?php echo htmlspecialchars($movie); ?>">
                        <div class="movie-details">
                            <h6><?php echo htmlspecialchars($movie); ?></h6>
                            <p><?php echo htmlspecialchars($date_display); ?> - <?php echo htmlspecialchars($time_display); ?></p>
                            <p><?php echo htmlspecialchars($cinema); ?> - <span id="summary-seats-label">Belum ada kursi</span></p>
                        </div>
                    </div>

                    <div class="ticket-section">
                        <p class="ticket-label">Kursi Dipilih</p>
                        <h5 class="ticket-seats" id="ticket-seats-display">Belum ada kursi</h5>

                        <div class="price-row" id="ticket-price-row" style="display: none;">
                            <span id="ticket-count-label">0x Tiket <?php echo htmlspecialchars($type); ?></span>
                            <span id="ticket-subtotal-display">Rp 0</span>
                        </div>
                        <div class="price-row price-row-last" id="service-fee-row" style="display: none;">
                            <span>Biaya Layanan</span>
                            <span>Rp 3.000</span>
                        </div>
                        
                        <div class="total-row">
                            <span class="total-label">Total Bayar</span>
                            <span class="total-price" id="total-price-display">Rp 0</span>
                        </div>
                    </div>

                    <a href="#" class="btn-checkout disabled" id="checkout-button" style="pointer-events: none; opacity: 0.5;">
                        Lanjutkan ke Pembayaran &rarr;
                    </a>

                </div>
            </div>

        </div> 
    </div> 
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php if ($showtime_id > 0): ?>
    <script>
        // Parameters from PHP
        const ticketType = "<?php echo htmlspecialchars($type); ?>";
        const ticketPrice = <?php echo $price; ?>;
        const movieTitle = "<?php echo addslashes($movie); ?>";
        const moviePoster = "<?php echo addslashes($poster); ?>";
        const movieDuration = "<?php echo addslashes($duration); ?>";
        const cinemaName = "<?php echo addslashes($cinema); ?>";
        const bookingDate = "<?php echo addslashes($date); ?>";
        const bookingTime = "<?php echo addslashes($time); ?>";

        let selectedSeats = [];

        // Auto-assign seat names based on row label and position
        document.querySelectorAll('.seat-row').forEach(row => {
            const rowLabel = row.querySelector('.seat-label').textContent.trim();
            const seatsInRow = row.querySelectorAll('.seat');
            seatsInRow.forEach((seat, index) => {
                const seatNum = index + 1;
                const seatName = rowLabel + seatNum;
                seat.setAttribute('data-seat-name', seatName);
                
                // If it is hardcoded as selected, add it to selectedSeats
                if (seat.classList.contains('selected') && !seat.classList.contains('occupied')) {
                    selectedSeats.push(seatName);
                }
            });
        });

        // Update booking summary
        function updateSummary() {
            const ticketCount = selectedSeats.length;
            const subtotal = ticketCount * ticketPrice;
            const serviceFee = 3000;
            const total = ticketCount > 0 ? subtotal + serviceFee : 0;

            // Elements
            const seatsDisplay = document.getElementById('ticket-seats-display');
            const seatsLabel = document.getElementById('summary-seats-label');
            const priceRow = document.getElementById('ticket-price-row');
            const countLabel = document.getElementById('ticket-count-label');
            const subtotalDisplay = document.getElementById('ticket-subtotal-display');
            const serviceRow = document.getElementById('service-fee-row');
            const totalDisplay = document.getElementById('total-price-display');
            const checkoutButton = document.getElementById('checkout-button');

            if (ticketCount > 0) {
                // Update seats list
                const seatsString = selectedSeats.join(', ');
                seatsDisplay.textContent = seatsString;
                seatsLabel.textContent = seatsString;

                // Update pricing details
                priceRow.style.display = 'flex';
                countLabel.textContent = `${ticketCount}x Tiket ${ticketType}`;
                subtotalDisplay.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
                
                serviceRow.style.display = 'flex';
                totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');

                // Enable checkout button and construct dynamic URL
                checkoutButton.classList.remove('disabled');
                checkoutButton.style.pointerEvents = 'auto';
                checkoutButton.style.opacity = '1';

                const queryParams = new URLSearchParams({
                    movie: movieTitle,
                    poster: moviePoster,
                    duration: movieDuration,
                    cinema: cinemaName,
                    type: ticketType,
                    price: ticketPrice,
                    date: bookingDate,
                    time: bookingTime,
                    seats: seatsString,
                    total: total
                });
                checkoutButton.href = 'payment.php?' + queryParams.toString();
            } else {
                seatsDisplay.textContent = 'Belum ada kursi';
                seatsLabel.textContent = 'Belum ada kursi';
                priceRow.style.display = 'none';
                serviceRow.style.display = 'none';
                totalDisplay.textContent = 'Rp 0';

                // Disable checkout button
                checkoutButton.classList.add('disabled');
                checkoutButton.style.pointerEvents = 'none';
                checkoutButton.style.opacity = '0.5';
                checkoutButton.href = '#';
            }
        }

        // Add click event listeners to seats
        document.querySelectorAll('.seat:not(.occupied)').forEach(seat => {
            seat.addEventListener('click', () => {
                const seatName = seat.getAttribute('data-seat-name');
                
                if (seat.classList.contains('selected')) {
                    seat.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(s => s !== seatName);
                } else {
                    seat.classList.add('selected');
                    selectedSeats.push(seatName);
                }
                
                updateSummary();
            });
        });

        // Initialize on load
        updateSummary();
    </script>
    <?php endif; ?>
</body>
</html>