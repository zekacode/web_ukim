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

// Validasi ID karya
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: karya.php?status=invalid_id");
    exit;
}
$id_karya = (int)$_GET['id'];

// Ambil data karya lama
$karya = get_karya_by_id($id_karya);
if (!$karya) {
    header("Location: karya.php?status=not_found");
    exit;
}

// Proses Update Karya
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_karya'])) {
    $karyaData = [
        'id_category'   => $_POST['id_category'],
        'judul'         => $_POST['judul'],
        'isi'           => $_POST['isi'],
        'status'        => $_POST['status'] ?? $karya['status']
    ];

    // File pendukung untuk karya cipta bersifat opsional di controller
    $fileData = (isset($_FILES['file_pendukung']) && $_FILES['file_pendukung']['error'] == UPLOAD_ERR_OK) ? $_FILES['file_pendukung'] : null;

    if (update_karya($id_karya, $karyaData, $fileData)) {
        $message = "Karya berhasil diperbarui!";
        $message_type = 'success';
        // Re-fetch data terbaru
        $karya = get_karya_by_id($id_karya);
        // header("Location: karya.php?status=updated");
        // exit;
    } else {
        $message = "Gagal memperbarui karya. Periksa kembali input Anda.";
        $message_type = 'danger';
    }
}

$karyaCategories = get_all_karya_categories();

include('./layout/header-admin.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Karya: <?php echo htmlspecialchars($karya['judul']); ?> - Admin UKIM</title>
    <link rel="stylesheet" href="../style/admin-form.css">
</head>
<body>
    <div class="admin-form-container">
        <form method="POST" action="karya-edit.php?id=<?php echo $id_karya; ?>" enctype="multipart/form-data">
            <h3>
                Edit Karya: <?php echo htmlspecialchars($karya['judul']); ?>
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
                            <option value="<?php echo htmlspecialchars($category['id_category']); ?>" <?php echo ($category['id_category'] == $karya['id_category']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['nama_category']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="judul" class="form-label">Judul Karya <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="judul" id="judul" value="<?php echo htmlspecialchars($karya['judul']); ?>" required>
            </div>

            <div class="form-group">
                <label for="isi" class="form-label">Isi/Deskripsi Karya <span class="text-danger">*</span></label>
                <textarea class="form-control" name="isi" id="isi" rows="10" required><?php echo htmlspecialchars($karya['isi']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="file_pendukung" class="form-label">Ganti File Pendukung (Opsional)</label>
                <input type="file" class="form-control" id="file_pendukung" name="file_pendukung" accept=".pdf,.doc,.docx,.txt,image/*">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti. Format: PDF, DOC, DOCX, TXT, Gambar. Maks 10MB.</small>
                 <?php if (!empty($karya['file_pendukung'])): ?>
                    <div class="mt-2">
                        <strong>File Saat Ini:</strong>
                        <a href="../<?php echo htmlspecialchars($karya['file_pendukung']); ?>" target="_blank">
                            <?php echo basename(htmlspecialchars($karya['file_pendukung'])); // Hanya nama file ?>
                        </a>
                        <?php // Cek apakah file gambar untuk preview ?>
                        <?php
                        $fileExt = strtolower(pathinfo($karya['file_pendukung'], PATHINFO_EXTENSION));
                        if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])):
                        ?>
                            <br><img src="../<?php echo htmlspecialchars($karya['file_pendukung']); ?>" alt="Preview File Pendukung" class="current-image-preview">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="pending" <?php echo ($karya['status'] == 'pending') ? 'selected' : ''; ?>>Pending Review</option>
                    <option value="published" <?php echo ($karya['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                    <option value="rejected" <?php echo ($karya['status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>

            <button type="submit" name="update_karya" class="btn btn-success">Update Karya</button>
        </form>
    </div>
</body>
</html>