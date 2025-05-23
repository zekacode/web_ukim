<?php
// Selalu mulai session di awal jika akan digunakan
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('koneksi.php'); // Pastikan koneksi.php sudah di-set ke utf8mb4

// --- Helper Functions ---

/**
 * Membuat slug URL-friendly dari string.
 * @param string $text Teks input.
 * @return string Slug yang dihasilkan.
 */
function generate_slug($text) {
    // Konversi ke huruf kecil
    $text = strtolower($text);
    // Hapus karakter non-alfanumerik kecuali spasi dan strip
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    // Ganti spasi dengan strip
    $text = preg_replace('/[\s-]+/', '-', $text);
    // Hapus strip di awal/akhir
    $text = trim($text, '-');
    // Batasi panjang slug (opsional)
    // $text = substr($text, 0, 70); // Contoh batas 70 karakter
    // Tambahkan unik ID jika perlu untuk menghindari duplikasi total
    // $text = $text . '-' . uniqid(); // Pertimbangkan jika judul bisa sama persis
    return $text;
}

/**
 * Menangani upload file dengan aman.
 * @param array $fileData Data file dari $_FILES['nama_input'].
 * @param string $targetDirectory Direktori tujuan (e.g., '../asset/gbr_blog/').
 * @param array $allowedTypes Array tipe MIME yang diizinkan (e.g., ['image/jpeg', 'image/png']).
 * @param int $maxSize Ukuran maksimum file dalam bytes.
 * @return string|false Path relatif database jika berhasil, false jika gagal.
 */
function upload_file($fileData, $targetDirectory, $allowedTypes, $maxSize = 5000000) { // Default max 5MB
    // Cek apakah ada error upload dasar
    if (!isset($fileData['error']) || is_array($fileData['error'])) {
        // error_log("Upload Error: Invalid parameters."); // Log error
        return false; // Error tidak diketahui atau multiple files
    }
    if ($fileData['error'] !== UPLOAD_ERR_OK) {
        // error_log("Upload Error Code: " . $fileData['error']); // Log error
        return false; // Ada error saat upload
    }

    // Cek ukuran file
    if ($fileData['size'] > $maxSize) {
        // error_log("Upload Error: File too large."); // Log error
        return false; // File terlalu besar
    }

    // Cek tipe file menggunakan finfo (lebih aman dari sekedar cek ekstensi)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $fileMimeType = $finfo->file($fileData['tmp_name']);
    if (!in_array($fileMimeType, $allowedTypes)) {
        // error_log("Upload Error: Invalid file type detected: " . $fileMimeType); // Log error
        return false; // Tipe file tidak diizinkan
    }

    // Buat nama file unik untuk mencegah overwrite dan masalah karakter aneh
    $fileExtension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
    $uniqueName = uniqid('file_', true) . '.' . strtolower($fileExtension);
    $targetFilePath = $targetDirectory . $uniqueName;

    // Pastikan direktori target ada dan bisa ditulis
    if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory, 0775, true); // Coba buat direktori jika belum ada
    }
    if (!is_writable($targetDirectory)) {
         // error_log("Upload Error: Target directory not writable: " . $targetDirectory); // Log error
        return false; // Direktori tidak bisa ditulis
    }

    // Pindahkan file
    if (move_uploaded_file($fileData['tmp_name'], $targetFilePath)) {
        // Kembalikan path relatif yang akan disimpan di DB (sesuaikan formatnya jika perlu)
        // Asumsi: DB menyimpan path relatif dari root web atau path tertentu
        // Contoh: Mengembalikan path relatif dari 'asset/'
        $relativePathForDb = './asset/' . basename($targetDirectory) . '/' . $uniqueName; // Sesuaikan ini!
        return $relativePathForDb;
    } else {
        // error_log("Upload Error: Failed to move uploaded file."); // Log error
        return false; // Gagal memindahkan file
    }
}


/**
 * Menghapus file dari server.
 * @param string $dbFilePath Path file yang tersimpan di database.
 * @return bool True jika berhasil atau file tidak ada, false jika gagal hapus.
 */
function delete_file($dbFilePath) {
    // Konversi path DB ke path server fisik (ini mungkin perlu penyesuaian!)
    // Asumsi: Path DB './asset/folder/namafile.jpg' menjadi '../asset/folder/namafile.jpg'
    $serverFilePath = str_replace('./asset/', '../asset/', $dbFilePath); // Sesuaikan ini!

    if ($serverFilePath && file_exists($serverFilePath)) {
        if (unlink($serverFilePath)) {
            return true; // Berhasil dihapus
        } else {
            // error_log("File Deletion Error: Could not delete file: " . $serverFilePath); // Log error
            return false; // Gagal menghapus
        }
    }
    return true; // File tidak ada di server (anggap sudah terhapus atau tidak pernah ada)
}


// --- Autentikasi ---

/**
 * Memproses login user.
 * @param string $username Username input.
 * @param string $password Password input (plain text).
 * @return bool True jika login berhasil, false jika gagal.
 */
function login_user($username, $password) {
    global $conn;
    $sql = "SELECT id_user, username, password, nama_lengkap, role FROM users WHERE username = ? AND status_keanggotaan = 'aktif' LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        // error_log("Login Prepare Error: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Login berhasil, set session
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['user_role'] = $user['role']; // Simpan role untuk hak akses
            mysqli_stmt_close($stmt);
            return true;
        }
    }
    // Jika username tidak ditemukan atau password salah
    mysqli_stmt_close($stmt);
    return false;
}

/**
 * Memproses logout user.
 */
function logout_user() {
    // Pastikan session sudah dimulai jika belum (walaupun idealnya sudah di awal controller)
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Hapus semua variabel session
    $_SESSION = array(); // Cara lebih bersih untuk menghapus semua data session

    // Jika menggunakan session cookies, hapus juga cookie session.
    // Catatan: Ini akan menghancurkan session, dan bukan hanya data session!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Akhirnya, hancurkan session.
    session_destroy();

    // Redirect ke halaman index.php di root project
    // Gunakan path root-relative untuk kejelasan dan keandalan
    header("Location: /web_ukim/index.php");
    exit(); // SANGAT PENTING: Hentikan eksekusi script setelah redirect
}

