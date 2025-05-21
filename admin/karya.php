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

$karyas = get_all_karya(); // Ambil semua karya dari controller

include('./layout/header-admin.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Karya Cipta - Admin UKIM</title>
    <!-- Link ke CSS Admin global atau CSS khusus tabel jika ada -->
    <!-- <link rel="stylesheet" href="../style/admin-table.css"> -->
     <style>
        /* Contoh CSS minimal untuk tabel jika belum ada (sama seperti article.php & event.php) */
        .admin-content-container { max-width: 1200px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .admin-content-container h3 { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-family: 'Lora', serif; color: #0C356A;}
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.9rem; }
        .data-table th, .data-table td { border: 1px solid #e0e0e0; padding: 10px 12px; text-align: left; vertical-align: middle;}
        .data-table thead th { background-color: #f8f9fa; font-weight: 600; color: #333;}
        .data-table tbody tr:nth-child(even) { background-color: #fdfdfd; }
        .action-buttons a { margin-right: 8px; text-decoration: none; padding: 5px 8px; border-radius: 4px; font-size: 0.85rem; display: inline-block; margin-bottom: 5px;}
        .edit-btn { background-color: #ffc107; color: #212529; border: 1px solid #ffc107;}
        .edit-btn:hover { background-color: #e0a800; }
        .delete-btn { background-color: #dc3545; color: white; border: 1px solid #dc3545;}
        .delete-btn:hover { background-color: #c82333; }
        .btn-sm { font-size: 0.9rem; padding: 0.4rem 0.8rem; }
        .badge { display: inline-block; padding: .35em .65em; font-size: .75em; font-weight: 700; line-height: 1; color: #fff; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: .25rem; }
        .bg-success { background-color: #28a745!important;}
        .bg-warning { background-color: #ffc107!important; color: #212529!important;}
        .bg-danger { background-color: #dc3545!important;}
        .bg-secondary { background-color: #6c757d!important;}
    </style>
</head>
<body>
    <div class="admin-content-container">
        <h3>
            Daftar Semua Karya Cipta
            <a href="karya-add.php" class="btn btn-sm btn-success">Tambah Karya Baru</a>
        </h3>

         <?php if (isset($_GET['status'])): ?>
            <div class="alert alert-<?php echo ($_GET['status'] == 'deleted' || $_GET['status'] == 'added' || $_GET['status'] == 'updated') ? 'success' : 'danger'; ?>">
                <?php
                    if ($_GET['status'] == 'deleted') echo "Karya berhasil dihapus.";
                    if ($_GET['status'] == 'added') echo "Karya berhasil ditambahkan.";
                    if ($_GET['status'] == 'updated') echo "Karya berhasil diperbarui.";
                ?>
            </div>
        <?php endif; ?>

        <?php if ($karyas && count($karyas) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Tgl Dibuat</th>
                        <th>File</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                     <?php $nomor = 1; ?>
                    <?php foreach ($karyas as $karya): ?>
                        <tr>
                            <td><?php echo $nomor++; ?></td>
                            <td><?php echo htmlspecialchars($karya['judul']); ?></td>
                            <td><?php echo htmlspecialchars($karya['nama_category'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($karya['author_name'] ?? 'N/A'); ?></td>
                            <td><span class="badge bg-<?php
                                    switch ($karya['status']) {
                                        case 'published': echo 'success'; break;
                                        case 'pending': echo 'warning'; break;
                                        case 'rejected': echo 'danger'; break;
                                        default: echo 'secondary'; break;
                                    }
                                ?>">
                                <?php echo htmlspecialchars(ucfirst($karya['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($karya['created_at']))); ?></td>
                            <td>
                                <?php if (!empty($karya['file_pendukung'])): ?>
                                    <a href="../<?php echo htmlspecialchars($karya['file_pendukung']); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-download"></i> <?php echo basename(htmlspecialchars($karya['file_pendukung'])); ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <a href="karya-edit.php?id=<?php echo htmlspecialchars($karya['id_karya']); ?>" class="edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="../conn/controller.php?action=delete&type=karya&id=<?php echo htmlspecialchars($karya['id_karya']); ?>"
                                   class="delete-btn"
                                   onclick="return confirm('Yakin hapus karya: \'<?php echo htmlspecialchars(addslashes($karya['judul'])); ?>\'?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
             <div class="alert alert-info">Belum ada karya yang ditambahkan.</div>
        <?php endif; ?>
    </div>
</body>
</html>