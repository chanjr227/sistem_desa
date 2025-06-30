<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['setujui'])) {
    $id = intval($_GET['setujui']);
    $data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM berita_pending WHERE id = $id"));

    $judul = $data['judul'];
    $isi = $data['isi'];
    $penulis = "Warga (ID: {$data['userid']})";

    $stmt = $koneksi->prepare("INSERT INTO berita_desa (judul, isi, penulis, tanggal) VALUES (?, ?, ?, CURDATE())");
    $stmt->bind_param("sss", $judul, $isi, $penulis);
    $stmt->execute();
    $stmt->close();

    mysqli_query($koneksi, "UPDATE berita_pending SET status = 'disetujui' WHERE id = $id");
    header("Location: review-berita.php");
    exit;
}

if (isset($_GET['tolak'])) {
    $id = intval($_GET['tolak']);
    mysqli_query($koneksi, "UPDATE berita_pending SET status = 'ditolak' WHERE id = $id");
    header("Location: review-berita.php");
    exit;
}

$berita = mysqli_query($koneksi, "SELECT * FROM berita_pending WHERE status = 'menunggu' ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Review Berita Warga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h3>Berita Menunggu Persetujuan</h3>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Tanggal</th>
                <th>Isi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($b = mysqli_fetch_assoc($berita)): ?>
                <tr>
                    <td><?= htmlspecialchars($b['judul']) ?></td>
                    <td><?= $b['tanggal'] ?></td>
                    <td><?= nl2br(htmlspecialchars(substr($b['isi'], 0, 100))) ?>...</td>
                    <td>
                        <a href="?setujui=<?= $b['id'] ?>" class="btn btn-success btn-sm">Setujui</a>
                        <a href="?tolak=<?= $b['id'] ?>" class="btn btn-danger btn-sm">Tolak</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
