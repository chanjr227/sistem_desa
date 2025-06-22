<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['userid'];
$nama_user = $_SESSION['nama'] ?? 'Warga';

$result = mysqli_query($koneksi, "SELECT * FROM pengajuan_surat WHERE userid = $user_id ORDER BY tanggal_pengajuan DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Ajukan Surat Pengantar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg bg-primary text-white px-4 py-2">
    <div class="container-fluid flex justify-between items-center">
        
        <!-- Logo -->
        <a class="navbar-brand text-white font-bold" href="../index.php">Sistem Informasi Desa</a>

        <!-- Kanan: Info User + Tombol Kembali + Logout -->
        <div class="flex items-center space-x-2">
            <span class="text-white">Halo, <?= htmlspecialchars($nama_user) ?></span>
            
            <!-- Tombol Kembali -->
            <a href="../index.php" class="bg-white text-primary px-3 py-1 rounded hover:bg-blue-100 transition text-sm">
                ← Kembali
            </a>

            <!-- Tombol Login/Logout -->
            <?php if (isset($_SESSION['log']) && $_SESSION['log'] === true): ?>
                <a href="logout.php" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition text-sm">Logout</a>
            <?php else: ?>
                <a href="../login.php" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition text-sm">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- ✅ FORM -->
<div class="container mt-5">
    <h3>Ajukan Surat Pengantar</h3>
    <form action="proses_pengajuan.php" method="POST" class="card p-4 bg-white shadow-sm mb-4">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">

        <div class="mb-3">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>NIK</label>
            <input type="text" name="nik" class="form-control" required maxlength="20">
        </div>

        <div class="mb-3">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Tempat Lahir</label>
            <input type="text" name="tempat_lahir" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label>Pekerjaan</label>
            <input type="text" name="pekerjaan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jenis Surat</label>
            <select name="jenis_surat" class="form-select" required>
                <option value="">-- Pilih Surat --</option>
                <option value="KK">Surat Pengantar KK</option>
                <option value="KTP">Surat Pengantar KTP</option>
                <option value="Domisili">Surat Domisili</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Keperluan</label>
            <textarea name="keperluan" class="form-control" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Ajukan</button>
    </form>

    <!-- ✅ TABEL RIWAYAT -->
    <h5>Riwayat Pengajuan Anda</h5>
    <table class="table table-bordered table-striped bg-white">
        <thead>
            <tr>
                <th>Jenis Surat</th>
                <th>Keperluan</th>
                <th>Tanggal Pengajuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <td><?= htmlspecialchars($row['jenis_surat']) ?></td>
                <td><?= htmlspecialchars($row['keperluan']) ?></td>
                <td><?= !empty($row['tanggal_pengajuan']) ? date('d-m-Y H:i', strtotime($row['tanggal_pengajuan'])) : '-' ?></td>
                <td>
                    <?php
                        $status = strtolower($row['status']);
                        if ($status == 'menunggu') {
                            echo '<span class="badge bg-secondary">Belum Diperiksa</span>';
                        } elseif ($status == 'disetujui') {
                            echo '<span class="badge bg-success">Disetujui</span>';
                        } elseif ($status == 'ditolak') {
                            echo '<span class="badge bg-danger">Ditolak</span>';
                        } else {
                            echo '<span class="badge bg-warning">Diproses</span>';
                        }
                    ?>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">Belum ada pengajuan.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
