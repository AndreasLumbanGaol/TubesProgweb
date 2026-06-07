<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

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
    <title>Tixly Cinema - Semua Film</title>
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

        .movies-section { padding-bottom: 48px; padding-top: 40px; }
        .section-header { display: flex; align-items: center; margin-bottom: 25px; }
        .section-title { font-size: 21px; font-weight: bold; border-left: 3px solid #d4af37; padding-left: 15px; margin: 0; }
        
        .movie-grid { --bs-gutter-x: 24px; --bs-gutter-y: 24px; }
        .movie-link { text-decoration: none !important; color: inherit; display: block; }
        .movie-card { background: transparent; border: none; text-align: center; transition: transform 0.3s; cursor: pointer; }
        .movie-card:hover { transform: scale(1.05); }

        .poster-wrapper { position: relative; width: 100%; margin-bottom: 10px; }
        .movie-poster { border-radius: 10px; width: 100%; aspect-ratio: 2/3; object-fit: cover; margin-bottom: 0; }
        
        .movie-title { color: #d4af37; font-size: 14px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .movie-duration { color: #888; font-size: 12px; margin: 0; }
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
                            <img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" alt="Profile Icon" class="profile-icon">
                        </a>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm ms-3" style="border-radius: 20px; font-weight: bold; padding: 5px 15px;">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="login-button">LOG IN / SIGN UP</a>
                        <a href="login.php"><img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" alt="Profile Icon" class="profile-icon" style="opacity: 0.4;"></a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </nav>

    <div class="container movies-section">
        <div class="section-header">
            <h2 class="section-title">ALL MOVIES</h2>
        </div>

        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 movie-grid">
            <?php
            $loc = mysqli_real_escape_string($conn, $_SESSION['selected_location']);
            $query_all = "SELECT DISTINCT m.* 
                          FROM movie m
                          LEFT JOIN showtime s ON m.MovieID = s.MovieID
                          LEFT JOIN studio st ON s.StudioID = st.StudioID
                          LEFT JOIN theater t ON st.TheaterID = t.TheaterID
                          WHERE t.Location = '$loc' OR m.Rating = 0
                          ORDER BY m.MovieID ASC";
            $res_all = mysqli_query($conn, $query_all);
            if ($res_all) {
                while($film = mysqli_fetch_assoc($res_all)): 
            ?>
            <div class="col">
                <a href="#" class="movie-link" data-bs-toggle="modal" data-bs-target="#bookingModal" 
                   data-title="<?php echo htmlspecialchars($film['Title']); ?>" 
                   data-poster="<?php echo htmlspecialchars($film['PosterURL']); ?>" 
                   data-duration="<?php echo $film['Duration']; ?>m">
                    <div class="card movie-card">
                        <div class="poster-wrapper">
                            <img src="<?php echo htmlspecialchars($film['PosterURL']); ?>" class="movie-poster" alt="<?php echo htmlspecialchars($film['Title']); ?>">
                        </div>
                        <p class="movie-title"><?php echo htmlspecialchars($film['Title']); ?></p>
                        <p class="movie-duration"><?php echo htmlspecialchars($film['Genre']); ?></p>
                    </div>
                </a>
            </div>
            <?php 
                endwhile;
            } 
            ?>
        </div>
    </div>

    <?php 
    if(file_exists('components/booking_modal.php')){
        include 'components/booking_modal.php'; 
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>