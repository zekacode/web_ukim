<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('../conn/controller.php');

// Cek hak akses (mirip dengan add)

$message = '';
$message_type = '';

// Jika id_blog tidak ada di URL atau bukan angka, redirect
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: article.php?status=invalid_id");
    exit;
}
$id_blog = (int)$_GET['id'];

// Fetch data artikel menggunakan fungsi dari controller
$blog = get_blog_by_id($id_blog);

if (!$blog) {
    header("Location: article.php?status=not_found");
    exit;
}

// Proses Update Blog
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_blog'])) {
    $blogData = [
        'id_dept' => $_POST['id_dept'],
        'judul' => $_POST['judul'],
        'isi' => $_POST['isi'],
        'status' => $_POST['status'] ?? $blog['status'] // Ambil status dari form, atau gunakan yang lama
    ];
    $userId = $_SESSION['user_id'] ?? null; // Opsional untuk fungsi update, tergantung implementasi

    // Untuk fileData, kirim $_FILES['gambar'] jika ada file baru, atau null jika tidak
    $fileData = (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) ? $_FILES['gambar'] : null;

    if (update_blog($id_blog, $blogData, $fileData, $userId)) {
        $message = "Blog berhasil diperbarui!";
        $message_type = 'success';
        // Refresh data blog setelah update
        $blog = get_blog_by_id($id_blog);
        // header("Location: article.php?status=updated");
        // exit;
    } else {
        $message = "Gagal memperbarui blog. Periksa kembali input Anda.";
        $message_type = 'danger';
    }
}

$departments = get_all_departments();

include('./layout/header-admin.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog - <?php echo htmlspecialchars($blog['judul']); ?> - Admin UKIM</title>
    <link rel="stylesheet" href="../style/admin-form.css">
</head>
<body>
    <div class="admin-form-container">
        <form method="POST" action="article-edit.php?id=<?php echo $id_blog; ?>" enctype="multipart/form-data">
            <h3>
                Edit Blog: <?php echo htmlspecialchars($blog['judul']); ?>
                <a href="article.php" class="btn btn-sm btn-secondary">Kembali ke Daftar Blog</a>
            </h3>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="id_dept" class="form-label">Departemen <span class="text-danger">*</span></label>
                <select name="id_dept" id="id_dept" class="form-control" required>
                    <option value="">-- Pilih Departemen --</option>
                    <?php if ($departments): ?>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo htmlspecialchars($dept['id_dept']); ?>" <?php echo ($dept['id_dept'] == $blog['id_dept']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept['nama_dept']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="judul" class="form-label">Judul <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="judul" id="judul" value="<?php echo htmlspecialchars($blog['judul']); ?>" required>
            </div>

            <div class="form-group">
                <label for="gambar" class="form-label">Ganti Gambar Sampul</label>
                <input type="file" class="form-control" name="gambar" id="gambar" accept="image/jpeg,image/png,image/gif,image/webp">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti gambar. Format: JPG, PNG, GIF, WEBP. Maks 5MB.</small>
                <?php if (!empty($blog['gambar'])): ?>
                    <div class="mt-2">
                        <strong>Gambar Saat Ini:</strong><br>
                        <img src="../<?php echo htmlspecialchars($blog['gambar']); ?>" alt="Gambar Sampul Saat Ini" class="current-image-preview">
                        <?php // Path ../ disesuaikan jika lokasi file `admin/article-edit.php` dan `asset/` berbeda ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="isi" class="form-label">Isi Blog <span class="text-danger">*</span></label>
                <textarea class="form-control" name="isi" id="isi" rows="10" required><?php echo htmlspecialchars($blog['isi']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="published" <?php echo ($blog['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                    <option value="draft" <?php echo ($blog['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                    <option value="archived" <?php echo ($blog['status'] == 'archived') ? 'selected' : ''; ?>>Archived</option>
                </select>
            </div>

            <button type="submit" name="update_blog" class="btn btn-success">Update Blog</button>
        </form>
    </div>
</body>
</html>