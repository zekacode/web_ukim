<?php
include('./layout/header_index.php');
?>

<head>
    <link rel="stylesheet" href="./style/list.css">
</head>
<body>
    <?php
    // Mengambil departemen yang dipilih
    $departemenDipilih = isset($_GET['departemen']) ? $_GET['departemen'] : '';

    // Membuat query untuk mengambil data artikel
    $sql = "SELECT id_blog, tgl, departemen, judul, gambar, isi FROM blog_ukim";
    if (!empty($departemenDipilih)) {
        $sql .= " WHERE departemen = '$departemenDipilih'";
    }

    $result = mysqli_query($conn, $sql);
    ?>

    <!-- Artikel Section -->
    <section class="trending-topics">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <p class="welcome-title">ARTIKEL</p>
            <h2 class="welcome-message">
                Rekam jejak <span class="highlight">kegiatan</span> dan, 
                <span class="highlight2">program kerja 📆</span> kami, dirangkum dalam 
                <span class="highlight">artikel informatif 📰</span> sebagai bentuk dedikasi dan inspirasi untuk 
                <span class="highlight2">kemajuan bersama 🤝</span>
            </h2>
        </section>
        
        <!-- Filter Section -->
        <section class="filter-section">
            <form method="GET" action="list-artikel.php">
                <label for="departemen">Filter Berdasarkan Departemen:</label>
                <select name="departemen" id="departemen">
                    <option value="">Semua Departemen</option>
                    <?php
                    // Mengambil departemen unik dari database
                    $departemenQuery = "SELECT DISTINCT departemen FROM blog_ukim";
                    $departemenResult = mysqli_query($conn, $departemenQuery);
                    while ($departemenRow = mysqli_fetch_array($departemenResult)) {
                        $selected = $departemenDipilih == $departemenRow['departemen'] ? 'selected' : '';
                        echo "<option value='{$departemenRow['departemen']}' $selected>{$departemenRow['departemen']}</option>";
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
            <a href="artikel.php?id=<?= $row['id_blog']; ?>" class="article-card" style="text-decoration: none; color: inherit;">
                <span class="category"><?= $row['departemen']; ?></span>
                <?php if (!empty($row['gambar']) && $row['gambar'] !== '-') { ?>
                <img src="<?= $row['gambar']; ?>" alt="Image for <?= $row['judul']; ?>" class="article-image">
                <?php } ?>
                <h3><?= $row['judul']; ?></h3>
                <p>
                    <?php
                    $words = explode(" ", $row['isi']);
                    $limited_text = implode(" ", array_slice($words, 0, 10));
                    echo $limited_text . (count($words) > 10 ? "..." : "");
                    ?>
                </p>
                <div class="date-info">
                    <span><?= date("d/m/Y", strtotime($row['tgl'])); ?></span>
                </div>
            </a>
            <?php
                }
            } else {
                echo "<p class='no-articles'>Tidak ada artikel dalam departemen ini.</p>";
            }
            ?>
        </div>
    </section>

    <?php
    include('./layout/footer_index.php');
    ?>
</body>
</html>
