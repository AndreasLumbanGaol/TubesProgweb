<?php
$movie = isset($_GET['movie']) ? $_GET['movie'] : 'Wonka';
$poster = isset($_GET['poster']) ? $_GET['poster'] : 'https://image.tmdb.org/t/p/w200/qhb1qOilapbapxWQn9jtRCMwXJF.jpg';
$duration = isset($_GET['duration']) ? $_GET['duration'] : '1h 56m';
$cinema = isset($_GET['cinema']) ? $_GET['cinema'] : 'CGV Paskal 23';
$type = isset($_GET['type']) ? $_GET['type'] : 'Regular';
$price = isset($_GET['price']) ? intval($_GET['price']) : 50000;
$date = isset($_GET['date']) ? $_GET['date'] : 'Hari Ini';
$time = isset($_GET['time']) ? $_GET['time'] : '19:30';
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
                            <div class="seat-group"><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div><div class="seat"></div></div>
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
                        <img src="<?php echo htmlspecialchars($poster); ?>" class="movie-poster" alt="<?php echo htmlspecialchars($movie); ?>">
                        <div class="movie-details">
                            <h6><?php echo htmlspecialchars($movie); ?></h6>
                            <p><?php echo htmlspecialchars($date); ?> - <?php echo htmlspecialchars($time); ?></p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
</body>
</html>