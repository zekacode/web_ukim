<?php
require_once('./conn/controller.php'); // Panggil controller di awal
include_once('./layout/header_index.php');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Event - UKIM Unesa</title>
    <link rel="stylesheet" href="./style/list.css">
    <!-- Tambahkan FontAwesome jika ikon digunakan -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php
    // --- Paginasi Sederhana ---
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $events_per_page = 6; // Jumlah event per halaman, bisa disesuaikan
    $offset = ($page - 1) * $events_per_page;

    // Mengambil data event yang statusnya 'upcoming' atau 'ongoing', diurutkan dari yang paling dekat
    $event_list = get_all_events_filtered($events_per_page, $offset, ['upcoming', 'ongoing'], 'tgl_event_mulai ASC');
    $total_events = count_all_events_filtered(['upcoming', 'ongoing']);
    $total_pages = ceil($total_events / $events_per_page);
    // --- Akhir Paginasi Sederhana ---
    ?>

    <!-- Artikel Section (Class dari CSS Anda) -->
    <section class="trending-topics">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <p class="welcome-title">EVENT UKIM UNESA</p>
            <h2 class="welcome-message">
                Ada <span class="highlight2">acara seru</span> 🎫 menunggumu!<br>
                Lihat <span class="highlight">detailnya</span> 🔍, <span class="highlight2">hitung mundur</span> ⏱️, dan jangan lupa untuk <span class="highlight">mendaftar</span> 📝.
            </h2>
        </section>

        <?php // Untuk list-event.php publik, mungkin tidak perlu filter, tampilkan semua yang relevan (upcoming/ongoing) ?>
        <?php /*
        <!-- Filter Section (Jika diperlukan filter status di halaman publik) -->
        <section class="filter-section">
            <form method="GET" action="list-event.php">
                <label for="status_event">Filter Berdasarkan Status:</label>
                <select name="status_event" id="status_event">
                    <option value="">Semua (Upcoming & Ongoing)</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="past">Past Events</option>
                </select>
                <button type="submit">Filter</button>
            </form>
        </section>
        */ ?>

        <!-- Articles Section (Class dari CSS Anda, di sini untuk menampung event) -->
        <div class="articles-container">
            <?php
            if ($event_list && count($event_list) > 0) {
                foreach ($event_list as $event) {
            ?>
            <a href="event.php?slug=<?php echo htmlspecialchars($event['slug']); ?>" class="article-card" style="text-decoration: none; color: inherit;">
                <span class="category">
                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['lokasi']); ?>
                </span>
                <?php
                $gambar_path = htmlspecialchars($event['gambar']);
                // if (strpos($gambar_path, './') !== 0) { // Penyesuaian path jika perlu
                //     $gambar_path = './' . ltrim($gambar_path, '/');
                // }
                ?>
                <?php if (!empty($event['gambar'])): ?>
                <img src="<?php echo $gambar_path; ?>" alt="Gambar untuk <?php echo htmlspecialchars($event['judul']); ?>" class="article-image">
                <?php else: ?>
                <img src="./asset/placeholder-event.png" alt="Placeholder Event" class="article-image"> <?php // Sediakan placeholder ?>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($event['judul']); ?></h3>
                <?php if (!empty($event['tema'])): ?>
                    <p style="font-style: italic; color: #555; font-size: 0.9em; margin-bottom: 10px;">
                        Tema: <?php echo htmlspecialchars($event['tema']); ?>
                    </p>
                <?php endif; ?>
                <div class="date-info">
                    <span>
                        <i class="fas fa-calendar-alt"></i>
                        <?php echo date("d M Y, H:i", strtotime($event['tgl_event_mulai'])); ?>
                         <?php if ($event['status'] == 'upcoming'): ?>
                            <span style="background-color: #17a2b8; color: white; padding: 2px 6px; border-radius: 3px; font-size: 0.8em; margin-left: 5px;">Upcoming</span>
                        <?php elseif ($event['status'] == 'ongoing'): ?>
                            <span style="background-color: #ffc107; color: #212529; padding: 2px 6px; border-radius: 3px; font-size: 0.8em; margin-left: 5px;">Sedang Berlangsung</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="event-links" style="margin-top: 10px;">
                    <?php if (!empty($event['link_pendaftaran'])): ?>
                        <span class="btn-event-action register" onclick="event.stopPropagation(); window.open('<?php echo htmlspecialchars($event['link_pendaftaran']); ?>', '_blank');">
                            <i class="fas fa-edit"></i> Daftar
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($event['link_info'])): ?>
                        <span class="btn-event-action info" onclick="event.stopPropagation(); window.open('<?php echo htmlspecialchars($event['link_info']); ?>', '_blank');">
                            <i class="fas fa-info-circle"></i> Info Lanjut
                        </span>
                    <?php endif; ?>
                </div>
            </a>
            <?php
                } // End foreach
            } else {
                echo "<p class='no-articles'>Belum ada event yang akan datang atau sedang berlangsung.</p>"; // Gunakan class no-articles
            }
            ?>
        </div>

        <!-- Paginasi Links (Sama seperti sebelumnya) -->
        <?php if ($total_pages > 1): ?>
        <nav class="pagination-container" aria-label="Paginasi Event">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Sebelumnya</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Berikutnya</a></li>
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