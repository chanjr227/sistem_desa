<?php
function check_access(array $allowed_roles)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Jika belum login, langsung redirect ke login
    if (empty($_SESSION['log'])) {
        header('Location: ../login.php');
        exit;
    }

    // Jika role tidak sesuai, set notifikasi & redirect
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        $_SESSION['access_denied'] = "⚠️ Anda tidak memiliki izin untuk mengakses halaman ini.";
        header('Location: dashboard.php'); // arahkan ke halaman publik/user
        exit;
    }
}
