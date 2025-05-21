<?php
include('./layout/header-admin.php');

// Jika id_karya tidak ada di URL, redirect ke halaman daftar karya
if (!isset($_GET['id'])) {
    header("Location: karya.php");
    exit;
}

$id_karya = mysqli_real_escape_string($conn, $_GET['id']);

// Mengambil data karya dari database berdasarkan id
$sql = "SELECT * FROM karya_cipta WHERE id_karya = '$id_karya'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "Karya tidak ditemukan.";
    exit;
}

// Ambil semua enum kategori
$queryEnum = "SHOW COLUMNS FROM karya_cipta LIKE 'kategori'";
$resultEnum = mysqli_query($conn, $queryEnum);
$rowEnum = mysqli_fetch_assoc($resultEnum);

preg_match("/^enum\((.*)\)$/", $rowEnum['Type'], $matches);
$kategoriValues = str_getcsv($matches[1], ',', "'");

// Proses update data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);

    // Update data karya
    $sql_update = "UPDATE karya_cipta SET 
                    kategori='$kategori', 
                    judul='$judul', 
                    isi='$isi' 
                    WHERE id_karya='$id_karya'";
    if (mysqli_query($conn, $sql_update)) {
        echo "<script>
            alert('Karya berhasil diperbarui');
            document.location.href = 'karya.php';
        </script>";
    } else {
        echo "Gagal mengupdate data.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Karya</title>
</head>
<body>
    <div class="main-table">
        <h3 class="mb-3">Edit Karya</h3>
        <form method="POST" enctype="multipart/form-data">
            <!-- Dropdown Kategori -->
            <div class="mb-3 col-6">
                <label for="kategori" class="form-label">Kategori</label>
                <select name="kategori" id="kategori" class="form-control" required>
                    <?php foreach ($kategoriValues as $value): ?>
                        <option value="<?= htmlspecialchars($value); ?>" 
                            <?= $value === $row['kategori'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($value); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Input Judul -->
            <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" class="form-control" name="judul" id="judul" value="<?= htmlspecialchars($row['judul']); ?>" required>
            </div>

            <!-- Input Isi -->
            <div class="mb-3">
                <label for="isi" class="form-label">Isi</label>
                <textarea class="form-control" name="isi" id="isi" rows="5" required><?= htmlspecialchars($row['isi']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
</body>
</html>
