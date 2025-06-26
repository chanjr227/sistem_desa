<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$nama_user = $_SESSION['nama'] ?? 'Warga';

// Ambil data jadwal posyandu
$jadwal_posyandu = [];
$query = mysqli_query($koneksi, "SELECT * FROM jadwal_posyandu ORDER BY tanggal ASC");

if ($query && mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $jadwal_posyandu[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kesehatan Desa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-primary text-white px-4 py-2">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a class="navbar-brand text-white fw-bold" href="../index.php">Sistem Informasi Desa</a>
        <div class="d-flex align-items-center gap-2">
            <span class="text-white">Halo, <?= htmlspecialchars($nama_user) ?></span>
            <a href="../index.php" class="btn btn-sm btn-light text-primary">← Kembali</a>
            <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
        </div>
    </div>
</nav>

<!-- Konten -->
<div class="container my-5">
    <h2 class="mb-4 text-center fw-bold">📅 Jadwal Posyandu Desa</h2>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white fw-semibold">
            Jadwal Posyandu Terdekat
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($jadwal_posyandu)): ?>
                        <?php foreach ($jadwal_posyandu as $jadwal): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($jadwal['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($jadwal['lokasi']) ?></td>
                                <td><?= htmlspecialchars($jadwal['waktu']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">Belum ada jadwal posyandu.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
