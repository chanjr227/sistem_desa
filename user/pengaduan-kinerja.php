<?php
session_start();
require '../config/config.php';
require '../helpers/log_helpers.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$success = '';
$error = '';

// Generate CSRF token jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Permintaan tidak valid (CSRF).";
    } else {
        $userid = intval($_SESSION['userid'] ?? 0);
        $nama_karyawan = htmlspecialchars(trim($_POST['nama_karyawan']));
        $jabatan_karyawan = htmlspecialchars(trim($_POST['jabatan_karyawan']));
        $isi_pengaduan = htmlspecialchars(trim($_POST['isi_pengaduan']));

        if ($userid <= 0 || empty($nama_karyawan) || empty($jabatan_karyawan) || empty($isi_pengaduan)) {
            $error = "Semua field harus diisi dengan benar.";
        } else {
            // Simpan ke database
            $stmt = $koneksi->prepare("INSERT INTO pengaduan (userid, nama_karyawan, jabatan_karyawan, isi_pengaduan) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $userid, $nama_karyawan, $jabatan_karyawan, $isi_pengaduan);

            if ($stmt->execute()) {
                $success = "Pengaduan berhasil dikirim.";
                // Regenerasi token baru setelah submit sukses
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } else {
                $error = "Gagal mengirim pengaduan: " . $stmt->error;
            }
            $stmt->close();
        }
    }
     // Panggil log
        simpan_log($koneksi, $_SESSION['userid'], $_SESSION['nama'], 'Mengirim laporan bencana');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Form Pengaduan</title>
    <link href="../css/style-pengaduan.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>Form Pengaduan Kinerja Karyawan</h2>

    <?php if (!empty($success)) : ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif (!empty($error)) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="mb-3">
            <label>Nama Karyawan</label>
            <input type="text" name="nama_karyawan" class="form-control" required />
        </div>
        <div class="mb-3">
            <label>Jabatan Karyawan</label>
            <input type="text" name="jabatan_karyawan" class="form-control" required />
        </div>
        <div class="mb-3">
            <label>Isi Pengaduan</label>
            <textarea name="isi_pengaduan" rows="5" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Kirim Pengaduan</button>
    </form>

    <a href="../index.php" class="btn btn-secondary mt-3">← Kembali ke Dashboard</a>
</div>
</body>
</html>
