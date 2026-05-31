<?php
$page = 'dashboard';

// Sample data
$stats = [
    'transaksi_hari_ini' => 1284,
    'pendapatan_hari_ini' => 89.4,
    'tiket_resell_aktif' => 847
];

$films = [
    ['judul' => 'AVATAR: The Way of Water', 'genre' => 'Aksi, Drama', 'rating' => '8,4', 'tiket' => '45,280', 'pendapatan' => 'Rp 3,2 M', 'preorder' => true],
    ['judul' => 'AVATAR: The Way of Water', 'genre' => 'Aksi, Drama', 'rating' => '8,4', 'tiket' => '45,280', 'pendapatan' => 'Rp 3,2 M', 'preorder' => false],
    ['judul' => 'AVATAR: The Way of Water', 'genre' => 'Aksi, Drama', 'rating' => '8,4', 'tiket' => '45,280', 'pendapatan' => 'Rp 3,2 M', 'preorder' => true],
    ['judul' => 'AVATAR: The Way of Water', 'genre' => 'Aksi, Drama', 'rating' => '8,4', 'tiket' => '45,280', 'pendapatan' => 'Rp 3,2 M', 'preorder' => true],
];

$jadwals = [
    ['showtime_id' => 'ST01', 'movie_id' => 'MOV01', 'studio_id' => 'STD-A', 'harga' => '65000', 'tanggal' => '2026-05-10', 'waktu' => '13:00'],
    ['showtime_id' => 'ST02', 'movie_id' => 'MOV01', 'studio_id' => 'STD-A', 'harga' => '65000', 'tanggal' => '2026-05-10', 'waktu' => '16:30'],
    ['showtime_id' => 'ST03', 'movie_id' => 'MOV03', 'studio_id' => 'STD-B', 'harga' => '170000', 'tanggal' => '2026-06-15', 'waktu' => '19:00'],
];

$theaters = [
    ['theater_id' => 'THT01', 'nama' => 'Tixly Central', 'lokasi' => 'Bandung'],
];

