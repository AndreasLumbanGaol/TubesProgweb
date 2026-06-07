<?php
session_start();
include '../koneksi.php';
$page = 'dashboard';

// Handle delete film action
$message = '';
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // Since movie.MovieID has ON DELETE CASCADE constraint on showtimes, deleting a movie will automatically delete its showtimes and tickets!
    $delete_query = mysqli_query($conn, "DELETE FROM movie WHERE MovieID = $delete_id");
    if ($delete_query) {
        $message = "Film berhasil dihapus!";
    } else {
        $message = "Gagal menghapus film: " . mysqli_error($conn);
    }
}

// Fetch stats
$stats = [
    'total_film' => 0,
    'total_transaksi' => 0,
    'total_pendapatan' => 0,
    'resell_aktif' => 0
];

// Total Film
$q_film = mysqli_query($conn, "SELECT COUNT(*) AS total FROM movie");
if ($q_film) {
    $stats['total_film'] = mysqli_fetch_assoc($q_film)['total'];
}

// Total Transaksi (Sukses)
$q_trx = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `transaction` WHERE PaymentStatus = 'sukses'");
if ($q_trx) {
    $stats['total_transaksi'] = mysqli_fetch_assoc($q_trx)['total'];
}

// Total Pendapatan
$q_rev = mysqli_query($conn, "SELECT SUM(TotalPrice) AS total FROM `transaction` WHERE PaymentStatus = 'sukses'");
if ($q_rev) {
    $stats['total_pendapatan'] = mysqli_fetch_assoc($q_rev)['total'] ?? 0;
}

// Resell Aktif
$q_resell = mysqli_query($conn, "SELECT COUNT(*) AS total FROM ticket WHERE IsResale = 1 AND Status = 'aktif'");
if ($q_resell) {
    $stats['resell_aktif'] = mysqli_fetch_assoc($q_resell)['total'];
}

