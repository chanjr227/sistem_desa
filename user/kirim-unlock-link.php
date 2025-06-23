<?php
require '../config/config.php';
require '../vendor/autoload.php'; // pastikan sudah install PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_GET['email'] ?? '';
if (!$email) {
    exit("Email tidak valid.");
}

// Cek user berdasarkan email
$stmt = $koneksi->prepare("SELECT userid, is_locked FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $user = $result->fetch_assoc()) {
    if (!$user['is_locked']) {
        exit("Akun ini tidak sedang terkunci.");
    }

    // Generate token dan simpan
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', time() + 30 * 60); // 30 menit

    $stmt = $koneksi->prepare("UPDATE users SET unlock_token = ?, token_expiry = ? WHERE userid = ?");
    $stmt->bind_param("ssi", $token, $expiry, $user['userid']);
    $stmt->execute();

    // Kirim Email
    $mail = new PHPMailer(true);
    try {
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Ganti jika pakai SMTP lain
        $mail->SMTPAuth   = true;
        $mail->Username   = '@gmail.com'; // GANTI!
        $mail->Password   = '';     // GANTI!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Email
        $mail->setFrom('chandrajr227@gmail.com', 'Sistem Desa');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Link Buka Blokir Akun Anda';
        $mail->Body = "Klik link berikut untuk membuka akun Anda:<br><br>
    <a href='http://localhost/sistem_desa/user/unlock-akun.php?token=$token'>
        http://localhost/sistem_desa/user/unlock-akun.php?token=$token
    </a><br><br>Link ini hanya berlaku selama 30 menit.";


        $mail->send();
        echo "✅ Link buka blokir berhasil dikirim ke email.";
    } catch (Exception $e) {
        echo "❌ Gagal mengirim email. Error: {$mail->ErrorInfo}";
    }

} else {
    echo "❌ Email tidak ditemukan atau belum terdaftar.";
}
