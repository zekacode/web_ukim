<?php
include('./layout/header_index.php');
?>

<head>
    <link rel="stylesheet" href="./style/page.css">
</head>
<body>
    <div class="container">
        <?php
        // Query untuk mengambil data blog berdasarkan id_blog dari parameter GET
        $sql = "SELECT id_blog, tgl, departemen, judul, gambar, isi FROM blog_ukim WHERE id_blog=" . $_GET['id'];
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
        <img src="<?= htmlspecialchars($row['gambar']); ?>" alt="Gambar" class="image">

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
