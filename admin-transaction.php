<?php
$page = 'transaction';

$stats = [
    'total_transaksi' => 12847,
    'sukses' => 11234,
    'pending' => 892,
    'refund' => 721,
    'pendapatan' => 1.2
];

$transactions = [
    ['id' => 'TRX001', 'user' => 'John Doe', 'film' => 'Avatar: The Way of Water', 'qty' => 2, 'total' => 'Rp 130.000', 'date' => '2026-05-28', 'status' => 'sukses'],
    ['id' => 'TRX002', 'user' => 'Jane Smith', 'film' => 'Wonka', 'qty' => 4, 'total' => 'Rp 200.000', 'date' => '2026-05-28', 'status' => 'pending'],
    ['id' => 'TRX003', 'user' => 'Bob Wilson', 'film' => 'Dune: Part Two', 'qty' => 1, 'total' => 'Rp 85.000', 'date' => '2026-05-27', 'status' => 'sukses'],
    ['id' => 'TRX004', 'user' => 'Alice Brown', 'film' => 'Oppenheimer', 'qty' => 3, 'total' => 'Rp 255.000', 'date' => '2026-05-27', 'status' => 'refund'],
    ['id' => 'TRX005', 'user' => 'Charlie Lee', 'film' => 'Avatar: The Way of Water', 'qty' => 2, 'total' => 'Rp 130.000', 'date' => '2026-05-26', 'status' => 'sukses'],
];

