<?php
include('./layout/header_index.php');

// Mengambil kategori yang dipilih
$kategoriDipilih = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Membuat query untuk mengambil data artikel
$sql = "SELECT id_karya, tgl, kategori, judul, isi FROM karya_cipta";
if (!empty($kategoriDipilih)) {
    $sql .= " WHERE kategori = '" . mysqli_real_escape_string($conn, $kategoriDipilih) . "'";
}

$result = mysqli_query($conn, $sql);
?>

<head>
    <link rel="stylesheet" href="./style/list.css">
</head>
<body>
    <!-- Artikel Section -->
    <section class="trending-topics">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <p class="welcome-title">KARYA CIPTA</p>
            <h2 class="welcome-message">
                Media bagi pengurus dan anggota <span class="highlight">UKIM Unesa 👥</span> untuk menyalurkan hasil  
                <span class="highlight">karyanya 📕</span> baik berupa <span class="highlight">karya ilmiah </span> maupun <span class="highlight2">karya non ilmiah ✍️</span>
            </h2>
        </section>
        
        <!-- Filter Section -->
        <section class="filter-section">
            <form method="GET" action="list-karya.php">
                <label for="kategori">Filter Berdasarkan Kategori:</label>
                <select name="kategori" id="kategori">
                    <option value="">Semua Kategori</option>
                    <?php
                    // Mengambil kategori unik dari database
                    $kategoriQuery = "SELECT DISTINCT kategori FROM karya_cipta";
                    $kategoriResult = mysqli_query($conn, $kategoriQuery);
                    while ($kategoriRow = mysqli_fetch_array($kategoriResult)) {
                        $selected = $kategoriDipilih == $kategoriRow['kategori'] ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($kategoriRow['kategori']) . "' $selected>" . htmlspecialchars($kategoriRow['kategori']) . "</option>";
                    }
                    ?>
                </select>
                <button type="submit">Filter</button>
            </form>
        </section>
        
        <!-- Articles Section -->
        <div class="articles-container">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
            ?>
            <a href="karya.php?id=<?= htmlspecialchars($row['id_karya']); ?>" class="article-card" style="text-decoration: none; color: inherit;">
                <span class="category"><?= htmlspecialchars($row['kategori']); ?></span>
                <h3><?= htmlspecialchars($row['judul']); ?></h3>
                <p>
                    <?php
                    $words = explode(" ", $row['isi']);
                    $limited_text = implode(" ", array_slice($words, 0, 10));
                    echo htmlspecialchars($limited_text) . (count($words) > 10 ? "..." : "");
                    ?>
                </p>
                <div class="date-info">
                    <span><?= date("d/m/Y", strtotime($row['tgl'])); ?></span>
                </div>
            </a>
            <?php
                }
            } else {
                echo "<p>Tidak ada artikel dalam kategori ini.</p>";
            }
            ?>
        </div>
    </section>

    <?php
    include('./layout/footer_index.php');
    ?>
</body>
</html>
