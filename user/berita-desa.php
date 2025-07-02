<?php
require '../config/config.php';
session_start();

// Akses hanya untuk admin (opsional)
if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

// Ambil semua data berita
$query = mysqli_query($koneksi, "SELECT * FROM berita_desa ORDER BY tanggal DESC");

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Semua Berita Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">📰 Semua Berita Desa</h2>
        <div class="row g-4">
            <?php if (mysqli_num_rows($query) > 0): ?>
                <?php while ($berita = mysqli_fetch_assoc($query)): ?>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <?php
                            $gambarPath = "../uploads/berita/" . htmlspecialchars($berita['gambar']);
                            if (file_exists($gambarPath)): ?>
                                <img src="<?= $gambarPath ?>" class="card-img-top" alt="Gambar Berita" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="text-center bg-secondary text-white py-5">
                                    <small>Gambar tidak tersedia</small>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($berita['judul']) ?></h5>
                                <p class="card-text"><?= substr(htmlspecialchars($berita['isi']), 0, 100) ?>...</p>
                                <p class="text-muted mb-1"><small><i><?= $berita['tanggal'] ?></i></small></p>
                                <span class="badge <?= $berita['status'] === 'disetujui' ? 'bg-success' : 'bg-warning' ?>">
                                    <?= ucfirst($berita['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">Belum ada berita tersedia.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>