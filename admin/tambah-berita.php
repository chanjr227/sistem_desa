<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = htmlspecialchars($_POST['judul']);
    $isi = htmlspecialchars($_POST['isi']);
    $penulis = $_SESSION['nama_admin'] ?? 'Admin Desa';

    $stmt = $koneksi->prepare("INSERT INTO berita_desa (judul, isi, penulis, tanggal) VALUES (?, ?, ?, CURDATE())");
    $stmt->bind_param("sss", $judul, $isi, $penulis);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Berita berhasil ditambahkan!";
    header("Location: tambah-berita.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Berita Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h3>Tambah Berita Desa</h3>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Judul Berita</label>
            <input type="text" name="judul" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Isi Berita</label>
            <textarea name="isi" rows="6" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Posting</button>
    </form>
</div>
</body>
</html>
