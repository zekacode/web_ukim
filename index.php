<?php
include('./layout/header_index.php');
?>

<head>
    <link rel="stylesheet" href="./style/index.css">
</head>
<body>
    <section class="hero">
        <img src="./asset/desktop.png" alt="Hero Background" class="hero-image">
        <div class="hero-content">
            <h1>UKIM <span>Unesa</span></h1>
            <p>Unit Kegiatan Ilmiah Mahasiswa Universitas Negeri Surabaya</p>
        </div>
    </section>

    <!-- Logo Carousel Section -->
    <section class="logo-carousel">
        <div class="logo-carousel-track">
            <div class="logo-item">
                <img src="./asset/logo1.png" alt="Logo 1">
            </div>
            <div class="logo-item">
                <img src="./asset/logo2.png" alt="Logo 2">
            </div>
            <div class="logo-item">
                <img src="./asset/logo3.png" alt="Logo 3">
            </div>
            <div class="logo-item">
                <img src="./asset/logo4.png" alt="Logo 4">
            </div>
            <div class="logo-item">
                <img src="./asset/logo1.png" alt="Logo 1">
            </div>
            <div class="logo-item">
                <img src="./asset/logo2.png" alt="Logo 2">
            </div>
            <div class="logo-item">
                <img src="./asset/logo3.png" alt="Logo 3">
            </div>
            <div class="logo-item">
                <img src="./asset/logo4.png" alt="Logo 4">
            </div>
        </div>
    </section>

    <!-- Vision Section -->
    <section class="vision">
        <h2>Tentang Kami</h2>
        <p>UKIM adalah organisasi yang mewadahi dan menyalurkan minat serta bakat mahasiswa dalam bidang keilmiahan maupun non ilmiah dengan berpendirian pada empat Roh UKIM yaitu Penulis, Peneliti, Aktivis dan Wirausaha.</p>
        <h3>UKIM Memiliki Departemen</h3>
        <div class="vision-container">
            <div class="vision-items">
                <div class="vision-item" data-target="content-bph">
                    <h4>BPH</h4>
                </div>
                <div class="vision-item" data-target="content-dpo">
                    <h4>DPO</h4>
                </div>
                <div class="vision-item" data-target="content-dpr">
                    <h4>DPR</h4>
                </div>
                <div class="vision-item" data-target="content-dhm">
                    <h4>DHM</h4>
                </div>
                <div class="vision-item" data-target="content-dpe">
                    <h4>DPE</h4>
                </div>
            </div>
            <div class="vision-content">
                <div id="content-bph" class="content active">
                    <h4>Badan Pengurus Harian (BPH)</h4>
                    <p>Badan Pengurus Harian bertanggung jawab mengelola organisasi dan memastikan kegiatan berjalan sesuai rencana.</p>
                    <img src="./asset/bph-image.png" alt="BPH">
                </div>
                <div id="content-dpo" class="content">
                    <h4>Departemen Pengembangan Organisasi (DPO)</h4>
                    <p>Departemen ini berfokus pada peningkatan kualitas organisasi melalui pengembangan sumber daya manusia.</p>
                    <img src="./asset/dpo-image.png" alt="DPO">
                </div>
                <div id="content-dpr" class="content">
                    <h4>Departemen Penalaran & Riset (DPR)</h4>
                    <p>Departemen yang mengarahkan mahasiswa dalam kegiatan penelitian ilmiah dan logika berpikir.</p>
                    <img src="./asset/dpr-image.png" alt="DPR">
                </div>
                <div id="content-dhm" class="content">
                    <h4>Departemen Hubungan Masyarakat (DHM)</h4>
                    <p>Departemen ini bertugas menjalin relasi dengan pihak eksternal untuk memperluas jaringan.</p>
                    <img src="./asset/dhm-image.png" alt="DHM">
                </div>
                <div id="content-dpe" class="content">
                    <h4>Departemen Pemberdayaan Ekonomi (DPE)</h4>
                    <p>Departemen yang mengelola program kewirausahaan dan pengembangan ekonomi mahasiswa.</p>
                    <img src="./asset/dpe-image.png" alt="DPE">
                </div>
            </div>
        </div>
    </section>

    <!-- Updates Section -->
        <section class="updates">
        <h2>Blog Terbaru</h2> <?php // Ubah judul jika perlu ?>
        <p>Informasi dan kegiatan terkini dari UKIM Unesa</p>
        <div class="card-container">
            <?php
            // Mengambil 3 blog terbaru yang statusnya 'published' menggunakan fungsi dari controller
            $latest_blogs = get_latest_blogs(3); // Atau get_all_blogs(3, 0) jika dimodifikasi

            if ($latest_blogs && count($latest_blogs) > 0) {
                foreach ($latest_blogs as $blog) {
            ?>
            <div class="card">
                <?php
                // Path gambar di database disimpan sebagai './asset/gbr_blog/namafile.jpg'
                // Untuk menampilkannya dari index.php (yang ada di root), pathnya sudah benar
                // Jika path di DB berbeda, sesuaikan di sini.
                $gambar_path = htmlspecialchars($blog['gambar']);
                // Jika path di DB tidak ada './' di depan, tambahkan:
                // if (strpos($gambar_path, './') !== 0) {
                //     $gambar_path = './' . ltrim($gambar_path, '/');
                // }
                ?>
                <?php if (!empty($blog['gambar'])): ?>
                    <img src="<?php echo $gambar_path; ?>" alt="Gambar untuk <?php echo htmlspecialchars($blog['judul']); ?>">
                <?php else: ?>
                    <img src="./asset/placeholder-image.png" alt="Placeholder Image"> <?php // Sediakan placeholder jika tidak ada gambar ?>
                <?php endif; ?>
                <div class="card-content">
                    <?php // Menggunakan nama_dept dari hasil join di controller ?>
                    <span class="tag"><?php echo htmlspecialchars($blog['nama_dept'] ?? 'Umum'); ?></span>
                    <span class="date"><?php echo date("d M Y", strtotime($blog['created_at'])); ?></span>
                    <h3><?php echo htmlspecialchars($blog['judul']); ?></h3>
                    <p>
                        <?php // Link ke halaman detail blog, idealnya menggunakan slug ?>
                        <a href="blog-detail.php?slug=<?php echo htmlspecialchars($blog['slug']); ?>" style="text-decoration: none; color: inherit;">Baca &rarr;</a>
                        <?php // Atau jika masih menggunakan ID: ?>
                        <!-- <a href="artikel.php?id=<?php //echo htmlspecialchars($blog['id_blog']); ?>" class="read-more-link">Baca Selengkapnya →</a> -->
                    </p>
                </div>
            </div>
            <?php
                } // End foreach
            } else {
                echo "<p class='no-blogs-message'>Belum ada blog terbaru yang dipublikasikan.</p>";
            }
            ?>
        </div>
    </section>

    <?php
    include('./layout/footer_index.php');
    ?>
</body>
</html>
