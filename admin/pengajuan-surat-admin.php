<?php
session_start();
require '../config/config.php';

// Pastikan admin login
if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil status dari filter GET
$statusFilter = $_GET['status'] ?? '';

// Siapkan query berdasarkan filter
if ($statusFilter && in_array($statusFilter, ['Menunggu', 'Disetujui', 'Ditolak'])) {
    $stmt = $koneksi->prepare("SELECT * FROM pengajuan_surat WHERE status = ? ORDER BY Tanggal_pengajuan DESC");
    $stmt->bind_param("s", $statusFilter);
} else {
    $stmt = $koneksi->prepare("SELECT * FROM pengajuan_surat ORDER BY Tanggal_pengajuan DESC");
}
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include '../template/header.php'; ?>
<?php include '../template/navbar.php'; ?>

<div id="layoutSidenav">
    <?php include '../template/sidebar.php'; ?>

    <div id="layoutSidenav_content">
        <main class="container-fluid px-4">
            <h2 class="my-4 text-center">📑 Daftar Pengajuan Surat</h2>

            <!-- Filter Form -->
            <form method="GET" class="row g-3 mb-3">
                <div class="col-md-4">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">📋 Tampilkan Semua Status</option>
                        <option value="Menunggu" <?= $statusFilter == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                        <option value="Disetujui" <?= $statusFilter == 'Disetujui' ? 'selected' : '' ?>>Disetujui</option>
                        <option value="Ditolak" <?= $statusFilter == 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="pengajuan-surat-admin.php" class="btn btn-secondary">🔄 Reset</a>
                </div>
            </form>

            <div class="table-responsive shadow rounded bg-white p-3">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jenis Surat</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $result->fetch_assoc()):
                        ?>
                            <tr class="text-center">
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['jenis_surat']) ?></td>
                                <td><?= date('d-m-Y', strtotime($row['Tanggal_pengajuan'])) ?></td>
                                <td>
                                    <?php
                                    $status = $row['status'];
                                    $badgeClass = match ($status) {
                                        'Menunggu' => 'bg-warning text-dark',
                                        'Disetujui' => 'bg-success',
                                        'Ditolak' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                                </td>
                                <td>
                                    <?php if ($status === 'Menunggu'): ?>
                                        <a href="ubah_status.php?id=<?= $row['id'] ?>&status=Disetujui&redirect=cetak" class="btn btn-success btn-sm">Setujui</a>
                                        <a href="ubah_status.php?id=<?= $row['id'] ?>&status=Ditolak" class="btn btn-danger btn-sm">Tolak</a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php $stmt->close(); ?>
                    </tbody>
                </table>
            </div>
        </main>

        <?php include '../template/footer.php'; ?>
    </div>
</div>