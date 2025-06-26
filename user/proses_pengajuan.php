<?php
require '../config/config.php';
require '../helpers/log_helpers.php';
 // Panggil log
        simpan_log($koneksi, $_SESSION['userid'], $_SESSION['nama'], 'Mengirim surat pengajuan');

// Validasi dan sanitasi input
$user_id        = $_POST['user_id'] ?? '';
$nama_lengkap   = htmlspecialchars(trim($_POST['nama_lengkap'] ?? ''));
$nik            = htmlspecialchars(trim($_POST['nik'] ?? ''));
$jenis_kelamin  = htmlspecialchars(trim($_POST['jenis_kelamin'] ?? ''));
$tempat_lahir   = htmlspecialchars(trim($_POST['tempat_lahir'] ?? ''));
$tanggal_lahir  = $_POST['tanggal_lahir'] ?? '';
$alamat         = htmlspecialchars(trim($_POST['alamat'] ?? ''));
$pekerjaan      = htmlspecialchars(trim($_POST['pekerjaan'] ?? ''));
$jenis_surat    = htmlspecialchars(trim($_POST['jenis_surat'] ?? ''));
$keperluan      = htmlspecialchars(trim($_POST['keperluan'] ?? ''));
$tanggal_pengajuan = date('Y-m-d H:i:s');

// Gunakan prepared statement untuk mencegah SQL injection
$stmt = $koneksi->prepare("
    INSERT INTO pengajuan_surat 
    (userid, nama_lengkap, nik, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, pekerjaan, jenis_surat, keperluan, Tanggal_pengajuan) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssssssss",
    $user_id,
    $nama_lengkap,
    $nik,
    $jenis_kelamin,
    $tempat_lahir,
    $tanggal_lahir,
    $alamat,
    $pekerjaan,
    $jenis_surat,
    $keperluan,
    $tanggal_pengajuan
);

if ($stmt->execute()) {
    header("Location: menu-pengajuan-surat.php?status=sukses");
    exit;
} else {
    echo "❌ Gagal menyimpan data: " . $stmt->error;
}
?>