// Fetch all movies
$movies_query = mysqli_query($conn, "SELECT * FROM movie ORDER BY MovieID DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Admin Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #0d0606; color: #ffffff; font-family: 'Segoe UI', sans-serif; min-height: 100vh; }
        .navbar { border-bottom: 1px solid #3a2626; padding: 16px 24px; background-color: #0d0606; }
        .navbar-brand { color: #d4af37 !important; font-family: Georgia, serif; font-size: 24px; font-weight: bold; }
        .navbar-brand span { font-style: italic; color: #b8962e; font-weight: normal; }
        .sidebar { background-color: #0d0606; min-height: calc(100vh - 73px); padding: 24px 16px; border-right: 1px solid #3a2626; }
        .sidebar-link { display: block; color: #888; text-decoration: none; padding: 10px 16px; border-radius: 8px; margin-bottom: 8px; font-size: 15px; transition: all 0.3s; }
        .sidebar-link:hover, .sidebar-link.active { color: #d4af37; background: rgba(212, 175, 55, 0.15); border: 1px solid rgba(212, 175, 55, 0.3); }
        .main-content { padding: 32px; background-color: #1a0a0a; min-height: calc(100vh - 73px); }
        .welcome-text { color: #d4af37; font-size: 24px; font-weight: 600; margin-bottom: 24px; }
        
        .stat-card { background: linear-gradient(135deg, #1a0f0f 0%, #2d1a1a 100%); border: 1px solid #3a2626; border-radius: 12px; padding: 20px; text-align: center; }
        .stat-card h6 { color: #888; font-size: 12px; margin-bottom: 8px; font-weight: 500; }
        .stat-card .stat-value { font-size: 28px; font-weight: bold; margin: 0; }
        .stat-card .stat-value.gold { color: #d4af37; }
        .stat-card .stat-value.green { color: #28a745; }
        .stat-card .stat-value.teal { color: #5bb9b0; }
        
        .data-panel { background-color: #150808; border: 1px solid #3a2626; border-radius: 12px; padding: 24px; margin-top: 24px; }
        .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .panel-title { color: #ffffff; font-size: 18px; font-weight: 600; margin: 0; }
        
        .table { color: #ffffff; margin-bottom: 0; }
        .table thead th { color: #d4af37; font-weight: 500; font-size: 13px; border-bottom: 1px solid #3a2626; padding: 12px 8px; background: transparent; }
        .table tbody td { color: #ccc; font-size: 14px; border-bottom: 1px solid #2a1a1a; padding: 14px 8px; vertical-align: middle; }
        .movie-poster-thumb { width: 45px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #d4af37; }
        
        .btn-add { background-color: #d4af37; color: #000; border: none; border-radius: 6px; padding: 8px 16px; font-size: 13px; font-weight: 600; text-decoration: none; transition: 0.3s; }
        .btn-add:hover { background-color: #b8962e; color: #000; }
        .btn-edit-action { background-color: #d4af37; color: #000; border: none; border-radius: 4px; padding: 6px 12px; font-size: 12px; text-decoration: none; transition: 0.3s; font-weight: 600; display: inline-block; }
        .btn-edit-action:hover { background-color: #b8962e; color: #000; }
        .btn-delete { background-color: #dc3545; color: #fff; border: none; border-radius: 4px; padding: 6px 12px; font-size: 12px; transition: 0.3s; cursor: pointer; display: inline-block; }
        .btn-delete:hover { background-color: #bd2130; }
        
        .alert-success { background-color: rgba(40, 167, 69, 0.2); border: 1px solid #28a745; color: #28a745; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1a0a0a; }
        ::-webkit-scrollbar-thumb { background: #3a2626; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #4a3636; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <a class="navbar-brand" href="../index.php">Tixly<span>Cinema</span></a>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <a href="index.php" class="sidebar-link active">Dashboard</a>
                <a href="cinema.php" class="sidebar-link">Cinema</a>
                <a href="transaction.php" class="sidebar-link">Transaction</a>
            </div>

            <div class="col-md-10 main-content">
                <h2 class="welcome-text">Dashboard Admin</h2>
                
                <?php if ($message): ?>
                <div class="alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <h6>Total Film</h6>
                            <p class="stat-value gold"><?php echo number_format($stats['total_film'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <h6>Total Transaksi</h6>
                            <p class="stat-value gold"><?php echo number_format($stats['total_transaksi'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <h6>Total Pendapatan</h6>
                            <p class="stat-value green">Rp <?php echo number_format($stats['total_pendapatan'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <h6>Resell Tiket Aktif</h6>
                            <p class="stat-value teal"><?php echo number_format($stats['resell_aktif'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                </div>

                <div class="data-panel">
                    <div class="panel-header">
                        <h5 class="panel-title">Daftar Film</h5>
                        <a href="cinema.php" class="btn-add"><i class="fas fa-plus me-1"></i> Tambah Film Baru</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Poster</th>
                                    <th>Judul Film</th>
                                    <th>Genre</th>
                                    <th>Durasi</th>
                                    <th>Rating</th>
                                    <th style="width: 100px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                if ($movies_query && mysqli_num_rows($movies_query) > 0):
                                    while ($movie = mysqli_fetch_assoc($movies_query)):
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($movie['PosterURL']); ?>" alt="Poster" class="movie-poster-thumb" onerror="this.src='https://placehold.co/150x200?text=No+Poster'">
                                    </td>
                                    <td class="fw-semibold text-white"><?php echo htmlspecialchars($movie['Title']); ?></td>
                                    <td><?php echo htmlspecialchars($movie['Genre']); ?></td>
                                    <td><?php echo $movie['Duration']; ?> Menit</td>
                                    <td>
                                        <span class="text-warning"><i class="fas fa-star me-1"></i><?php echo number_format($movie['Rating'], 1); ?></span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="cinema.php?edit_id=<?php echo $movie['MovieID']; ?>" class="btn-edit-action"><i class="fas fa-edit me-1"></i> Edit</a>
                                            <a href="?delete_id=<?php echo $movie['MovieID']; ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus film ini? Semua jadwal dan tiket terkait juga akan terhapus.');"><i class="fas fa-trash-alt me-1"></i> Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada film di database.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>