<?php
include('./layout/header_index.php');
?>

<head>
    <link rel="stylesheet" href="./style/event.css">
</head>
<body>
    <!-- Event Hero Section -->
    <section class="event">
        <?php
        // Query untuk mengambil data event berdasarkan id_event dari parameter GET
        $sql = "SELECT id_event, judul, tema, deskripsi, tgl_event, lokasi, gambar, link, tgl FROM events WHERE id_event=" . $_GET['id'];
        $result = mysqli_query($conn, $sql); // Pastikan menggunakan $conn jika memakai MySQLi prosedural

        // Loop untuk menampilkan hasil query
        while ($row = mysqli_fetch_array($result)) {
        ?>
        <section class="event-hero">
            <div class="event-info">
                <p class="location"><?= htmlspecialchars($row['lokasi']); ?></p>
                <h1><?= htmlspecialchars($row['judul']); ?></h1>
                <?php if (!empty($row['tema']) && $row['tema'] !== '-') : ?>
                    <h2><?= htmlspecialchars($row['tema']); ?></h2>
                <?php endif; ?>
                <button class="event-button">
                    <a href="https://<?= htmlspecialchars($row['link']); ?>" target="_blank" style="text-decoration: none; color: inherit;">Daftar</a>
                </button>
                <p class="event-description"><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></p>
            </div>
            <div class="event-carousel">
                <img src="<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['judul']); ?>">
            </div>
        </section>

        <!-- Countdown Section -->
        <section class="countdown" id="countdown-<?= $row['id_event']; ?>">
            <div class="countdown-item">
                <h4>0</h4>
                <span>Days</span>
            </div>
            <div class="countdown-item">
                <h4>0</h4>
                <span>Hours</span>
            </div>
            <div class="countdown-item">
                <h4>0</h4>
                <span>Minutes</span>
            </div>
            <div class="countdown-item">
                <h4>0</h4>
                <span>Seconds</span>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const countdownElement<?= $row['id_event']; ?> = document.getElementById("countdown-<?= $row['id_event']; ?>");
                    initializeCountdown("<?= $row['tgl_event']; ?>", countdownElement<?= $row['id_event']; ?>);
                });
            </script>
        </section>
        <?php } ?>
    </section>

    <?php
    include('./layout/footer_index.php');
    ?>
</body>
</html>
