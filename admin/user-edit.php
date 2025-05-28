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

$message = '';
$message_type = '';

// Validasi ID user
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'ID User tidak valid.'];
    header("Location: users.php");
    exit;
}
$id_user_edit = (int)$_GET['id'];

// Ambil data user yang akan diedit
$user_to_edit = get_user_by_id_admin($id_user_edit);
if (!$user_to_edit) {
    $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'User tidak ditemukan.'];
    header("Location: users.php");
    exit;
}

// Inisialisasi input values dengan data user yang ada
$input_values = $user_to_edit;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $input_values = $_POST; // Update dengan data POST untuk re-populate jika error
    $userData = [
        'nama_lengkap'      => $_POST['nama_lengkap'],
        'nim'               => $_POST['nim'] ?? null,
        'jurusan'           => $_POST['jurusan'] ?? null,
        'angkatan'          => $_POST['angkatan'] ?? null,
        'email'             => $_POST['email'],
        'no_telepon'        => $_POST['no_telepon'] ?? null,
        'username'          => $_POST['username'],
        'password'          => $_POST['password'], // Kosongkan jika tidak ingin ganti password
        'role'              => $_POST['role'],
        'status_keanggotaan'=> $_POST['status_keanggotaan']
    ];

    $result = update_user_by_admin($id_user_edit, $userData);

    if ($result === true) {
        $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Data user berhasil diperbarui!'];
        header("Location: users.php"); // Redirect ke daftar user
        exit();
    } else {
        $message = is_string($result) ? $result : "Gagal memperbarui user. Kesalahan tidak diketahui.";
        $message_type = 'danger';
        // Re-populate form dengan data yang baru diinput jika error, kecuali password
        $input_values['password'] = ''; // Jangan tampilkan password lama/baru di form
    }
}


$roleOptions = ['admin', 'pengurus', 'anggota', 'demisioner', 'alumni'];
$statusOptions = ['aktif', 'pasif', 'alumni'];

include('./layout/header-admin.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User: <?php echo htmlspecialchars($user_to_edit['nama_lengkap']); ?> - Admin</title>
    <link rel="stylesheet" href="../style/admin-form.css">
</head>
<body>
    <div class="admin-form-container">
        <form action="user-edit.php?id=<?php echo $id_user_edit; ?>" method="post">
            <h3>
                Edit User: <?php echo htmlspecialchars($user_to_edit['nama_lengkap']); ?>
                <a href="users.php" class="btn btn-sm btn-secondary">Kembali ke Daftar User</a>
            </h3>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required value="<?php echo htmlspecialchars($input_values['nama_lengkap'] ?? ''); ?>">
            </div>
             <div class="row">
                <div class="col-md-6">
                     <div class="form-group">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" required pattern="[a-zA-Z0-9_]+" title="Hanya huruf, angka, dan underscore" value="<?php echo htmlspecialchars($input_values['username'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password" class="form-label">Password Baru (Opsional)</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="8">
                        <small>Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter jika diisi.</small>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($input_values['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="no_telepon" class="form-label">No. Telepon</label>
                <input type="tel" class="form-control" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($input_values['no_telepon'] ?? ''); ?>">
            </div>
            <hr>
             <p class="text-muted"><small>Informasi Akademik (Opsional untuk non-mahasiswa)</small></p>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nim" class="form-label">NIM</label>
                        <input type="text" class="form-control" id="nim" name="nim" pattern="[0-9]*" title="Hanya angka" value="<?php echo htmlspecialchars($input_values['nim'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                     <div class="form-group">
                        <label for="jurusan" class="form-label">Jurusan</label>
                        <input type="text" class="form-control" id="jurusan" name="jurusan" value="<?php echo htmlspecialchars($input_values['jurusan'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="angkatan" class="form-label">Angkatan</label>
                        <input type="number" class="form-control" id="angkatan" name="angkatan" min="2000" max="<?php echo date('Y'); ?>" placeholder="YYYY" value="<?php echo htmlspecialchars($input_values['angkatan'] ?? ''); ?>">
                    </div>
                </div>
            </div>
            <hr>
             <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-control" required <?php if ($id_user_edit == $_SESSION['user_id']) echo 'disabled title="Anda tidak bisa mengubah role diri sendiri."'; ?>>
                            <?php foreach ($roleOptions as $roleOpt): ?>
                                <option value="<?php echo $roleOpt; ?>" <?php echo (isset($input_values['role']) && $input_values['role'] == $roleOpt) ? 'selected' : ''; ?>><?php echo ucfirst($roleOpt); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($id_user_edit == $_SESSION['user_id']): ?>
                            <input type="hidden" name="role" value="<?php echo htmlspecialchars($user_to_edit['role']); ?>">
                            <small class="text-muted">Admin tidak bisa mengubah role diri sendiri.</small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status_keanggotaan" class="form-label">Status Keanggotaan <span class="text-danger">*</span></label>
                        <select name="status_keanggotaan" id="status_keanggotaan" class="form-control" required>
                             <?php foreach ($statusOptions as $statusOpt): ?>
                                <option value="<?php echo $statusOpt; ?>" <?php echo (isset($input_values['status_keanggotaan']) && $input_values['status_keanggotaan'] == $statusOpt) ? 'selected' : ''; ?>><?php echo ucfirst($statusOpt); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" name="update_user" class="btn btn-success">Update User</button>
        </form>
    </div>
</body>
</html>