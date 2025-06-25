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
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Fonts + Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            background-color: #fff;
            padding: 2rem;
            width: 100%;
            max-width: 420px;
        }

        h4 {
            font-weight: 600;
        }

        .form-control {
            border-radius: 0.5rem;
        }

        .btn-primary {
            background-color: #4e73df;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #2e59d9;
        }

        .alert {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="card">
        <?php if ($success): ?>
            <div class="text-center">
                <h4 class="text-success mb-3">✅ Password berhasil diubah!</h4>
                <a href="../login.php" class="btn btn-success w-100">➡️ Kembali ke Login</a>
            </div>
        <?php elseif ($user): ?>
            <h4 class="mb-3 text-center">🔐 Atur Password Baru</h4>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" name="password" id="password" class="form-control" required minlength="6">
                </div>
                <div class="mb-3">
                    <label for="confirm" class="form-label">Ulangi Password</label>
                    <input type="password" name="confirm" id="confirm" class="form-control" required minlength="6">
                </div>
                <button class="btn btn-primary w-100">Reset Password</button>
            </form>
        <?php else: ?>
            <div class="alert alert-danger text-center"><?= $error ?></div>
            <a href="reset-request.php" class="btn btn-outline-light bg-secondary text-white w-100 mt-3">← Minta Link Baru</a>
        <?php endif; ?>
    </div>

</body>
</html>
