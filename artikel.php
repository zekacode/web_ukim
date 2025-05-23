<?php
require_once('./conn/controller.php'); // Panggil controller di awal
include_once('./layout/header_index.php');

$article = null; // Inisialisasi variabel artikel

// Mengambil artikel berdasarkan SLUG dari parameter GET
if (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $slug = $_GET['slug'];
    $article = get_blog_by_slug($slug);
}

// Jika artikel tidak ditemukan atau tidak ada parameter, tampilkan pesan atau redirect
if (!$article) {
    echo "<head><link rel='stylesheet' href='./style/page.css'></head><body>";
    echo "<div class='container error-container'>"; // Tambahkan class error-container jika perlu styling khusus
    echo "<h1 class='judul'>Artikel Tidak Ditemukan</h1>"; // Gunakan class .judul
    echo "<div class='content'><p>Maaf, artikel yang Anda cari tidak tersedia atau telah dihapus.</p></div>"; // Gunakan .content p
    echo "<a href='list-artikel.php' class='btn-back' style='display: block; text-align: center; margin-top: 20px; text-decoration: none; background-color: var(--secondary-color); color: white; padding: 10px 20px; border-radius: 5px;'>Kembali ke Daftar Artikel</a>"; // Styling inline sederhana untuk tombol
    echo "</div>";
    include_once('./layout/footer_index.php');
    echo "</body></html>";
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['judul']); ?> - UKIM Unesa</title>
    <link rel="stylesheet" href="./style/page.css">
    <!-- Jika header_index.php sudah punya tag <head>, ini bisa dihapus -->
    <!-- Tambahkan FontAwesome jika ikon digunakan -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Bagian header artikel -->
        <header>
            <h1 class="judul"><?php echo htmlspecialchars($article['judul']); ?></h1>
        </header>

        <!-- Bagian meta informasi (tanggal, kategori, penulis) -->
        <section class="content">
            <p style="font-size: 0.9rem; color: #777; margin-bottom: 20px;">
                <span class="meta-item category-tag">
                    <i class="fas fa-folder-open"></i>
                    <?php echo htmlspecialchars($article['nama_dept'] ?? 'Umum'); ?>
                </span>
                 | 
                <span class="meta-item published-date">
                    <i class="fas fa-calendar-alt"></i>
                    Dipublikasikan pada: <?php echo date("d F Y", strtotime($article['created_at'])); ?>
                </span>
                <?php if (!empty($article['author_name'])): ?>
                 | 
                <span class="meta-item author-name">
                    <i class="fas fa-user"></i>
                    Oleh: <?php echo htmlspecialchars($article['author_name']); ?>
                </span>
                <?php endif; ?>
            </p>
        </section>

        <!-- Gambar utama artikel -->
        <?php
        $gambar_path = htmlspecialchars($article['gambar']);
        ?>
        <?php if (!empty($article['gambar'])): ?>
            <img src="<?php echo $gambar_path; ?>" alt="Gambar <?php echo htmlspecialchars($article['judul']); ?>" class="image">
        <?php endif; ?>

        <!-- Isi utama artikel -->
        <section class="article">
            <div class="article-content">
                <?php echo nl2br($article['isi']); ?>
            </div>
        </section>

        <!-- Tombol kembali (opsional, jika ingin styling beda dari CSS) -->
        <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
            <a href="list-artikel.php" style="text-decoration: none; background-color: var(--secondary-color); color: white; padding: 10px 25px; border-radius: 5px; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Artikel
            </a>
        </div>
    </div>

    <?php
    include_once('./layout/footer_index.php');
    ?>
</body>
</html>