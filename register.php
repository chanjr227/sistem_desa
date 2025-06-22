<?php
require 'config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($password !== $password_confirm) {
        $error = "Password dan konfirmasi tidak cocok!";
    } else {
        $check = $koneksi->prepare("SELECT email FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $role = "user";

            $stmt = $koneksi->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nama, $email, $hashedPassword, $role);

            if ($stmt->execute()) {
                header("Location: login.php?register=success");
                exit();
            } else {
                $error = "Gagal registrasi: " . $koneksi->error;
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link rel="stylesheet" href="css/login.css"> <!-- Sama seperti login -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="box">
        <form method="POST">
            <h1>Registrasi</h1>

            <?php if (isset($error)): ?>
                <div class="alert error"><?= $error ?></div>
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
                <label>Password</label>
                <i class="fa-solid fa-lock"></i>
            </div>

            <div class="input-box">
                <input type="password" name="password_confirm" required>
                <label>Konfirmasi Password</label>
                <i class="fa-solid fa-lock"></i>
            </div>

            <button type="submit" class="btnLogin">Daftar</button>

            <div class="registrasi">
                <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </form>
    </div>
</body>
</html>
