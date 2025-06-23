<?php
require '../config/config.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    $status = "❌ Token tidak ditemukan.";
    $success = false;
} else {
    $stmt = $koneksi->prepare("SELECT userid, token_expiry FROM users WHERE unlock_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $user = $result->fetch_assoc()) {
        $now = date('Y-m-d H:i:s');

        if ($now > $user['token_expiry']) {
            $status = "❌ Token sudah kedaluwarsa. Silakan minta ulang link.";
            $success = false;
        } else {
            $stmt = $koneksi->prepare("UPDATE users SET is_locked = 0, login_attempts = 0, unlock_token = NULL, token_expiry = NULL WHERE userid = ?");
            $stmt->bind_param("i", $user['userid']);
            $stmt->execute();

            $status = "✅ Akun Anda berhasil dibuka. Silakan login kembali.";
            $success = true;
        }
    } else {
        $status = "❌ Token tidak valid atau akun tidak ditemukan.";
        $success = false;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unlock Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #4facfe, #00f2fe);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            animation: fadeIn 0.8s ease-in-out;
            padding: 30px;
            max-width: 450px;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        .success-icon {
            font-size: 3rem;
            animation: bounce 1s infinite;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .btn-login {
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="card bg-white text-center">
        <h3 class="mb-3">
            <?= $success ? "<span class='text-success success-icon'>✅</span>" : "<span class='text-danger success-icon'>❌</span>" ?>
        </h3>
        <p class="fs-5"><?= $status ?></p>
        <?php if ($success): ?>
            <a href="../login.php" class="btn btn-primary btn-login">➡️ Kembali ke Login</a>
        <?php else: ?>
            <a href="../index.php" class="btn btn-secondary btn-login">← Kembali ke Beranda</a>
        <?php endif; ?>
    </div>
</body>
</html>
