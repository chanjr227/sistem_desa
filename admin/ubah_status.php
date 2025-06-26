<?php
require '../config/config.php';

$id     = intval($_GET['id']);
$status = $_GET['status'];
$redirect = $_GET['redirect'] ?? ''; // tambahan opsional

// Validasi status hanya boleh yang diizinkan
$allowed = ['Menunggu', 'Disetujui', 'Ditolak'];
if (!in_array($status, $allowed)) {
    die("Status tidak valid.");
}

$update = mysqli_query($koneksi, "UPDATE pengajuan_surat SET status='$status' WHERE id='$id'");

if ($update) {
    if ($status == 'Disetujui' && $redirect == 'cetak') {
    header("Location: redirect-cetak.php?id=$id");
    exit;
    } else {
        header("Location: pengajuan-surat-admin.php?pesan=berhasil");
    }
} else {
    echo "❌ Gagal mengubah status.";
}
?>
