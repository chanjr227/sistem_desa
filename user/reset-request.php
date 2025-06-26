<?php
require '../config/config.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $koneksi->prepare("SELECT userid, is_locked FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $user = $result->fetch_assoc()) {
            // Generate token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', time() + 1800); // 30 menit

            $update = $koneksi->prepare("UPDATE users SET unlock_token = ?, token_expiry = ? WHERE userid = ?");
            $update->bind_param("ssi", $token, $expiry, $user['userid']);
            $update->execute();

            // Kirim email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = ''; // Ganti dengan email kamu
                $mail->Password   = ''; // Ganti dengan App Password Gmail kamu
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                $mail->setFrom('', 'Sistem Informasi Desa');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = '🔐 Link Reset Password Akun Anda';

                $link = "https://sagarawatch.biz.id/user/reset-password.php?token=$token";

                $mail->Body = "Halo,<br><br>
                    Anda meminta reset password akun Anda.<br>
                    Klik link berikut untuk mengatur ulang password:<br><br>
                    <a href='$link'>$link</a><br><br>
                    Link ini berlaku selama <strong>30 menit</strong>.<br><br>
                    Jika Anda tidak meminta ini, abaikan saja email ini.<br><br>
                    Salam,<br>Sistem Informasi Desa";

                $mail->send();
                $message = "✅ Link reset password telah dikirim ke email Anda.";
            } catch (Exception $e) {
                $message = "❌ Gagal mengirim email. Error: {$mail->ErrorInfo}";
            }
        } else {
            $message = "❌ Email tidak ditemukan.";
        }
    } else {
        $message = "❌ Format email tidak valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 shadow" style="max-width: 400px;">
        <h4 class="mb-3 text-center">🔐 Reset Password</h4>
        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Email Anda</label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>
            <button class="btn btn-primary w-100" type="submit" id="btnReset">
    Kirim Link Reset
</button>
        </form>
        <p class="text-muted small mt-2">
  Kami akan mengirimkan link untuk reset password jika email Anda terdaftar.
</p>

    </div>
    <script>
document.querySelector('form').addEventListener('submit', () => {
    const btn = document.getElementById('btnReset');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Mengirim...`;
});
</script>
</body>
</html>