$resells = [
    ['id' => 'RSL001', 'seller' => 'John Doe', 'film' => 'Avatar: The Way of Water', 'seat' => 'A5, A6', 'price' => 'Rp 120.000', 'date' => '2026-05-30', 'status' => 'aktif'],
    ['id' => 'RSL002', 'seller' => 'Jane Smith', 'film' => 'Wonka', 'seat' => 'C3', 'price' => 'Rp 55.000', 'date' => '2026-05-29', 'status' => 'terjual'],
    ['id' => 'RSL003', 'seller' => 'Bob Wilson', 'film' => 'Dune: Part Two', 'seat' => 'D7, D8, D9', 'price' => 'Rp 240.000', 'date' => '2026-05-31', 'status' => 'aktif'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Transaction</title>
    
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
        .navbar-brand span { font-style: italic; color: #b8962e; font-weight: normal; }

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
        .sidebar-link:hover { color: #d4af37; background: rgba(212, 175, 55, 0.1); }
        .sidebar-link.active { color: #d4af37; background: rgba(212, 175, 55, 0.15); border: 1px solid rgba(212, 175, 55, 0.3); }

        .main-content { padding: 32px; background-color: #1a0a0a; min-height: calc(100vh - 73px); }
        .welcome-text { color: #d4af37; font-size: 24px; font-weight: 600; margin-bottom: 24px; }

        .stat-card { background: linear-gradient(135deg, #1a0f0f 0%, #2d1a1a 100%); border: 1px solid #3a2626; border-radius: 12px; padding: 20px; text-align: center; }
        .stat-card h6 { color: #888; font-size: 12px; margin-bottom: 8px; font-weight: 500; }
        .stat-card .stat-value { font-size: 28px; font-weight: bold; margin: 0; }
        .stat-card .stat-value.gold { color: #d4af37; }
        .stat-card .stat-value.green { color: #28a745; }
        .stat-card .stat-value.yellow { color: #ffc107; }
        .stat-card .stat-value.red { color: #dc3545; }
        .stat-card .stat-value.teal { color: #5bb9b0; }

        .data-panel { background-color: #150808; border: 1px solid #3a2626; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
        .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px; }
        .panel-title { color: #ffffff; font-size: 18px; font-weight: 600; margin: 0; }

        .filter-group { display: flex; gap: 12px; flex-wrap: wrap; width: 100%; justify-content: flex-end; }
        .search-input { background-color: #2d1a1a; border: 1px solid #5a3a3a; color: #ffffff; border-radius: 6px; padding: 8px 16px; font-size: 13px; width: 240px; }
        .search-input:focus { background-color: #3a2626; border-color: #d4af37; color: #ffffff; box-shadow: none; outline: none; }
        .filter-select { background-color: #2d1a1a; border: 1px solid #5a3a3a; color: #ffffff; border-radius: 6px; padding: 8px 16px; font-size: 13px; cursor: pointer; }
        .filter-select:focus { border-color: #d4af37; outline: none; }
        .filter-select option { background-color: #2d1a1a; color: #ffffff; }

        .table { color: #ffffff; margin-bottom: 0; }
        .table thead th { color: #d4af37; font-weight: 500; font-size: 13px; border-bottom: 1px solid #3a2626; padding: 12px 8px; background: transparent; }
        .table tbody td { color: #ccc; font-size: 14px; border-bottom: 1px solid #2a1a1a; padding: 14px 8px; vertical-align: middle; }
        
        .badge-success { background-color: #28a745; color: #fff; padding: 4px 12px; border-radius: 12px; font-size: 11px; }
        .badge-pending { background-color: #ffc107; color: #000; padding: 4px 12px; border-radius: 12px; font-size: 11px; }
        .badge-failed { background-color: #dc3545; color: #fff; padding: 4px 12px; border-radius: 12px; font-size: 11px; }
        .badge-refund { background-color: #6c757d; color: #fff; padding: 4px 12px; border-radius: 12px; font-size: 11px; }
        .badge-active { background-color: #17a2b8; color: #fff; padding: 4px 12px; border-radius: 12px; font-size: 11px; }
        .badge-sold { background-color: #28a745; color: #fff; padding: 4px 12px; border-radius: 12px; font-size: 11px; }

        .btn-detail { background-color: #d4af37; color: #000; border: none; border-radius: 4px; padding: 4px 12px; font-size: 12px; font-weight: 500; cursor: pointer; }
        .btn-detail:hover { background-color: #b8962e; }

        .modal-content { background-color: #150808; border: 1px solid #3a2626; color: #ffffff; }
        .modal-header { border-bottom: 1px solid #3a2626; }
        .modal-header .btn-close { filter: invert(1); }
        .modal-title { color: #d4af37; }
        .detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #2a1a1a; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark">
        <a class="navbar-brand" href="index.php">Tixly<span>Cinema</span></a>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <a href="admin-index.php" class="sidebar-link">Dashboard</a>
                <a href="admin-cinema.php" class="sidebar-link">Cinema</a>
                <a href="admin-transaction.php" class="sidebar-link active">Transaction</a>
            </div>

            <div class="col-md-10 main-content">
                <h2 class="welcome-text">Selamat Datang Admin</h2>

                <div class="row mb-4">
                    <div class="col mb-3"><div class="stat-card"><h6>Total Transaksi</h6><p class="stat-value gold"><?php echo number_format($stats['total_transaksi'], 0, ',', '.'); ?></p></div></div>
                    <div class="col mb-3"><div class="stat-card"><h6>Sukses</h6><p class="stat-value green"><?php echo number_format($stats['sukses'], 0, ',', '.'); ?></p></div></div>
                    <div class="col mb-3"><div class="stat-card"><h6>Pending</h6><p class="stat-value yellow"><?php echo number_format($stats['pending'], 0, ',', '.'); ?></p></div></div>
                    <div class="col mb-3"><div class="stat-card"><h6>Refund</h6><p class="stat-value red"><?php echo number_format($stats['refund'], 0, ',', '.'); ?></p></div></div>
                    <div class="col mb-3"><div class="stat-card"><h6>Pendapatan</h6><p class="stat-value teal"><?php echo $stats['pendapatan']; ?> M</p></div></div>
                </div>

                <div class="data-panel">
                    <div class="panel-header">
                        <h5 class="panel-title">Transaksi Tiket</h5>
                        
                        <form method="GET" action="admin-transaction.php" class="filter-group">
                            <input type="text" name="search_trx" class="search-input" placeholder="Cari user / film..." value="<?php echo isset($_GET['search_trx']) ? htmlspecialchars($_GET['search_trx']) : ''; ?>">
                            <select name="status_trx" class="filter-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="sukses" <?php echo (isset($_GET['status_trx']) && $_GET['status_trx'] == 'sukses') ? 'selected' : ''; ?>>Sukses</option>
                                <option value="pending" <?php echo (isset($_GET['status_trx']) && $_GET['status_trx'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="refund" <?php echo (isset($_GET['status_trx']) && $_GET['status_trx'] == 'refund') ? 'selected' : ''; ?>>Refund</option>
                            </select>
                            <noscript><button type="submit" class="btn-detail">Cari</button></noscript>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID Transaksi</th><th>Nama User</th><th>Film</th><th>Jumlah Tiket</th><th>Total</th><th>Tanggal</th><th>Status</th><th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $trx): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($trx['id']); ?></td>
                                    <td><?php echo htmlspecialchars($trx['user']); ?></td>
                                    <td><?php echo htmlspecialchars($trx['film']); ?></td>
                                    <td><?php echo $trx['qty']; ?></td>
                                    <td><?php echo $trx['total']; ?></td>
                                    <td><?php echo $trx['date']; ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = 'badge-success';
                                        if ($trx['status'] == 'pending') $badgeClass = 'badge-pending';
                                        elseif ($trx['status'] == 'gagal') $badgeClass = 'badge-failed';
                                        elseif ($trx['status'] == 'refund') $badgeClass = 'badge-refund';
                                        ?>
                                        <span class="<?php echo $badgeClass; ?>"><?php echo ucfirst($trx['status']); ?></span>
                                    </td>
                                    <td><button class="btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal">Detail</button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="data-panel">
                    <div class="panel-header">
                        <h5 class="panel-title">Transaksi Resell Tiket</h5>
                        <form method="GET" action="admin-transaction.php" class="filter-group">
                            <input type="text" name="search_rsl" class="search-input" placeholder="Cari penjual / film..." value="<?php echo isset($_GET['search_rsl']) ? htmlspecialchars($_GET['search_rsl']) : ''; ?>">
                            <select name="status_rsl" class="filter-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="aktif" <?php echo (isset($_GET['status_rsl']) && $_GET['status_rsl'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                <option value="terjual" <?php echo (isset($_GET['status_rsl']) && $_GET['status_rsl'] == 'terjual') ? 'selected' : ''; ?>>Terjual</option>
                            </select>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID Resell</th><th>Penjual</th><th>Film</th><th>Kursi</th><th>Harga Jual</th><th>Tanggal Tayang</th><th>Status</th><th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resells as $resell): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($resell['id']); ?></td>
                                    <td><?php echo htmlspecialchars($resell['seller']); ?></td>
                                    <td><?php echo htmlspecialchars($resell['film']); ?></td>
                                    <td><?php echo htmlspecialchars($resell['seat']); ?></td>
                                    <td><?php echo $resell['price']; ?></td>
                                    <td><?php echo $resell['date']; ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = 'badge-active';
                                        if ($resell['status'] == 'terjual') $badgeClass = 'badge-sold';
                                        ?>
                                        <span class="<?php echo $badgeClass; ?>"><?php echo ucfirst($resell['status']); ?></span>
                                    </td>
                                    <td><button class="btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal">Detail</button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-row"><span class="detail-label">ID Transaksi</span><span class="detail-value">TRX001</span></div>
                    <div class="detail-row"><span class="detail-label">Nama User</span><span class="detail-value">John Doe</span></div>
                    <div class="detail-row"><span class="detail-label">Film</span><span class="detail-value">Avatar: The Way of Water</span></div>
                    <div class="detail-row"><span class="detail-label">Bioskop</span><span class="detail-value">CGV Paskal 23</span></div>
                    <div class="detail-row"><span class="detail-label">Kursi</span><span class="detail-value">A5, A6</span></div>
                    <div class="detail-row"><span class="detail-label">Total Pembayaran</span><span class="detail-value" style="color: #d4af37;">Rp 130.000</span></div>
                    <div class="detail-row"><span class="detail-label">Status</span><span class="badge-success">Sukses</span></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>