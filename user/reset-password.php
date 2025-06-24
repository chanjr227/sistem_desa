<?php
require '../config/config.php';

$token = $_GET['token'] ?? '';
$success = false;
$error = '';
$user = null;

if ($token) {
    $stmt = $koneksi->prepare("SELECT userid, token_expiry FROM users WHERE unlock_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || strtotime($user['token_expiry']) < time()) {
        $error = "❌ Token tidak valid atau sudah kedaluwarsa.";
    }
} else {
    $error = "❌ Token tidak ditemukan.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (strlen($password) < 6) {
        $error = "❌ Password minimal 6 karakter.";
    } elseif ($password !== $confirm) {
        $error = "❌ Password dan konfirmasi tidak cocok.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $koneksi->prepare("UPDATE users SET password = ?, is_locked = 0, login_attempts = 0, unlock_token = NULL, token_expiry = NULL WHERE userid = ?");
        $stmt->bind_param("si", $hashed, $user['userid']);
        $stmt->execute();
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 shadow" style="max-width: 400px;">
        <?php if ($success): ?>
            <h4 class="text-success mb-3">✅ Password berhasil diubah!</h4>
            <a href="../login.php" class="btn btn-success w-100">➡️ Kembali ke Login</a>
        <?php elseif ($user): ?>
            <h4 class="mb-3">🔐 Atur Password Baru</h4>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label>Password Baru</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="mb-3">
                    <label>Ulangi Password</label>
                    <input type="password" name="confirm" class="form-control" required minlength="6">
                </div>
                <button class="btn btn-primary w-100">Reset Password</button>
            </form>
        <?php else: ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <a href="reset-request.php" class="btn btn-secondary w-100 mt-2">← Minta Link Baru</a>
        <?php endif; ?>
    </div>
</body>
</html>
