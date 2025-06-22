<?php
require '../config/config.php';

$id     = $_GET['id'];
$status = $_GET['status'];

$update = mysqli_query($koneksi, "UPDATE pengajuan_surat SET status='$status' WHERE id='$id'");

if ($update) {
    header("Location: pengajuan-surat-admin.php?pesan=berhasil");
} else {
    echo "Gagal mengubah status.";
}
?>
