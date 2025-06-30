<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_SESSION['userid'];
    $judul = htmlspecialchars($_POST['judul']);
    $isi = htmlspecialchars($_POST['isi']);

    $stmt = $koneksi->prepare("INSERT INTO berita_pending (userid, judul, isi) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userid, $judul, $isi);
    $stmt->execute();
    $stmt->close();

    $_SESSION['berita_success'] = "Berita berhasil dikirim dan akan ditinjau oleh Admin.";
    header("Location: kirim-berita.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kirim Berita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h3>Kirim Berita Desa</h3>

    <?php if (isset($_SESSION['berita_success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['berita_success']; unset($_SESSION['berita_success']); ?></div>
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
        <button type="submit" class="btn btn-primary">Kirim</button>
    </form>
</div>
</body>
</html>
