<?php
require '../config/config.php';
session_start();

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$success = '';
$error = '';

// === Form Tambah Jadwal Posyandu ===
if (isset($_POST['submit_jadwal'])) {
    $tanggal = $_POST['tanggal'];
    $lokasi = $_POST['lokasi'];
    $keterangan = $_POST['keterangan'];
    $petugas = $_POST['petugas'];   
    $waktu = $_POST['waktu'];   

    $stmt = $koneksi->prepare("INSERT INTO jadwal_posyandu (tanggal, lokasi, keterangan, petugas, waktu) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $tanggal, $lokasi, $keterangan, $petugas, $waktu);
    $stmt->execute();
    $stmt->close();
}

// === Form Tambah Warga Rentan ===
if (isset($_POST['submit_rentan'])) {
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $usia = $_POST['usia'];
    $alamat = $_POST['alamat'];

    $stmt = $koneksi->prepare("INSERT INTO warga_rentan (nama, kategori, usia, alamat) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $nama, $kategori, $usia, $alamat);
    $stmt->execute();
    $stmt->close();
}

// === Form Tambah Imunisasi ===
if (isset($_POST['submit_imunisasi'])) {
    $nama_orang_tua = $_POST['nama_orang_tua'];
    $nama_anak = $_POST['nama_anak'];
    $umur = $_POST['umur'];
    $jenis_imunisasi = $_POST['jenis_imunisasi'];
    $tanggal = $_POST['tanggal_imunisasi'];
    $catatan = $_POST['catatan'];

    $stmt = $koneksi->prepare("INSERT INTO data_imunisasi (nama_orang_tua, nama_anak, umur, jenis_imunisasi, tanggal, catatan) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisss", $nama_orang_tua, $nama_anak, $umur, $jenis_imunisasi, $tanggal, $catatan);
    $stmt->execute();
    $stmt->close();
}


// Ambil data
$jadwal = $koneksi->query("SELECT * FROM jadwal_posyandu ORDER BY tanggal ASC");
$rentan = $koneksi->query("SELECT * FROM warga_rentan");
$imunisasi = $koneksi->query("SELECT * FROM data_imunisasi");
?>

<?php include '../template/header.php'; ?>
<?php include '../template/navbar.php'; ?>

<div id="layoutSidenav">
    <?php include '../template/sidebar.php'; ?>
    <div id="layoutSidenav_content">
        <main class="container-fluid px-4 py-4">
            <h2 class="mb-4">💉 Manajemen Kesehatan Desa</h2>

            <!-- ✳️ FORM TAMBAH JADWAL POSYANDU -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">Tambah Jadwal Posyandu</div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-3">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Lokasi</label>
                            <input type="text" name="lokasi" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Keterangan</label>
                            <input type="text" name="keterangan" class="form-control">
                        </div>
                         <div class="col-md-2">
                                <label>Petugas</label>
                                <input type="text" name="petugas" class="form-control">
                        </div>
                        <div class="col-md-2">
                                <label>Waktu</label>
                                <input type="text" name="waktu" class="form-control">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button name="submit_jadwal" class="btn btn-primary w-100">Simpan</button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive p-3">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light text-center">
                            <tr><th>Tanggal</th><th>Lokasi</th><th>Keterangan</th><th>petugas</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($j = $jadwal->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d-m-Y', strtotime($j['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($j['lokasi']) ?></td>
                                <td><?= htmlspecialchars($j['keterangan']) ?></td>
                                <td><?= htmlspecialchars($j['petugas']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ✳️ FORM TAMBAH WARGA RENTAN -->
            <div class="card mb-4">
                <div class="card-header bg-warning">Tambah Warga Rentan</div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-3">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label>Kategori</label>
                            <select name="kategori" class="form-control" required>
                                <option value="Balita">Balita</option>
                                <option value="Lansia">Lansia</option>
                                <option value="Ibu Hamil">Ibu Hamil</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Usia</label>
                            <input type="number" name="usia" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Alamat</label>
                            <input type="text" name="alamat" class="form-control" required>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button name="submit_rentan" class="btn btn-primary w-100">Simpan</button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive p-3">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light text-center">
                            <tr><th>Nama</th><th>Kategori</th><th>Usia</th><th>Alamat</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($r = $rentan->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['nama']) ?></td>
                                <td><?= htmlspecialchars($r['kategori']) ?></td>
                                <td><?= (int)$r['usia'] ?> th</td>
                                <td><?= htmlspecialchars($r['alamat']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ✳️ FORM TAMBAH IMUNISASI -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">Tambah Data Imunisasi</div>
                <div class="card-body">
                    <!-- FORM TAMBAH IMUNISASI -->
                    <form method="POST" class="row g-3">
    <div class="col-md-3">
        <label>Nama Orang Tua</label>
        <input type="text" name="nama_orang_tua" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label>Nama Anak</label>
        <input type="text" name="nama_anak" class="form-control" required>
    </div>
    <div class="col-md-2">
        <label>Umur Anak</label>
        <input type="number" name="umur" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label>Jenis Imunisasi</label>
        <input type="text" name="jenis_imunisasi" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label>Tanggal Imunisasi</label>
        <input type="date" name="tanggal_imunisasi" class="form-control" required>
    </div>
    <div class="col-md-5">
        <label>Catatan</label>
        <input type="text" name="catatan" class="form-control">
    </div>
    <div class="col-md-1 d-flex align-items-end">
        <button name="submit_imunisasi" class="btn btn-primary w-100">Simpan</button>
    </div>
</form>

                </div>
                <div class="table-responsive p-3">
                    <table class="table table-bordered table-striped">
                                <thead class="table-light text-center">
    <tr>
        <th>Nama Orang Tua</th>
        <th>Nama Anak</th>
        <th>Umur</th>
        <th>Jenis</th>
        <th>Tanggal</th>
        <th>Catatan</th>
    </tr>
</thead>
<tbody>
    <?php while ($i = $imunisasi->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($i['nama_orang_tua']) ?></td>
        <td><?= htmlspecialchars($i['nama_anak']) ?></td>
        <td><?= (int)$i['umur'] ?> th</td>
        <td><?= htmlspecialchars($i['jenis_imunisasi']) ?></td>
        <td><?= date('d-m-Y', strtotime($i['tanggal'])) ?></td>
        <td><?= htmlspecialchars($i['catatan']) ?></td>
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
