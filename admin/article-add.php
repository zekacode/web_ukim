<?php
// Pastikan session sudah dimulai (biasanya di header-admin.php atau controller.php)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Sertakan controller (yang sudah include koneksi.php)
// Sesuaikan path jika struktur folder berbeda
require_once('../conn/controller.php'); // Asumsi controller ada di folder 'conn' satu level di atas 'admin'

// Cek hak akses, misalnya hanya admin atau pengurus yang bisa menambah
if (!isset($_SESSION['user_id']) /* || !in_array($_SESSION['user_role'], ['admin', 'pengurus']) */) {
    // Jika tidak ada session user_id, atau role tidak sesuai, redirect ke login atau halaman lain
    // echo "<script>alert('Anda tidak memiliki akses!'); document.location.href='../login.php';</script>";
    // exit;
    // Untuk sementara, kita anggap cek akses sudah ada di header-admin.php atau akan ditambahkan nanti
}

// Inisialisasi pesan
$message = '';
$message_type = ''; // 'success' atau 'danger'

// Proses Tambah Blog menggunakan fungsi dari controller.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_blog'])) {
    // Kumpulkan data dari form
    $blogData = [
        'id_dept' => $_POST['id_dept'], // Nama input harus 'id_dept'
        'judul' => $_POST['judul'],
        'isi' => $_POST['isi'],
        'status' => $_POST['status'] ?? 'draft' // Ambil status, default ke 'draft' jika tidak ada
    ];

    // Ambil user_id dari session (asumsi sudah login)
    $userId = $_SESSION['user_id'] ?? null; // Beri nilai default jika session belum ada (seharusnya sudah dicek)

    if ($userId) {
        $newBlogId = create_blog($blogData, $_FILES['gambar'], $userId);

        if ($newBlogId) {
            $message = "Blog baru berhasil ditambahkan!";
            $message_type = 'success';
            // Opsional: Bersihkan POST agar form kosong setelah sukses
            $_POST = array();
            // header("Location: article.php?status=added"); // Redirect lebih baik daripada alert JS
            // exit;
        } else {
            $message = "Gagal menambahkan blog. Pastikan semua field terisi dan gambar valid.";
            $message_type = 'danger';
        }
    } else {
        $message = "Sesi pengguna tidak ditemukan. Silakan login kembali.";
        $message_type = 'danger';
    }
}

// Ambil data departemen dari controller
$departments = get_all_departments(); // Fungsi ini harus ada di controller.php

// Sertakan header admin
include('./layout/header-admin.php'); // Path relatif dari file ini
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Blog Baru - Admin UKIM</title>
    <!-- Link ke CSS Admin Form -->
    <link rel="stylesheet" href="../style/admin-form.css"> <?php // Path relatif dari admin/article-add.php ke style/admin-form.css ?>
    <!-- Asumsi CSS lain (Bootstrap, FontAwesome) sudah di-include di header-admin.php -->
</head>
<body>
    <div class="admin-form-container">
        <form action="article-add.php" method="post" enctype="multipart/form-data">
            <h3>
                Buat Blog Baru
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
                            <option value="<?php echo htmlspecialchars($dept['id_dept']); ?>" <?php echo (isset($_POST['id_dept']) && $_POST['id_dept'] == $dept['id_dept']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept['nama_dept']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="judul" class="form-label">Judul <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan judul blog..." required value="<?php echo htmlspecialchars($_POST['judul'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="gambar" class="form-label">Gambar Sampul <span class="text-danger">*</span></label>
                <input type="file" class="form-control" id="gambar" name="gambar" accept="image/jpeg,image/png,image/gif,image/webp" required>
                <small class="form-text text-muted">Format yang diizinkan: JPG, PNG, GIF, WEBP. Maksimal 5MB.</small>
            </div>

            <div class="form-group">
                <label for="isi" class="form-label">Isi Blog <span class="text-danger">*</span></label>
                <textarea class="form-control" id="isi" name="isi" rows="10" placeholder="Tulis isi blog di sini..." required><?php echo htmlspecialchars($_POST['isi'] ?? ''); ?></textarea>
                <!-- Anda bisa integrasikan Rich Text Editor di sini seperti TinyMCE atau CKEditor -->
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="published" <?php echo (isset($_POST['status']) && $_POST['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                    <option value="draft" <?php echo (isset($_POST['status']) && $_POST['status'] == 'draft') ? 'selected' : (!isset($_POST['status']) ? 'selected' : ''); ?>>Draft</option>
                </select>
            </div>

            <button type="submit" name="tambah_blog" class="btn btn-primary">Tambah Blog</button>
        </form>
    </div>

    <?php
    // Sertakan footer admin (jika ada)
    // include('./layout/footer-admin.php');
    ?>
</body>
</html>