/**
 * Memproses registrasi user baru.
 * @param array $data Data user dari form ['nama_lengkap', 'nim', 'jurusan', 'angkatan', 'email', 'username', 'password'].
 * @return string|true Pesan error jika gagal, true jika berhasil.
 */
function register_user($data) {
    global $conn;

    // Validasi Input Sederhana (tambahkan lebih banyak jika perlu)
    $required_fields = ['nama_lengkap', 'email', 'username', 'password', 'password_confirm'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            return "Mohon lengkapi semua field yang wajib diisi.";
        }
    }

    // Validasi email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return "Format email tidak valid.";
    }

    // Validasi password
    if ($data['password'] !== $data['password_confirm']) {
        return "Konfirmasi password tidak cocok.";
    }
    if (strlen($data['password']) < 8) { // Contoh minimal 8 karakter
        return "Password minimal harus 8 karakter.";
    }
    // Anda bisa menambahkan validasi kekuatan password lebih lanjut (huruf besar, kecil, angka, simbol)

    // Cek keunikan username
    $sql_check_username = "SELECT id_user FROM users WHERE username = ? LIMIT 1";
    $stmt_check_username = mysqli_prepare($conn, $sql_check_username);
    mysqli_stmt_bind_param($stmt_check_username, "s", $data['username']);
    mysqli_stmt_execute($stmt_check_username);
    mysqli_stmt_store_result($stmt_check_username); // Penting untuk mysqli_stmt_num_rows
    if (mysqli_stmt_num_rows($stmt_check_username) > 0) {
        mysqli_stmt_close($stmt_check_username);
        return "Username sudah digunakan. Silakan pilih username lain.";
    }
    mysqli_stmt_close($stmt_check_username);

    // Cek keunikan email
    $sql_check_email = "SELECT id_user FROM users WHERE email = ? LIMIT 1";
    $stmt_check_email = mysqli_prepare($conn, $sql_check_email);
    mysqli_stmt_bind_param($stmt_check_email, "s", $data['email']);
    mysqli_stmt_execute($stmt_check_email);
    mysqli_stmt_store_result($stmt_check_email);
    if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
        mysqli_stmt_close($stmt_check_email);
        return "Email sudah terdaftar. Silakan gunakan email lain.";
    }
    mysqli_stmt_close($stmt_check_email);

    // Cek keunikan NIM jika diisi dan dijadikan syarat unik
    if (!empty($data['nim'])) {
        $sql_check_nim = "SELECT id_user FROM users WHERE nim = ? LIMIT 1";
        $stmt_check_nim = mysqli_prepare($conn, $sql_check_nim);
        mysqli_stmt_bind_param($stmt_check_nim, "s", $data['nim']);
        mysqli_stmt_execute($stmt_check_nim);
        mysqli_stmt_store_result($stmt_check_nim);
        if (mysqli_stmt_num_rows($stmt_check_nim) > 0) {
            mysqli_stmt_close($stmt_check_nim);
            return "NIM sudah terdaftar.";
        }
        mysqli_stmt_close($stmt_check_nim);
    }

    // Hash password
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

    // Set default role dan status (misalnya 'anggota' dan 'aktif')
    // Role bisa juga ditentukan berdasarkan input jika diperlukan
    $role = 'anggota';
    $status_keanggotaan = 'aktif'; // Atau 'pending' jika butuh approval admin

    // Siapkan query untuk insert user baru
    $sql_insert = "INSERT INTO users (nama_lengkap, nim, jurusan, angkatan, email, username, password, role, status_keanggotaan, created_at, updated_at)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);

    if (!$stmt_insert) {
        // error_log("Register User Prepare Error: " . mysqli_error($conn));
        return "Terjadi kesalahan pada server. Silakan coba lagi nanti.";
    }

    // Bind parameter (pastikan jumlah dan tipe 's' sesuai dengan kolom)
    // Jika angkatan adalah YEAR, pastikan $data['angkatan'] adalah integer atau null
    $angkatan_val = !empty($data['angkatan']) ? (int)$data['angkatan'] : null;
    $nim_val = !empty($data['nim']) ? $data['nim'] : null;
    $jurusan_val = !empty($data['jurusan']) ? $data['jurusan'] : null;

    mysqli_stmt_bind_param($stmt_insert, "sssisssss",
        $data['nama_lengkap'],
        $nim_val,
        $jurusan_val,
        $angkatan_val,
        $data['email'],
        $data['username'],
        $hashed_password,
        $role,
        $status_keanggotaan
    );

    if (mysqli_stmt_execute($stmt_insert)) {
        mysqli_stmt_close($stmt_insert);
        return true; // Registrasi berhasil
    } else {
        // error_log("Register User Execute Error: " . mysqli_stmt_error($stmt_insert));
        mysqli_stmt_close($stmt_insert);
        return "Gagal melakukan registrasi. Silakan coba lagi.";
    }
}

// --- CRUD Functions ---

// == Blog ==

/**
 * Menambah data blog baru.
 * @param array $data Data dari form ['id_dept', 'judul', 'isi', 'status'].
 * @param array $fileData Data file dari $_FILES['gambar'].
 * @param int $user_id ID user yang membuat blog (dari session).
 * @return int|false ID blog baru jika berhasil, false jika gagal.
 */
function create_blog($data, $fileData, $user_id) {
    global $conn;

    // Validasi input dasar (tambahkan lebih banyak jika perlu)
    if (empty($data['id_dept']) || empty($data['judul']) || empty($data['isi']) || empty($fileData)) {
        return false;
    }

    // Proses upload gambar
    $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $uploadPath = '../asset/gbr_blog/'; // Path server fisik
    $dbImagePath = upload_file($fileData, $uploadPath, $allowedImageTypes);

    if (!$dbImagePath) {
        return false; // Gagal upload gambar
    }

    // Generate slug
    $slug = generate_slug($data['judul']);
    // Opsional: Cek keunikan slug, jika duplikat, tambahkan angka/unik id
    // (Untuk simple CRUD, kita asumsikan slug unik atau tidak masalah jika mirip)

    $sql = "INSERT INTO blog_ukim (id_dept, user_id, judul, slug, gambar, isi, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        // error_log("Create Blog Prepare Error: " . mysqli_error($conn));
        delete_file($dbImagePath); // Hapus file yang sudah terupload jika query gagal disiapkan
        return false;
    }

    // Ambil status dari data, default ke 'draft' jika tidak ada
    $status = isset($data['status']) ? $data['status'] : 'draft';

    mysqli_stmt_bind_param($stmt, "iisssss",
        $data['id_dept'],
        $user_id,
        $data['judul'],
        $slug,
        $dbImagePath,
        $data['isi'],
        $status
    );

    if (mysqli_stmt_execute($stmt)) {
        $new_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return $new_id; // Berhasil, kembalikan ID blog baru
    } else {
        // error_log("Create Blog Execute Error: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        delete_file($dbImagePath); // Hapus file yang sudah terupload jika eksekusi gagal
        return false; // Gagal eksekusi
    }
}

