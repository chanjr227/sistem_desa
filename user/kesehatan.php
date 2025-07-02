<?php
session_start();
require '../config/config.php';
require '../helpers/log_helpers.php';

// Cegah akses ilegal
if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$nama_user = $_SESSION['nama'] ?? 'Warga';

// Ambil 5 jadwal posyandu terdekat dari hari ini
$jadwal_posyandu = [];
$stmt = $koneksi->prepare("SELECT * FROM jadwal_posyandu WHERE tanggal >= CURDATE() ORDER BY tanggal ASC LIMIT 5");
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jadwal_posyandu[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Jadwal Posyandu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-primary text-white px-4 py-2 shadow">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="navbar-brand text-white fw-bold" href="../index.php">Sistem Informasi Desa</a>
            <div class="d-flex align-items-center gap-2">
                <span class="text-white fw-light">Halo, <?= htmlspecialchars($nama_user) ?></span>
                <a href="../index.php" class="btn btn-sm btn-light text-primary">← Kembali</a>
                <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Konten -->
    <div class="container my-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-primary">📅 5 Jadwal Posyandu Terdekat</h2>
            <p class="text-muted">Berikut ini adalah jadwal posyandu yang akan dilaksanakan dalam waktu dekat di desa.</p>
        </div>

        <div class="row justify-content-center">
            <?php if (!empty($jadwal_posyandu)): ?>
                <?php foreach ($jadwal_posyandu as $jadwal): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-success fw-bold">
                                    <?= date('l, d M Y', strtotime($jadwal['tanggal'])) ?>
                                </h5>
                                <p class="mb-1">
                                    <strong>📍 Lokasi:</strong> <?= htmlspecialchars($jadwal['lokasi']) ?>
                                </p>
                                <p class="mb-0">
                                    <strong>⏰ Waktu:</strong> <?= htmlspecialchars($jadwal['waktu']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="alert alert-warning">
                        Belum ada jadwal posyandu dalam waktu dekat.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>