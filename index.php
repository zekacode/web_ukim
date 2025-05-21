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
        <h2>Blog</h2>
        <p>Terbaru dari UKIM</p>
        <div class="card-container">
            <?php
            // Query untuk mengambil 3 blog terbaru berdasarkan tanggal
            $sql = "SELECT id_blog, tgl, judul, gambar, departemen FROM blog_ukim ORDER BY tgl DESC LIMIT 3";
            $result = mysqli_query($conn, $sql);

            // Cek apakah query berhasil
            if (!$result) {
                die('Query failed: ' . mysqli_error($conn));
            }

            // Loop untuk menampilkan hasil query
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="card">
                <?php if (!empty($row['gambar']) && $row['gambar'] !== '-') { ?>
                    <img src="<?= $row['gambar']; ?>" alt="Image for <?= $row['judul']; ?>">
                <?php } ?>
                <div class="card-content">
                    <span class="tag"><?= $row['departemen']; ?></span>
                    <span class="date"><?= date("d/m/Y", strtotime($row['tgl'])); ?></span>
                    <h3><?= htmlspecialchars($row['judul']); ?></h3>
                    <p>
                        <a href="artikel.php?id=<?= $row['id_blog']; ?>" style="text-decoration: none; color: inherit;">Baca &rarr;</a>
                    </p>
                </div>
            </div>
            <?php } ?>
        </div>
    </section>

    <?php
    include('./layout/footer_index.php');
    ?>
</body>
</html>