/**
 * Mengambil semua data blog (dengan join ke departemen dan user).
 * @param int $limit Jumlah data per halaman (opsional).
 * @param int $offset Mulai dari data ke berapa (opsional).
 * @return array|false Array data blog jika berhasil, false jika gagal.
 */
function get_all_blogs($limit = null, $offset = 0) {
    global $conn;
    $sql = "SELECT b.*, d.nama_dept, u.nama_lengkap as author_name
            FROM blog_ukim b
            LEFT JOIN departments d ON b.id_dept = d.id_dept
            LEFT JOIN users u ON b.user_id = u.id_user
            ORDER BY b.created_at DESC"; // Urutkan dari terbaru

    if ($limit !== null && is_numeric($limit)) {
        $sql .= " LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    } else {
         $stmt = mysqli_prepare($conn, $sql);
    }

     if (!$stmt) {
        // error_log("Get All Blogs Prepare Error: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $blogs = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $blogs;
}

/**
 * Mengambil beberapa blog terbaru untuk ditampilkan di beranda.
 * @param int $limit Jumlah blog yang ingin ditampilkan.
 * @return array|false Array data blog jika berhasil, false jika gagal.
 */
function get_latest_blogs($limit = 3) {
    global $conn;
    // Mengambil blog yang statusnya 'published' dan diurutkan dari terbaru
    $sql = "SELECT b.id_blog, b.slug, b.judul, b.gambar, b.created_at, d.nama_dept
            FROM blog_ukim b
            LEFT JOIN departments d ON b.id_dept = d.id_dept
            WHERE b.status = 'published'
            ORDER BY b.created_at DESC
            LIMIT ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        // error_log("Get Latest Blogs Prepare Error: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $blogs = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $blogs;
}

/**
 * Mengambil data satu blog berdasarkan ID.
 * @param int $id_blog ID blog.
 * @return array|false Array data blog jika ditemukan, false jika tidak.
 */
function get_blog_by_id($id_blog) {
    global $conn;
    $sql = "SELECT b.*, d.nama_dept, u.nama_lengkap as author_name
            FROM blog_ukim b
            LEFT JOIN departments d ON b.id_dept = d.id_dept
            LEFT JOIN users u ON b.user_id = u.id_user
            WHERE b.id_blog = ?";
    $stmt = mysqli_prepare($conn, $sql);

     if (!$stmt) {
        // error_log("Get Blog By ID Prepare Error: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_blog);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $blog = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $blog; // Mengembalikan data atau null jika tidak ditemukan
}

// Di dalam conn/controller.php

/**
 * Mengambil semua data blog dengan filter opsional dan paginasi.
 * @param int|null $limit Jumlah data per halaman.
 * @param int $offset Mulai dari data ke berapa.
 * @param int|null $department_id ID departemen untuk filter.
 * @param string $status Status blog yang ingin diambil (e.g., 'published').
 * @return array|false Array data blog jika berhasil, false jika gagal.
 */
function get_all_blogs_filtered($limit = null, $offset = 0, $department_id = null, $status = 'published') {
    global $conn;
    $params = [];
    $types = "";

    $sql = "SELECT b.id_blog, b.slug, b.judul, b.gambar, b.isi, b.created_at, d.nama_dept, u.nama_lengkap as author_name
            FROM blog_ukim b
            LEFT JOIN departments d ON b.id_dept = d.id_dept
            LEFT JOIN users u ON b.user_id = u.id_user
            WHERE 1=1"; // Klausa WHERE awal

    if (!empty($status)) {
        $sql .= " AND b.status = ?";
        $params[] = $status;
        $types .= "s";
    }

    if ($department_id !== null && ctype_digit((string)$department_id) && $department_id > 0) {
        $sql .= " AND b.id_dept = ?";
        $params[] = (int)$department_id;
        $types .= "i";
    }

    $sql .= " ORDER BY b.created_at DESC";

    if ($limit !== null && is_numeric($limit) && $limit > 0) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        $types .= "ii";
    }

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        // error_log("Get All Blogs Filtered Prepare Error: " . mysqli_error($conn));
        return false;
    }

    if (!empty($types) && count($params) > 0) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $blogs = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $blogs;
}

/**
 * Menghitung total blog dengan filter opsional (untuk paginasi).
 * @param int|null $department_id
 * @param string $status
 * @return int|false
 */
function count_all_blogs_filtered($department_id = null, $status = 'published') {
    global $conn;
    $params = [];
    $types = "";

    $sql = "SELECT COUNT(b.id_blog) as total
            FROM blog_ukim b
            WHERE 1=1";

    if (!empty($status)) {
        $sql .= " AND b.status = ?";
        $params[] = $status;
        $types .= "s";
    }

     if ($department_id !== null && ctype_digit((string)$department_id) && $department_id > 0) {
        $sql .= " AND b.id_dept = ?";
        $params[] = (int)$department_id;
        $types .= "i";
    }

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        // error_log("Count Blogs Filtered Prepare Error: " . mysqli_error($conn));
        return false;
    }
     if (!empty($types) && count($params) > 0) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row ? (int)$row['total'] : 0;
}

// Di dalam conn/controller.php

/**
 * Mengambil data satu blog berdasarkan SLUG.
 * @param string $slug Slug blog.
 * @return array|false Array data blog jika ditemukan, false jika tidak.
 */
