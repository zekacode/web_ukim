<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('../conn/controller.php');

// Cek hak akses
if (!isset($_SESSION['user_id'])) {
    header('Location: /web_ukim/login.php');
    exit();
}

$message = '';
$message_type = '';

// Validasi ID prestasi
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: prestasi.php?status=invalid_id");
    exit;
}
$id_prestasi = (int)$_GET['id'];

// Ambil data prestasi lama
$prestasi = get_prestasi_by_id($id_prestasi);
if (!$prestasi) {
    header("Location: prestasi.php?status=not_found");
    exit;
}

// Proses Update Prestasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_prestasi'])) {
    $prestasiData = [
        'nama_lomba'    => $_POST['nama_lomba'],
        'tingkat'       => $_POST['tingkat'],
        'juara'         => $_POST['juara'],
        'tahun'         => $_POST['tahun'],
        'penyelenggara' => $_POST['penyelenggara'] ?? $prestasi['penyelenggara'],
        'peserta_nama'  => $_POST['peserta_nama'],
        'is_tim'        => isset($_POST['is_tim']) ? 1 : 0,
        'deskripsi'     => $_POST['deskripsi'] ?? $prestasi['deskripsi']
    ];

    $fileData = (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == UPLOAD_ERR_OK) ? $_FILES['bukti'] : null;

    if (update_prestasi($id_prestasi, $prestasiData, $fileData)) {
        $message = "Prestasi berhasil diperbarui!";
        $message_type = 'success';
        // Re-fetch data terbaru
        $prestasi = get_prestasi_by_id($id_prestasi);
        // header("Location: prestasi.php?status=updated");
        // exit;
    } else {
        $message = "Gagal memperbarui prestasi. Periksa kembali input Anda.";
        $message_type = 'danger';
    }
}

include('./layout/header-admin.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Prestasi: <?php echo htmlspecialchars($prestasi['nama_lomba']); ?> - Admin UKIM</title>
    <link rel="stylesheet" href="../style/admin-form.css">
</head>
<body>
    <div class="admin-form-container">
        <form method="POST" action="prestasi-edit.php?id=<?php echo $id_prestasi; ?>" enctype="multipart/form-data">
            <h3>
                Edit Data Prestasi
                <a href="prestasi.php" class="btn btn-sm btn-secondary">Kembali ke Daftar Prestasi</a>
            </h3>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="nama_lomba" class="form-label">Nama Lomba/Kegiatan <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama_lomba" name="nama_lomba" value="<?php echo htmlspecialchars($prestasi['nama_lomba']); ?>" required>
            </div>

            <div class="form-group">
                <label for="tingkat" class="form-label">Tingkat <span class="text-danger">*</span></label>
                <select name="tingkat" id="tingkat" class="form-control" required>
                    <option value="">-- Pilih Tingkat --</option>
                    <?php
                    $tingkatOptions = ['Internal Kampus', 'Kota/Kabupaten', 'Provinsi', 'Regional', 'Nasional', 'Internasional'];
                    foreach ($tingkatOptions as $option) {
                        $selected = ($prestasi['tingkat'] == $option) ? 'selected' : '';
                        echo "<option value=\"$option\" $selected>$option</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="juara" class="form-label">Pencapaian/Juara <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="juara" name="juara" value="<?php echo htmlspecialchars($prestasi['juara']); ?>" required>
            </div>

            <div class="form-group">
                <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="tahun" name="tahun" value="<?php echo htmlspecialchars($prestasi['tahun']); ?>" min="2000" max="<?php echo date('Y'); ?>" required>
            </div>

            <div class="form-group">
                <label for="penyelenggara" class="form-label">Penyelenggara</label>
                <input type="text" class="form-control" id="penyelenggara" name="penyelenggara" value="<?php echo htmlspecialchars($prestasi['penyelenggara'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="peserta_nama" class="form-label">Nama Peserta/Tim <span class="text-danger">*</span></label>
                <textarea class="form-control" id="peserta_nama" name="peserta_nama" rows="2" required><?php echo htmlspecialchars($prestasi['peserta_nama']); ?></textarea>
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="is_tim" name="is_tim" value="1" <?php echo ($prestasi['is_tim'] == 1) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="is_tim">Merupakan Prestasi Tim</label>
            </div>

            <div class="form-group">
                <label for="deskripsi" class="form-label">Deskripsi Tambahan (Opsional)</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?php echo htmlspecialchars($prestasi['deskripsi'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="bukti" class="form-label">Ganti Bukti (Sertifikat/Foto)</label>
                <input type="file" class="form-control" id="bukti" name="bukti" accept="image/*,.pdf">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti. Format: JPG, PNG, PDF. Maks 10MB.</small>
                 <?php if (!empty($prestasi['bukti'])): ?>
                    <div class="mt-2">
                        <strong>Bukti Saat Ini:</strong>
                        <a href="../<?php echo htmlspecialchars($prestasi['bukti']); ?>" target="_blank">
                            <?php echo basename(htmlspecialchars($prestasi['bukti'])); ?>
                        </a>
                        <?php
                        $fileExt = strtolower(pathinfo($prestasi['bukti'], PATHINFO_EXTENSION));
                        if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])):
                        ?>
                            <br><img src="../<?php echo htmlspecialchars($prestasi['bukti']); ?>" alt="Preview Bukti" class="current-image-preview">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" name="update_prestasi" class="btn btn-success">Update Prestasi</button>
        </form>
    </div>
</body>
</html>