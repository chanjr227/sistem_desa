<?php
require '../config/config.php';
session_start();

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$userid = $_SESSION['userid'];
$success = '';
$error = '';

// Tambah kegiatan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_kegiatan'];
    $lokasi = $_POST['lokasi'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $keterangan = $_POST['keterangan'];

    $stmt = $koneksi->prepare("INSERT INTO jadwal_kegiatan (userid, nama_kegiatan, lokasi, tanggal, waktu, keterangan) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $userid, $nama, $lokasi, $tanggal, $waktu, $keterangan);

    if ($stmt->execute()) {
        $success = "✅ Jadwal berhasil ditambahkan.";
    } else {
        $error = "❌ Gagal menambahkan jadwal.";
    }
    $stmt->close();
}

// Ambil data
$data = $koneksi->query("SELECT * FROM jadwal_kegiatan ORDER BY tanggal ASC, waktu ASC");
?>

<?php include '../template/header.php'; ?>
<?php include '../template/navbar.php'; ?>

<div id="layoutSidenav">
    <?php include '../template/sidebar.php'; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">
                <h1 class="mb-4">🗓️ Manajemen Jadwal Kegiatan</h1>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">Tambah Jadwal Baru</div>
                    <div class="card-body">
                        <form method="POST" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Kegiatan</label>
                                <input type="text" name="nama_kegiatan" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lokasi</label>
                                <input type="text" name="lokasi" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waktu</label>
                                <input type="time" name="waktu" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control">
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-secondary">Reset</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive shadow-sm">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Lokasi</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = $data->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                                    <td><?= htmlspecialchars($row['lokasi']) ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= date('H:i', strtotime($row['waktu'])) ?></td>
                                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <?php include '../template/footer.php'; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/scripts.js"></script>