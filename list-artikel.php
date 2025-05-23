<?php
require_once('./conn/controller.php');
include_once('./layout/header_index.php');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Artikel - UKIM Unesa</title>
    <link rel="stylesheet" href="./style/list.css">
</head>
<body>
    <?php
    // Mengambil departemen yang dipilih (ID departemen)
    $selected_department_id = null;
    if (isset($_GET['id_dept']) && ctype_digit($_GET['id_dept']) && $_GET['id_dept'] > 0) {
        $selected_department_id = (int)$_GET['id_dept'];
    }

    // --- Paginasi Sederhana (Opsional, bisa dikembangkan) ---
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $articles_per_page = 9; // Jumlah artikel per halaman
    $offset = ($page - 1) * $articles_per_page;

    // Mengambil data artikel yang sudah difilter dan dipaginasi
    // Hanya ambil artikel dengan status 'published'
    $articles = get_all_blogs_filtered($articles_per_page, $offset, $selected_department_id, 'published');
    $total_articles = count_all_blogs_filtered($selected_department_id, 'published');
    $total_pages = ceil($total_articles / $articles_per_page);
    // --- Akhir Paginasi Sederhana ---


    // Mengambil semua departemen untuk filter dropdown
    $departments = get_all_departments();
    ?>

    <!-- Artikel Section -->
    <section class="trending-topics">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <p class="welcome-title">ARTIKEL UKIM UNESA</p> <?php // Judul lebih ringkas ?>
            <h2 class="welcome-message">
                Jelajahi rekam jejak <span class="highlight">kegiatan</span> dan
                <span class="highlight2">program kerja 📆</span> kami, dirangkum dalam
                <span class="highlight">artikel informatif 📰</span>. Temukan inspirasi untuk
                <span class="highlight2">kemajuan bersama 🤝</span>.
            </h2>
        </section>

        <!-- Filter Section -->
        <section class="filter-section">
            <form method="GET" action="list-artikel.php">
                <label for="id_dept">Filter Berdasarkan Departemen:</label>
                <select name="id_dept" id="id_dept">
                    <option value="">Semua Departemen</option>
                    <?php if ($departments): ?>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo htmlspecialchars($dept['id_dept']); ?>" <?php echo ($selected_department_id == $dept['id_dept']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept['nama_dept']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <button type="submit">Filter</button>
            </form>
        </section>

        <!-- Articles Section -->
        <div class="articles-container">
            <?php
            if ($articles && count($articles) > 0) {
                foreach ($articles as $article) {
            ?>
            <a href="artikel.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" class="article-card" style="text-decoration: none; color: inherit;">
                <span class="category"><?php echo htmlspecialchars($article['nama_dept'] ?? 'Umum'); ?></span>
                <?php
                $gambar_path = htmlspecialchars($article['gambar']);
                // if (strpos($gambar_path, './') !== 0) { // Penyesuaian path jika perlu
                //     $gambar_path = './' . ltrim($gambar_path, '/');
                // }
                ?>
                <?php if (!empty($article['gambar'])): ?>
                <img src="<?php echo $gambar_path; ?>" alt="Gambar untuk <?php echo htmlspecialchars($article['judul']); ?>" class="article-image">
                <?php else: ?>
                <img src="./asset/placeholder-image.png" alt="Placeholder" class="article-image"> <?php // Sediakan placeholder ?>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($article['judul']); ?></h3>
                <p>
                    <?php
                    // Ambil isi dari DB, potong jika terlalu panjang (misal 15-20 kata)
                    $words = explode(" ", strip_tags($article['isi'])); // strip_tags untuk keamanan jika isi ada HTML
                    $limited_text = implode(" ", array_slice($words, 0, 15));
                    echo htmlspecialchars($limited_text) . (count($words) > 15 ? "..." : "");
                    ?>
                </p>
                <div class="date-info">
                    <span><?php echo date("d M Y", strtotime($article['created_at'])); ?></span>
                </div>
            </a>
            <?php
                } // End foreach
            } else {
                if ($selected_department_id) {
                    echo "<p class='no-articles'>Tidak ada artikel yang ditemukan untuk departemen ini.</p>";
                } else {
                    echo "<p class='no-articles'>Belum ada artikel yang dipublikasikan.</p>";
                }
            }
            ?>
        </div>

        <!-- Paginasi Links -->
        <?php if ($total_pages > 1): ?>
        <nav class="pagination-container" aria-label="Paginasi Artikel">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&id_dept=<?php echo $selected_department_id; ?>">Sebelumnya</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&id_dept=<?php echo $selected_department_id; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&id_dept=<?php echo $selected_department_id; ?>">Berikutnya</a></li>
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