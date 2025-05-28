<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('../conn/controller.php');

// HANYA ADMIN YANG BISA AKSES
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    // Simpan pesan di session flash atau redirect dengan parameter error
    $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'Anda tidak memiliki izin untuk mengakses halaman ini.'];
    header('Location: admin.php'); // Redirect ke dashboard admin atau halaman lain
    exit();
}

$message = '';
$message_type = '';
$input_values = []; // Untuk menyimpan input jika ada error

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_user'])) {
    $input_values = $_POST; // Simpan semua input
    $userData = [
        'nama_lengkap'      => $_POST['nama_lengkap'],
        'nim'               => $_POST['nim'] ?? null,
        'jurusan'           => $_POST['jurusan'] ?? null,
        'angkatan'          => $_POST['angkatan'] ?? null,
        'email'             => $_POST['email'],
        'no_telepon'        => $_POST['no_telepon'] ?? null,
        'username'          => $_POST['username'],
        'password'          => $_POST['password'], // Akan di-hash di controller
        'role'              => $_POST['role'],
        'status_keanggotaan'=> $_POST['status_keanggotaan']
    ];

    $result = create_user_by_admin($userData);

    if (is_int($result) && $result > 0) { // Jika berhasil, controller mengembalikan ID
        $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'User baru berhasil ditambahkan!'];
        header("Location: users.php"); // Redirect ke daftar user
        exit();
    } else {
        $message = is_string($result) ? $result : "Gagal menambahkan user. Kesalahan tidak diketahui.";
        $message_type = 'danger';
    }
}

$roleOptions = ['admin', 'pengurus', 'anggota', 'demisioner', 'alumni']; // Sesuaikan dengan ENUM di DB
$statusOptions = ['aktif', 'pasif', 'alumni']; // Sesuaikan dengan ENUM di DB

include('./layout/header-admin.php'); // Asumsi file ini ada di admin/layout/
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User Baru - Admin</title>
    <link rel="stylesheet" href="../style/admin-form.css">
</head>
<body>
    <div class="admin-form-container">
        <form action="user-add.php" method="post">
            <h3>
                Tambah User Baru
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
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="8">
                        <small>Minimal 8 karakter.</small>
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
                        <select name="role" id="role" class="form-control" required>
                            <?php foreach ($roleOptions as $roleOpt): ?>
                                <option value="<?php echo $roleOpt; ?>" <?php echo (isset($input_values['role']) && $input_values['role'] == $roleOpt) ? 'selected' : ''; ?>><?php echo ucfirst($roleOpt); ?></option>
                            <?php endforeach; ?>
                        </select>
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

            <button type="submit" name="tambah_user" class="btn btn-primary">Tambah User</button>
        </form>
    </div>
</body>
</html>