<?php
// 1. Include header admin
// Path relatif dari admin/admin.php ke layout/header-admin.php
include('./layout/header-admin.php');

// Header sudah melakukan cek login, jadi tidak perlu cek lagi di sini
// Session juga sudah dimulai oleh header

?>

<!-- Mulai Konten Halaman Dashboard -->
<div class="dashboard-container">

    <div class="dashboard-header">
        <h1>Dashboard Admin</h1>
        <p>Selamat datang kembali, <?php echo isset($_SESSION['nama_lengkap']) ? htmlspecialchars($_SESSION['nama_lengkap']) : 'Admin'; ?>!</p>
    </div>

    <div class="dashboard-summary">
        <div class="summary-card">
            <i class="fas fa-blog icon"></i>
            <?php
                // Contoh ambil jumlah data (perlu fungsi di controller atau query langsung)
                // $jumlah_blog = count_table_rows('blog_ukim'); // Buat fungsi ini di controller jika perlu
                $sql_count_blog = "SELECT COUNT(*) as total FROM blog_ukim";
                $res_blog = mysqli_query($conn, $sql_count_blog);
                $count_blog = ($res_blog) ? mysqli_fetch_assoc($res_blog)['total'] : 0;
            ?>
            <div class="card-content">
                <h3>Total Artikel Blog</h3>
                <p class="count"><?php echo $count_blog; ?></p>
            </div>
            <a href="article.php" class="card-link">Kelola Blog <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="summary-card">
            <i class="fas fa-pencil-alt icon"></i>
             <?php
                $sql_count_karya = "SELECT COUNT(*) as total FROM karya_cipta";
                $res_karya = mysqli_query($conn, $sql_count_karya);
                $count_karya = ($res_karya) ? mysqli_fetch_assoc($res_karya)['total'] : 0;
            ?>
            <div class="card-content">
                <h3>Total Karya Cipta</h3>
                <p class="count"><?php echo $count_karya; ?></p>
            </div>
            <a href="kelola_karya.php" class="card-link">Kelola Karya <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="summary-card">
            <i class="fas fa-calendar-alt icon"></i>
             <?php
                $sql_count_event = "SELECT COUNT(*) as total FROM events";
                $res_event = mysqli_query($conn, $sql_count_event);
                $count_event = ($res_event) ? mysqli_fetch_assoc($res_event)['total'] : 0;
            ?>
            <div class="card-content">
                <h3>Total Event</h3>
                <p class="count"><?php echo $count_event; ?></p>
            </div>
            <a href="event.php" class="card-link">Kelola Event <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="summary-card">
             <i class="fas fa-trophy icon"></i>
             <?php
                $sql_count_prestasi = "SELECT COUNT(*) as total FROM prestasi";
                $res_prestasi = mysqli_query($conn, $sql_count_prestasi);
                $count_prestasi = ($res_prestasi) ? mysqli_fetch_assoc($res_prestasi)['total'] : 0;
            ?>
            <div class="card-content">
                <h3>Total Prestasi</h3>
                <p class="count"><?php echo $count_prestasi; ?></p>
            </div>
            <a href="kelola_prestasi.php" class="card-link">Kelola Prestasi <i class="fas fa-arrow-right"></i></a>
        </div>

         <?php // Kartu Kelola User hanya untuk admin ?>
         <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
         <div class="summary-card">
             <i class="fas fa-users-cog icon"></i>
              <?php
                // Hitung user aktif (misalnya)
                $sql_count_user = "SELECT COUNT(*) as total FROM users WHERE status_keanggotaan='aktif'";
                $res_user = mysqli_query($conn, $sql_count_user);
                $count_user = ($res_user) ? mysqli_fetch_assoc($res_user)['total'] : 0;
            ?>
            <div class="card-content">
                <h3>Total User Aktif</h3>
                <p class="count"><?php echo $count_user; ?></p>
            </div>
            <a href="kelola_user.php" class="card-link">Kelola User <i class="fas fa-arrow-right"></i></a>
        </div>
        <?php endif; ?>

    </div>

    <div class="quick-actions">
        <h2>Aksi Cepat</h2>
        <a href="article-add.php" class="btn btn-quick-action"><i class="fas fa-plus"></i> Tambah Blog Baru</a>
        <a href="event-add.php" class="btn btn-quick-action"><i class="fas fa-plus"></i> Tambah Event Baru</a>
        <!-- Tambahkan link aksi cepat lainnya -->
    </div>

</div>

</main> 
</body>
</html>