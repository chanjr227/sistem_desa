<?php
require 'config/config.php';
session_start();

// Jika user sudah login, arahkan berdasarkan role
if (isset($_SESSION['log'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$message = null;
$messageType = null;

// Jika form disubmit (metode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        $data = null;
    }

    if ($data && password_verify($password, $data['password'])) {
        $_SESSION['log'] = true;
        $_SESSION['userid'] = $data['userid'];
        $_SESSION['email'] = $data['email'];
        $_SESSION['role'] = $data['role'];

        // Pesan login berhasil hanya untuk user biasa (bukan admin)
        if ($data['role'] === 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            $_SESSION['login_success'] = "Login berhasil. Selamat datang!";
            header('Location: index.php');
        }
        exit;
    } else {
        $message = "Email atau password salah!";
        $messageType = "error";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>Login</title>
</head>
<body>
    <div class="box">
        <form method="POST">
            <h1>Login</h1>

            <?php if ($message): ?>
    <div class="alert <?= $messageType ?>">
        <?= $message ?>
    </div>
<?php endif; ?>


            <div class="input-box">
                <input type="text" name="email" required>
                <label for="">Email</label>
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="input-box">
                <input type="password" id="pass" name="password" required>
                <label for="">password</label>
                <button type="button" id="btnView" onclick="fnView()" title="lihat password"><i class="fa-solid fa-eye-slash"></i></button>
            </div>
            <div class="extra">
                <label for="">
                    <input type="checkbox" name="">ingat saya
                </label>
                <a href="">Lupa password</a>
            </div>
            <button type="submit" class="btnLogin">Login</button>
            <div class="registrasi">
                <p>Belum punya akun ? <a href="register.php">Daftar</a></p>
            </div>
        </form>
    </div>

    <script src="js/login.js"></script>
</body>
</html>