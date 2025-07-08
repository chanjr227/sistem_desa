<?php

function check_access(array $allowed_roles)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // Pastikan session aktif
    }

    // Cek apakah user belum login atau role-nya tidak termasuk yang diizinkan
    if (empty($_SESSION['log']) || empty($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
        header('Location: ../login.php'); // Redirect ke login
        exit;
    }
}
