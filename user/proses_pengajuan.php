<?php
require '../config/config.php';

$user_id        = $_POST['user_id'];
$nama_lengkap   = $_POST['nama_lengkap'];
$nik            = $_POST['nik'];
$jenis_kelamin  = $_POST['jenis_kelamin'];
$tempat_lahir   = $_POST['tempat_lahir'];
$tanggal_lahir  = $_POST['tanggal_lahir'];
$alamat         = $_POST['alamat'];
$pekerjaan      = $_POST['pekerjaan'];
$jenis_surat    = $_POST['jenis_surat'];
$keperluan      = $_POST['keperluan'];
$tanggal_pengajuan = date('Y-m-d H:i:s');

// Simpan ke database
$query = "INSERT INTO pengajuan_surat 
(userid, nama_lengkap, nik, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, pekerjaan, jenis_surat, keperluan, Tanggal_pengajuan) 
VALUES 
('$user_id', '$nama_lengkap', '$nik', '$jenis_kelamin', '$tempat_lahir', '$tanggal_lahir', '$alamat', '$pekerjaan', '$jenis_surat', '$keperluan', '$tanggal_pengajuan')";

if (mysqli_query($koneksi, $query)) {
    header("Location: menu-pengajuan-surat.php?status=sukses");
} else {
    echo "Gagal menyimpan: " . mysqli_error($koneksi);
}
?>