// PERBAIKAN: Struktur atribut data disesuaikan dengan rancangan tabel ERD relasional
$studios = [
    ['studio_id' => 'STD01', 'theater_id' => 'THT01', 'nama' => 'Studio 1', 'type' => 'Reguler', 'capacity' => 100],
    ['studio_id' => 'STD02', 'theater_id' => 'THT01', 'nama' => 'Studio 2', 'type' => 'Gold Class', 'capacity' => 40],
];
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
        body { 
            background-color: #0d0606; 
            color: #ffffff;
            font-family: 'Segoe UI', sans-serif; 
            min-height: 100vh;
        }

        .navbar { 
            border-bottom: 1px solid #3a2626;
            padding: 16px 24px;
            background-color: #0d0606;
        }
        .navbar-brand { 
            color: #d4af37 !important; 
            font-family: Georgia, serif; 
            font-size: 24px; 
            font-weight: bold;
        }
        .navbar-brand span {
            font-style: italic; 
            color: #b8962e; 
            font-weight: normal;
        }

        .sidebar {
            background-color: #0d0606;
            min-height: calc(100vh - 73px);
            padding: 24px 16px;
            border-right: 1px solid #3a2626;
        }
        .sidebar-link {
            display: block;
            color: #888;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }
        .sidebar-link:hover {
            color: #d4af37;
            background: rgba(212, 175, 55, 0.1);
        }
        .sidebar-link.active {
            color: #d4af37;
            background: rgba(212, 175, 55, 0.15);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .main-content {
            padding: 32px;
            background-color: #1a0a0a;
            min-height: calc(100vh - 73px);
        }
        .welcome-text {
            color: #d4af37;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .stat-card {
            background: linear-gradient(135deg, #1a0f0f 0%, #2d1a1a 100%);
            border: 1px solid #3a2626;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            height: 100%;
        }
        .stat-card h6 {
            color: #888;
            font-size: 14px;
            margin-bottom: 12px;
            font-weight: 500;
        }
        .stat-card .stat-value {
            font-size: 42px;
            font-weight: bold;
            margin: 0;
        }
        .stat-card .stat-value.gold { color: #d4af37; }
        .stat-card .stat-value.teal { color: #5bb9b0; }
        .stat-card .stat-value.white { color: #ffffff; }

        .data-panel {
            background-color: #150808;
            border: 1px solid #3a2626;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .panel-title {
            color: #ffffff;
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }
        .btn-add {
            background-color: #2d1f1f;
            color: #d4af37;
            border: 1px solid #d4af37;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-add:hover {
            background-color: #d4af37;
            color: #000;
        }

        .table {
            color: #ffffff;
            margin-bottom: 0;
        }
        .table thead th {
            color: #d4af37;
            font-weight: 500;
            font-size: 13px;
            border-bottom: 1px solid #3a2626;
            padding: 12px 8px;
            background: transparent;
        }
        .table tbody td {
            color: #ccc;
            font-size: 14px;
            border-bottom: 1px solid #2a1a1a;
            padding: 14px 8px;
            vertical-align: middle;
        }
        .table tbody tr:hover {
            background-color: rgba(212, 175, 55, 0.05);
        }

        .badge-true { background-color: #28a745; color: #fff; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 500; }
        .badge-false { background-color: #dc3545; color: #fff; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 500; }

        .btn-edit {
            background-color: #d4af37;
            color: #000;
            border: none;
            border-radius: 4px;
            padding: 4px 16px;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-edit:hover { background-color: #b8962e; color: #000; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark">
        <a class="navbar-brand" href="index.php">Tixly<span>Cinema</span></a>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <a href="admin-index.php" class="sidebar-link active">Dashboard</a>
                <a href="admin-cinema.php" class="sidebar-link">Cinema</a>
                <a href="admin-transaction.php" class="sidebar-link">Transaction</a>
            </div>

            <div class="col-md-10 main-content">
                <h2 class="welcome-text">Selamat Datang Admin</h2>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="stat-card">
                            <h6>Transaksi Hari Ini</h6>
                            <p class="stat-value gold"><?php echo number_format($stats['transaksi_hari_ini'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card">
                            <h6>Pendapatan Hari Ini</h6>
                            <p class="stat-value teal"><?php echo $stats['pendapatan_hari_ini']; ?> Jt</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card">
                            <h6>Tiket Resell Aktif</h6>
                            <p class="stat-value white"><?php echo number_format($stats['tiket_resell_aktif'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                </div>

                <div class="data-panel">
                    <div class="panel-header">
                        <h5 class="panel-title">Film Sedang Tayang</h5>
                        <a href="admin-cinema.php" class="btn-add">+ Tambah Film</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Judul</th><th>Genre</th><th>Rating</th><th>Tiket Terjual</th><th>Pendapatan</th><th>Preorder</th><th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($films as $film): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($film['judul']); ?></td>
                                    <td><?php echo htmlspecialchars($film['genre']); ?></td>
                                    <td><?php echo $film['rating']; ?></td>
                                    <td><?php echo $film['tiket']; ?></td>
                                    <td><?php echo $film['pendapatan']; ?></td>
                                    <td><span class="<?php echo $film['preorder'] ? 'badge-true' : 'badge-false'; ?>"><?php echo $film['preorder'] ? 'true' : 'false'; ?></span></td>
                                    <td><a href="#" class="btn-edit">Edit</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="data-panel">
                    <div class="panel-header">
                        <h5 class="panel-title">Jadwal Tayang</h5>
                        <a href="#" class="btn-add">+ Tambah Jadwal</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ShowtimeID</th><th>MovieID</th><th>StudioID</th><th>Harga</th><th>Tanggal Mulai</th><th>Waktu Mulai</th><th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jadwals as $jadwal): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($jadwal['showtime_id']); ?></td>
                                    <td><?php echo htmlspecialchars($jadwal['movie_id']); ?></td>
                                    <td><?php echo htmlspecialchars($jadwal['studio_id']); ?></td>
                                    <td><?php echo number_format($jadwal['harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo $jadwal['tanggal']; ?></td>
                                    <td><?php echo $jadwal['waktu']; ?></td>
                                    <td><a href="#" class="btn-edit">Edit</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="data-panel">
                    <div class="panel-header">
                        <h5 class="panel-title">Theater</h5>
                        <a href="#" class="btn-add">+ Tambah Theater</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>TheaterID</th><th>Nama</th><th>Lokasi</th><th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($theaters as $theater): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($theater['theater_id']); ?></td>
                                    <td><?php echo htmlspecialchars($theater['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($theater['lokasi']); ?></td>
                                    <td><a href="#" class="btn-edit">Edit</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="data-panel">
                    <div class="panel-header">
                        <h5 class="panel-title">Studio</h5>
                        <a href="#" class="btn-add">+ Tambah Studio</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>StudioID</th><th>TheaterID</th><th>Nama Studio</th><th>Tipe</th><th>Kapasitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($studios as $studio): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($studio['studio_id']); ?></td>
                                    <td><?php echo htmlspecialchars($studio['theater_id']); ?></td>
                                    <td><?php echo htmlspecialchars($studio['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($studio['type']); ?></td>
                                    <td><?php echo $studio['capacity']; ?> Kursi</td>
                                </tr>
                                <?php endforeach; ?>
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