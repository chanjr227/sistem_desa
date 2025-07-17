<?php
require 'config/config.php';
require 'helpers/log_helpers.php';
session_start();

// Redirect jika sudah login
if (isset($_SESSION['log']) && in_array($_SESSION['role'], ['admin', 'staff_desa', 'rt'])) {
    header('Location: admin/dashboard.php');
    exit;
}

$message = null;
$messageType = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // ❌ Versi raw SQL yang rentan SQL Injection
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($koneksi, $sql);

    if ($result && $data = mysqli_fetch_assoc($result)) {
        if ($data['is_locked']) {
            $message = "⚠️ Akun Anda dikunci karena terlalu banyak percobaan login gagal.";
            $messageType = "error";
        } elseif ($password == $data['password']) { // ❌ Tidak menggunakan hash (juga berbahaya!)
            $_SESSION['log'] = true;
            $_SESSION['userid'] = $data['userid'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['nama'] = $data['name'];
            $_SESSION['last_active'] = time();

            header('Location: index.php');
            exit;
        } else {
            $message = "Email atau password salah!";
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

            <!-- Google reCAPTCHA v2 -->
            <div class="g-recaptcha" data-sitekey=""></div>

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
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>

</html>