function get_blog_by_slug($slug) {
    global $conn;
    // Ambil blog yang statusnya 'published'
    $sql = "SELECT b.*, d.nama_dept, u.nama_lengkap as author_name
            FROM blog_ukim b
            LEFT JOIN departments d ON b.id_dept = d.id_dept
            LEFT JOIN users u ON b.user_id = u.id_user
            WHERE b.slug = ? AND b.status = 'published' LIMIT 1"; // Hanya ambil yang published
    $stmt = mysqli_prepare($conn, $sql);

     if (!$stmt) {
        // error_log("Get Blog By SLUG Prepare Error: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $blog = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $blog;
}

/**
 * Memperbarui data blog.
 * @param int $id_blog ID blog yang akan diperbarui.
 * @param array $data Data baru dari form ['id_dept', 'judul', 'isi', 'status'].
 * @param array|null $fileData Data file baru dari $_FILES['gambar'] atau null jika tidak ganti gambar.
 * @param int $user_id ID user yang melakukan update (opsional, untuk logging/history).
 * @return bool True jika berhasil, false jika gagal.
 */
function update_blog($id_blog, $data, $fileData, $user_id = null) {
    global $conn;

    // Ambil data blog lama untuk perbandingan dan mendapatkan path gambar lama
    $oldBlog = get_blog_by_id($id_blog);
    if (!$oldBlog) {
        return false; // Blog tidak ditemukan
    }

    $dbImagePath = $oldBlog['gambar']; // Gunakan gambar lama sebagai default
    $imageChanged = false;

    // Jika ada file gambar baru diupload
    if (isset($fileData['error']) && $fileData['error'] == UPLOAD_ERR_OK) {
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $uploadPath = '../asset/gbr_blog/';
        $newDbImagePath = upload_file($fileData, $uploadPath, $allowedImageTypes);

        if ($newDbImagePath) {
            $dbImagePath = $newDbImagePath; // Gunakan path gambar baru
            $imageChanged = true;
        } else {
            return false; // Gagal upload gambar baru, batalkan update
        }
    }

    // Generate slug baru jika judul berubah
    $slug = ($oldBlog['judul'] !== $data['judul']) ? generate_slug($data['judul']) : $oldBlog['slug'];
    $status = isset($data['status']) ? $data['status'] : $oldBlog['status'];

    $sql = "UPDATE blog_ukim SET
            id_dept = ?,
            judul = ?,
            slug = ?,
            gambar = ?,
            isi = ?,
            status = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE id_blog = ?";
    $stmt = mysqli_prepare($conn, $sql);

     if (!$stmt) {
        // error_log("Update Blog Prepare Error: " . mysqli_error($conn));
        if ($imageChanged) delete_file($dbImagePath); // Hapus gambar baru jika prepare gagal
        return false;
    }

    mysqli_stmt_bind_param($stmt, "isssssi",
        $data['id_dept'],
        $data['judul'],
        $slug,
        $dbImagePath,
        $data['isi'],
        $status,
        $id_blog
    );

    if (mysqli_stmt_execute($stmt)) {
        if ($imageChanged) {
            delete_file($oldBlog['gambar']); // Hapus gambar lama jika update berhasil & gambar diganti
        }
        mysqli_stmt_close($stmt);
        return true; // Berhasil update
    } else {
        // error_log("Update Blog Execute Error: " . mysqli_stmt_error($stmt));
        if ($imageChanged) delete_file($dbImagePath); // Hapus gambar baru jika eksekusi gagal
        mysqli_stmt_close($stmt);
        return false; // Gagal update
    }
}

/**
 * Menghapus data blog berdasarkan ID.
 * @param int $id_blog ID blog yang akan dihapus.
 * @return bool True jika berhasil, false jika gagal.
 */
function delete_blog($id_blog) {
    global $conn;

    // 1. Ambil path gambar sebelum dihapus dari DB
    $blog = get_blog_by_id($id_blog);
    if (!$blog) {
        return false; // Blog tidak ditemukan
    }
    $dbImagePath = $blog['gambar'];

    // 2. Hapus data dari database
    $sql = "DELETE FROM blog_ukim WHERE id_blog = ?";
    $stmt = mysqli_prepare($conn, $sql);

     if (!$stmt) {
        // error_log("Delete Blog Prepare Error: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_blog);

    if (mysqli_stmt_execute($stmt)) {
        // 3. Jika berhasil hapus DB, hapus file gambar dari server
        delete_file($dbImagePath);
        mysqli_stmt_close($stmt);
        return true; // Berhasil
    } else {
        // error_log("Delete Blog Execute Error: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false; // Gagal hapus DB
    }
}

// == Karya Cipta == (Struktur serupa dengan Blog, sesuaikan field)

/**
 * Menambah data karya cipta baru.
 * @param array $data ['id_category', 'judul', 'isi', 'status'].
 * @param array|null $fileData Data file pendukung opsional dari $_FILES['file_pendukung'].
 * @param int $user_id ID user pembuat.
 * @return int|false ID karya baru atau false.
 */
function create_karya($data, $fileData, $user_id) {
    global $conn;

     if (empty($data['id_category']) || empty($data['judul']) || empty($data['isi'])) {
        return false;
    }

    $dbFilePath = null; // File pendukung opsional
    // Proses upload file pendukung jika ada
    if (isset($fileData['error']) && $fileData['error'] == UPLOAD_ERR_OK) {
        // Tentukan tipe file yang diizinkan untuk karya (misal: PDF, DOCX)
        $allowedFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $uploadPath = '../asset/file_karya/'; // Direktori berbeda untuk file karya
        $dbFilePath = upload_file($fileData, $uploadPath, $allowedFileTypes, 10000000); // Max 10MB

        if (!$dbFilePath) {
            return false; // Gagal upload file pendukung
        }
    }

    $slug = generate_slug($data['judul']);
    $status = isset($data['status']) ? $data['status'] : 'pending'; // Default status 'pending'

    $sql = "INSERT INTO karya_cipta (id_category, user_id, judul, slug, isi, file_pendukung, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    $stmt = mysqli_prepare($conn, $sql);

     if (!$stmt) {
        // error_log("Create Karya Prepare Error: " . mysqli_error($conn));
        if ($dbFilePath) delete_file($dbFilePath);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "iisssss",
        $data['id_category'],
        $user_id,
        $data['judul'],
        $slug,
        $data['isi'],
        $dbFilePath, // Bisa null jika tidak ada file
        $status
    );

    if (mysqli_stmt_execute($stmt)) {
        $new_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return $new_id;
    } else {
        // error_log("Create Karya Execute Error: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        if ($dbFilePath) delete_file($dbFilePath);
        return false;
    }
}

/**
 * Mengambil semua data karya cipta.
 * @param int|null $limit
 * @param int $offset
 * @return array|false
 */
function get_all_karya($limit = null, $offset = 0) {
     global $conn;
    $sql = "SELECT k.*, kc.nama_category, u.nama_lengkap as author_name
            FROM karya_cipta k
            LEFT JOIN karya_categories kc ON k.id_category = kc.id_category
            LEFT JOIN users u ON k.user_id = u.id_user
            ORDER BY k.created_at DESC";

    if ($limit !== null && is_numeric($limit)) {
        $sql .= " LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    } else {
         $stmt = mysqli_prepare($conn, $sql);
    }

     if (!$stmt) {
        // error_log("Get All Karya Prepare Error: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $karyas = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $karyas;
}

// Di dalam conn/controller.php

/**
 * Mengambil data satu karya cipta berdasarkan SLUG.
 * @param string $slug Slug karya.
 * @return array|false Array data karya jika ditemukan, false jika tidak.
 */
function get_karya_by_slug($slug) {
    global $conn;
    // Ambil karya yang statusnya 'published'
    $sql = "SELECT k.*, kc.nama_category, u.nama_lengkap as author_name
            FROM karya_cipta k
            LEFT JOIN karya_categories kc ON k.id_category = kc.id_category
            LEFT JOIN users u ON k.user_id = u.id_user
            WHERE k.slug = ? AND k.status = 'published' LIMIT 1"; // Hanya ambil yang published
    $stmt = mysqli_prepare($conn, $sql);

     if (!$stmt) {
        // error_log("Get Karya By SLUG Prepare Error: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $karya = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $karya;
}

/**
 * Mengambil semua data karya cipta dengan filter opsional dan paginasi.
 * @param int|null $limit
 * @param int $offset
 * @param int|null $category_id ID kategori untuk filter.
 * @param string $status Status karya yang ingin diambil.
 * @return array|false
 */
function get_all_karya_filtered($limit = null, $offset = 0, $category_id = null, $status = 'published') {
    global $conn;
    $params = [];
    $types = "";

    $sql = "SELECT k.id_karya, k.slug, k.judul, k.isi, k.created_at, k.file_pendukung, kc.nama_category, u.nama_lengkap as author_name
            FROM karya_cipta k
            LEFT JOIN karya_categories kc ON k.id_category = kc.id_category
            LEFT JOIN users u ON k.user_id = u.id_user
            WHERE 1=1";

    if (!empty($status)) {
        $sql .= " AND k.status = ?";
        $params[] = $status;
        $types .= "s";
    }

    if ($category_id !== null && ctype_digit((string)$category_id) && $category_id > 0) {
        $sql .= " AND k.id_category = ?";
        $params[] = (int)$category_id;
        $types .= "i";
    }

    $sql .= " ORDER BY k.created_at DESC";

    if ($limit !== null && is_numeric($limit) && $limit > 0) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        $types .= "ii";
    }

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        // error_log("Get All Karya Filtered Prepare Error: " . mysqli_error($conn));
        return false;
    }

    if (!empty($types) && count($params) > 0) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $karyas = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $karyas;
}

/**
 * Menghitung total karya cipta dengan filter opsional (untuk paginasi).
 * @param int|null $category_id
 * @param string $status
 * @return int|false
 */
function count_all_karya_filtered($category_id = null, $status = 'published') {
    global $conn;
    $params = [];
    $types = "";

    $sql = "SELECT COUNT(k.id_karya) as total
            FROM karya_cipta k
            WHERE 1=1";

    if (!empty($status)) {
        $sql .= " AND k.status = ?";
        $params[] = $status;
        $types .= "s";
    }

    if ($category_id !== null && ctype_digit((string)$category_id) && $category_id > 0) {
        $sql .= " AND k.id_category = ?";
        $params[] = (int)$category_id;
        $types .= "i";
    }

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        // error_log("Count Karya Filtered Prepare Error: " . mysqli_error($conn));
        return false;
    }
     if (!empty($types) && count($params) > 0) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row ? (int)$row['total'] : 0;
}

/**
 * Mengambil data satu karya cipta berdasarkan ID.
 * @param int $id_karya
 * @return array|false
 */
function get_karya_by_id($id_karya) {
    global $conn;
    $sql = "SELECT k.*, kc.nama_category, u.nama_lengkap as author_name
            FROM karya_cipta k
            LEFT JOIN karya_categories kc ON k.id_category = kc.id_category
            LEFT JOIN users u ON k.user_id = u.id_user
            WHERE k.id_karya = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
       // error_log("Get Karya By ID Prepare Error: " . mysqli_error($conn));
       return false;
   }

    mysqli_stmt_bind_param($stmt, "i", $id_karya);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $karya = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $karya;
}

