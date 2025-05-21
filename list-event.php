<?php
include('./layout/header_index.php');

// Jalankan query
$sql = "SELECT id_event, judul, tema, deskripsi, tgl_event, lokasi, gambar, link FROM events";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error); // Debug jika query gagal
}
?>

<head>
    <link rel="stylesheet" href="./style/list.css">
</head>
<body>
    <!-- Artikel Section -->
    <section class="trending-topics">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <p class="welcome-title">EVENT</p>
            <h2 class="welcome-message">
                Ada <span class="highlight2">acara seru</span> 🎫 menunggumu!<br> 
                Lihat <span class="highlight">detailnya</span> 🔍, <span class="highlight2">hitung mundur</span> ⏱️, dan jangan lupa untuk <span class="highlight">mendaftar</span> 📝
            </h2>
        </section>

        <!-- Articles Section -->
        <div class="articles-container">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <a href="event.php?id=<?= htmlspecialchars($row['id_event']); ?>" class="article-card" style="text-decoration: none; color: inherit;">
                        <span class="category"><?= htmlspecialchars($row['lokasi']); ?></span>
                        <?php if (!empty($row['gambar']) && $row['gambar'] !== '-'): ?>
                            <img src="<?= htmlspecialchars($row['gambar']); ?>" alt="Image for <?= htmlspecialchars($row['judul']); ?>" class="article-image">
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($row['judul']); ?></h3>
                        <p>
                            <?php if (!empty($row['tema']) && $row['tema'] !== '-'): ?>
                                <?php
                                $words = explode(" ", $row['tema']);
                                $limited_text = implode(" ", array_slice($words, 0, 10));
                                echo htmlspecialchars($limited_text . (count($words) > 10 ? "..." : ""));
                                ?>
                            <?php endif; ?>
                        </p>
                        <div class="date-info">
                            <span><?= htmlspecialchars(date("d/m/Y", strtotime($row['tgl_event']))); ?></span>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Belum Ada Event</p>
            <?php endif; ?>
        </div>
    </section>

    <?php
    include('./layout/footer_index.php');
    ?>
</body>
</html>
