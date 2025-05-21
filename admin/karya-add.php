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

// Proses Tambah Karya menggunakan fungsi dari controller.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_karya'])) {
    $karyaData = [
        'id_category'   => $_POST['id_category'], // Nama input harus 'id_category'
        'judul'         => $_POST['judul'],
        'isi'           => $_POST['isi'],
        'status'        => $_POST['status'] ?? 'pending' // Default status 'pending'
    ];

    $userId = $_SESSION['user_id'];
    // File pendukung untuk karya cipta bersifat opsional di controller
    $fileData = (isset($_FILES['file_pendukung']) && $_FILES['file_pendukung']['error'] == UPLOAD_ERR_OK) ? $_FILES['file_pendukung'] : null;

    $newKaryaId = create_karya($karyaData, $fileData, $userId);

    if ($newKaryaId) {
        $message = "Karya baru berhasil ditambahkan!";
        $message_type = 'success';
        $_POST = array(); // Kosongkan form
        // header("Location: karya.php?status=added");
        // exit;
    } else {
        $message = "Gagal menambahkan karya. Periksa kembali input Anda.";
        $message_type = 'danger';
    }
}

// Ambil data kategori karya dari controller
$karyaCategories = get_all_karya_categories(); // Fungsi ini harus ada di controller.php

include('./layout/header-admin.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Karya Baru - Admin UKIM</title>
    <link rel="stylesheet" href="../style/admin-form.css">
</head>
<body>
    <div class="admin-form-container">
        <form action="karya-add.php" method="post" enctype="multipart/form-data">
            <h3>
                Buat Karya Baru
                <a href="karya.php" class="btn btn-sm btn-secondary">Kembali ke Daftar Karya</a>
            </h3>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="id_category" class="form-label">Kategori Karya <span class="text-danger">*</span></label>
                <select name="id_category" id="id_category" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php if ($karyaCategories): ?>
                        <?php foreach ($karyaCategories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id_category']); ?>" <?php echo (isset($_POST['id_category']) && $_POST['id_category'] == $category['id_category']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['nama_category']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="judul" class="form-label">Judul Karya <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan judul karya..." required value="<?php echo htmlspecialchars($_POST['judul'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="isi" class="form-label">Isi/Deskripsi Karya <span class="text-danger">*</span></label>
                <textarea class="form-control" id="isi" name="isi" rows="10" placeholder="Tulis isi atau deskripsi karya di sini..." required><?php echo htmlspecialchars($_POST['isi'] ?? ''); ?></textarea>
                <!-- Bisa integrasikan Rich Text Editor jika perlu -->
            </div>

            <div class="form-group">
                <label for="file_pendukung" class="form-label">File Pendukung (Opsional)</label>
                <input type="file" class="form-control" id="file_pendukung" name="file_pendukung" accept=".pdf,.doc,.docx,.txt,image/*">
                <small class="form-text text-muted">Format: PDF, DOC, DOCX, TXT, Gambar. Maks 10MB.</small>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="pending" <?php echo (isset($_POST['status']) && $_POST['status'] == 'pending') ? 'selected' : (!isset($_POST['status']) ? 'selected' : ''); ?>>Pending Review</option>
                    <option value="published" <?php echo (isset($_POST['status']) && $_POST['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                    <option value="rejected" <?php echo (isset($_POST['status']) && $_POST['status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>

            <button type="submit" name="tambah_karya" class="btn btn-primary">Tambah Karya</button>
        </form>
    </div>
</body>
</html>