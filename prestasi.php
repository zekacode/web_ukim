<?php
require_once('./conn/controller.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once('./layout/header_index.php');

$prestasi_item = null;
$error_message_prestasi = null;

// Mengambil prestasi berdasarkan ID dari parameter GET (karena prestasi biasanya tidak pakai slug)
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id_prestasi = (int)$_GET['id'];
    $prestasi_item = get_prestasi_by_id($id_prestasi); // Fungsi ini sudah ada di controller
    if (!$prestasi_item) {
        $error_message_prestasi = "Detail prestasi tidak ditemukan atau mungkin telah dihapus.";
    }
} else {
    $error_message_prestasi = "Parameter prestasi tidak valid.";
}

// Jika ada error atau prestasi tidak ditemukan
if ($error_message_prestasi) {
    echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Prestasi Tidak Ditemukan</title><link rel='stylesheet' href='./style/prestasi-detail.css'></head><body>";
    echo "<div class='container error-page-container'>";
    echo "<h1>Prestasi Tidak Ditemukan</h1>";
    echo "<p>" . htmlspecialchars($error_message_prestasi) . "</p>";
    echo "<a href='list-prestasi.php' class='btn-back-custom'>Kembali ke Daftar Prestasi</a>";
    echo "</div>";
    include_once('./layout/footer_index.php');
    echo "</body></html>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($prestasi_item['nama_lomba']); ?> - Prestasi UKIM Unesa</title>
    <link rel="stylesheet" href="./style/prestasi.css">
    <!-- FontAwesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <main class="container">
        <div class="prestasi-detail-page">
            <header class="prestasi-header">
                <h1 class="prestasi-title"><?php echo htmlspecialchars($prestasi_item['nama_lomba']); ?></h1>
                <p class="prestasi-juara"><?php echo htmlspecialchars($prestasi_item['juara']); ?></p>
                <div class="prestasi-meta">
                    <span class="meta-item">
                        <i class="fas fa-medal"></i>Tingkat: <?php echo htmlspecialchars($prestasi_item['tingkat']); ?>
                    </span>
                    <span class="meta-item">
                        <i class="fas fa-calendar-alt"></i>Tahun: <?php echo htmlspecialchars($prestasi_item['tahun']); ?>
                    </span>
                    <?php if (!empty($prestasi_item['penyelenggara'])): ?>
                    <span class="meta-item">
                        <i class="fas fa-building"></i>Penyelenggara: <?php echo htmlspecialchars($prestasi_item['penyelenggara']); ?>
                    </span>
                    <?php endif; ?>
                </div>
            </header>

            <section class="prestasi-body">
                <div class="prestasi-section">
                    <h4><i class="fas fa-users"></i> Peserta / Tim Peraih Prestasi</h4>
                    <p><?php echo nl2br(htmlspecialchars($prestasi_item['peserta_nama'])); ?></p>
                    <?php if($prestasi_item['is_tim'] == 1): ?>
                        <p><small><em>(Prestasi ini diraih secara tim)</em></small></p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($prestasi_item['deskripsi'])): ?>
                <div class="prestasi-section">
                    <h4><i class="fas fa-info-circle"></i> Deskripsi Tambahan</h4>
                    <p><?php echo nl2br(htmlspecialchars($prestasi_item['deskripsi'])); ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($prestasi_item['bukti'])): ?>
                <div class="bukti-container">
                    <h4><i class="fas fa-award"></i> Bukti Pencapaian</h4>
                    <?php
                    $bukti_path = htmlspecialchars($prestasi_item['bukti']);
                    // Asumsi path di DB: ./asset/bukti_prestasi/namafile.jpg
                    // Dari root project, pathnya sudah benar.
                    $bukti_ext = strtolower(pathinfo($bukti_path, PATHINFO_EXTENSION));
                    if (in_array($bukti_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])):
                    ?>
                        <img src="<?php echo $bukti_path; ?>" alt="Bukti Prestasi" class="bukti-preview-image">
                        <p style="margin-top:10px;"><a href="<?php echo $bukti_path; ?>" target="_blank" class="bukti-link"><i class="fas fa-external-link-alt"></i> Lihat Gambar Penuh</a></p>
                    <?php else: // Untuk PDF atau file lain ?>
                        <p>
                            <a href="<?php echo $bukti_path; ?>" target="_blank" class="bukti-link">
                                <i class="fas fa-file-download"></i> Unduh Bukti (<?php echo htmlspecialchars(basename($bukti_path)); ?>)
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </section>

            <div class="back-button-container">
                <a href="list-prestasi.php" class="btn-back-custom">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Prestasi
                </a>
            </div>
        </div>
    </main>

    <?php include_once('./layout/footer_index.php'); ?>
</body>
</html>