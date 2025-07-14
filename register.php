<?php
require 'config/config.php';
session_start();

// Redirect jika sudah login
if (isset($_SESSION['log'])) {
    header("Location: index.php");
    exit;
}

// Generate CSRF token
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ✅ CSRF Protection
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        die("⚠️ Permintaan tidak valid (CSRF).");
    }

    // ✅ reCAPTCHA v2 Verification
    if (empty($_POST['g-recaptcha-response'])) {
        $error = "⚠️ Silakan centang reCAPTCHA terlebih dahulu.";
    } else {
        $recaptcha = $_POST['g-recaptcha-response'];
        $secretKey = ''; // Ganti dengan secret key kamu
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptcha");
        $captchaResponse = json_decode($verify, true);

        if (!$captchaResponse["success"]) {
            $error = "❌ Verifikasi reCAPTCHA gagal. Silakan coba lagi.";
        }
    }

    // Jika tidak ada error dari captcha, lanjut validasi form
    if (!$error) {
        $nama = htmlspecialchars(trim($_POST['nama'] ?? ''));
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (!$email) {
            $error = "Email tidak valid.";
        } elseif (strlen($password) < 8) {
            $error = "Password minimal 8 karakter.";
        } elseif ($password !== $password_confirm) {
            $error = "Password dan konfirmasi tidak cocok!";
        } else {
            // ✅ SQL Injection Prevention
            $check = $koneksi->prepare("SELECT email FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = "Email sudah terdaftar!";
            } else {
                // ✅ Password Hashing
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $role = "user";

                $stmt = $koneksi->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $nama, $email, $hashedPassword, $role);

                if ($stmt->execute()) {
                    header("Location: login.php?register=success");
                    exit();
                } else {
                    $error = "Terjadi kesalahan saat registrasi. Silakan coba lagi.";
                }
                $stmt->close();
            }
            $check->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registrasi</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
    <div class="box">
        <form method="POST" autocomplete="off">
            <h1>Registrasi</h1>

            <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="input-box">
                <input type="text" name="nama" required>
                <label>Nama Lengkap</label>
                <i class="fa-solid fa-user"></i>
            </div>

            <div class="input-box">
                <input type="email" name="email" required>
                <label>Email</label>
                <i class="fa-solid fa-envelope"></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" required>
                <label>Password (min 8 karakter)</label>
                <i class="fa-solid fa-lock"></i>
            </div>

            <div class="input-box">
                <input type="password" name="password_confirm" required>
                <label>Konfirmasi Password</label>
                <i class="fa-solid fa-lock"></i>
            </div>

            <!-- ✅ reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey=""></div>

            <!-- ✅ CSRF Token -->
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

            <button type="submit" class="btnLogin">Daftar</button>
            <div class="registrasi">
                <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </form>
    </div>
</body>

</html>