<?php
// event-detail.php

// 1. Panggil controller dan mulai session (jika belum)
require_once('./conn/controller.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Meskipun untuk halaman publik mungkin tidak selalu butuh session aktif
}

// 2. Sertakan header utama situs
include_once('./layout/header_index.php');

// 3. Logika pengambilan data event
$event_detail = null;
$error_message_event = null;

if (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $slug = $_GET['slug'];
    $event_detail = get_event_by_slug($slug); // Fungsi ini ada di controller.php
    if (!$event_detail) {
        $error_message_event = "Event yang Anda cari tidak ditemukan atau mungkin telah berakhir.";
    }
} else {
    $error_message_event = "Parameter event tidak valid.";
}

// Jika ada error atau event tidak ditemukan, tampilkan pesan dan hentikan
if ($error_message_event) {
    // Tampilkan header minimal jika error sebelum HTML utama
    echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Event Tidak Ditemukan</title><link rel='stylesheet' href='./style/event.css'></head><body>";
    echo "<div class='container error-page-container'>"; // Class khusus untuk halaman error
    echo "<h1>Event Tidak Ditemukan</h1>";
    echo "<p>" . htmlspecialchars($error_message_event) . "</p>";
    echo "<a href='list-event.php' class='btn btn-primary-custom'>Kembali ke Daftar Event</a>";
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
    <title><?php echo htmlspecialchars($event_detail['judul']); ?> - Event UKIM Unesa</title>
    <link rel="stylesheet" href="./style/event.css">
    <!-- FontAwesome untuk ikon (jika digunakan di header atau footer, tidak perlu lagi) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php // Bagian utama halaman hanya ditampilkan jika $event_detail ada ?>
    <main class="event-detail-page">
        <!-- Event Hero Section -->
        <section class="event-hero">
            <div class="event-info">
                <p class="location">
                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event_detail['lokasi']); ?>
                </p>
                <h1><?php echo htmlspecialchars($event_detail['judul']); ?></h1>
                <?php if (!empty($event_detail['tema'])): ?>
                    <h2><?php echo htmlspecialchars($event_detail['tema']); ?></h2>
                <?php endif; ?>

                <div class="event-actions">
                    <?php if (!empty($event_detail['link_pendaftaran']) && ($event_detail['status'] == 'upcoming' || $event_detail['status'] == 'ongoing')): ?>
                        <a href="<?php echo htmlspecialchars($event_detail['link_pendaftaran']); ?>" target="_blank" class="event-button primary">
                            <i class="fas fa-edit"></i> Daftar Sekarang
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($event_detail['link_info'])): ?>
                         <a href="<?php echo htmlspecialchars($event_detail['link_info']); ?>" target="_blank" class="event-button secondary">
                            <i class="fas fa-info-circle"></i> Info Selengkapnya
                        </a>
                    <?php endif; ?>
                </div>

                <div class="event-description">
                    <?php echo nl2br(htmlspecialchars($event_detail['deskripsi'])); ?>
                </div>
            </div>
            <div class="event-carousel">
                <?php
                $gambar_path = htmlspecialchars($event_detail['gambar']);
                // Asumsi path di DB: ./asset/gbr_event/namafile.jpg
                // Dari root project, pathnya sudah benar.
                ?>
                <?php if (!empty($event_detail['gambar'])): ?>
                    <img src="<?php echo $gambar_path; ?>" alt="Poster <?php echo htmlspecialchars($event_detail['judul']); ?>">
                <?php else: ?>
                    <img src="./asset/placeholder-event-detail.png" alt="Placeholder Event UKIM Unesa">
                <?php endif; ?>
            </div>
        </section>

        <!-- Countdown Section -->
        <?php
        $event_start_time = strtotime($event_detail['tgl_event_mulai']);
        $is_event_active_for_countdown = ($event_detail['status'] == 'upcoming' || ($event_detail['status'] == 'ongoing' && $event_start_time > time()));

        if ($event_start_time && $is_event_active_for_countdown):
        ?>
        <section class="countdown-section" id="countdown-container-<?php echo $event_detail['id_event']; ?>">
            <h3 class="countdown-title">Hitung Mundur Menuju Event</h3>
            <div class="countdown-timer">
                <!-- Konten countdown akan diisi oleh JavaScript -->
                <div class="countdown-item"><h4>00</h4><span>Hari</span></div>
                <div class="countdown-item"><h4>00</h4><span>Jam</span></div>
                <div class="countdown-item"><h4>00</h4><span>Menit</span></div>
                <div class="countdown-item"><h4>00</h4><span>Detik</span></div>
            </div>
        </section>
        <?php elseif ($event_detail['status'] == 'past' || ($event_start_time && $event_start_time <= time() && $event_detail['status'] != 'upcoming')): ?>
        <section class="event-status-message past">
            <h3>Event Telah Berakhir</h3>
        </section>
        <?php elseif ($event_detail['status'] == 'cancelled'): ?>
        <section class="event-status-message cancelled">
            <h3>Event Dibatalkan</h3>
        </section>
        <?php endif; ?>

        <div class="back-button-container">
            <a href="list-event.php" class="btn btn-secondary-custom">
                <i class="fas fa-arrow-left"></i> Kembali ke Semua Event
            </a>
        </div>
    </main>

    <?php include_once('./layout/footer_index.php'); ?>

    <?php // Script hanya dimuat jika countdown perlu ditampilkan ?>
    <?php if ($event_start_time && $is_event_active_for_countdown): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            initializeCountdown(
                "<?php echo date('Y-m-d\TH:i:s', $event_start_time); ?>",
                "countdown-container-<?php echo $event_detail['id_event']; ?>"
            );
        });
    </script>
    <?php endif; ?>
</body>
</html>