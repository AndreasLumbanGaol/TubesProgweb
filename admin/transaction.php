<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include_once __DIR__ . '/../koneksi.php';
$page = 'transaction';

// Fetch stats dynamically from database
$stats = [
    'total_transaksi' => 0,
    'sukses' => 0,
    'pending' => 0,
    'refund' => 0,
    'pendapatan' => 0
];

// Total Transaksi
$q_total = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `transaction`");
if ($q_total) {
    $stats['total_transaksi'] = mysqli_fetch_assoc($q_total)['total'];
}

// Sukses
$q_sukses = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `transaction` WHERE PaymentStatus = 'sukses'");
if ($q_sukses) {
    $stats['sukses'] = mysqli_fetch_assoc($q_sukses)['total'];
}

// Pending
$q_pending = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `transaction` WHERE PaymentStatus = 'pending'");
if ($q_pending) {
    $stats['pending'] = mysqli_fetch_assoc($q_pending)['total'];
}

// Refund
$q_refund = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `transaction` WHERE PaymentStatus = 'refund'");
if ($q_refund) {
    $stats['refund'] = mysqli_fetch_assoc($q_refund)['total'];
}

// Total Pendapatan
$q_rev = mysqli_query($conn, "SELECT SUM(TotalPrice) AS total FROM `transaction` WHERE PaymentStatus = 'sukses'");
if ($q_rev) {
    $stats['pendapatan'] = mysqli_fetch_assoc($q_rev)['total'] ?? 0;
}

// ----------------------------------------------------
// Normal Transaction list query with filters
// ----------------------------------------------------
$search_trx = isset($_GET['search_trx']) ? mysqli_real_escape_string($conn, $_GET['search_trx']) : '';
$status_trx = isset($_GET['status_trx']) ? mysqli_real_escape_string($conn, $_GET['status_trx']) : '';

$where_trx = " WHERE 1=1 ";
if ($search_trx !== '') {
    $where_trx .= " AND (u.Nama LIKE '%$search_trx%' OR m.Title LIKE '%$search_trx%' OR t.TransactionID LIKE '%$search_trx%') ";
}
if ($status_trx !== '') {
    $where_trx .= " AND t.PaymentStatus = '$status_trx' ";
}

$query_trx = "SELECT 
    t.TransactionID, 
    u.Nama AS UserNama, 
    m.Title AS MovieTitle, 
    COUNT(tk.TicketID) AS Qty, 
    t.TotalPrice, 
    t.TransDate, 
    t.PaymentStatus,
    t.PaymentMethod,
    GROUP_CONCAT(DISTINCT th.Name SEPARATOR ', ') AS TheaterName,
    GROUP_CONCAT(tk.SeatInfo SEPARATOR ', ') AS Seats
FROM `transaction` t
LEFT JOIN `user` u ON t.UserID = u.UserID
LEFT JOIN ticket tk ON t.TransactionID = tk.TransactionID
LEFT JOIN showtime s ON tk.ShowtimeID = s.ShowtimeID
LEFT JOIN movie m ON s.MovieID = m.MovieID
LEFT JOIN studio st ON tk.StudioID = st.StudioID
LEFT JOIN theater th ON st.TheaterID = th.TheaterID
$where_trx
GROUP BY t.TransactionID
ORDER BY t.TransactionID DESC";

$res_trx = mysqli_query($conn, $query_trx);
$transactions = [];
if ($res_trx) {
    while ($row = mysqli_fetch_assoc($res_trx)) {
        $transactions[] = [
            'id' => 'TRX' . str_pad($row['TransactionID'], 3, '0', STR_PAD_LEFT),
            'user' => $row['UserNama'] ? $row['UserNama'] : 'Guest',
            'film' => $row['MovieTitle'] ? $row['MovieTitle'] : 'Tiket Resell / Dummy Film',
            'qty' => $row['Qty'],
            'total' => 'Rp ' . number_format($row['TotalPrice'], 0, ',', '.'),
            'date' => date('Y-m-d', strtotime($row['TransDate'])),
            'status' => $row['PaymentStatus'],
            'method' => $row['PaymentMethod'] ? $row['PaymentMethod'] : 'Tixly Wallet',
            'theater' => $row['TheaterName'],
            'seats' => $row['Seats']
        ];
    }
}

