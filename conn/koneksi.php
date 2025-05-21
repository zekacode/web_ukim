<?php
$dbHost = 'localhost'; // Gunakan 'localhost' (huruf kecil) untuk konsistensi.
$dbName = 'web_ukim_new';
$dbUsername = 'root';  // Perbaiki penulisan variabel yang hilang tanda '='
$dbPassword = '';

// Koneksi ke database
$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);

// Cek apakah koneksi berhasil
if (!$conn) {
    die('Gagal terhubung dengan MySQL: ' . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4"); 

// Query untuk mengambil data
$sql = 'SELECT * FROM blog_ukim'; // Perbaiki query

// Eksekusi query
$query = mysqli_query($conn, $sql);

// Cek apakah query berhasil
if (!$query) {
    die('SQL Error: ' . mysqli_error($conn));
}
?>
