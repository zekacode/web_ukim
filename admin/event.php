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

$events = get_all_events(); // Ambil semua event dari controller

include('./layout/header-admin.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Event - Admin UKIM</title>
    <!-- Link ke CSS Admin global atau CSS khusus tabel jika ada -->
    <!-- <link rel="stylesheet" href="../style/admin-table.css"> -->
     <style>
        /* Contoh CSS minimal untuk tabel jika belum ada (sama seperti article.php) */
        .admin-content-container { max-width: 1200px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .admin-content-container h3 { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-family: 'Lora', serif; color: #0C356A;}
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.9rem; }
        .data-table th, .data-table td { border: 1px solid #e0e0e0; padding: 10px 12px; text-align: left; vertical-align: middle;}
        .data-table thead th { background-color: #f8f9fa; font-weight: 600; color: #333;}
        .data-table tbody tr:nth-child(even) { background-color: #fdfdfd; }
        .data-table img { max-width: 100px; max-height: 70px; border-radius: 4px; object-fit: cover; display: block; margin: auto;} /* Pusatkan gambar */
        .action-buttons a { margin-right: 8px; text-decoration: none; padding: 5px 8px; border-radius: 4px; font-size: 0.85rem; display: inline-block; margin-bottom: 5px;}
        .edit-btn { background-color: #ffc107; color: #212529; border: 1px solid #ffc107;}
        .edit-btn:hover { background-color: #e0a800; }
        .delete-btn { background-color: #dc3545; color: white; border: 1px solid #dc3545;}
        .delete-btn:hover { background-color: #c82333; }
        .btn-sm { font-size: 0.9rem; padding: 0.4rem 0.8rem; }
        .badge { display: inline-block; padding: .35em .65em; font-size: .75em; font-weight: 700; line-height: 1; color: #fff; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: .25rem; }
        .bg-success { background-color: #28a745!important;}
        .bg-warning { background-color: #ffc107!important; color: #212529!important;}
        .bg-info { background-color: #17a2b8!important;}
        .bg-secondary { background-color: #6c757d!important;}
        .bg-danger { background-color: #dc3545!important;}
    </style>
</head>
<body>
    <div class="admin-content-container">
        <h3>
            Daftar Semua Event
            <a href="event-add.php" class="btn btn-sm btn-success">Tambah Event Baru</a>
        </h3>

         <?php if (isset($_GET['status'])): ?>
            <div class="alert alert-<?php echo ($_GET['status'] == 'deleted' || $_GET['status'] == 'added' || $_GET['status'] == 'updated') ? 'success' : 'danger'; ?>">
                <?php
                    if ($_GET['status'] == 'deleted') echo "Event berhasil dihapus.";
                    if ($_GET['status'] == 'added') echo "Event berhasil ditambahkan.";
                    if ($_GET['status'] == 'updated') echo "Event berhasil diperbarui.";
                ?>
            </div>
        <?php endif; ?>


        <?php if ($events && count($events) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Tanggal Mulai</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Gambar</th>
                        <th>Links</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                     <?php $nomor = 1; ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo $nomor++; ?></td>
                            <td><?php echo htmlspecialchars($event['judul']); ?>
                                <?php if (!empty($event['tema'])): ?>
                                    <br><small><i>Tema: <?php echo htmlspecialchars($event['tema']); ?></i></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($event['tgl_event_mulai']))); ?></td>
                            <td><?php echo htmlspecialchars($event['lokasi']); ?></td>
                            <td>
                                 <span class="badge bg-<?php
                                    switch ($event['status']) {
                                        case 'upcoming': echo 'info'; break;
                                        case 'ongoing': echo 'warning'; break;
                                        case 'past': echo 'secondary'; break;
                                        case 'cancelled': echo 'danger'; break;
                                        default: echo 'light'; break;
                                    }
                                ?>">
                                <?php echo htmlspecialchars(ucfirst($event['status'])); ?>
                                </span>
                            </td>
                             <td>
                                <?php if (!empty($event['gambar'])): ?>
                                    <img src="../<?php echo htmlspecialchars($event['gambar']); ?>" alt="Gambar <?php echo htmlspecialchars($event['judul']); ?>">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($event['link_pendaftaran'])): ?>
                                    <a href="<?php echo htmlspecialchars($event['link_pendaftaran']); ?>" target="_blank" class="btn btn-sm btn-outline-primary mb-1">Pendaftaran</a><br>
                                <?php endif; ?>
                                <?php if (!empty($event['link_info'])): ?>
                                     <a href="<?php echo htmlspecialchars($event['link_info']); ?>" target="_blank" class="btn btn-sm btn-outline-info">Info</a>
                                <?php endif; ?>
                                <?php if (empty($event['link_pendaftaran']) && empty($event['link_info'])): ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <a href="event-edit.php?id=<?php echo htmlspecialchars($event['id_event']); ?>" class="edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="../conn/controller.php?action=delete&type=event&id=<?php echo htmlspecialchars($event['id_event']); ?>"
                                   class="delete-btn"
                                   onclick="return confirm('Yakin hapus event: \'<?php echo htmlspecialchars(addslashes($event['judul'])); ?>\'?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
             <div class="alert alert-info">Belum ada event yang ditambahkan.</div>
        <?php endif; ?>
    </div>
</body>
</html>