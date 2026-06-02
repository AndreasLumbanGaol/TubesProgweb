<?php
include 'koneksi.php';
$message = '';
$alertClass = 'alert-danger';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaDepan = $_POST['namaDepan'] ?? '';
    $namaBelakang = $_POST['namaBelakang'] ?? '';
    $namaLengkap = trim($namaDepan . ' ' . $namaBelakang);
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($namaLengkap) && !empty($email) && !empty($password)) {
        // Cek duplikasi email di database
        $cekEmail = mysqli_query($conn, "SELECT * FROM User WHERE Email = '$email'");
        if (mysqli_num_rows($cekEmail) > 0) {
            $message = "Email sudah terdaftar! Gunakan email lain.";
        } else {
            // Masukkan data ke tabel User sesuai ERD
            $query = "INSERT INTO User (Nama, Email, Password, Phone, Role) VALUES ('$namaLengkap', '$email', '$password', '$phone', 'user')";
            if (mysqli_query($conn, $query)) {
                header("Location: login.php?status=success");
                exit();
            } else {
                $message = "Gagal mendaftar: " . mysqli_error($conn);
            }
        }
    } else {
        $message = "Semua kolom wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0d0606;
            color: #ffffff;
            font-family: sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        }
        .nav-link {
            color: #cccccc !important;
            font-weight: 500;
            margin: 0 10px;
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
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #d4af37;
        }
        .auth-wrapper {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 40px 0;
        }
        .auth-title {
            font-family: monospace;
            font-size: 20px;
            margin-bottom: 16px;
            text-align: center;
        }
        .auth-box {
            background-color: #a27b3e;
            padding: 32px;
            border-radius: 16px;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
        }
        .form-row {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }
        .form-col {
            flex: 1;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .auth-box label {
            color: #f8f9fa;
            font-size: 14px;
            margin-bottom: 8px;
            font-family: monospace;
            display: block;
        }
        .auth-box input {
            background-color: #e9ecef;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            width: 100%;
            box-sizing: border-box;
        }
        .auth-box input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.5);
        }
        .btn-auth-container {
            text-align: center;
            margin-top: 32px;
        }
        .btn-auth {
            background-color: #000000;
            color: #ffffff;
            border: none;
            padding: 10px 48px;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-auth:hover {
            background-color: #333333;
            color: #ffffff;
        }
        .auth-footer {
            margin-top: 16px;
            font-size: 14px;
            font-family: monospace;
            text-align: center;
        }
        .auth-footer a {
            color: #ff4d4d;
            text-decoration: none;
        }
        .auth-footer a:hover {
            text-decoration: underline;
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
                    <li class="nav-item"><a class="nav-link" href="#">Resell Ticket</a></li>
                </ul>
                <div class="user-actions">
                    <a href="login.php" class="login-button">LOG IN / SIGN UP</a>
                    <a href="#"><img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" alt="Profile Icon" class="profile-icon"></a>
                </div>
            </div>
        </div>
    </nav>
 
    <div class="auth-wrapper">
        <div class="auth-title">Hi, Silahkan buat akunmu!</div>
        
        <?php if(!empty($message)): ?>
            <div class="alert alert-danger w-100" style="max-width: 550px; text-align: center;"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="auth-box">
            <form action="register.php" method="POST">
                <div class="form-row">
                    <div class="form-col">
                        <label for="namaDepan">Nama Depan</label>
                        <input type="text" name="namaDepan" id="namaDepan" required>
                    </div>
                    <div class="form-col">
                        <label for="namaBelakang">Nama Belakang</label>
                        <input type="text" name="namaBelakang" id="namaBelakang" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">No. Handphone</label>
                    <input type="tel" name="phone" id="phone" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="btn-auth-container">
                    <button type="submit" class="btn-auth">SIGN UP</button>
                </div>
            </form>
        </div>
        
        <div class="auth-footer">
            Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </div>
    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 </body>
 </html>