// ----------------------------------------------------
// Resell Transaction list query with filters
// ----------------------------------------------------
$search_rsl = isset($_GET['search_rsl']) ? mysqli_real_escape_string($conn, $_GET['search_rsl']) : '';
$status_rsl = isset($_GET['status_rsl']) ? mysqli_real_escape_string($conn, $_GET['status_rsl']) : '';

$where_rsl = " WHERE (t.IsResale = 1 OR (t.Status = 'terjual' AND t.SellerID IS NOT NULL)) ";
if ($search_rsl !== '') {
    $where_rsl .= " AND (u.Nama LIKE '%$search_rsl%' OR m.Title LIKE '%$search_rsl%' OR t.TicketID LIKE '%$search_rsl%') ";
}
if ($status_rsl !== '') {
    if ($status_rsl === 'aktif') {
        $where_rsl .= " AND t.Status = 'aktif' ";
    } else {
        $where_rsl .= " AND t.Status = 'terjual' "; // fallback
    }
}

$query_rsl = "SELECT 
    t.TicketID, 
    u.Nama AS SellerNama, 
    m.Title AS MovieTitle, 
    t.SeatInfo, 
    t.SecondPrice, 
    s.PlayDate, 
    t.Status,
    th.Name AS TheaterName
FROM ticket t
LEFT JOIN transaction tr ON t.TransactionID = tr.TransactionID
LEFT JOIN user u ON tr.UserID = u.UserID
LEFT JOIN showtime s ON t.ShowtimeID = s.ShowtimeID
LEFT JOIN movie m ON s.MovieID = m.MovieID
LEFT JOIN studio st ON t.StudioID = st.StudioID
LEFT JOIN theater th ON st.TheaterID = th.TheaterID
$where_rsl
ORDER BY t.TicketID DESC";

