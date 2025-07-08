<?php
session_start();
require '../config/config.php';
require '../helpers/auth_helpers.php';
check_access(['admin']);


$success = '';
$error = '';

// Proses simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama           = trim($_POST['nama']);
    $jabatan        = trim($_POST['jabatan']);
    $nip            = trim($_POST['nip']);
    $jenis_kelamin  = $_POST['jenis_kelamin'];
    $tanggal_lahir  = $_POST['tanggal_lahir'];
    $alamat         = trim($_POST['alamat']);
    $foto           = null;

    // Upload foto
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "../uploads/foto_karyawan/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $foto = $filename;
        } else {
            $error = "❌ Gagal mengupload foto.";
        }
    }

    if (!$error) {
        $stmt = $koneksi->prepare("INSERT INTO karyawan (nama, jabatan, nip, jenis_kelamin, tanggal_lahir, alamat, foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nama, $jabatan, $nip, $jenis_kelamin, $tanggal_lahir, $alamat, $foto);

        if ($stmt->execute()) {
            $success = "✅ Karyawan berhasil ditambahkan.";
        } else {
            $error = "❌ Gagal menyimpan data.";
        }
        $stmt->close();
    }
}

// Ambil data karyawan
$data = $koneksi->query("SELECT * FROM karyawan ORDER BY created_at DESC");
?>

<?php include '../template/header.php'; ?>
<?php include '../template/navbar.php'; ?>

<div id="layoutSidenav">
    <?php include '../template/sidebar.php'; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">
                <h1 class="mb-4">👥 Manajemen Karyawan</h1>

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
                    <div class="card-header bg-primary text-white">Tambah Karyawan Baru</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jabatan</label>
                                <select name="jabatan" class="form-select" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="Sekretaris Desa">Sekretaris Desa</option>
                                    <option value="Bendhara Desa">Bendhara Desa</option>
                                    <option value="Kaur Umum">Kaur Umum</option>
                                    <option value="Kasi Pelayanan">Kasi Pelayanan</option>
                                    <option value="Kasi Kesejahteraan">Kasi Kesejahteraan</option>
                                    <option value="Kadus 1">Kadus 1</option>
                                    <option value="Kadus 2">Kadus 2</option>
                                    <option value="Staff">Staff Desa</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">NIP</label>
                                <input type="text" name="nip" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Foto</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-secondary">Reset</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive shadow-sm">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>NIP</th>
                                <th>JK</th>
                                <th>Tgl Lahir</th>
                                <th>Alamat</th>
                                <th>Foto</th>
                                <th>Waktu Input</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = $data->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['jabatan']) ?></td>
                                    <td><?= htmlspecialchars($row['nip']) ?></td>
                                    <td><?= $row['jenis_kelamin'] ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['tanggal_lahir'])) ?></td>
                                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                                    <td>
                                        <?php if ($row['foto']): ?>
                                            <img src="../uploads/foto_karyawan/<?= $row['foto'] ?>" width="50" class="img-thumbnail">
                                        <?php else: ?>
                                            <em>Belum Ada</em>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
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