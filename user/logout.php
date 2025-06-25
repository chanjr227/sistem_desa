<?php
session_start();
require '../helpers/log_helpers.php';

// Hapus semua data session
$_SESSION = [];
session_unset();
session_destroy();

// Hapus cookie remember me jika digunakan
if (isset($_COOKIE['remember'])) {
    setcookie('remember', '', time() - 3600, '/');
}

// Redirect ke login atau halaman utama
header('Location: ../login.php?logout=1');
exit;
?>
