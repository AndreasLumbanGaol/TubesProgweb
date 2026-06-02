<?php
$host     = "localhost";
$username = "root";
$password = "";
$database = "tixly_cinema";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi ke database phpMyAdmin gagal: " . mysqli_connect_error());
}
?>