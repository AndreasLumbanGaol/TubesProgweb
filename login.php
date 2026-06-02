<?php
include 'koneksi.php';
session_start();
$message = '';
$alertClass = 'alert-danger';

// Tangkap notifikasi sukses pendaftaran dari register.php
if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $message = "Pendaftaran berhasil! Silakan login dengan akun baru Anda.";
    $alertClass = 'alert-success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        // Ambil data user berdasarkan email dan password sesuai rancangan ERD
        $query = "SELECT * FROM User WHERE Email = '$email' AND Password = '$password'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Menyimpan informasi login ke dalam Session PHP
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['user_name'] = $user['Nama'];
            $_SESSION['role'] = $user['Role']; // Mengambil peran (admin/user) dari database

            // Logika Pengalihan Menu berdasarkan Role ERD
            if ($user['Role'] === 'admin') {
                header("Location: admin-index.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $message = "Email atau Password salah!";
            $alertClass = 'alert-danger';
        }
    } else {
        $message = "Email dan Password wajib diisi!";
        $alertClass = 'alert-danger';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tixly Cinema - Login</title>
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
            background-color: #b89626;
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
            max-width: 450px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
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
                    <li class="nav-item"><a class="nav-link" href="resell.php">Resell Ticket</a></li>
                </ul>
                <div class="user-actions">
                    <a href="login.php" class="login-button">LOG IN / SIGN UP</a>
                    <a href="#"><img src="https://static.vecteezy.com/system/resources/thumbnails/007/033/146/small/profile-icon-login-head-icon-vector.jpg" alt="Profile Icon" class="profile-icon"></a>
                </div>
            </div>
        </div>
    </nav>

    <div class="auth-wrapper">
        <div class="auth-title">Hi, Sobat Tixly!</div>
        
        <?php if(!empty($message)): ?>
            <div class="alert <?php echo $alertClass; ?> w-100 text-center" style="max-width: 450px;"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="auth-box">
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Pengguna</label>
                    <input type="email" name="email" id="email" required placeholder="Masukkan email anda">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required placeholder="Masukkan password anda">
                </div>
                <div class="btn-auth-container">
                    <button type="submit" class="btn-auth">LOGIN</button>
                </div>
            </form>
        </div>
        <div class="auth-footer">
            Belum punya akun? <a href="register.php">Daftar sekarang</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>