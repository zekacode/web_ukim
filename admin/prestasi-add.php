<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('../conn/controller.php'); // Gunakan require_once

// Cek hak akses
if (!isset($_SESSION['user_id'])) {
    header('Location: /web_ukim/login.php'); // Sesuaikan path
    exit();
}

$message = '';
$message_type = '';

// Proses Tambah Prestasi menggunakan fungsi dari controller.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_prestasi'])) {
    $prestasiData = [
        'nama_lomba'    => $_POST['nama_lomba'],
        'tingkat'       => $_POST['tingkat'],
        'juara'         => $_POST['juara'],
        'tahun'         => $_POST['tahun'],
        'penyelenggara' => $_POST['penyelenggara'] ?? null,
        'peserta_nama'  => $_POST['peserta_nama'],
        'is_tim'        => isset($_POST['is_tim']) ? 1 : 0, // Checkbox
        'deskripsi'     => $_POST['deskripsi'] ?? null
    ];

    $userIdPencatat = $_SESSION['user_id'];
    $fileData = (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == UPLOAD_ERR_OK) ? $_FILES['bukti'] : null;

    $newPrestasiId = create_prestasi($prestasiData, $fileData, $userIdPencatat);

    if ($newPrestasiId) {
        $message = "Prestasi baru berhasil ditambahkan!";
        $message_type = 'success';
        $_POST = array(); // Kosongkan form
        // header("Location: prestasi.php?status=added");
        // exit;
    } else {
        $message = "Gagal menambahkan prestasi. Periksa kembali input Anda.";
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
    <title>Tambah Prestasi Baru - Admin UKIM</title>
    <link rel="stylesheet" href="../style/admin-form.css">
</head>
<body>
    <div class="admin-form-container">
        <form action="prestasi-add.php" method="post" enctype="multipart/form-data">
            <h3>
                Tambah Data Prestasi
                <a href="prestasi.php" class="btn btn-sm btn-secondary">Kembali ke Daftar Prestasi</a>
            </h3>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="nama_lomba" class="form-label">Nama Lomba/Kegiatan <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama_lomba" name="nama_lomba" placeholder="Nama lomba atau kegiatan..." required value="<?php echo htmlspecialchars($_POST['nama_lomba'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="tingkat" class="form-label">Tingkat <span class="text-danger">*</span></label>
                <select name="tingkat" id="tingkat" class="form-control" required>
                    <option value="">-- Pilih Tingkat --</option>
                    <option value="Internal Kampus" <?php echo (isset($_POST['tingkat']) && $_POST['tingkat'] == 'Internal Kampus') ? 'selected' : ''; ?>>Internal Kampus</option>
                    <option value="Kota/Kabupaten" <?php echo (isset($_POST['tingkat']) && $_POST['tingkat'] == 'Kota/Kabupaten') ? 'selected' : ''; ?>>Kota/Kabupaten</option>
                    <option value="Provinsi" <?php echo (isset($_POST['tingkat']) && $_POST['tingkat'] == 'Provinsi') ? 'selected' : ''; ?>>Provinsi</option>
                    <option value="Regional" <?php echo (isset($_POST['tingkat']) && $_POST['tingkat'] == 'Regional') ? 'selected' : ''; ?>>Regional</option>
                    <option value="Nasional" <?php echo (isset($_POST['tingkat']) && $_POST['tingkat'] == 'Nasional') ? 'selected' : ''; ?>>Nasional</option>
                    <option value="Internasional" <?php echo (isset($_POST['tingkat']) && $_POST['tingkat'] == 'Internasional') ? 'selected' : ''; ?>>Internasional</option>
                </select>
            </div>

            <div class="form-group">
                <label for="juara" class="form-label">Pencapaian/Juara <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="juara" name="juara" placeholder="Contoh: Juara 1, Medali Emas, Finalis, Peserta Terbaik" required value="<?php echo htmlspecialchars($_POST['juara'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="tahun" name="tahun" placeholder="YYYY" min="2000" max="<?php echo date('Y'); ?>" required value="<?php echo htmlspecialchars($_POST['tahun'] ?? date('Y')); ?>">
            </div>

            <div class="form-group">
                <label for="penyelenggara" class="form-label">Penyelenggara</label>
                <input type="text" class="form-control" id="penyelenggara" name="penyelenggara" placeholder="Nama institusi penyelenggara..." value="<?php echo htmlspecialchars($_POST['penyelenggara'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="peserta_nama" class="form-label">Nama Peserta/Tim <span class="text-danger">*</span></label>
                <textarea class="form-control" id="peserta_nama" name="peserta_nama" rows="2" placeholder="Nama peraih prestasi. Jika tim, sebutkan nama anggota tim." required><?php echo htmlspecialchars($_POST['peserta_nama'] ?? ''); ?></textarea>
            </div>

             <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="is_tim" name="is_tim" value="1" <?php echo isset($_POST['is_tim']) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="is_tim">Merupakan Prestasi Tim</label>
            </div>

            <div class="form-group">
                <label for="deskripsi" class="form-label">Deskripsi Tambahan (Opsional)</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Detail tambahan mengenai prestasi..."><?php echo htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="bukti" class="form-label">Bukti (Sertifikat/Foto - Opsional)</label>
                <input type="file" class="form-control" id="bukti" name="bukti" accept="image/*,.pdf">
                <small class="form-text text-muted">Format: JPG, PNG, PDF. Maks 10MB.</small>
            </div>

            <button type="submit" name="tambah_prestasi" class="btn btn-primary">Tambah Prestasi</button>
        </form>
    </div>
</body>
</html>