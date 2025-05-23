<?php
require_once('./conn/controller.php'); // Panggil controller di awal
include_once('./layout/header_index.php');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Karya Cipta - UKIM Unesa</title>
    <link rel="stylesheet" href="./style/list.css">
</head>
<body>
    <?php
    // Mengambil kategori yang dipilih (ID kategori)
    $selected_category_id = null;
    if (isset($_GET['id_category']) && ctype_digit($_GET['id_category']) && $_GET['id_category'] > 0) {
        $selected_category_id = (int)$_GET['id_category'];
    }

    // --- Paginasi Sederhana ---
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $karya_per_page = 9; // Jumlah karya per halaman
    $offset = ($page - 1) * $karya_per_page;

    // Mengambil data karya yang sudah difilter dan dipaginasi
    // Hanya ambil karya dengan status 'published'
    $karya_list = get_all_karya_filtered($karya_per_page, $offset, $selected_category_id, 'published');
    $total_karya = count_all_karya_filtered($selected_category_id, 'published');
    $total_pages = ceil($total_karya / $karya_per_page);
    // --- Akhir Paginasi Sederhana ---

    // Mengambil semua kategori karya untuk filter dropdown
    $karya_categories = get_all_karya_categories();
    ?>

    <!-- Artikel Section (Class dari CSS Anda) -->
    <section class="trending-topics">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <p class="welcome-title">KARYA CIPTA UKIM UNESA</p>
            <h2 class="welcome-message">
                Media bagi pengurus dan anggota <span class="highlight">UKIM Unesa 👥</span> untuk menyalurkan hasil
                <span class="highlight">karyanya 📕</span> baik berupa <span class="highlight">karya ilmiah </span> maupun <span class="highlight2">karya non ilmiah ✍️</span>.
            </h2>
        </section>

        <!-- Filter Section -->
        <section class="filter-section">
            <form method="GET" action="list-karya.php">
                <label for="id_category">Filter Berdasarkan Kategori:</label>
                <select name="id_category" id="id_category">
                    <option value="">Semua Kategori</option>
                    <?php if ($karya_categories): ?>
                        <?php foreach ($karya_categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id_category']); ?>" <?php echo ($selected_category_id == $category['id_category']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['nama_category']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <button type="submit">Filter</button>
            </form>
        </section>

        <!-- Articles Section (Class dari CSS Anda, di sini untuk menampung karya) -->
        <div class="articles-container">
            <?php
            if ($karya_list && count($karya_list) > 0) {
                foreach ($karya_list as $karya) {
            ?>
            <a href="karya.php?slug=<?php echo htmlspecialchars($karya['slug']); ?>" class="article-card" style="text-decoration: none; color: inherit;">
                <span class="category"><?php echo htmlspecialchars($karya['nama_category'] ?? 'Lainnya'); ?></span>
                <?php // Karya cipta mungkin tidak selalu punya gambar utama, bisa fokus ke judul dan deskripsi ?>
                <?php // Jika ingin ada gambar, pastikan ada field gambar di tabel karya_cipta dan diambil controllernya ?>
                <h3><?php echo htmlspecialchars($karya['judul']); ?></h3>
                <p>
                    <?php
                    // Ambil isi dari DB, potong jika terlalu panjang
                    $words = explode(" ", strip_tags($karya['isi']));
                    $limited_text = implode(" ", array_slice($words, 0, 15));
                    echo htmlspecialchars($limited_text) . (count($words) > 15 ? "..." : "");
                    ?>
                </p>
                <div class="date-info">
                    <span>Dipublikasikan: <?php echo date("d M Y", strtotime($karya['created_at'])); ?></span>
                </div>
                 <?php if (!empty($karya['file_pendukung'])): ?>
                    <div class="file-info" style="margin-top: 5px; font-size: 0.85em;">
                        <i class="fas fa-paperclip"></i> <!-- Asumsi FontAwesome -->
                        <a href="<?php echo htmlspecialchars($karya['file_pendukung']); ?>" target="_blank" onclick="event.stopPropagation();" style="color: var(--accent-color, #007bff);">
                            Lihat/Unduh File
                        </a>
                    </div>
                <?php endif; ?>
            </a>
            <?php
                } // End foreach
            } else {
                if ($selected_category_id) {
                    echo "<p class='no-articles'>Tidak ada karya yang ditemukan untuk kategori ini.</p>"; // Gunakan class no-articles dari list.css
                } else {
                    echo "<p class='no-articles'>Belum ada karya yang dipublikasikan.</p>";
                }
            }
            ?>
        </div>

        <!-- Paginasi Links (Sama seperti list-artikel.php) -->
        <?php if ($total_pages > 1): ?>
        <nav class="pagination-container" aria-label="Paginasi Karya Cipta">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&id_category=<?php echo $selected_category_id; ?>">Sebelumnya</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&id_category=<?php echo $selected_category_id; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&id_category=<?php echo $selected_category_id; ?>">Berikutnya</a></li>
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