<?php
require 'config/config.php';
require 'helpers/log_helpers.php';
session_start();

// ✅ Tambahan: Generate CSRF token saat form login ditampilkan
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Notifikasi timeout logout
if (isset($_GET['timeout'])) {
    $message = "⏱️ Sesi Anda telah berakhir karena tidak aktif selama 20 menit.";
    $messageType = "warning";
}

// Redirect jika sudah login
if (isset($_SESSION['log']) && in_array($_SESSION['role'], ['admin', 'staff_desa', 'rt'])) {
    header('Location: admin/dashboard.php');
    exit;
}

$message = $message ?? null;
$messageType = $messageType ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // ✅ Tambahan: Verifikasi CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("🚫 Permintaan tidak sah (CSRF terdeteksi)");
    }

    // SQL Injection protection
    $stmt = $koneksi->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $data = $result->fetch_assoc()) {
        if ($data['is_locked']) {
            $message = "⚠️ Akun Anda dikunci karena terlalu banyak percobaan login gagal.<br>
                <a href='user/reset-request.php?email=" . urlencode($email) . "'>Klik di sini untuk reset password via email</a>";
            $messageType = "error";
        } elseif (password_verify($password, $data['password'])) {
            // ✅ Tambahan: Regenerasi session ID
            session_regenerate_id(true);

            // Reset percobaan login
            $stmt = $koneksi->prepare("UPDATE users SET login_attempts = 0, is_locked = 0 WHERE userid = ?");
            $stmt->bind_param("i", $data['userid']);
            $stmt->execute();

            // Set session
            $_SESSION['log'] = true;
            $_SESSION['userid'] = $data['userid'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['nama'] = $data['name'];
            $_SESSION['last_active'] = time();

            // Logging
            simpan_log($koneksi, $data['userid'], $data['name'], 'Login berhasil');

            if (in_array($data['role'], ['admin', 'staff_desa', 'rt'])) {
                header('Location: admin/dashboard.php');
            } else {
                $_SESSION['login_success'] = "Login berhasil. Selamat datang!";
                header('Location: index.php');
            }
            exit;
        } else {
            // Brute force protection
            $attempts = $data['login_attempts'] + 1;
            $is_locked = $attempts >= 3 ? 1 : 0;

            $stmt = $koneksi->prepare("UPDATE users SET login_attempts = ?, is_locked = ? WHERE userid = ?");
            $stmt->bind_param("iii", $attempts, $is_locked, $data['userid']);
            $stmt->execute();

            simpan_log($koneksi, $data['userid'], $data['name'], "Login gagal (ke-$attempts)");

            if ($is_locked) {
                simpan_log($koneksi, $data['userid'], $data['name'], "Akun dikunci otomatis");
                $message = "⚠️ Akun Anda telah dikunci karena 3 kali login gagal.<br>
                    <a href='user/reset-request.php?email=" . urlencode($email) . "'>Klik di sini untuk reset password via email</a>";
            } else {
                $message = "Email atau password salah! Percobaan ke-{$attempts}.";
            }

            $messageType = "error";
        }
    } else {
        $message = "Akun tidak ditemukan!";
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <div class="box">
        <form method="POST">
            <h1>Login</h1>

            <?php if ($message): ?>
                <div class="alert <?= htmlspecialchars($messageType) ?>" style="font-size: 14px; line-height: 1.5;">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <!-- ✅ CSRF token -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="input-box">
                <input type="text" name="email" required>
                <label>Email</label>
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="input-box">
                <input type="password" id="pass" name="password" required>
                <label>Password</label>
                <button type="button" id="btnView" onclick="fnView()" title="Lihat Password">
                    <i class="fa-solid fa-eye-slash"></i>
                </button>
            </div>
            <div class="extra">
                <label><input type="checkbox" name=""> Ingat saya</label>
                <a href="user/reset-request.php">Lupa password?</a>
            </div>
            <button type="submit" class="btnLogin">Login</button>
            <div class="registrasi">
                <p>Belum punya akun? <a href="register.php">Daftar</a></p>
            </div>
        </form>
    </div>
    <script src="js/login.js"></script>
</body>

</html>