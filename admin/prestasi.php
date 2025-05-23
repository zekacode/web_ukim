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

$prestasiList = get_all_prestasi(); // Ambil semua prestasi dari controller

include('./layout/header-admin.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Prestasi - Admin UKIM</title>
    <!-- Link ke CSS Admin global atau CSS khusus tabel jika ada -->
    <!-- <link rel="stylesheet" href="../style/admin-table.css"> -->
     <style>
        /* Contoh CSS minimal untuk tabel (sama seperti sebelumnya) */
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
        .data-table img.bukti-preview { max-width: 80px; max-height: 50px; border-radius: 3px; object-fit: cover; display: block; margin: auto;}
    </style>
</head>
<body>
    <div class="admin-content-container">
        <h3>
            Daftar Semua Prestasi
            <a href="prestasi-add.php" class="btn btn-sm btn-success">Tambah Prestasi Baru</a>
        </h3>

         <?php if (isset($_GET['status'])): ?>
            <div class="alert alert-<?php echo ($_GET['status'] == 'deleted' || $_GET['status'] == 'added' || $_GET['status'] == 'updated') ? 'success' : 'danger'; ?>">
                <?php
                    if ($_GET['status'] == 'deleted') echo "Prestasi berhasil dihapus.";
                    if ($_GET['status'] == 'added') echo "Prestasi berhasil ditambahkan.";
                    if ($_GET['status'] == 'updated') echo "Prestasi berhasil diperbarui.";
                ?>
            </div>
        <?php endif; ?>

        <?php if ($prestasiList && count($prestasiList) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lomba</th>
                        <th>Tingkat</th>
                        <th>Juara</th>
                        <th>Tahun</th>
                        <th>Peserta/Tim</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                     <?php $nomor = 1; ?>
                    <?php foreach ($prestasiList as $item): ?>
                        <tr>
                            <td><?php echo $nomor++; ?></td>
                            <td><?php echo htmlspecialchars($item['nama_lomba']); ?></td>
                            <td><?php echo htmlspecialchars($item['tingkat']); ?></td>
                            <td><?php echo htmlspecialchars($item['juara']); ?></td>
                            <td><?php echo htmlspecialchars($item['tahun']); ?></td>
                            <td>
                                <?php echo nl2br(htmlspecialchars($item['peserta_nama'])); ?>
                                <?php if($item['is_tim'] == 1): ?>
                                    <br><small>(Tim)</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($item['bukti'])): ?>
                                    <a href="../<?php echo htmlspecialchars($item['bukti']); ?>" target="_blank">
                                        <?php
                                        $buktiExt = strtolower(pathinfo($item['bukti'], PATHINFO_EXTENSION));
                                        if (in_array($buktiExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])):
                                        ?>
                                            <img src="../<?php echo htmlspecialchars($item['bukti']); ?>" alt="Bukti" class="bukti-preview">
                                        <?php else: ?>
                                            <i class="fas fa-file-alt"></i> <?php echo basename(htmlspecialchars($item['bukti'])); ?>
                                        <?php endif; ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <a href="prestasi-edit.php?id=<?php echo htmlspecialchars($item['id_prestasi']); ?>" class="edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="../conn/controller.php?action=delete&type=prestasi&id=<?php echo htmlspecialchars($item['id_prestasi']); ?>"
                                   class="delete-btn"
                                   onclick="return confirm('Yakin hapus prestasi: \'<?php echo htmlspecialchars(addslashes($item['nama_lomba'])); ?>\'?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
             <div class="alert alert-info">Belum ada data prestasi yang ditambahkan.</div>
        <?php endif; ?>
    </div>
</body>
</html>