<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('../conn/controller.php'); // Gunakan require_once

// Cek hak akses (contoh sederhana)
if (!isset($_SESSION['user_id'])) {
    // Redirect jika belum login
    header('Location: /web_ukim/login.php'); // Sesuaikan path
    exit();
}

$message = '';
$message_type = ''; // 'success' atau 'danger'

// Proses Tambah Event menggunakan fungsi dari controller.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_event'])) {
    $eventData = [
        'judul'             => $_POST['judul'],
        'tema'              => $_POST['tema'] ?? null, // Tema bisa opsional
        'deskripsi'         => $_POST['deskripsi'],
        'tgl_event_mulai'   => $_POST['tgl_event_mulai'], // Input type="datetime-local"
        'tgl_event_selesai' => $_POST['tgl_event_selesai'] ?? null, // Opsional
        'lokasi'            => $_POST['lokasi'],
        // 'link' field di DB baru adalah 'link_pendaftaran' dan 'link_info'
        'link_pendaftaran'  => $_POST['link_pendaftaran'] ?? null,
        'link_info'         => $_POST['link_info'] ?? null, // Misalnya link ke detail event/pamflet
        'status'            => $_POST['status'] ?? 'upcoming'
    ];

    $userId = $_SESSION['user_id'];
    $fileData = $_FILES['gambar'] ?? null; // Ambil data file

    // Validasi minimal file sebelum kirim ke controller (opsional)
    $fileValid = false;
    if ($fileData && $fileData['error'] === UPLOAD_ERR_OK) {
        $fileValid = true;
    } elseif (!$fileData || $fileData['error'] !== UPLOAD_ERR_NO_FILE) {
        // Ada file diupload tapi error, atau error lain
        $message = "Terjadi masalah saat mengupload gambar.";
        $message_type = 'danger';
    } else {
         // Tidak ada file diupload (anggap gambar wajib)
        $message = "Gambar wajib diunggah.";
        $message_type = 'danger';
        // Jika gambar tidak wajib, hapus else ini atau sesuaikan validasi
    }

    // Hanya proses jika file valid atau tidak wajib dan tidak ada error upload
    if ($fileValid) { // Ubah kondisi ini jika gambar tidak wajib
        $newEventId = create_event($eventData, $fileData, $userId);

        if ($newEventId) {
            $message = "Event baru berhasil ditambahkan!";
            $message_type = 'success';
            $_POST = array(); // Kosongkan form
            // header("Location: event.php?status=added");
            // exit;
        } else {
            $message = "Gagal menambahkan event. Periksa kembali input dan file gambar.";
            $message_type = 'danger';
        }
    }
    // Jika $message sudah di-set karena error file, tidak perlu proses create_event
}

include('./layout/header-admin.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Event Baru - Admin UKIM</title>
    <link rel="stylesheet" href="../style/admin-form.css">
</head>
<body>
    <div class="admin-form-container">
        <form action="event-add.php" method="post" enctype="multipart/form-data">
            <h3>
                Buat Event Baru
                <a href="event.php" class="btn btn-sm btn-secondary">Kembali ke Daftar Event</a>
            </h3>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="judul" class="form-label">Judul Event <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan judul event..." required value="<?php echo htmlspecialchars($_POST['judul'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="tema" class="form-label">Tema</label>
                <input type="text" class="form-control" id="tema" name="tema" placeholder="Tema acara (jika ada)..." value="<?php echo htmlspecialchars($_POST['tema'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" placeholder="Jelaskan detail event..." required><?php echo htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
            </div>

            <div class="row"> <?php // Gunakan row jika layout support grid (misal Bootstrap) ?>
                <div class="col-md-6"> <?php // Kolom untuk tanggal mulai ?>
                    <div class="form-group">
                        <label for="tgl_event_mulai" class="form-label">Tanggal & Waktu Mulai <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="tgl_event_mulai" name="tgl_event_mulai" required value="<?php echo htmlspecialchars($_POST['tgl_event_mulai'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-6"> <?php // Kolom untuk tanggal selesai ?>
                    <div class="form-group">
                        <label for="tgl_event_selesai" class="form-label">Tanggal & Waktu Selesai (Opsional)</label>
                        <input type="datetime-local" class="form-control" id="tgl_event_selesai" name="tgl_event_selesai" value="<?php echo htmlspecialchars($_POST['tgl_event_selesai'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Tempat pelaksanaan event..." required value="<?php echo htmlspecialchars($_POST['lokasi'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="gambar" class="form-label">Gambar/Poster Event <span class="text-danger">*</span></label>
                <input type="file" class="form-control" id="gambar" name="gambar" accept="image/jpeg,image/png,image/gif,image/webp" required>
                 <small class="form-text text-muted">Format: JPG, PNG, GIF, WEBP. Maks 5MB.</small>
            </div>

            <div class="form-group">
                <label for="link_pendaftaran" class="form-label">Link Pendaftaran (Opsional)</label>
                <input type="url" class="form-control" id="link_pendaftaran" name="link_pendaftaran" placeholder="https://..." value="<?php echo htmlspecialchars($_POST['link_pendaftaran'] ?? ''); ?>">
            </div>

             <div class="form-group">
                <label for="link_info" class="form-label">Link Info Tambahan (Opsional)</label>
                <input type="url" class="form-control" id="link_info" name="link_info" placeholder="https://..." value="<?php echo htmlspecialchars($_POST['link_info'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="upcoming" <?php echo (isset($_POST['status']) && $_POST['status'] == 'upcoming') ? 'selected' : (!isset($_POST['status']) ? 'selected' : ''); ?>>Upcoming</option>
                    <option value="ongoing" <?php echo (isset($_POST['status']) && $_POST['status'] == 'ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="past" <?php echo (isset($_POST['status']) && $_POST['status'] == 'past') ? 'selected' : ''; ?>>Past</option>
                     <option value="cancelled" <?php echo (isset($_POST['status']) && $_POST['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <button type="submit" name="tambah_event" class="btn btn-primary">Tambah Event</button>
        </form>
    </div>
</body>
</html>