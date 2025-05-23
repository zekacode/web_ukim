<?php
// Mulai session di paling awal!
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include koneksi dan controller (cek path relatif dari folder layout)
include('../conn/koneksi.php'); // Asumsi conn 1 level di atas layout
require_once('../conn/controller.php'); // Asumsi conn 1 level di atas layout

// Cek login - PENTING! Jika tidak ada di halaman utama, harus ada di header
// Sebaiknya cek juga di setiap halaman admin
if (!isset($_SESSION['user_id'])) {
    // Jika tidak ada session user_id, redirect ke login
    // Path dari layout ke login.php (asumsi login.php 1 level di atas layout)
    header('Location: ../login.php');
    exit();
}

// Ambil nama user dari session untuk sapaan
$nama_user = isset($_SESSION['nama_lengkap']) ? htmlspecialchars($_SESSION['nama_lengkap']) : 'Admin';
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Dinamiskan title jika perlu -->
    <title>Admin Dashboard - UKIM Unesa</title>
    <link rel="stylesheet" href="./style/admin.css"> 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">UKIM Admin</div>
        <nav class="nav">
            <ul>
                <!-- Sesuaikan href dengan lokasi file admin.php -->
                <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="article.php"><i class="fas fa-blog"></i> Blog</a></li>
                <li><a href="karya.php"><i class="fas fa-pencil-alt"></i> Karya Cipta</a></li>
                <li><a href="event.php"><i class="fas fa-calendar-alt"></i> Event</a></li>
                <li><a href="prestasi.php"><i class="fas fa-trophy"></i> Prestasi</a></li>
                <?php // Tampilkan menu Kelola User hanya untuk role 'admin' ?>
                <?php if ($user_role === 'admin'): ?>
                    <li><a href="kelola_user.php"><i class="fas fa-users-cog"></i> Kelola User</a></li>
                <?php endif; ?>
                <li><a href="../conn/controller.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navigation -->
        <header class="top-nav">
            <div class="welcome">
                <!-- Sapaan menggunakan nama dari session -->
                <span>Halo, <?php echo $nama_user; ?>!</span>
            </div>
            <div class="top-nav-actions">
                <a href="../conn/controller.php?action=logout" class="logout-link-top"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </header>
        <!-- Konten utama halaman akan dimulai setelah ini -->