<?php
require_once('./conn/controller.php'); // Panggil controller di awal
include_once('./layout/header_index.php');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Prestasi - UKIM Unesa</title>
    <link rel="stylesheet" href="./style/list.css">
    <!-- FontAwesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php
    // Mengambil filter yang dipilih
    $selected_tingkat = $_GET['tingkat'] ?? ''; // Ambil filter tingkat
    $selected_tahun = isset($_GET['tahun']) && ctype_digit($_GET['tahun']) ? (int)$_GET['tahun'] : null;


    // --- Paginasi ---
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $prestasi_per_page = 9; // Jumlah prestasi per halaman
    $offset = ($page - 1) * $prestasi_per_page;

    // Mengambil data prestasi yang sudah difilter dan dipaginasi
    $prestasi_list = get_all_prestasi_filtered($prestasi_per_page, $offset, $selected_tingkat, $selected_tahun);
    $total_prestasi = count_all_prestasi_filtered($selected_tingkat, $selected_tahun);
    $total_pages = ceil($total_prestasi / $prestasi_per_page);
    // --- Akhir Paginasi ---

    // Opsi untuk filter Tingkat (bisa diambil dari ENUM di DB jika dinamis)
    $tingkat_options = ['Internal Kampus', 'Kota/Kabupaten', 'Provinsi', 'Regional', 'Nasional', 'Internasional'];
    // Opsi untuk filter Tahun (misal 5 tahun terakhir + semua)
    $current_year = date('Y');
    $tahun_options = [];
    for ($i = 0; $i < 5; $i++) {
        $tahun_options[] = $current_year - $i;
    }
    ?>

    <!-- Section Utama (menggunakan class .trending-topics dari CSS) -->
    <section class="trending-topics">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <p class="welcome-title">PRESTASI UKIM UNESA</p>
            <h2 class="welcome-message">
                Mengukir <span class="highlight">jejak gemilang</span> 🏆, wujud nyata <span class="highlight2">dedikasi</span> dan
                <span class="highlight">kerja keras</span> 🧠. Inilah <span class="highlight2">pencapaian</span> ✨ anggota kami.
            </h2>
        </section>

        <!-- Filter Section -->
        <section class="filter-section">
            <form method="GET" action="list-prestasi.php">
                <label for="tingkat">Filter Tingkat:</label>
                <select name="tingkat" id="tingkat">
                    <option value="">Semua Tingkat</option>
                    <?php foreach ($tingkat_options as $tingkat_opt): ?>
                        <option value="<?php echo htmlspecialchars($tingkat_opt); ?>" <?php echo ($selected_tingkat == $tingkat_opt) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tingkat_opt); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="tahun">Filter Tahun:</label>
                <select name="tahun" id="tahun">
                    <option value="">Semua Tahun</option>
                    <?php foreach ($tahun_options as $tahun_opt): ?>
                        <option value="<?php echo $tahun_opt; ?>" <?php echo ($selected_tahun == $tahun_opt) ? 'selected' : ''; ?>>
                            <?php echo $tahun_opt; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Filter</button>
            </form>
        </section>

        <!-- Container untuk card prestasi (menggunakan .articles-container) -->
        <div class="articles-container">
            <?php
            if ($prestasi_list && count($prestasi_list) > 0) {
                foreach ($prestasi_list as $prestasi) { // Variabel loop adalah $prestasi
            ?>
            <!-- PERBAIKAN DI SINI: Gunakan $prestasi['id_prestasi'] dan nama file yang benar -->
            <a href="prestasi.php?id=<?php echo htmlspecialchars($prestasi['id_prestasi']); ?>" class="article-card" style="text-decoration:none; color:inherit;">
                <span class="category">
                    <i class="fas fa-trophy"></i> <?php echo htmlspecialchars($prestasi['tingkat']); ?> - <?php echo htmlspecialchars($prestasi['tahun']); ?>
                </span>
                <h3><?php echo htmlspecialchars($prestasi['nama_lomba']); ?></h3>
                <p style="font-weight:bold; color: var(--primary-color);">
                    <?php echo htmlspecialchars($prestasi['juara']); ?>
                </p>
                <p>
                    <strong>Peserta/Tim:</strong><br>
                    <?php echo nl2br(htmlspecialchars($prestasi['peserta_nama'])); ?>
                    <?php if($prestasi['is_tim'] == 1): ?>
                        <small>(Tim)</small>
                    <?php endif; ?>
                </p>
                <?php if (!empty($prestasi['penyelenggara'])): ?>
                <p style="font-size: 0.85em; color: #777;">
                    <strong>Penyelenggara:</strong> <?php echo htmlspecialchars($prestasi['penyelenggara']); ?>
                </p>
                <?php endif; ?>

                <div class="date-info">
                    <?php if (!empty($prestasi['bukti'])): ?>
                        <a href="<?php echo htmlspecialchars($prestasi['bukti']); ?>" target="_blank" style="color: var(--accent-color); text-decoration:none; font-weight:500;" onclick="event.stopPropagation();">
                            <i class="fas fa-award"></i> Lihat Bukti
                        </a>
                    <?php else: ?>
                        <span><i class="fas fa-info-circle"></i> Dicatat oleh: <?php echo htmlspecialchars($prestasi['pencatat_name'] ?? 'Admin'); ?></span>
                    <?php endif; ?>
                </div>
            </a>
            <?php
                } // End foreach
            } else {
                echo "<p class='no-articles'>Tidak ada data prestasi yang ditemukan sesuai filter.</p>";
            }
            ?>
        </div>

        <!-- Paginasi Links -->
        <?php if ($total_pages > 1): ?>
        <nav class="pagination-container" aria-label="Paginasi Prestasi">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&tingkat=<?php echo urlencode($selected_tingkat); ?>&tahun=<?php echo $selected_tahun; ?>">Sebelumnya</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&tingkat=<?php echo urlencode($selected_tingkat); ?>&tahun=<?php echo $selected_tahun; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&tingkat=<?php echo urlencode($selected_tingkat); ?>&tahun=<?php echo $selected_tahun; ?>">Berikutnya</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

    </section>

    <?php
    include_once('./layout/footer_index.php');
    ?>
</body>
</html>