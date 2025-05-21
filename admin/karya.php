<?php
include('./layout/header-admin.php');
?>
<body>
    <div class="main-table">
        <h3 class="mb-3">Semua Karya
            <a href="karya-add.php" class="btn btn-success">Tambah</a>
        </h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID Karya</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Judul</th>
                    <th>Isi</th>
                    <th>Aksi</th> <!-- Kolom aksi untuk edit dan delete -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Mengambil data karya dari database
                $sql = "SELECT id_karya, tgl, kategori, judul, isi FROM karya_cipta";
                $result = mysqli_query($conn, $sql);

                // Menampilkan data karya dalam tabel
                while ($row = mysqli_fetch_array($result)) {
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_karya']); ?></td>
                        <td><?= htmlspecialchars($row['tgl']); ?></td>
                        <td><?= htmlspecialchars($row['kategori']); ?></td>
                        <td><?= htmlspecialchars($row['judul']); ?></td>
                        <td>
                            <?php
                            // Membatasi isi hingga 10 kata pertama
                            $words = explode(" ", $row['isi']);
                            $limited_text = implode(" ", array_slice($words, 0, 10));
                            echo htmlspecialchars($limited_text) . (count($words) > 10 ? "..." : "");
                            ?>
                        </td>
                        <td>
                            <a href="karya-edit.php?id=<?= htmlspecialchars($row['id_karya']); ?>" class="edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="../conn/controller.php?action=delete&type=karya&id=<?= htmlspecialchars($row['id_karya']); ?>"
                               class="delete-btn"
                               onclick="return confirm('Apakah Anda yakin ingin menghapus karya ini?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
