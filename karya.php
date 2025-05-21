<?php
include('./layout/header_index.php');
?>

<head>
    <link rel="stylesheet" href="./style/page.css">
</head>
<body>
    <div class="container">
        <?php
        // Query untuk mengambil data karya berdasarkan id_karya dari parameter GET
        $sql = "SELECT id_karya, tgl, kategori, judul, isi FROM karya_cipta WHERE id_karya=" . intval($_GET['id']);
        $result = mysqli_query($conn, $sql); // Pastikan menggunakan $conn jika memakai MySQLi prosedural

        // Loop untuk menampilkan hasil query
        while ($row = mysqli_fetch_array($result)) {
        ?>
        <header>
            <h1 class="judul"><?= htmlspecialchars($row['judul']); ?></h1>
        </header>
        <section class="content">
            <p><?= date("d/m/Y", strtotime($row['tgl'])); ?></p>
        </section>

        <section class="article">
            <div class="article-content">
                <p><?= nl2br(htmlspecialchars($row['isi'])); ?></p>
            </div>
        </section>
        <?php } ?>
    </div>

    <?php
    include('./layout/footer_index.php');
    ?>
</body>
</html>
