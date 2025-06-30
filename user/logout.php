<?php
session_start();
require '../config/config.php';
require '../helpers/log_helpers.php';

// Simpan data user sebelum session dihancurkan
$userid = $_SESSION['userid'] ?? null;
$nama = $_SESSION['nama'] ?? '';

// Log aktivitas logout (jika data tersedia)
if ($userid && $nama) {
    simpan_log($koneksi, $userid, $nama, 'Logout dari sistem');
}

// Hapus semua data session
$_SESSION = [];
session_unset();
session_destroy();

// Hapus cookie remember me jika digunakan
if (isset($_COOKIE['remember'])) {
    setcookie('remember', '', time() - 3600, '/');
}

// Redirect ke login atau halaman utama
header('Location: ../index.php?logout=1');
exit;
?>
