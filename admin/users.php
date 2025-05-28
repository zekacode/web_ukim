<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('../conn/controller.php');

// HANYA ADMIN YANG BISA AKSES
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'Anda tidak memiliki izin.'];
    header('Location: admin.php');
    exit();
}

// --- Paginasi ---
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$users_per_page = 10;
$offset = ($page - 1) * $users_per_page;

$users = get_all_users_admin($users_per_page, $offset);
$total_users = count_all_users_admin();
$total_pages = ceil($total_users / $users_per_page);
// --- Akhir Paginasi ---

// Ambil flash message jika ada
$flash_message = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']); // Hapus setelah diambil

include('./layout/header-admin.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin</title>
    <!-- Link ke CSS Admin global atau CSS khusus tabel -->
    <style>
        /* Contoh CSS minimal untuk tabel (sama seperti article.php & event.php) */
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
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        /* Paginasi */
        .pagination-container { display: flex; justify-content: center; margin-top: 20px; }
        .pagination { list-style: none; padding: 0; display: flex; }
        .page-item { margin: 0 3px; }
        .page-item .page-link { display: block; padding: 8px 12px; text-decoration: none; color: #0C356A; border: 1px solid #dee2e6; border-radius: .25rem; }
        .page-item.active .page-link { z-index: 1; color: #fff; background-color: #0C356A; border-color: #0C356A; }
        .page-item.disabled .page-link { color: #6c757d; pointer-events: none; cursor: auto; background-color: #fff; border-color: #dee2e6; }
    </style>
</head>
<body>
    <div class="admin-content-container">
        <h3>
            Kelola Pengguna
            <a href="user-add.php" class="btn btn-sm btn-success">Tambah User Baru</a>
        </h3>

        <?php if ($flash_message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($flash_message['type']); ?>">
                <?php echo htmlspecialchars($flash_message['text']); ?>
            </div>
        <?php endif; ?>

        <?php if ($users && count($users) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Tgl Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $nomor = $offset + 1; ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $nomor++; ?></td>
                            <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($user['role'])); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($user['status_keanggotaan'])); ?></td>
                            <td><?php echo htmlspecialchars(date('d M Y', strtotime($user['created_at']))); ?></td>
                            <td class="action-buttons">
                                <a href="user-edit.php?id=<?php echo htmlspecialchars($user['id_user']); ?>" class="edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if ($user['id_user'] != $_SESSION['user_id']): // Admin tidak bisa hapus diri sendiri ?>
                                <a href="../conn/controller.php?action=delete&type=user&id=<?php echo htmlspecialchars($user['id_user']); ?>"
                                   class="delete-btn"
                                   onclick="return confirm('Yakin hapus user \'<?php echo htmlspecialchars(addslashes($user['nama_lengkap'])); ?> (<?php echo htmlspecialchars(addslashes($user['username'])); ?>)\'? Ini tidak bisa dibatalkan.')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Paginasi Links -->
            <?php if ($total_pages > 1): ?>
            <nav class="pagination-container" aria-label="Paginasi User">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Sebelumnya</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Berikutnya</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>

        <?php else: ?>
             <div class="alert alert-info">Belum ada data pengguna.</div>
        <?php endif; ?>
    </div>
</body>
</html>