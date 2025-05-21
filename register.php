<?php
// 1. Mulai Session di paling awal
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Include Controller
// Sesuaikan path jika controller.php ada di folder berbeda
include('./conn/controller.php');

// 3. Cek jika user sudah login, redirect ke dashboard admin (atau halaman profil)
if (isset($_SESSION['user_id'])) {
    // Sesuaikan path ke halaman yang sesuai jika sudah login
    header('Location: ./admin/admin.php');
    exit();
}

// 4. Variabel untuk menyimpan pesan
$message = '';
$message_type = ''; // 'success' atau 'error'

// 5. Inisialisasi variabel untuk menyimpan nilai input agar form tidak kosong saat error
$input_values = [
    'nama_lengkap' => '',
    'nim' => '',
    'jurusan' => '',
    'angkatan' => '',
    'email' => '',
    'username' => ''
];

// 6. Proses form jika metode adalah POST dan tombol 'register' ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Simpan nilai input untuk ditampilkan kembali jika ada error
    $input_values['nama_lengkap'] = $_POST['nama_lengkap'] ?? '';
    $input_values['nim'] = $_POST['nim'] ?? '';
    $input_values['jurusan'] = $_POST['jurusan'] ?? '';
    $input_values['angkatan'] = $_POST['angkatan'] ?? '';
    $input_values['email'] = $_POST['email'] ?? '';
    $input_values['username'] = $_POST['username'] ?? '';

    $registration_data = [
        'nama_lengkap' => $_POST['nama_lengkap'],
        'nim' => $_POST['nim'], // Opsional, bisa dikosongkan
        'jurusan' => $_POST['jurusan'], // Opsional
        'angkatan' => $_POST['angkatan'], // Opsional, pastikan valid YEAR
        'email' => $_POST['email'],
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'password_confirm' => $_POST['password_confirm']
    ];

    $result = register_user($registration_data);

    if ($result === true) {
        $message = 'Registrasi berhasil! Silakan login.';
        $message_type = 'success';
        // Kosongkan input values setelah sukses
        $input_values = array_fill_keys(array_keys($input_values), '');
        // Opsional: Redirect ke halaman login setelah beberapa detik atau langsung
        // header('Refresh: 3; URL=login.php'); // Redirect setelah 3 detik
    } else {
        $message = $result; // $result akan berisi pesan error dari controller
        $message_type = 'error';
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - UKIM Unesa</title>
    <link rel="stylesheet" href="./style/register.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-wrapper"> 
        <div class="login-form-container"> 
            <h2>Registrasi Admin Baru</h2>

            <?php
            // 7. Tampilkan pesan
            if (!empty($message)):
            ?>
                <div class="<?php echo ($message_type === 'success') ? 'success-message' : 'error-message'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php
            endif;
            ?>

            <!-- {/* Action tetap ke file ini sendiri */} -->
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" required value="<?php echo htmlspecialchars($input_values['nama_lengkap']); ?>">
                </div>

                <div class="form-group">
                    <label for="nim">NIM (Nomor Induk Mahasiswa)</label>
                    <input type="text" name="nim" id="nim" pattern="[0-9]*" title="Hanya angka" value="<?php echo htmlspecialchars($input_values['nim']); ?>">
                </div>

                <div class="form-group">
                    <label for="jurusan">Jurusan / Program Studi</label>
                    <input type="text" name="jurusan" id="jurusan" value="<?php echo htmlspecialchars($input_values['jurusan']); ?>">
                </div>

                <div class="form-group">
                    <label for="angkatan">Angkatan (Tahun Masuk)</label>
                    <input type="number" name="angkatan" id="angkatan" min="2000" max="<?php echo date('Y'); ?>" placeholder="YYYY" value="<?php echo htmlspecialchars($input_values['angkatan']); ?>">
                </div>

                <hr style="margin: 20px 0;">

                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($input_values['email']); ?>">
                </div>

                <div class="form-group">
                    <label for="username">Username <span class="required">*</span></label>
                    <input type="text" name="username" id="username" required pattern="[a-zA-Z0-9_]+" title="Hanya huruf, angka, dan underscore" value="<?php echo htmlspecialchars($input_values['username']); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" name="password" id="password" required minlength="8">
                    <small>Minimal 8 karakter.</small>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Konfirmasi Password <span class="required">*</span></label>
                    <input type="password" name="password_confirm" id="password_confirm" required minlength="8">
                </div>

                <button type="submit" class="btn-register" name="register">Register</button> 
            </form>

            <div class="register-link"> 
                <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>
    </div>
</body>
</html>