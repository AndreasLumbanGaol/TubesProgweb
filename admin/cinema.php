<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include_once __DIR__ . '/../koneksi.php';
$page = 'cinema';

$genres = ['Aksi', 'Drama', 'Komedi', 'Horror', 'Romantis', 'Sci-Fi', 'Animasi', 'Thriller'];
$jadwalOptions = ['10:00', '12:00', '15:00'];

// Fetch all available theaters (Only allow Tixly Central, CGV Paskal 23, and XXI Botanica Mall)
$theaters = [];
$theaters_query = mysqli_query($conn, "SELECT * FROM theater WHERE TheaterID IN (1, 2, 3) ORDER BY Name ASC");
if ($theaters_query) {
    while ($row = mysqli_fetch_assoc($theaters_query)) {
        $theaters[] = $row;
    }
}

// Fetch theater to studio mappings
$theater_studios = [];
$studio_types_query = mysqli_query($conn, "SELECT DISTINCT TheaterID, Type FROM studio");
if ($studio_types_query) {
    while ($row = mysqli_fetch_assoc($studio_types_query)) {
        $theater_studios[$row['TheaterID']][] = $row['Type'];
    }
}

// Handle GET Edit Mode
$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$movie_data = null;
$existing_schedules = [];

if ($edit_id > 0) {
    $movie_query = mysqli_query($conn, "SELECT * FROM movie WHERE MovieID = $edit_id");
    if ($movie_query && mysqli_num_rows($movie_query) > 0) {
        $movie_data = mysqli_fetch_assoc($movie_query);
        
        // Fetch showtimes for this movie
        $showtimes_query = mysqli_query($conn, "SELECT st.StartTime, st.PlayDate, st.StudioID, s.TheaterID, s.Type AS StudioType 
                                               FROM showtime st 
                                               JOIN studio s ON st.StudioID = s.StudioID
                                               WHERE st.MovieID = $edit_id");
        if ($showtimes_query) {
            $grouped = [];
            while ($row = mysqli_fetch_assoc($showtimes_query)) {
                $key = $row['TheaterID'] . '_' . $row['PlayDate'] . '_' . $row['StudioType'];
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'theater_id' => $row['TheaterID'],
                        'play_date' => $row['PlayDate'],
                        'studio_type' => $row['StudioType'],
                        'times' => []
                    ];
                }
                
                $time_option = '';
                $st_hour = date('H:i:s', strtotime($row['StartTime']));
                if ($st_hour === '10:00:00') $time_option = '10:00';
                elseif ($st_hour === '12:00:00') $time_option = '12:00';
                elseif ($st_hour === '15:00:00') $time_option = '15:00';
                // Fallbacks for older/other schedules
                elseif ($st_hour === '07:00:00') $time_option = '10:00';
                elseif ($st_hour === '13:00:00') $time_option = '12:00';
                elseif ($st_hour === '16:00:00') $time_option = '15:00';
                elseif ($st_hour === '19:00:00') $time_option = '15:00';
                
                if ($time_option) {
                    $grouped[$key]['times'][] = $time_option;
                }
            }
            $existing_schedules = array_values($grouped);
        }
    }
}