$res_rsl = mysqli_query($conn, $query_rsl);
$resells = [];
if ($res_rsl) {
    while ($row = mysqli_fetch_assoc($res_rsl)) {
        $resells[] = [
            'id' => 'RSL' . str_pad($row['TicketID'], 3, '0', STR_PAD_LEFT),
            'seller' => $row['SellerNama'] ? $row['SellerNama'] : 'Unknown',
            'film' => $row['MovieTitle'] ? $row['MovieTitle'] : 'Unknown Film',
            'seat' => $row['SeatInfo'],
            'price' => 'Rp ' . number_format($row['SecondPrice'], 0, ',', '.'),
            'date' => date('Y-m-d', strtotime($row['PlayDate'])),
            'status' => $row['Status'],
            'theater' => $row['TheaterName']
        ];
    }
}
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

        .table { 
            --bs-table-bg: transparent; 
            --bs-table-color: #ffffff; 
            --bs-table-border-color: #3a2626; 
            --bs-table-striped-bg: rgba(255, 255, 255, 0.02);
            --bs-table-hover-bg: rgba(212, 175, 55, 0.05);
            color: #ffffff; 
            margin-bottom: 0; 
        }
        .table th, .table td { background-color: transparent !important; }
        .table thead th { color: #d4af37 !important; font-weight: 500; font-size: 13px; border-bottom: 1px solid #3a2626 !important; padding: 12px 8px; }
        .table tbody td { color: #ccc !important; font-size: 14px; border-bottom: 1px solid #2a1a1a !important; padding: 14px 8px; vertical-align: middle; }
        
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

    <nav class="navbar navbar-dark d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="index.php">Tixly<span>Cinema</span> (Admin Panel)</a>
        <div>
            <span class="text-white-50 me-3">Hi, Admin!</span>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm" style="border-radius: 20px; font-weight: bold; padding: 5px 15px;">Logout</a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <a href="index.php" class="sidebar-link">Dashboard</a>
                <a href="cinema.php" class="sidebar-link">Cinema</a>
                <a href="transaction.php" class="sidebar-link active">Transaction</a>
            </div>

            <div class="col-md-10 main-content">
                <h2 class="welcome-text">Kelola Transaksi</h2>

                <div class="row mb-4">
                    <div class="col mb-3"><div class="stat-card"><h6>Total Transaksi</h6><p class="stat-value gold"><?php echo number_format($stats['total_transaksi'], 0, ',', '.'); ?></p></div></div>
                    <div class="col mb-3"><div class="stat-card"><h6>Sukses</h6><p class="stat-value green"><?php echo number_format($stats['sukses'], 0, ',', '.'); ?></p></div></div>
                    <div class="col mb-3"><div class="stat-card"><h6>Pending</h6><p class="stat-value yellow"><?php echo number_format($stats['pending'], 0, ',', '.'); ?></p></div></div>
                    <div class="col mb-3"><div class="stat-card"><h6>Refund</h6><p class="stat-value red"><?php echo number_format($stats['refund'], 0, ',', '.'); ?></p></div></div>
                    <div class="col mb-3"><div class="stat-card"><h6>Pendapatan</h6><p class="stat-value teal">Rp <?php echo number_format($stats['pendapatan'], 0, ',', '.'); ?></p></div></div>
                </div>

                <div class="data-panel">
                    <div class="panel-header">
                        <h5 class="panel-title">Transaksi Tiket</h5>
                        
                        <form method="GET" action="transaction.php" class="filter-group">
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
                                <?php if (!empty($transactions)): foreach ($transactions as $trx): ?>
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
                                    <td>
                                        <button class="btn-detail btn-trx-detail" 
                                                data-id="<?php echo htmlspecialchars($trx['id']); ?>"
                                                data-user="<?php echo htmlspecialchars($trx['user']); ?>"
                                                data-film="<?php echo htmlspecialchars($trx['film']); ?>"
                                                data-theater="<?php echo htmlspecialchars($trx['theater']); ?>"
                                                data-seats="<?php echo htmlspecialchars($trx['seats']); ?>"
                                                data-total="<?php echo htmlspecialchars($trx['total']); ?>"
                                                data-status="<?php echo htmlspecialchars(ucfirst($trx['status'])); ?>"
                                                data-method="<?php echo htmlspecialchars($trx['method']); ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailModal">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada transaksi tiket.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="data-panel">
                    <div class="panel-header">
                        <h5 class="panel-title">Transaksi Resell Tiket</h5>
                        <form method="GET" action="transaction.php" class="filter-group">
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
                                <?php if (!empty($resells)): foreach ($resells as $resell): ?>
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
                                    <td>
                                        <button class="btn-detail btn-rsl-detail" 
                                                data-id="<?php echo htmlspecialchars($resell['id']); ?>"
                                                data-seller="<?php echo htmlspecialchars($resell['seller']); ?>"
                                                data-film="<?php echo htmlspecialchars($resell['film']); ?>"
                                                data-theater="<?php echo htmlspecialchars($resell['theater']); ?>"
                                                data-seat="<?php echo htmlspecialchars($resell['seat']); ?>"
                                                data-price="<?php echo htmlspecialchars($resell['price']); ?>"
                                                data-status="<?php echo htmlspecialchars(ucfirst($resell['status'])); ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailModal">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada resell tiket aktif.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Dynamic Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-white">
                    <div class="detail-row"><span class="detail-label" id="det-label-id">ID Transaksi</span><span class="detail-value" id="det-id">-</span></div>
                    <div class="detail-row"><span class="detail-label" id="det-label-user">Nama User</span><span class="detail-value" id="det-user">-</span></div>
                    <div class="detail-row"><span class="detail-label">Film</span><span class="detail-value" id="det-film">-</span></div>
                    <div class="detail-row"><span class="detail-label">Bioskop</span><span class="detail-value" id="det-theater">-</span></div>
                    <div class="detail-row"><span class="detail-label" id="det-label-seats">Kursi</span><span class="detail-value" id="det-seats">-</span></div>
                    <div class="detail-row" id="det-row-method"><span class="detail-label">Metode Pembayaran</span><span class="detail-value" id="det-method">-</span></div>
                    <div class="detail-row"><span class="detail-label" id="det-label-total">Total Pembayaran</span><span class="detail-value" id="det-total" style="color: #d4af37;">-</span></div>
                    <div class="detail-row"><span class="detail-label">Status</span><span id="det-status" class="badge bg-success">Sukses</span></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const detailModal = document.getElementById('detailModal');
        detailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (button.classList.contains('btn-trx-detail')) {
                const id = button.getAttribute('data-id');
                const user = button.getAttribute('data-user');
                const film = button.getAttribute('data-film');
                const theater = button.getAttribute('data-theater');
                const seats = button.getAttribute('data-seats');
                const total = button.getAttribute('data-total');
                const status = button.getAttribute('data-status');
                const method = button.getAttribute('data-method');
 
                detailModal.querySelector('.modal-title').textContent = 'Detail Transaksi ' + id;
                detailModal.querySelector('#det-row-method').style.display = 'flex';
                detailModal.querySelector('#det-method').textContent = method;
                detailModal.querySelector('#det-label-id').textContent = 'ID Transaksi';
                detailModal.querySelector('#det-id').textContent = id;
                
                detailModal.querySelector('#det-label-user').textContent = 'Nama User';
                detailModal.querySelector('#det-user').textContent = user;
                
                detailModal.querySelector('#det-film').textContent = film;
                detailModal.querySelector('#det-theater').textContent = theater ? theater : '-';
                
                detailModal.querySelector('#det-label-seats').textContent = 'Kursi';
                detailModal.querySelector('#det-seats').textContent = seats ? seats : '-';
                
                detailModal.querySelector('#det-label-total').textContent = 'Total Pembayaran';
                detailModal.querySelector('#det-total').textContent = total;
                
                const statusEl = detailModal.querySelector('#det-status');
                statusEl.textContent = status;
                statusEl.className = ''; 
                if (status.toLowerCase() === 'sukses') {
                    statusEl.className = 'badge bg-success';
                } else if (status.toLowerCase() === 'pending') {
                    statusEl.className = 'badge bg-warning text-dark';
                } else if (status.toLowerCase() === 'refund') {
                    statusEl.className = 'badge bg-secondary';
                } else {
                    statusEl.className = 'badge bg-danger';
                }
            } else if (button.classList.contains('btn-rsl-detail')) {
                const id = button.getAttribute('data-id');
                const seller = button.getAttribute('data-seller');
                const film = button.getAttribute('data-film');
                const theater = button.getAttribute('data-theater');
                const seat = button.getAttribute('data-seat');
                const price = button.getAttribute('data-price');
                const status = button.getAttribute('data-status');
 
                detailModal.querySelector('.modal-title').textContent = 'Detail Resell ' + id;
                detailModal.querySelector('#det-row-method').style.display = 'none';
                
                detailModal.querySelector('#det-label-id').textContent = 'ID Resell';
                detailModal.querySelector('#det-id').textContent = id;
                
                detailModal.querySelector('#det-label-user').textContent = 'Penjual';
                detailModal.querySelector('#det-user').textContent = seller;
                
                detailModal.querySelector('#det-film').textContent = film;
                detailModal.querySelector('#det-theater').textContent = theater ? theater : '-';
                
                detailModal.querySelector('#det-label-seats').textContent = 'Kursi';
                detailModal.querySelector('#det-seats').textContent = seat;
                
                detailModal.querySelector('#det-label-total').textContent = 'Harga Jual';
                detailModal.querySelector('#det-total').textContent = price;
                
                const statusEl = detailModal.querySelector('#det-status');
                statusEl.textContent = status;
                statusEl.className = ''; 
                if (status.toLowerCase() === 'aktif') {
                    statusEl.className = 'badge bg-info text-dark';
                } else if (status.toLowerCase() === 'terjual') {
                    statusEl.className = 'badge bg-success';
                } else {
                    statusEl.className = 'badge bg-secondary';
                }
            }
        });
    </script>
</body>
</html>