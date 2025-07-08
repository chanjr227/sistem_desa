<?php
session_start();
require '../config/config.php';
require '../helpers/auth_helpers.php';
check_access(['admin', 'staff_desa']);

$success = '';
$error = '';

// Proses simpan data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'] ?? '';
    $nik  = $_POST['nik'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $userid = $_SESSION['userid'] ?? null;

    if ($userid) {
        $stmt = $koneksi->prepare("INSERT INTO penduduk (nama, NIK, alamat, jenis_kelamin, tanggal_lahir) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $nik, $alamat, $jenis_kelamin, $tanggal_lahir);



        if ($stmt->execute()) {
            $success = "Data berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan data: " . $koneksi->error;
        }
        $stmt->close();
    } else {
        $error = "User ID tidak ditemukan.";
    }
}
?>

<?php include '../template/header.php'; ?>
<?php include '../template/navbar.php'; ?>

<div id="layoutSidenav">
    <?php include '../template/sidebar.php'; ?>

    <div id="layoutSidenav_content">
        <main class="container-fluid px-4 py-4">
            <h3 class="mb-4">Form Tambah Data Kependudukan</h3>

            <?php if (!empty($success)) : ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php elseif (!empty($error)) : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-body">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" class="form-control" id="nik" name="nik" required maxlength="16">
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_kelamin" id="laki" value="Laki-laki" required>
                                    <label class="form-check-label" for="laki">Laki-laki</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_kelamin" id="perempuan" value="Perempuan">
                                    <label class="form-check-label" for="perempuan">Perempuan</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                        </div>
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <!-- <a href="dashboard.php" class="btn btn-secondary">← Kembali</a> -->
                    </form>
                </div>
            </div>
        </main>

        <?php include '../template/footer.php'; ?>
    </div>
</div>