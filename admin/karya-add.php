<?php
include('./layout/header-admin.php');

// Proses tambah karya
if (isset($_POST['tambah'])) {
    if (tambah_karya($_POST, $_FILES) > 0) {
        echo "<script>
            alert('Karya Baru Berhasil Ditambahkan');
            document.location.href = 'karya.php';
        </script>";
    } else {
        echo "<script>
            alert('Karya Baru Gagal Ditambahkan');
            document.location.href = 'karya-add.php';
        </script>";
    }
}

// Fungsi untuk mendapatkan nilai ENUM dari database
function getEnumValues($conn, $table, $column) {
    $query = "SHOW COLUMNS FROM $table LIKE '$column'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($result);
    $type = $row['Type'];

    preg_match("/^enum\((.*)\)$/", $type, $matches);
    $enumValues = explode(",", $matches[1]);

    return array_map(function ($value) {
        return trim($value, "'");
    }, $enumValues);
}

// Ambil nilai ENUM untuk dropdown kategori
$kategoriValues = getEnumValues($conn, 'karya_cipta', 'kategori');
?>
<body>
    <div class="main-table">
        <form action="" method="post" enctype="multipart/form-data">
            <h3 class="mb-3">Buat Karya Baru
                <a href="karya.php" class="btn btn-secondary">Daftar Karya</a>
            </h3>

            <!-- Dropdown Kategori -->
            <div class="mb-3 col-6">
                <label for="kategori" class="form-label">Kategori</label>
                <select name="kategori" id="kategori" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategoriValues as $value): ?>
                        <option value="<?= htmlspecialchars($value); ?>">
                            <?= htmlspecialchars($value); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Input Judul -->
            <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" class="form-control" id="judul" name="judul" placeholder="Judul ..." required>
            </div>

            <!-- Input Isi -->
            <div class="mb-3">
                <label for="isi" class="form-label">Isi</label>
                <textarea class="form-control" id="isi" name="isi" rows="5" placeholder="Isi karya ..." required></textarea>
            </div>

            <button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
        </form>
    </div>
</body>
</html>