/**
 * Memperbarui data karya cipta.
 * @param int $id_karya
 * @param array $data ['id_category', 'judul', 'isi', 'status'].
 * @param array|null $fileData File pendukung baru atau null.
 * @return bool
 */
function update_karya($id_karya, $data, $fileData) {
    global $conn;
    $oldKarya = get_karya_by_id($id_karya);
    if (!$oldKarya) return false;

    $dbFilePath = $oldKarya['file_pendukung'];
    $fileChanged = false;

    // Handle file upload baru
    if (isset($fileData['error']) && $fileData['error'] == UPLOAD_ERR_OK) {
        $allowedFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $uploadPath = '../asset/file_karya/';
        $newDbFilePath = upload_file($fileData, $uploadPath, $allowedFileTypes, 10000000);

        if ($newDbFilePath) {
            $dbFilePath = $newDbFilePath;
            $fileChanged = true;
        } else {
            return false; // Gagal upload file baru
        }
    }

    $slug = ($oldKarya['judul'] !== $data['judul']) ? generate_slug($data['judul']) : $oldKarya['slug'];
    $status = isset($data['status']) ? $data['status'] : $oldKarya['status'];

    $sql = "UPDATE karya_cipta SET
            id_category = ?, judul = ?, slug = ?, isi = ?, file_pendukung = ?, status = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id_karya = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        // error_log("Update Karya Prepare Error: " . mysqli_error($conn));
        if ($fileChanged) delete_file($dbFilePath);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "isssssi",
        $data['id_category'], $data['judul'], $slug, $data['isi'], $dbFilePath, $status, $id_karya);

    if (mysqli_stmt_execute($stmt)) {
        if ($fileChanged && $oldKarya['file_pendukung']) {
            delete_file($oldKarya['file_pendukung']); // Hapus file lama
        }
        mysqli_stmt_close($stmt);
        return true;
    } else {
        // error_log("Update Karya Execute Error: " . mysqli_stmt_error($stmt));
        if ($fileChanged) delete_file($dbFilePath); // Hapus file baru jika gagal
        mysqli_stmt_close($stmt);
        return false;
    }
}

