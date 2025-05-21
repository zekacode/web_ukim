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

// Validasi ID event
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: event.php?status=invalid_id");
    exit;
}
$id_event = (int)$_GET['id'];

// Ambil data event lama
$event = get_event_by_id($id_event);
if (!$event) {
    header("Location: event.php?status=not_found");
    exit;
}

// Proses Update Event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_event'])) {
    $eventData = [
        'judul'             => $_POST['judul'],
        'tema'              => $_POST['tema'] ?? $event['tema'],
        'deskripsi'         => $_POST['deskripsi'],
        'tgl_event_mulai'   => $_POST['tgl_event_mulai'],
        'tgl_event_selesai' => $_POST['tgl_event_selesai'] ?? null,
        'lokasi'            => $_POST['lokasi'],
        'link_pendaftaran'  => $_POST['link_pendaftaran'] ?? null,
        'link_info'         => $_POST['link_info'] ?? null,
        'status'            => $_POST['status'] ?? $event['status']
    ];

    // Kirim file hanya jika ada upload baru yang valid
    $fileData = (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) ? $_FILES['gambar'] : null;

    if (update_event($id_event, $eventData, $fileData)) {
        $message = "Event berhasil diperbarui!";
        $message_type = 'success';
        // Re-fetch data terbaru untuk ditampilkan di form
        $event = get_event_by_id($id_event);
        // header("Location: event.php?status=updated");
        // exit;
    } else {
        $message = "Gagal memperbarui event. Periksa kembali input Anda.";
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
    <title>Edit Event: <?php echo htmlspecialchars($event['judul']); ?> - Admin UKIM</title>
    <link rel="stylesheet" href="../style/admin-form.css">
</head>
<body>
    <div class="admin-form-container">
        <form method="POST" action="event-edit.php?id=<?php echo $id_event; ?>" enctype="multipart/form-data">
            <h3>
                Edit Event: <?php echo htmlspecialchars($event['judul']); ?>
                <a href="event.php" class="btn btn-sm btn-secondary">Kembali ke Daftar Event</a>
            </h3>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="judul" class="form-label">Judul Event <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="judul" name="judul" value="<?php echo htmlspecialchars($event['judul']); ?>" required>
            </div>

            <div class="form-group">
                <label for="tema" class="form-label">Tema</label>
                <input type="text" class="form-control" id="tema" name="tema" value="<?php echo htmlspecialchars($event['tema'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required><?php echo htmlspecialchars($event['deskripsi']); ?></textarea>
            </div>

             <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tgl_event_mulai" class="form-label">Tanggal & Waktu Mulai <span class="text-danger">*</span></label>
                        <?php // Format untuk value datetime-local: YYYY-MM-DDTHH:MM ?>
                        <input type="datetime-local" class="form-control" id="tgl_event_mulai" name="tgl_event_mulai" required value="<?php echo date('Y-m-d\TH:i', strtotime($event['tgl_event_mulai'])); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                     <div class="form-group">
                        <label for="tgl_event_selesai" class="form-label">Tanggal & Waktu Selesai (Opsional)</label>
                        <input type="datetime-local" class="form-control" id="tgl_event_selesai" name="tgl_event_selesai" value="<?php echo !empty($event['tgl_event_selesai']) ? date('Y-m-d\TH:i', strtotime($event['tgl_event_selesai'])) : ''; ?>">
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label for="lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="lokasi" name="lokasi" value="<?php echo htmlspecialchars($event['lokasi']); ?>" required>
            </div>

            <div class="form-group">
                <label for="gambar" class="form-label">Ganti Gambar/Poster</label>
                <input type="file" class="form-control" id="gambar" name="gambar" accept="image/jpeg,image/png,image/gif,image/webp">
                 <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti. Format: JPG, PNG, GIF, WEBP. Maks 5MB.</small>
                 <?php if (!empty($event['gambar'])): ?>
                    <div class="mt-2">
                        <strong>Gambar Saat Ini:</strong><br>
                        <img src="../<?php echo htmlspecialchars($event['gambar']); ?>" alt="Gambar Event Saat Ini" class="current-image-preview">
                    </div>
                <?php endif; ?>
            </div>

             <div class="form-group">
                <label for="link_pendaftaran" class="form-label">Link Pendaftaran (Opsional)</label>
                <input type="url" class="form-control" id="link_pendaftaran" name="link_pendaftaran" value="<?php echo htmlspecialchars($event['link_pendaftaran'] ?? ''); ?>">
            </div>

             <div class="form-group">
                <label for="link_info" class="form-label">Link Info Tambahan (Opsional)</label>
                <input type="url" class="form-control" id="link_info" name="link_info" value="<?php echo htmlspecialchars($event['link_info'] ?? ''); ?>">
            </div>

             <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control">
                     <option value="upcoming" <?php echo ($event['status'] == 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="ongoing" <?php echo ($event['status'] == 'ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="past" <?php echo ($event['status'] == 'past') ? 'selected' : ''; ?>>Past</option>
                     <option value="cancelled" <?php echo ($event['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <button type="submit" name="update_event" class="btn btn-success">Update Event</button>
        </form>
    </div>
</body>
</html>