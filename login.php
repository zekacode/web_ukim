<?php
// 1. Mulai Session di paling awal
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Include Controller (yang sudah include koneksi.php)
//    Sesuaikan path jika controller.php ada di folder berbeda, misal '../php/controller.php'
include('./conn/controller.php');

// 3. Cek jika user sudah login, redirect ke dashboard admin
if (isset($_SESSION['user_id'])) {
    // Sesuaikan path ke halaman admin utama Anda
    header('Location: /web_ukim/admin/admin.php');
    exit(); // Pastikan script berhenti setelah redirect
}

// 4. Variabel untuk menyimpan pesan error
$error_message = '';

// 5. Proses form jika metode adalah POST dan tombol 'login' ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Pastikan username dan password ada
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Panggil fungsi login dari controller
        if (login_user($username, $password)) {
            // Login berhasil, controller sudah mengatur session, redirect ke admin
            header('Location: /web_ukim/admin/admin.php'); // Sesuaikan path
            exit();
        } else {
            // Login gagal
            $error_message = 'Username atau password salah!';
        }
    } else {
        $error_message = 'Mohon isi username dan password.';
    }
}

// 6. Cek jika ada parameter error dari redirect sebelumnya (opsional, dari controller)
if (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        $error_message = 'Username atau password salah!';
    } elseif ($_GET['error'] == 2) {
         $error_message = 'Mohon isi username dan password.';
    }
}

?>
<!DOCTYPE html>
<html lang="id"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UKIM Unesa</title>
    <link rel="stylesheet" href="./style/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-form-container">
            <h2>Login Admin</h2>

            <?php
            // 7. Tampilkan pesan error jika ada
            if (!empty($error_message)):
            ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php
            endif;
            ?>

            <!-- {/* Action tetap ke file ini sendiri */} -->
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    <!-- {/* Menambahkan value agar username tidak hilang jika login gagal */} -->
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <button type="submit" class="btn-login" name="login">Login</button>
            </form>

            <!-- {/* 8. Tombol/Link Register */} -->
            <div class="register-link">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>

        </div>
    </div>
</body>
</html>