/**
 * Menghapus data karya cipta.
 * @param int $id_karya
 * @return bool
 */
function delete_karya($id_karya) {
    global $conn;
    $karya = get_karya_by_id($id_karya);
    if (!$karya) return false;
    $dbFilePath = $karya['file_pendukung'];

    $sql = "DELETE FROM karya_cipta WHERE id_karya = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        // error_log("Delete Karya Prepare Error: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_karya);

    if (mysqli_stmt_execute($stmt)) {
        if ($dbFilePath) {
            delete_file($dbFilePath); // Hapus file pendukung jika ada
        }
        mysqli_stmt_close($stmt);
        return true;
    } else {
        // error_log("Delete Karya Execute Error: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
    }
}


// == Events == (Struktur serupa, sesuaikan field: tgl_event_mulai, tgl_event_selesai, lokasi, link_pendaftaran, link_info, etc.)

/**
 * Menambah data event baru.
 * @param array $data ['judul', 'tema', 'deskripsi', 'tgl_event_mulai', 'tgl_event_selesai', 'lokasi', 'link_pendaftaran', 'link_info', 'status'].
 * @param array $fileData Data file gambar dari $_FILES['gambar'].
 * @param int $user_id ID user pembuat.
 * @return int|false ID event baru atau false.
 */
function create_event($data, $fileData, $user_id) {
    global $conn;

    // Validasi dasar
    if (empty($data['judul']) || empty($data['deskripsi']) || empty($data['tgl_event_mulai']) || empty($data['lokasi']) || empty($fileData)) {
        return false;
    }

    // Upload gambar event
    $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $uploadPath = '../asset/gbr_event/';
    $dbImagePath = upload_file($fileData, $uploadPath, $allowedImageTypes);
    if (!$dbImagePath) return false;

    $slug = generate_slug($data['judul']);
    $status = isset($data['status']) ? $data['status'] : 'upcoming'; // Default 'upcoming'
    // Pastikan format tanggal sesuai dengan MySQL DATETIME ('YYYY-MM-DD HH:MM:SS') atau DATE ('YYYY-MM-DD')
    // Jika input dari form type="date", mungkin perlu ditambah jam default
    $tgl_mulai = date('Y-m-d H:i:s', strtotime($data['tgl_event_mulai']));
    $tgl_selesai = !empty($data['tgl_event_selesai']) ? date('Y-m-d H:i:s', strtotime($data['tgl_event_selesai'])) : null;

    $sql = "INSERT INTO events (user_id, judul, slug, tema, deskripsi, tgl_event_mulai, tgl_event_selesai, lokasi, gambar, link_pendaftaran, link_info, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        // error_log("Create Event Prepare Error: " . mysqli_error($conn));
        delete_file($dbImagePath);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "isssssssssss",
        $user_id, $data['judul'], $slug, $data['tema'], $data['deskripsi'],
        $tgl_mulai, $tgl_selesai, $data['lokasi'], $dbImagePath,
        $data['link_pendaftaran'], $data['link_info'], $status
    );

    if (mysqli_stmt_execute($stmt)) {
        $new_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return $new_id;
    } else {
        // error_log("Create Event Execute Error: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        delete_file($dbImagePath);
        return false;
    }
}

/**
 * Mengambil semua data event.
 * @param int|null $limit
 * @param int $offset
 * @return array|false
 */
function get_all_events($limit = null, $offset = 0) {
     global $conn;
    $sql = "SELECT e.*, u.nama_lengkap as creator_name
            FROM events e
            LEFT JOIN users u ON e.user_id = u.id_user
            ORDER BY e.tgl_event_mulai DESC"; // Urutkan dari tanggal event terbaru

    if ($limit !== null && is_numeric($limit)) {
        $sql .= " LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    } else {
         $stmt = mysqli_prepare($conn, $sql);
    }

     if (!$stmt) {
        // error_log("Get All Events Prepare Error: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $events = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $events;
}

/**
 * Mengambil data satu event berdasarkan ID.
 * @param int $id_event
 * @return array|false
 */
function get_event_by_id($id_event) {
    global $conn;
    $sql = "SELECT e.*, u.nama_lengkap as creator_name
            FROM events e
            LEFT JOIN users u ON e.user_id = u.id_user
            WHERE e.id_event = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
       // error_log("Get Event By ID Prepare Error: " . mysqli_error($conn));
       return false;
   }

    mysqli_stmt_bind_param($stmt, "i", $id_event);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $event = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $event;
}

/**
 * Memperbarui data event.
 * @param int $id_event
 * @param array $data ['judul', 'tema', 'deskripsi', 'tgl_event_mulai', 'tgl_event_selesai', 'lokasi', 'link_pendaftaran', 'link_info', 'status'].
 * @param array|null $fileData Gambar baru atau null.
 * @return bool
 */
