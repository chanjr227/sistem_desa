<?php
require '../config/config.php';
require '../vendor/autoload.php'; // Pastikan PHPMailer sudah diinstall via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_GET['email'] ?? '';
if (!$email) {
    exit("❌ Email tidak valid.");
}

// Cek user berdasarkan email
$stmt = $koneksi->prepare("SELECT userid, is_locked FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $user = $result->fetch_assoc()) {
    if ((int)$user['is_locked'] !== 1) {
        exit("ℹ️ Akun tidak sedang terkunci.");
    }

    // Buat token dan simpan
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', time() + 1800); // 30 menit

    $update = $koneksi->prepare("UPDATE users SET unlock_token = ?, token_expiry = ? WHERE userid = ?");
    $update->bind_param("ssi", $token, $expiry, $user['userid']);
    $update->execute();

    // Kirim Email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = ''; // GANTI dengan email kamu
        $mail->Password   = '';    // GANTI dengan app password Gmail kamu
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Email pengirim dan penerima
        $mail->setFrom('', 'Sistem Informasi Desa');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = '🔓 Link Pembukaan Blokir Akun Anda';

        // Gunakan domain HTTPS kamu di sini saat sudah live
        $link = "https://sagarawatch.biz.id/user/unlock-akun.php?token=$token";

        $mail->Body = "Halo,<br><br>
            Anda telah meminta untuk membuka akun yang terkunci.<br>
            Klik link berikut untuk membuka blokir akun Anda:<br><br>
            <a href='$link'>$link</a><br><br>
            Link ini hanya berlaku selama <strong>30 menit</strong>.<br><br>
            Jika Anda tidak meminta ini, abaikan saja email ini.<br><br>
            Salam,<br>Sistem Informasi Desa";

        $mail->send();
        echo "✅ Link untuk membuka akun telah dikirim ke email: <strong>$email</strong>";
    } catch (Exception $e) {
        echo "❌ Gagal mengirim email. Error: {$mail->ErrorInfo}";
    }
} else {
    echo "❌ Email tidak ditemukan atau belum terdaftar.";
}
