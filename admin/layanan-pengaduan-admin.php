<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require '../vendor/autoload.php';

use Dompdf\Dompdf;

// Proses hapus pengaduan
$search = '';
$success = '';
$error = '';

// Hapus satu pengaduan
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $koneksi->prepare("DELETE FROM pengaduan WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = "Pengaduan berhasil dihapus.";
    } else {
        $error = "Gagal menghapus pengaduan.";
    }
    $stmt->close();
}

// Hapus banyak
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete']) && !empty($_POST['selected_ids'])) {
    $ids = $_POST['selected_ids'];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $stmt = $koneksi->prepare("DELETE FROM pengaduan WHERE id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    if ($stmt->execute()) {
        $success = count($ids) . " pengaduan berhasil dihapus.";
    } else {
        $error = "Gagal menghapus pengaduan.";
    }
    $stmt->close();
}

// Ambil data
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $stmt = $koneksi->prepare("
        SELECT id, userid, nama_karyawan, jabatan_karyawan, isi_pengaduan, tanggal_pengaduan
        FROM pengaduan
        WHERE nama_karyawan LIKE CONCAT('%', ?, '%')
        ORDER BY tanggal_pengaduan DESC
    ");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $koneksi->query("
        SELECT id, userid, nama_karyawan, jabatan_karyawan, isi_pengaduan, tanggal_pengaduan
        FROM pengaduan
        ORDER BY tanggal_pengaduan DESC
    ");
}

?>



<?php include '../template/header.php'; ?>
<?php include '../template/navbar.php'; ?>

<div id="layoutSidenav">
    <?php include '../template/sidebar.php'; ?>

    <div id="layoutSidenav_content">
        <!-- Konten Utama -->
        <main class="container-fluid px-4 py-4">
            <h2>Manajemen Pengaduan Karyawan</h2>

            <?php if (!empty($success)) : ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php elseif (!empty($error)) : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama karyawan" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-primary">Cari</button>
                </div>
            </form>

            <form method="POST">
                <a href="cetak_pengaduan.php<?= !empty($search) ? '?search=' . urlencode($search) : '' ?>" target="_blank" class="btn btn-success mb-3">Cetak PDF</a>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100">
                        <thead class="table-dark">
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>#</th>
                                <th>User ID</th>
                                <th>Nama Karyawan</th>
                                <th>Jabatan</th>
                                <th>Isi Pengaduan</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td><input type="checkbox" name="selected_ids[]" value="<?= $row['id'] ?>"></td>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['userid']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_karyawan']) ?></td>
                                    <td><?= htmlspecialchars($row['jabatan_karyawan']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($row['isi_pengaduan'])) ?></td>
                                    <td><?= $row['tanggal_pengaduan'] ?></td>
                                    <td>
                                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus pengaduan ini?')" class="btn btn-danger btn-sm">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <button type="submit" name="bulk_delete" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus yang dipilih?')">Hapus yang Dipilih</button>
                <!-- <a href="dashboard.php" class="btn btn-secondary mt-3">← Kembali ke Dashboard</a> -->
            </form>
        </main>

        <?php include '../template/footer.php'; ?>
    </div>
</div>

<script>
    document.getElementById('select-all').onclick = function() {
        const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
        for (const checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    };
</script>