<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('../conn/controller.php'); // Asumsi controller ada di folder 'conn' satu level di atas 'admin'
// Cek hak akses

$blogs = get_all_blogs(); // Ambil semua blog dari controller

include('./layout/header-admin.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Blog - Admin UKIM</title>
    <!-- Link ke CSS Admin global atau CSS khusus tabel jika ada -->
    <!-- <link rel="stylesheet" href="../style/admin-table.css"> -->
    <style>
        /* Contoh CSS minimal untuk tabel jika belum ada */
        .admin-content-container { max-width: 1200px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .admin-content-container h3 { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-family: 'Lora', serif; color: #0C356A;}
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.9rem; }
        .data-table th, .data-table td { border: 1px solid #e0e0e0; padding: 10px 12px; text-align: left; vertical-align: middle;}
        .data-table thead th { background-color: #f8f9fa; font-weight: 600; color: #333;}
        .data-table tbody tr:nth-child(even) { background-color: #fdfdfd; }
        .data-table img { max-width: 100px; max-height: 70px; border-radius: 4px; object-fit: cover; }
        .action-buttons a { margin-right: 8px; text-decoration: none; padding: 5px 8px; border-radius: 4px; font-size: 0.85rem;}
        .edit-btn { background-color: #ffc107; color: #212529; border: 1px solid #ffc107;}
        .edit-btn:hover { background-color: #e0a800; }
        .delete-btn { background-color: #dc3545; color: white; border: 1px solid #dc3545;}
        .delete-btn:hover { background-color: #c82333; }
        .btn-sm { font-size: 0.9rem; padding: 0.4rem 0.8rem; } /* Untuk tombol Tambah */
    </style>
</head>
<body>
    <div class="admin-content-container"> <?php // Ganti .main-table dengan class yang lebih deskriptif ?>
        <h3>
            Daftar Semua Blog
            <a href="article-add.php" class="btn btn-sm btn-success">Tambah Blog Baru</a>
        </h3>

        <?php if (isset($_GET['status'])): ?>
            <div class="alert alert-<?php echo ($_GET['status'] == 'deleted' || $_GET['status'] == 'added' || $_GET['status'] == 'updated') ? 'success' : 'danger'; ?>">
                <?php
                    if ($_GET['status'] == 'deleted') echo "Blog berhasil dihapus.";
                    if ($_GET['status'] == 'added') echo "Blog berhasil ditambahkan.";
                    if ($_GET['status'] == 'updated') echo "Blog berhasil diperbarui.";
                    // Tambahkan pesan lain jika perlu
                ?>
            </div>
        <?php endif; ?>

        <?php if ($blogs && count($blogs) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Departemen</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Tgl Dibuat</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $nomor = 1; ?>
                    <?php foreach ($blogs as $blog): ?>
                        <tr>
                            <td><?php echo $nomor++; ?></td>
                            <td><?php echo htmlspecialchars($blog['judul']); ?></td>
                            <td><?php echo htmlspecialchars($blog['nama_dept'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($blog['author_name'] ?? 'N/A'); ?></td>
                            <td><span class="badge bg-<?php echo ($blog['status'] == 'published') ? 'success' : (($blog['status'] == 'draft') ? 'warning' : 'secondary'); ?>">
                                <?php echo htmlspecialchars(ucfirst($blog['status'])); ?>
                            </span></td>
                            <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($blog['created_at']))); ?></td>
                            <td>
                                <?php if (!empty($blog['gambar'])): ?>
                                    <img src="../<?php echo htmlspecialchars($blog['gambar']); ?>" alt="Gambar <?php echo htmlspecialchars($blog['judul']); ?>">
                                    <?php // Path ../ disesuaikan jika lokasi file `admin/article.php` dan `asset/` berbeda ?>
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <a href="article-edit.php?id=<?php echo htmlspecialchars($blog['id_blog']); ?>" class="edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="../conn/controller.php?action=delete&type=blog&id=<?php echo htmlspecialchars($blog['id_blog']); ?>"
                                   class="delete-btn"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus blog ini: \'<?php echo htmlspecialchars(addslashes($blog['judul'])); ?>\'?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">Belum ada blog yang ditambahkan.</div>
        <?php endif; ?>
    </div>
</body>
</html>