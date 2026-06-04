<?php
session_start();
include 'koneksi.php';
$page = 'cinema';

$genres = ['Aksi', 'Drama', 'Komedi', 'Horror', 'Romantis', 'Sci-Fi', 'Animasi', 'Thriller'];
$jadwalOptions = ['07.00 - 09.00', '10.00 - 12.00', '13.00 - 15.00', '16.00 - 18.00', '19.00 - 21.00'];
$theaterOptions = ['XXI', 'CGV'];
$studioOptions = ['Studio 1', 'Studio 2', 'Studio 3', 'Studio 4', 'Studio 5'];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul'] ?? '');
    $genre = mysqli_real_escape_string($conn, $_POST['genre'] ?? '');
    $posterUrl = '';

    // Pastikan folder uploads ada (catatan: ini akan membuat folder "uploads" di direktori aplikasi Anda)
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileName = time() . '_' . basename($_FILES['poster']['name']);
        $uploadFile = $uploadDir . $fileName;
        if(move_uploaded_file($_FILES['poster']['tmp_name'], $uploadFile)){
            $posterUrl = $uploadFile;
        }
    }

    if(!empty($judul) && !empty($genre)) {
        // Karena durasi dan rating tidak ada di form UI saat ini, diset default
        $insert = mysqli_query($conn, "INSERT INTO movie (Title, Genre, PosterURL, Duration, Rating) VALUES ('$judul', '$genre', '$posterUrl', 120, 0)");
        if($insert) {
            $message = 'Film berhasil ditambahkan ke Database!';
        }
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
        body { background-color: #0d0606; color: #ffffff; font-family: 'Segoe UI', sans-serif; min-height: 100vh; }
        .navbar { border-bottom: 1px solid #3a2626; padding: 16px 24px; background-color: #0d0606; }
        .navbar-brand { color: #d4af37 !important; font-family: Georgia, serif; font-size: 24px; font-weight: bold; }
        .navbar-brand span { font-style: italic; color: #b8962e; font-weight: normal; }
        .sidebar { background-color: #0d0606; min-height: calc(100vh - 73px); padding: 24px 16px; border-right: 1px solid #3a2626; }
        .sidebar-link { display: block; color: #888; text-decoration: none; padding: 10px 16px; border-radius: 8px; margin-bottom: 8px; font-size: 15px; transition: all 0.3s; }
        .sidebar-link:hover, .sidebar-link.active { color: #d4af37; background: rgba(212, 175, 55, 0.15); border: 1px solid rgba(212, 175, 55, 0.3); }
        .main-content { padding: 32px; background-color: #1a0a0a; min-height: calc(100vh - 73px); }
        .welcome-text { color: #d4af37; font-size: 24px; font-weight: 600; margin-bottom: 24px; }
        .form-panel { display: flex; gap: 32px; align-items: flex-start; }
        .image-upload { background-color: #2d1a1a; border: 2px dashed #5a3a3a; border-radius: 12px; width: 280px; height: 360px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; }
        .image-upload:hover { border-color: #d4af37; background-color: #3a2626; }
        .image-upload i { font-size: 48px; color: #d4af37; margin-bottom: 16px; }
        .image-upload span { color: #d4af37; font-size: 16px; font-weight: 500; text-align: center; }
        .image-upload-preview { width: 280px; height: 360px; border-radius: 12px; object-fit: cover; display: none; }
        .form-section { flex: 1; }
        .form-label { color: #ffffff; font-size: 16px; font-weight: 600; margin-bottom: 8px; }
        .form-control, .form-select { background-color: #2d1a1a; border: 1px solid #5a3a3a; color: #ffffff; border-radius: 6px; padding: 12px 16px; font-size: 14px; width: 100%;}
        .form-control:focus, .form-select:focus { background-color: #3a2626; border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2); outline: none; }
        .chip-group { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 8px; }
        .chip { background-color: transparent; border: 1px solid #5a3a3a; color: #888; padding: 8px 16px; border-radius: 20px; font-size: 13px; cursor: pointer; transition: all 0.3s; }
        .chip:hover, .chip.active { border-color: #d4af37; color: #d4af37; }
        .chip input { display: none; }
        .btn-submit { background-color: #d4af37; color: #000; border: none; border-radius: 6px; padding: 12px 32px; font-size: 14px; font-weight: 600; cursor: pointer; float: right; }
        .alert-success { background-color: rgba(40, 167, 69, 0.2); border: 1px solid #28a745; color: #28a745; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; }
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
                <a href="admin-cinema.php" class="sidebar-link active">Cinema</a>
                <a href="admin-transaction.php" class="sidebar-link">Transaction</a>
            </div>

            <div class="col-md-10 main-content">
                <h2 class="welcome-text">Selamat Datang Admin</h2>
                <?php if ($message): ?>
                <div class="alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-panel">
                        <div>
                            <label class="image-upload" id="imageUploadLabel">
                                <input type="file" name="poster" id="posterInput" accept="image/*" style="display: none;">
                                <i class="fas fa-plus"></i>
                                <span>Masukkan<br>Gambar</span>
                            </label>
                            <img src="" alt="Preview" class="image-upload-preview" id="imagePreview">
                        </div>

                        <div class="form-section">
                            <div class="mb-4">
                                <label class="form-label">Judul Film</label>
                                <input type="text" name="judul" class="form-control" placeholder="Masukkan judul film" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Genre</label>
                                <select name="genre" class="form-select" required>
                                    <option value="">Pilih Genre</option>
                                    <?php foreach ($genres as $g): ?>
                                    <option value="<?php echo $g; ?>"><?php echo $g; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Jadwal Tayang</label>
                                <div class="chip-group">
                                    <?php foreach ($jadwalOptions as $j): ?>
                                    <label class="chip"><input type="checkbox" name="jadwal[]" value="<?php echo $j; ?>"> <?php echo $j; ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Theater</label>
                                <div class="chip-group">
                                    <?php foreach ($theaterOptions as $t): ?>
                                    <label class="chip"><input type="radio" name="theater" value="<?php echo $t; ?>"> <?php echo $t; ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Studio</label>
                                <div class="chip-group">
                                    <?php foreach ($studioOptions as $s): ?>
                                    <label class="chip"><input type="radio" name="studio" value="<?php echo $s; ?>"> <?php echo $s; ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button type="submit" class="btn-submit">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.chip').forEach(chip => {
            chip.addEventListener('click', function() {
                const input = this.querySelector('input');
                if (input.type === 'checkbox') {
                    this.classList.toggle('active');
                } else if (input.type === 'radio') {
                    const groupName = input.name;
                    document.querySelectorAll(`input[name="${groupName}"]`).forEach(radio => radio.closest('.chip').classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        const posterInput = document.getElementById('posterInput');
        const imageUploadLabel = document.getElementById('imageUploadLabel');
        const imagePreview = document.getElementById('imagePreview');

        posterInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    imageUploadLabel.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>