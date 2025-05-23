<?php
require_once('./conn/controller.php'); // Panggil controller di awal
include_once('./layout/header_index.php');

$karya_item = null; // Inisialisasi variabel

// Mengambil karya berdasarkan SLUG dari parameter GET
if (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $slug = $_GET['slug'];
    $karya_item = get_karya_by_slug($slug);
}
// Alternatif jika masih menggunakan ID:
/*
else if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id_karya = (int)$_GET['id'];
    $karya_item = get_karya_by_id($id_karya); // Pastikan filter status 'published'
}
*/

// Jika karya tidak ditemukan atau tidak ada parameter
if (!$karya_item) {
    echo "<head><link rel='stylesheet' href='./style/page.css'></head><body>";
    echo "<div class='container error-container'>"; // Sesuaikan class jika perlu
    echo "<h1 class='judul'>Karya Tidak Ditemukan</h1>";
    echo "<div class='content'><p>Maaf, karya yang Anda cari tidak tersedia atau telah dihapus.</p></div>";
    echo "<a href='list-karya.php' class='btn-back' style='display: block; text-align: center; margin-top: 20px; text-decoration: none; background-color: var(--secondary-color); color: white; padding: 10px 20px; border-radius: 5px;'>Kembali ke Daftar Karya</a>";
    echo "</div>";
    include_once('./layout/footer_index.php');
    echo "</body></html>";
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($karya_item['judul']); ?> - Karya Cipta UKIM Unesa</title>
    <link rel="stylesheet" href="./style/page.css">
    <!-- Tambahkan FontAwesome jika ikon digunakan -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1 class="judul"><?php echo htmlspecialchars($karya_item['judul']); ?></h1>
        </header>

        <section class="content">
            <p style="font-size: 0.9rem; color: #777; margin-bottom: 20px;">
                <span class="meta-item category-tag">
                    <i class="fas fa-tag"></i> <!-- Ikon kategori -->
                    Kategori: <?php echo htmlspecialchars($karya_item['nama_category'] ?? 'Tidak Dikategorikan'); ?>
                </span>
                 | 
                <span class="meta-item published-date">
                    <i class="fas fa-calendar-alt"></i>
                    Dipublikasikan: <?php echo date("d F Y", strtotime($karya_item['created_at'])); ?>
                </span>
                <?php if (!empty($karya_item['author_name'])): ?>
                 | 
                <span class="meta-item author-name">
                    <i class="fas fa-user"></i>
                    Oleh: <?php echo htmlspecialchars($karya_item['author_name']); ?>
                </span>
                <?php endif; ?>
            </p>
        </section>

        <?php // Karya cipta mungkin tidak memiliki "gambar utama" seperti blog, fokus ke isi dan file pendukung ?>

        <section class="article"> <?php // Tetap gunakan class .article untuk styling utama isi ?>
            <div class="article-content">
                <?php echo nl2br($karya_item['isi']); // Asumsi isi adalah teks, bukan HTML kompleks ?>
            </div>

            <?php if (!empty($karya_item['file_pendukung'])): ?>
                <div class="file-attachment" style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
                    <h4><i class="fas fa-paperclip"></i> File Pendukung:</h4>
                    <p>
                        <a href="<?php echo htmlspecialchars($karya_item['file_pendukung']); ?>" target="_blank" style="color: var(--accent-color, #007bff); text-decoration: none; font-weight: 500;">
                            <?php echo basename(htmlspecialchars($karya_item['file_pendukung'])); ?>
                        </a>
                        (Klik untuk melihat/mengunduh)
                    </p>
                </div>
            <?php endif; ?>
        </section>

        <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
            <a href="list-karya.php" style="text-decoration: none; background-color: var(--secondary-color); color: white; padding: 10px 25px; border-radius: 5px; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Karya
            </a>
        </div>
    </div>

    <?php
    include_once('./layout/footer_index.php');
    ?>
</body>
</html>