// Handle Form Submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $post_edit_id = intval($_POST['edit_id'] ?? 0);
        $judul = mysqli_real_escape_string($conn, $_POST['judul'] ?? '');
        $genre = mysqli_real_escape_string($conn, $_POST['genre'] ?? '');
        $posterUrl = mysqli_real_escape_string($conn, $_POST['poster_url'] ?? '');
        $duration = intval($_POST['duration'] ?? 120);
        $rating = floatval($_POST['rating'] ?? 0.0);
        $trailerUrl = mysqli_real_escape_string($conn, $_POST['trailer_url'] ?? '');
        
        $schedules = $_POST['schedules'] ?? [];

        if (!empty($judul) && !empty($genre) && !empty($posterUrl)) {
            if ($post_edit_id > 0) {
                // Update Movie
                $updateMovie = mysqli_query($conn, "UPDATE movie SET Title = '$judul', Duration = $duration, Genre = '$genre', Rating = $rating, PosterURL = '$posterUrl', TrailerURL = '$trailerUrl' WHERE MovieID = $post_edit_id");
                if ($updateMovie) {
                    $movie_id = $post_edit_id;
                    
                    // Clear old showtimes
                    mysqli_query($conn, "DELETE FROM showtime WHERE MovieID = $movie_id");
                    
                    $showtimes_inserted = 0;
                    // Insert new schedules
                    foreach ($schedules as $sched) {
                        $selected_theater_id = intval($sched['theater_id'] ?? 0);
                        $selected_studio_type = mysqli_real_escape_string($conn, $sched['studio_type'] ?? '');
                        $play_date = mysqli_real_escape_string($conn, $sched['play_date'] ?? '');
                        $selected_times = $sched['times'] ?? [];

                        if ($selected_theater_id > 0 && !empty($selected_studio_type) && !empty($play_date) && !empty($selected_times)) {
                            // Find StudioID
                            $selected_studio_id = 0;
                            $studio_lookup = mysqli_query($conn, "SELECT StudioID FROM studio WHERE TheaterID = $selected_theater_id AND Type = '$selected_studio_type' LIMIT 1");
                            if ($studio_lookup && mysqli_num_rows($studio_lookup) > 0) {
                                $selected_studio_id = mysqli_fetch_assoc($studio_lookup)['StudioID'];
                            } else {
                                $fallback_query = mysqli_query($conn, "SELECT StudioID FROM studio WHERE TheaterID = $selected_theater_id LIMIT 1");
                                if ($fallback_query && mysqli_num_rows($fallback_query) > 0) {
                                    $selected_studio_id = mysqli_fetch_assoc($fallback_query)['StudioID'];
                                }
                            }

                            if ($selected_studio_id > 0) {
                                foreach ($selected_times as $j) {
                                    $start_time = '';
                                    if ($j === '10:00') $start_time = '10:00:00';
                                    elseif ($j === '12:00') $start_time = '12:00:00';
                                    elseif ($j === '15:00') $start_time = '15:00:00';

                                    if ($start_time) {
                                        mysqli_query($conn, "INSERT INTO showtime (StartTime, PlayDate, MovieID, StudioID) VALUES ('$start_time', '$play_date', $movie_id, $selected_studio_id)");
                                        $showtimes_inserted++;
                                    }
                                }
                            }
                        }
                    }
                    $message = "Film dan jadwal tayang berhasil diperbarui!";
                    header("Location: index.php?message=" . urlencode($message));
                    exit();
                } else {
                    $message = "Gagal memperbarui film: " . mysqli_error($conn);
                }
            } else {
                // Insert Movie
                $insertMovie = mysqli_query($conn, "INSERT INTO movie (Title, Duration, Genre, Rating, PosterURL, TrailerURL) VALUES ('$judul', $duration, '$genre', $rating, '$posterUrl', '$trailerUrl')");
                if ($insertMovie) {
                    $movie_id = mysqli_insert_id($conn);
                    $showtimes_inserted = 0;

                    // Insert schedules
                    foreach ($schedules as $sched) {
                        $selected_theater_id = intval($sched['theater_id'] ?? 0);
                        $selected_studio_type = mysqli_real_escape_string($conn, $sched['studio_type'] ?? '');
                        $play_date = mysqli_real_escape_string($conn, $sched['play_date'] ?? '');
                        $selected_times = $sched['times'] ?? [];

                        if ($selected_theater_id > 0 && !empty($selected_studio_type) && !empty($play_date) && !empty($selected_times)) {
                            // Find StudioID
                            $selected_studio_id = 0;
                            $studio_lookup = mysqli_query($conn, "SELECT StudioID FROM studio WHERE TheaterID = $selected_theater_id AND Type = '$selected_studio_type' LIMIT 1");
                            if ($studio_lookup && mysqli_num_rows($studio_lookup) > 0) {
                                $selected_studio_id = mysqli_fetch_assoc($studio_lookup)['StudioID'];
                            } else {
                                $fallback_query = mysqli_query($conn, "SELECT StudioID FROM studio WHERE TheaterID = $selected_theater_id LIMIT 1");
                                if ($fallback_query && mysqli_num_rows($fallback_query) > 0) {
                                    $selected_studio_id = mysqli_fetch_assoc($fallback_query)['StudioID'];
                                }
                            }

                            if ($selected_studio_id > 0) {
                                foreach ($selected_times as $j) {
                                    $start_time = '';
                                    if ($j === '10:00') $start_time = '10:00:00';
                                    elseif ($j === '12:00') $start_time = '12:00:00';
                                    elseif ($j === '15:00') $start_time = '15:00:00';

                                    if ($start_time) {
                                        mysqli_query($conn, "INSERT INTO showtime (StartTime, PlayDate, MovieID, StudioID) VALUES ('$start_time', '$play_date', $movie_id, $selected_studio_id)");
                                        $showtimes_inserted++;
                                    }
                                }
                            }
                        }
                    }
                    $message = "Film berhasil ditambahkan ke Database!";
                    header("Location: index.php?message=" . urlencode($message));
                    exit();
                } else {
                    $message = "Gagal menambahkan film: " . mysqli_error($conn);
                }
            }
        } else {
            $message = "Semua kolom input wajib diisi!";
        }
    } catch (Exception $e) {
        $message = "Gagal memproses bioskop/jadwal: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Cinema Management</title>
    
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
        .sidebar-link:hover, .sidebar-link.active {
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

        .form-panel {
            display: flex;
            gap: 32px;
            align-items: flex-start;
        }

        .image-preview-box {
            background-color: #2d1a1a;
            border: 2px dashed #5a3a3a;
            border-radius: 12px;
            width: 280px;
            height: 360px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }
        .image-preview-box i {
            font-size: 48px;
            color: #d4af37;
            margin-bottom: 16px;
        }
        .image-preview-box span {
            color: #d4af37;
            font-size: 16px;
            font-weight: 500;
            text-align: center;
        }

        .form-section {
            flex: 1;
        }
        .form-label {
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-control {
            background-color: #2d1a1a;
            border: 1px solid #5a3a3a;
            color: #ffffff;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 14px;
        }
        .form-control:focus {
            background-color: #3a2626;
            border-color: #d4af37;
            color: #ffffff;
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
        }
        .form-control::placeholder {
            color: #666;
        }
        .form-select {
            background-color: #2d1a1a;
            border: 1px solid #5a3a3a;
            color: #ffffff;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 14px;
        }
        .form-select:focus {
            background-color: #3a2626;
            border-color: #d4af37;
            color: #ffffff;
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
        }
        .form-select option {
            background-color: #2d1a1a;
            color: #ffffff;
        }

        .chip-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 8px;
        }
        .chip {
            background-color: transparent;
            border: 1px solid #5a3a3a;
            color: #888;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .chip:hover {
            border-color: #d4af37;
            color: #d4af37;
        }
        .chip.active {
            background-color: #d4af37;
            border-color: #d4af37;
            color: #000;
        }
        .chip input {
            display: none;
        }

        .btn-submit {
            background-color: #d4af37;
            color: #000;
            border: none;
            border-radius: 6px;
            padding: 12px 32px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            float: right;
        }
        .btn-submit:hover {
            background-color: #b8962e;
            color: #000;
        }

        .btn-add-schedule {
            background-color: transparent;
            border: 1px dashed #d4af37;
            color: #d4af37;
            padding: 10px;
            width: 100%;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-add-schedule:hover {
            background-color: rgba(212, 175, 55, 0.1);
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #28a745;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1a0a0a; }
        ::-webkit-scrollbar-thumb { background: #3a2626; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #4a3636; }
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
                <a href="index.php" class="sidebar-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
                    Dashboard
                </a>
                <a href="cinema.php" class="sidebar-link <?php echo $page == 'cinema' ? 'active' : ''; ?>">
                    Cinema
                </a>
                <a href="transaction.php" class="sidebar-link <?php echo $page == 'transaction' ? 'active' : ''; ?>">
                    Transaction
                </a>
            </div>

            <div class="col-md-10 main-content">
                
                <h2 class="welcome-text"><?php echo $edit_id > 0 ? 'Edit Film' : 'Kelola Film & Jadwal'; ?></h2>

                <?php if ($message): ?>
                <div class="alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form action="cinema.php" method="POST">
                    <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                    
                    <div class="form-panel">
                        
                        <div>
                            <div class="image-preview-box" id="imagePreviewBox">
                                <i class="fas fa-image" id="previewIcon"></i>
                                <span id="previewText">Preview Poster</span>
                            </div>
                        </div>

                        <div class="form-section">
                            
                            <div class="mb-4">
                                <label class="form-label">Judul Film</label>
                                <input type="text" name="judul" class="form-control" placeholder="Masukkan judul film" value="<?php echo htmlspecialchars($movie_data['Title'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Genre</label>
                                <input type="text" name="genre" class="form-control" placeholder="Contoh: Aksi, Drama, Sci-Fi" value="<?php echo htmlspecialchars($movie_data['Genre'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Link Poster Film (URL / Lokal)</label>
                                <input type="text" name="poster_url" id="posterUrlInput" class="form-control" placeholder="https://example.com/poster.jpg atau poster.jpg" value="<?php echo htmlspecialchars($movie_data['PosterURL'] ?? ''); ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Durasi Film (Menit)</label>
                                    <input type="number" name="duration" class="form-control" placeholder="Contoh: 120" min="1" value="<?php echo htmlspecialchars($movie_data['Duration'] ?? '120'); ?>" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Rating Film (0.0 - 10.0)</label>
                                    <input type="number" name="rating" class="form-control" placeholder="Masukkan rating (0.0 jika coming soon)" step="0.1" min="0" max="10" value="<?php echo htmlspecialchars($movie_data['Rating'] ?? '0.0'); ?>" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Link Trailer Film (Embed URL YouTube)</label>
                                <input type="text" name="trailer_url" class="form-control" placeholder="Contoh: https://www.youtube.com/embed/OU3VMoEhqu0" value="<?php echo htmlspecialchars($movie_data['TrailerURL'] ?? ''); ?>">
                                <div class="form-text text-muted-50 small" style="color: rgba(255,255,255,0.4)">Masukkan URL embed YouTube (misal: <code>https://www.youtube.com/embed/OU3VMoEhqu0</code>).</div>
                            </div>

                            <div class="border-top border-secondary my-4 pt-4">
                                <h5 class="text-warning mb-3">Atur Penjadwalan Bioskop</h5>
                            </div>

                            <!-- Showtime schedules container -->
                            <div id="schedulesContainer">
                                
                                <?php 
                                if (empty($existing_schedules)) {
                                    $existing_schedules = [
                                        [
                                            'theater_id' => '',
                                            'play_date' => date('Y-m-d'),
                                            'studio_type' => '',
                                            'times' => []
                                        ]
                                    ];
                                }
                                
                                foreach ($existing_schedules as $idx => $sched):
                                ?>
                                <div class="row g-3 align-items-center mb-4 schedule-row <?php echo $idx > 0 ? 'border-top border-secondary-subtle pt-3' : ''; ?>" data-index="<?php echo $idx; ?>">
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Bioskop</label>
                                        <select name="schedules[<?php echo $idx; ?>][theater_id]" class="form-select" required>
                                            <option value="" disabled <?php echo empty($sched['theater_id']) ? 'selected' : ''; ?> hidden>Pilih Bioskop</option>
                                            <?php foreach ($theaters as $theater): ?>
                                            <option value="<?php echo $theater['TheaterID']; ?>" <?php echo $theater['TheaterID'] == $sched['theater_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($theater['Name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">Tipe Studio</label>
                                        <select name="schedules[<?php echo $idx; ?>][studio_type]" class="form-select" required>
                                            <option value="" disabled <?php echo empty($sched['studio_type']) ? 'selected' : ''; ?> hidden>Pilih Tipe Studio</option>
                                            <option value="Regular" <?php echo $sched['studio_type'] == 'Regular' ? 'selected' : ''; ?>>Regular</option>
                                            <option value="Velvet" <?php echo $sched['studio_type'] == 'Velvet' ? 'selected' : ''; ?>>Velvet Class</option>
                                            <option value="Gold Class" <?php echo $sched['studio_type'] == 'Gold Class' ? 'selected' : ''; ?>>Gold Class</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Tanggal & Jam Tayang</label>
                                        <div class="d-flex flex-column gap-2">
                                            <input type="date" name="schedules[<?php echo $idx; ?>][play_date]" class="form-control mb-1" value="<?php echo htmlspecialchars($sched['play_date']); ?>" required>
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php foreach ($jadwalOptions as $j): 
                                                    $checked = in_array($j, $sched['times']) ? 'checked' : '';
                                                    $active = $checked ? 'active' : '';
                                                ?>
                                                <label class="chip <?php echo $active; ?>">
                                                    <input type="checkbox" name="schedules[<?php echo $idx; ?>][times][]" value="<?php echo $j; ?>" <?php echo $checked; ?> onchange="toggleChipActive(this)">
                                                    <?php echo $j; ?> WIB
                                                </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <label class="form-label d-block">&nbsp;</label>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row" onclick="removeScheduleRow(this)"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                                <?php endforeach; ?>

                            </div>

                            <button type="button" class="btn-add-schedule mb-4" onclick="addScheduleRow()"><i class="fas fa-plus me-1"></i> Tambah Jadwal Tayang Baru</button>

                            <button type="submit" class="btn-submit"><?php echo $edit_id > 0 ? 'Simpan Perubahan' : 'Simpan Film & Jadwal'; ?></button>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let scheduleIndex = <?php echo count($existing_schedules); ?>;

        // Add dynamic scheduling rows
        function addScheduleRow() {
            const container = document.getElementById('schedulesContainer');
            const template = `
                <div class="row g-3 align-items-center mb-4 schedule-row border-top border-secondary-subtle pt-3" data-index="${scheduleIndex}">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Bioskop</label>
                        <select name="schedules[${scheduleIndex}][theater_id]" class="form-select" required>
                            <option value="" disabled selected hidden>Pilih Bioskop</option>
                            <?php foreach ($theaters as $theater): ?>
                            <option value="<?php echo $theater['TheaterID']; ?>"><?php echo htmlspecialchars($theater['Name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Tipe Studio</label>
                        <select name="schedules[${scheduleIndex}][studio_type]" class="form-select" required>
                            <option value="" disabled selected hidden>Pilih Tipe Studio</option>
                            <option value="Regular">Regular</option>
                            <option value="Velvet">Velvet Class</option>
                            <option value="Gold Class">Gold Class</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Tanggal & Jam Tayang</label>
                        <div class="d-flex flex-column gap-2">
                            <input type="date" name="schedules[${scheduleIndex}][play_date]" class="form-control mb-1" value="<?php echo date('Y-m-d'); ?>" required>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach (['10:00', '12:00', '15:00'] as $j): ?>
                                <label class="chip">
                                    <input type="checkbox" name="schedules[${scheduleIndex}][times][]" value="<?php echo $j; ?>" onchange="toggleChipActive(this)">
                                    <?php echo $j; ?> WIB
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 text-end">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row" onclick="removeScheduleRow(this)"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
            scheduleIndex++;
        }

        // Remove a scheduling row
        function removeScheduleRow(button) {
            const row = button.closest('.schedule-row');
            const container = document.getElementById('schedulesContainer');
            if (container.querySelectorAll('.schedule-row').length > 1) {
                row.remove();
            } else {
                alert("Minimal harus ada satu jadwal tayang!");
            }
        }

        // Toggle chip active class when checkbox changes
        function toggleChipActive(checkbox) {
            const label = checkbox.closest('.chip');
            if (checkbox.checked) {
                label.classList.add('active');
            } else {
                label.classList.remove('active');
            }
        }

        // URL image preview handler
        const posterUrlInput = document.getElementById('posterUrlInput');
        const imagePreviewBox = document.getElementById('imagePreviewBox');
        const previewIcon = document.getElementById('previewIcon');
        const previewText = document.getElementById('previewText');

        function updatePreview() {
            const url = posterUrlInput.value.trim();
            if (url) {
                imagePreviewBox.style.backgroundImage = `url('${url}')`;
                imagePreviewBox.style.borderStyle = 'solid';
                previewIcon.style.display = 'none';
                previewText.style.display = 'none';
            } else {
                imagePreviewBox.style.backgroundImage = 'none';
                imagePreviewBox.style.borderStyle = 'dashed';
                previewIcon.style.display = 'block';
                previewText.style.display = 'block';
            }
        }

        posterUrlInput.addEventListener('input', updatePreview);
        window.addEventListener('load', updatePreview);
    </script>
</body>
</html>
