<?php
session_start();
require '../config/config.php';

// pastikan admin login
if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$result = $koneksi->query("SELECT * FROM pengajuan_surat ORDER BY tanggal_pengajuan DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Pengajuan Surat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
    <h3>Daftar Pengajuan Surat</h3>
    <table class="table">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Jenis Surat</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $query = mysqli_query($koneksi, "SELECT * FROM pengajuan_surat");
    while ($row = mysqli_fetch_assoc($query)) {
    ?>
        <tr>
            <td><?= $row['nama_lengkap'] ?></td>
            <td><?= $row['jenis_surat'] ?></td>
            <td><?= date('d-m-Y', strtotime($row['Tanggal_pengajuan'])) ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <?php if ($row['status'] == 'Menunggu') { ?>
                    <a href="ubah_status.php?id=<?= $row['id'] ?>&status=Disetujui" class="btn btn-success btn-sm">Setujui</a>
                    <a href="ubah_status.php?id=<?= $row['id'] ?>&status=Ditolak" class="btn btn-danger btn-sm">Tolak</a>
                <?php } else { ?>
                    <span class="badge bg-secondary"><?= $row['status'] ?></span>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

</div>
</body>
</html>
