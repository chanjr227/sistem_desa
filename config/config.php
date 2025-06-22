<?php
// Set zona waktu default
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi database
$host   = 'localhost';
$user   = 'root';
$pass   = 'S4g4r4@w4tch';
$db     = 'sistem_desa';

// Membuat koneksi ke database dengan pengecekan error
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi dan matikan eksekusi jika gagal
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Base URL (ganti jika online atau pindah folder)
$main_url = 'http://localhost/sistem_desa/';
?>
