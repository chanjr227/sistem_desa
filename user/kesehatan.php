<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$nama_user = $_SESSION['nama'] ?? 'Warga';

// ✅ Ambil data jadwal posyandu
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

<!-- ✅ NAVBAR -->
<nav class="navbar navbar-expand-lg bg-primary text-white px-4 py-2">
    <div class="container-fluid flex justify-between items-center">
        <a class="navbar-brand text-white font-bold" href="../index.php">Sistem Informasi Desa</a>
        <div class="flex items-center space-x-2">
            <span class="text-white">Halo, <?= htmlspecialchars($nama_user) ?></span>
            <a href="../index.php" class="bg-white text-primary px-3 py-1 rounded hover:bg-blue-100 transition text-sm">← Kembali</a>
            <a href="logout.php" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition text-sm">Logout</a>
        </div>
    </div>
</nav>

<!-- ✅ KONTEN -->
<div class="container mt-5">
    <h2 class="mb-4 text-center">Fitur Kesehatan Desa</h2>

    <!-- ✅ Jadwal Posyandu -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Jadwal Posyandu</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
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
                                <td><?= htmlspecialchars($jadwal['tanggal']) ?></td>
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
