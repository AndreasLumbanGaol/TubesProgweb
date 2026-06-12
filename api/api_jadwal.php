<?php
// api_jadwal.php
include_once __DIR__ . '/../koneksi.php';
header('Content-Type: application/json');

$movie = mysqli_real_escape_string($conn, $_GET['movie'] ?? '');
$cinema = mysqli_real_escape_string($conn, $_GET['cinema'] ?? '');
$type = mysqli_real_escape_string($conn, $_GET['type'] ?? '');

// Query mencari jadwal berdasarkan Film, Nama Bioskop, dan Tipe Studio
$query = "SELECT st.PlayDate, st.StartTime 
          FROM showtime st
          JOIN movie m ON st.MovieID = m.MovieID
          JOIN studio s ON st.StudioID = s.StudioID
          JOIN theater t ON s.TheaterID = t.TheaterID
          WHERE m.Title = '$movie' AND t.Name = '$cinema' AND s.Type = '$type'
          ORDER BY st.PlayDate ASC, st.StartTime ASC";

$result = mysqli_query($conn, $query);
$jadwal = [];

if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $hari = date('Y-m-d', strtotime($row['PlayDate']));
        $label_hari = date('d M Y', strtotime($row['PlayDate']));
        
        // Mengubah label tanggal menjadi Hari Ini atau Besok agar user-friendly
        if ($hari == date('Y-m-d')) {
            $label_hari = "Hari Ini (" . date('d M', strtotime($row['PlayDate'])) . ")";
        } elseif ($hari == date('Y-m-d', strtotime('+1 day'))) {
            $label_hari = "Besok (" . date('d M', strtotime($row['PlayDate'])) . ")";
        }

        $jadwal[] = [
            'date_label' => $label_hari,
            'time' => date('H:i', strtotime($row['StartTime'])) . ' WIB',
            'raw_date' => $row['PlayDate'],
            'raw_time' => date('H:i', strtotime($row['StartTime']))
        ];
    }
}

echo json_encode($jadwal);
?>