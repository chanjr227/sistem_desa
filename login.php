<?php
require 'config/config.php';
require 'helpers/log_helpers.php';
session_start();

// Tampilkan notifikasi jika logout otomatis karena timeout
if (isset($_GET['timeout'])) {
    $message = "⏱️ Sesi Anda telah berakhir karena tidak aktif selama 20 menit.";
    $messageType = "warning";
}

if (isset($_SESSION['log'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$message = $message ?? null;
$messageType = $messageType ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);

        if ($data['is_locked']) {
            $message = "⚠️ Akun Anda dikunci karena terlalu banyak percobaan login gagal.<br>
                <a href='user/kirim-unlock-link.php?email=" . urlencode($email) . "'>Klik di sini untuk buka blokir via email</a>";
            $messageType = "error";
        } elseif (password_verify($password, $data['password'])) {
            $stmt = $koneksi->prepare("UPDATE users SET login_attempts = 0, is_locked = 0 WHERE userid = ?");
            $stmt->bind_param("i", $data['userid']);
            $stmt->execute();

            $_SESSION['log'] = true;
            $_SESSION['userid'] = $data['userid'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['nama'] = $data['name']; // agar bisa dipakai di log
            $_SESSION['last_active'] = time(); // ⏱️ tracking waktu aktif

            simpan_log($koneksi, $data['userid'], $data['name'], 'Login berhasil');

            if ($data['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                $_SESSION['login_success'] = "Login berhasil. Selamat datang!";
                header('Location: index.php');
            }
            exit;
        } else {
            $attempts = $data['login_attempts'] + 1;
            $is_locked = $attempts >= 3 ? 1 : 0;

            $stmt = $koneksi->prepare("UPDATE users SET login_attempts = ?, is_locked = ? WHERE userid = ?");
            $stmt->bind_param("iii", $attempts, $is_locked, $data['userid']);
            $stmt->execute();

            if ($is_locked) {
                $message = "⚠️ Akun Anda telah dikunci karena 3 kali login gagal.<br>
                    <a href='user/kirim-unlock-link.php?email=" . urlencode($email) . "'>Klik di sini untuk buka blokir via email</a>";
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <label>
                    <input type="checkbox" name=""> Ingat saya
                </label>
                <a href="#">Lupa password</a>
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