function update_event($id_event, $data, $fileData) {
    global $conn;
    $oldEvent = get_event_by_id($id_event);
    if (!$oldEvent) return false;

    $dbImagePath = $oldEvent['gambar'];
    $imageChanged = false;

    if (isset($fileData['error']) && $fileData['error'] == UPLOAD_ERR_OK) {
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $uploadPath = '../asset/gbr_event/';
        $newDbImagePath = upload_file($fileData, $uploadPath, $allowedImageTypes);
        if ($newDbImagePath) {
            $dbImagePath = $newDbImagePath;
            $imageChanged = true;
        } else {
            return false; // Gagal upload gambar baru
        }
    }

    $slug = ($oldEvent['judul'] !== $data['judul']) ? generate_slug($data['judul']) : $oldEvent['slug'];
    $status = isset($data['status']) ? $data['status'] : $oldEvent['status'];
    $tgl_mulai = date('Y-m-d H:i:s', strtotime($data['tgl_event_mulai']));
    $tgl_selesai = !empty($data['tgl_event_selesai']) ? date('Y-m-d H:i:s', strtotime($data['tgl_event_selesai'])) : null;

    $sql = "UPDATE events SET
            judul = ?, slug = ?, tema = ?, deskripsi = ?, tgl_event_mulai = ?, tgl_event_selesai = ?,
            lokasi = ?, gambar = ?, link_pendaftaran = ?, link_info = ?, status = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id_event = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
       // error_log("Update Event Prepare Error: " . mysqli_error($conn));
       if ($imageChanged) delete_file($dbImagePath);
       return false;
   }

    mysqli_stmt_bind_param($stmt, "sssssssssssi",
        $data['judul'], $slug, $data['tema'], $data['deskripsi'], $tgl_mulai, $tgl_selesai,
        $data['lokasi'], $dbImagePath, $data['link_pendaftaran'], $data['link_info'], $status,
        $id_event
    );

    if (mysqli_stmt_execute($stmt)) {
        if ($imageChanged && $oldEvent['gambar']) {
            delete_file($oldEvent['gambar']);
        }
        mysqli_stmt_close($stmt);
        return true;
    } else {
        // error_log("Update Event Execute Error: " . mysqli_stmt_error($stmt));
        if ($imageChanged) delete_file($dbImagePath);
        mysqli_stmt_close($stmt);
        return false;
    }
}

/**
 * Menghapus data event.
 * @param int $id_event
 * @return bool
 */
function delete_event($id_event) {
    global $conn;
    $event = get_event_by_id($id_event);
    if (!$event) return false;
    $dbImagePath = $event['gambar'];

    $sql = "DELETE FROM events WHERE id_event = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        // error_log("Delete Event Prepare Error: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_event);

    if (mysqli_stmt_execute($stmt)) {
        if ($dbImagePath) {
            delete_file($dbImagePath);
        }
        mysqli_stmt_close($stmt);
        return true;
    } else {
        // error_log("Delete Event Execute Error: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
    }
}

// == Prestasi == (Tabel baru)

/**
 * Menambah data prestasi baru.
 * @param array $data ['nama_lomba', 'tingkat', 'juara', 'tahun', 'penyelenggara', 'peserta_nama', 'is_tim', 'deskripsi'].
 * @param array|null $fileData Data file bukti opsional dari $_FILES['bukti'].
 * @param int $user_id_pencatat ID user yang mencatat (dari session).
 * @return int|false ID prestasi baru atau false.
 */
function create_prestasi($data, $fileData, $user_id_pencatat) {
    global $conn;

    if (empty($data['nama_lomba']) || empty($data['tingkat']) || empty($data['juara']) || empty($data['tahun']) || empty($data['peserta_nama'])) {
        return false;
    }

    $dbBuktiPath = null;
    if (isset($fileData['error']) && $fileData['error'] == UPLOAD_ERR_OK) {
        // Izinkan gambar atau PDF sebagai bukti
        $allowedProofTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
        $uploadPath = '../asset/bukti_prestasi/';
        $dbBuktiPath = upload_file($fileData, $uploadPath, $allowedProofTypes, 10000000); // Max 10MB
        if (!$dbBuktiPath) return false; // Gagal upload bukti
    }

    $is_tim = isset($data['is_tim']) ? (int)$data['is_tim'] : 0; // Default perorangan

    $sql = "INSERT INTO prestasi (user_id_pencatat, nama_lomba, tingkat, juara, tahun, penyelenggara, peserta_nama, is_tim, deskripsi, bukti, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        // error_log("Create Prestasi Prepare Error: " . mysqli_error($conn));
        if ($dbBuktiPath) delete_file($dbBuktiPath);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "isssississ",
        $user_id_pencatat, $data['nama_lomba'], $data['tingkat'], $data['juara'], $data['tahun'],
        $data['penyelenggara'], $data['peserta_nama'], $is_tim, $data['deskripsi'], $dbBuktiPath
    );

     if (mysqli_stmt_execute($stmt)) {
        $new_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return $new_id;
    } else {
        // error_log("Create Prestasi Execute Error: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        if ($dbBuktiPath) delete_file($dbBuktiPath);
        return false;
    }
}

/**
 * Mengambil semua data prestasi.
 * @param int|null $limit
 * @param int $offset
 * @return array|false
 */
function get_all_prestasi($limit = null, $offset = 0) {
    global $conn;
    $sql = "SELECT p.*, u.nama_lengkap as pencatat_name
            FROM prestasi p
            LEFT JOIN users u ON p.user_id_pencatat = u.id_user
            ORDER BY p.tahun DESC, p.id_prestasi DESC"; // Urutkan dari tahun terbaru

    if ($limit !== null && is_numeric($limit)) {
        $sql .= " LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    } else {
         $stmt = mysqli_prepare($conn, $sql);
    }

     if (!$stmt) {
        // error_log("Get All Prestasi Prepare Error: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $prestasi = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $prestasi;
}

/**
 * Mengambil data satu prestasi berdasarkan ID.
 * @param int $id_prestasi
 * @return array|false
 */
function get_prestasi_by_id($id_prestasi) {
     global $conn;
    $sql = "SELECT p.*, u.nama_lengkap as pencatat_name
            FROM prestasi p
            LEFT JOIN users u ON p.user_id_pencatat = u.id_user
            WHERE p.id_prestasi = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
       // error_log("Get Prestasi By ID Prepare Error: " . mysqli_error($conn));
       return false;
   }

    mysqli_stmt_bind_param($stmt, "i", $id_prestasi);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $prestasi = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $prestasi;
}

/**
 * Memperbarui data prestasi.
 * @param int $id_prestasi
 * @param array $data ['nama_lomba', 'tingkat', 'juara', 'tahun', 'penyelenggara', 'peserta_nama', 'is_tim', 'deskripsi'].
 * @param array|null $fileData Bukti baru atau null.
 * @return bool
 */
