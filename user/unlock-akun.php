<?php
require '../config/config.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    exit("❌ Token tidak ditemukan.");
}

// Cari user berdasarkan token
$stmt = $koneksi->prepare("SELECT userid, token_expiry FROM users WHERE unlock_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $user = $result->fetch_assoc()) {
    $now = date('Y-m-d H:i:s');
    
    // Cek apakah token sudah expired
    if ($now > $user['token_expiry']) {
        echo "❌ Token sudah kedaluwarsa. Silakan minta ulang link.";
        exit;
    }

    // Token valid, buka akun
    $stmt = $koneksi->prepare("UPDATE users SET is_locked = 0, login_attempts = 0, unlock_token = NULL, token_expiry = NULL WHERE userid = ?");
    $stmt->bind_param("i", $user['userid']);
    $stmt->execute();

    echo "✅ Akun Anda berhasil dibuka. Silakan login kembali.<br><br>";
    echo "<a href='../login.php'>➡️ Kembali ke Login</a>";

} else {
    echo "❌ Token tidak valid atau akun tidak ditemukan.";
}
