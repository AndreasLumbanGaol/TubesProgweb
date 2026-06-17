<?php
$host     = "127.0.0.1";
$username = "root";
$password = "";
$database = "tixly_cinema";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi ke database phpMyAdmin gagal: " . mysqli_connect_error());
}

// Global location session validation and fallback
if (session_status() !== PHP_SESSION_NONE) {
    $check_loc_query = mysqli_query($conn, "SELECT DISTINCT Location FROM theater ORDER BY Location ASC");
    $valid_locations = [];
    if ($check_loc_query) {
        while ($r = mysqli_fetch_assoc($check_loc_query)) {
            $valid_locations[] = $r['Location'];
        }
    }
    if (!empty($valid_locations)) {
        if (!isset($_SESSION['selected_location']) || !in_array($_SESSION['selected_location'], $valid_locations)) {
            $_SESSION['selected_location'] = $valid_locations[0];
        }
    } else {
        $_SESSION['selected_location'] = 'Bandung';
    }
}
?>