function update_prestasi($id_prestasi, $data, $fileData) {
     global $conn;
    $oldPrestasi = get_prestasi_by_id($id_prestasi);
    if (!$oldPrestasi) return false;

    $dbBuktiPath = $oldPrestasi['bukti'];
    $fileChanged = false;

    if (isset($fileData['error']) && $fileData['error'] == UPLOAD_ERR_OK) {
        $allowedProofTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
        $uploadPath = '../asset/bukti_prestasi/';
        $newDbBuktiPath = upload_file($fileData, $uploadPath, $allowedProofTypes, 10000000);
        if ($newDbBuktiPath) {
            $dbBuktiPath = $newDbBuktiPath;
            $fileChanged = true;
        } else {
            return false; // Gagal upload bukti baru
        }
    }

    $is_tim = isset($data['is_tim']) ? (int)$data['is_tim'] : $oldPrestasi['is_tim'];

    $sql = "UPDATE prestasi SET
            nama_lomba = ?, tingkat = ?, juara = ?, tahun = ?, penyelenggara = ?,
            peserta_nama = ?, is_tim = ?, deskripsi = ?, bukti = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id_prestasi = ?";
    $stmt = mysqli_prepare($conn, $sql);

     if (!$stmt) {
        // error_log("Update Prestasi Prepare Error: " . mysqli_error($conn));
        if ($fileChanged) delete_file($dbBuktiPath);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "sssssisssi",
        $data['nama_lomba'], $data['tingkat'], $data['juara'], $data['tahun'], $data['penyelenggara'],
        $data['peserta_nama'], $is_tim, $data['deskripsi'], $dbBuktiPath,
        $id_prestasi
    );

    if (mysqli_stmt_execute($stmt)) {
        if ($fileChanged && $oldPrestasi['bukti']) {
            delete_file($oldPrestasi['bukti']);
        }
        mysqli_stmt_close($stmt);
        return true;
    } else {
        // error_log("Update Prestasi Execute Error: " . mysqli_stmt_error($stmt));
        if ($fileChanged) delete_file($dbBuktiPath);
        mysqli_stmt_close($stmt);
        return false;
    }
}

/**
 * Menghapus data prestasi.
 * @param int $id_prestasi
 * @return bool
 */
function delete_prestasi($id_prestasi) {
    global $conn;
    $prestasi = get_prestasi_by_id($id_prestasi);
    if (!$prestasi) return false;
    $dbBuktiPath = $prestasi['bukti'];

    $sql = "DELETE FROM prestasi WHERE id_prestasi = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        // error_log("Delete Prestasi Prepare Error: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_prestasi);

    if (mysqli_stmt_execute($stmt)) {
        if ($dbBuktiPath) {
            delete_file($dbBuktiPath);
        }
        mysqli_stmt_close($stmt);
        return true;
    } else {
       // error_log("Delete Prestasi Execute Error: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
    }
}


// == Supporting Data (Read-only examples) ==

/**
 * Mengambil semua departemen.
 * @return array|false
 */
function get_all_departments() {
    global $conn;
    $sql = "SELECT id_dept, kode_dept, nama_dept FROM departments ORDER BY nama_dept ASC";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        // error_log("Get All Depts Error: " . mysqli_error($conn));
        return false;
    }
}

/**
 * Mengambil semua kategori karya.
 * @return array|false
 */
function get_all_karya_categories() {
    global $conn;
    $sql = "SELECT id_category, nama_category FROM karya_categories ORDER BY nama_category ASC";
    $result = mysqli_query($conn, $sql);
     if ($result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        // error_log("Get All Karya Cats Error: " . mysqli_error($conn));
        return false;
    }
}

// --- Old Delete Logic Handler (Improved) ---
// Pertimbangkan memindahkan logic ini ke file admin atau menggunakan routing yang lebih baik.
// Sebaiknya gunakan metode POST untuk delete untuk keamanan (CSRF).
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] == 'delete') {
    // Minimal check: pastikan user sudah login dan memiliki hak akses (misal admin/pengurus)
    if (!isset($_SESSION['user_id']) /* || !in_array($_SESSION['user_role'], ['admin', 'pengurus']) */ ) {
        echo "<script>alert('Akses ditolak. Silakan login.'); window.location='../login.php';</script>"; // Sesuaikan redirect
        exit();
    }

    if (isset($_GET['type']) && isset($_GET['id']) && ctype_digit($_GET['id'])) { // Pastikan ID adalah angka
        $id = (int)$_GET['id'];
        $type = $_GET['type'];
        $success = false;
        $redirectPage = '../admin/admin.php'; // Default redirect

        switch ($type) {
            case 'blog':
                $success = delete_blog($id);
                $redirectPage = '../admin/article.php'; // Sesuaikan
                break;
            case 'karya':
                $success = delete_karya($id);
                $redirectPage = '../admin/karya.php'; // Sesuaikan
                break;
            case 'event':
                $success = delete_event($id);
                $redirectPage = '../admin/event.php'; // Sesuaikan
                break;
            case 'prestasi': // Tambahkan case untuk prestasi
                $success = delete_prestasi($id);
                $redirectPage = '../admin/prestasi.php'; // Sesuaikan
                break;
            default:
                echo "<script>alert('Tipe data tidak valid'); window.location='$redirectPage';</script>";
                exit();
        }

        if ($success) {
            // Berhasil hapus, redirect tanpa pesan atau dengan pesan sukses via session flash message
            // header("Location: $redirectPage?status=deleted"); // Contoh redirect
             echo "<script>alert('Data berhasil dihapus.'); window.location='$redirectPage';</script>"; // Alternatif JS
        } else {
            // Gagal hapus
            echo "<script>alert('Gagal menghapus data. Cek log untuk detail.'); window.location='$redirectPage';</script>";
        }
        exit(); // Hentikan eksekusi setelah redirect atau alert

    } else {
        echo "<script>alert('ID atau tipe data tidak valid'); window.location='../admin/admin.php';</script>";
        exit();
    }
}

// --- Old Login Logic Handler (Improved) ---
// Pertimbangkan memindahkan ini ke file login.php
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (login_user($username, $password)) {
            // Login berhasil, redirect ke halaman admin
            header("Location: /web_ukim/admin/admin.php"); // Sesuaikan path
            exit();
        } else {
            // Login gagal
            // Sebaiknya tampilkan pesan error di halaman login itu sendiri, bukan via alert
            header("Location: ./login.php?error=1"); // Redirect kembali ke login dengan flag error
            // echo "<script>alert('Username atau password salah!'); window.location='../login.php';</script>"; // Kurang ideal
            exit();
        }
    } else {
        // Form tidak lengkap
         header("Location: ./login.php?error=2");
         exit();
    }
}

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] == 'logout') {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    logout_user();
}

?>