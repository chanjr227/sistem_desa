<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$tanggal = date('Y-m-d');
if (isset($_GET['tanggal']) && !empty($_GET['tanggal'])) {
    $tanggal = $_GET['tanggal'];
}

$stmt = $koneksi->prepare("SELECT * FROM user_log WHERE DATE(waktu) = ? ORDER BY waktu DESC");
$stmt->bind_param("s", $tanggal);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include '../template/header.php'; ?>
<?php include '../template/navbar.php'; ?>

<div id="layoutSidenav">
    <?php include '../template/sidebar.php'; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h2 class="my-4 text-center">📋 Log Aktivitas User</h2>

                <div class="card shadow-sm p-4 mb-4 bg-white">
                    <form method="get" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="tanggal" class="form-label fw-semibold">Pilih Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= htmlspecialchars($tanggal) ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">🔍 Cari</button>
                        </div>
                        <div class="col-md-4">
                            <a href="log-aktivitas.php" class="btn btn-outline-secondary w-100">🔄 Reset</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive shadow rounded bg-white p-3">
                    <table class="table table-bordered table-hover text-center align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th style="width:5%;">No</th>
                                <th>Nama</th>
                                <th>User ID</th>
                                <th>Aktivitas</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): $no = 1; ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td><?= $row['userid'] ?></td>
                                        <td><?= htmlspecialchars($row['aktivitas']) ?></td>
                                        <td><?= date('d-m-Y H:i:s', strtotime($row['waktu'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-muted">Tidak ada log aktivitas pada tanggal ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- <div class="mb-4 text-end">
                    <a href="ekspor-log.php?tanggal=<?= $tanggal ?>" target="_blank" class="btn btn-danger">🖨️ Ekspor PDF</a>
                </div> -->
            </div>
        </main>

        <?php include '../template/footer.php'; ?>
    </div>
</div>