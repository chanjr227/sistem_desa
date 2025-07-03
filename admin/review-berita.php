<?php
require '../config/config.php';
session_start();

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$success = '';
$error = '';

// Aksi setujui/ditolak
if (isset($_GET['setujui']) || isset($_GET['tolak'])) {
    $id = intval($_GET['setujui'] ?? $_GET['tolak']);
    $status = isset($_GET['setujui']) ? 'disetujui' : 'ditolak';

    if ($status === 'disetujui') {
        $stmt = $koneksi->prepare("SELECT userid, judul, gambar, isi FROM berita_pending WHERE id = ? AND status = 'menunggu'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $data = $result->fetch_assoc();
            $judul = $data['judul'];
            $isi = $data['isi'];
            $gambar = $data['gambar'];
            $penulis = "Warga (ID: {$data['userid']})";

            $insert = $koneksi->prepare("INSERT INTO berita_desa (judul, isi, gambar, penulis, tanggal) VALUES (?, ?, ?, ?, CURDATE())");
            $insert->bind_param("ssss", $judul, $isi, $gambar, $penulis);
            $insert->execute();
            $insert->close();
        }
        $stmt->close();
    }

    $update = $koneksi->prepare("UPDATE berita_pending SET status = ? WHERE id = ?");
    $update->bind_param("si", $status, $id);
    $update->execute();
    $update->close();

    $success = "Status berita berhasil diperbarui.";
}

$berita = $koneksi->query("SELECT * FROM berita_pending WHERE status = 'menunggu' ORDER BY tanggal DESC");

include '../template/header.php';
include '../template/navbar.php';
?>

<div id="layoutSidenav">
    <?php include '../template/sidebar.php'; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">
                <h1 class="mb-4">📋 Review Berita Warga</h1>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">Daftar Berita Menunggu Persetujuan</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="background-color: #0d6efd; color: white;" class="text-center">
                                    <tr>
                                        <th>NO</th>
                                        <th>Judul</th>
                                        <th>Tanggal</th>
                                        <th>Isi Ringkas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    while ($b = $berita->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($b['judul']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($b['tanggal']) ?></td>
                                            <td><?= nl2br(htmlspecialchars(substr($b['isi'], 0, 100))) ?>...</td>
                                            <td class="text-center">
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalBerita<?= $b['id'] ?>">Lihat</button>
                                                <a href="?setujui=<?= $b['id'] ?>" class="btn btn-success btn-sm">Setujui</a>
                                                <a href="?tolak=<?= $b['id'] ?>" class="btn btn-danger btn-sm">Tolak</a>
                                            </td>
                                        </tr>
                                        <!-- Modal -->
                                        <div class="modal fade" id="modalBerita<?= $b['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $b['id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalLabel<?= $b['id'] ?>">Detail Berita: <?= htmlspecialchars($b['judul']) ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?php if (!empty($b['gambar'])): ?>
                                                            <img src="../uploads/berita/<?= htmlspecialchars($b['gambar']) ?>" class="img-fluid mb-3 rounded shadow-sm" alt="Gambar Berita">
                                                        <?php endif; ?>
                                                        <p><?= nl2br(htmlspecialchars($b['isi'])) ?></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a href="?setujui=<?= $b['id'] ?>" class="btn btn-success">✅ Setujui</a>
                                                        <a href="?tolak=<?= $b['id'] ?>" class="btn btn-danger">❌ Tolak</a>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                    <?php if ($berita->num_rows === 0): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada berita menunggu saat ini.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
        <?php include '../template/footer.php'; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/scripts.js"></script>