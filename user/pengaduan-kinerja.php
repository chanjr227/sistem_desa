<?php
session_start();
require '../config/config.php';
require '../helpers/log_helpers.php';

$nama_user = $_SESSION['nama'] ?? 'Warga';

// Inisialisasi variabel
$success = '';
$error = '';
$nama_karyawan = '';
$jabatan_karyawan = '';
$isi_pengaduan = '';

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Token CSRF tidak valid.';
    } else {
        $nama_karyawan = trim($_POST['nama_karyawan']);
        $jabatan_karyawan = trim($_POST['jabatan_karyawan']);
        $isi_pengaduan = trim($_POST['isi_pengaduan']);
        $userid = $_SESSION['userid'] ?? null;
        $tanggal_pengaduan = date('Y-m-d');

        if (!$nama_karyawan || !$jabatan_karyawan || !$isi_pengaduan || !$userid) {
            $error = 'Semua kolom wajib diisi.';
        } else {
            $stmt = $koneksi->prepare("INSERT INTO pengaduan (userid, nama_karyawan, jabatan_karyawan, isi_pengaduan, tanggal_pengaduan) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $userid, $nama_karyawan, $jabatan_karyawan, $isi_pengaduan, $tanggal_pengaduan);

            if ($stmt->execute()) {
                $success = "✅ Pengaduan berhasil dikirim.";
                simpan_log($koneksi, $_SESSION['userid'], $_SESSION['nama'], 'Mengirim pengaduan terhadap karyawan: ' . $nama_karyawan);
                $nama_karyawan = $jabatan_karyawan = $isi_pengaduan = '';
            } else {
                $error = "❌ Gagal mengirim pengaduan.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pengaduan Karyawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f7fc;
        }
        .card-custom {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            animation: fadeIn 0.6s ease;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-primary text-white px-4 py-2 shadow">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a class="navbar-brand text-white fw-bold" href="../index.php">Sistem Informasi Desa</a>
        <div class="d-flex align-items-center gap-2">
            <span class="text-white fw-light">Halo, <?= htmlspecialchars($nama_user) ?></span>
            <a href="../index.php" class="btn btn-sm btn-light text-primary">← Kembali</a>
            <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card card-custom p-4 bg-white">
                <h3 class="mb-4 text-center text-primary fw-bold">📝 Pengaduan Kinerja Karyawan</h3>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="mb-3">
                        <label for="nama_karyawan" class="form-label">Nama Karyawan</label>
                        <input type="text" name="nama_karyawan" id="nama_karyawan" class="form-control" 
                            value="<?= htmlspecialchars($nama_karyawan) ?>" required placeholder="Masukkan nama lengkap karyawan">
                    </div>

                    <div class="mb-3">
                        <label for="jabatan_karyawan" class="form-label">Jabatan Karyawan</label>
                        <input type="text" name="jabatan_karyawan" id="jabatan_karyawan" class="form-control"
                            value="<?= htmlspecialchars($jabatan_karyawan) ?>" required placeholder="Contoh: Staf Pelayanan Publik">
                    </div>

                    <div class="mb-3">
                        <label for="isi_pengaduan" class="form-label">Isi Pengaduan</label>
                        <textarea name="isi_pengaduan" id="isi_pengaduan" rows="5" class="form-control" required
                                  placeholder="Tulis keluhan atau pengaduan Anda secara detail..."><?= htmlspecialchars($isi_pengaduan) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold">📨 Kirim Pengaduan</button>
                </form>
<!-- 
                <div class="text-center mt-3">
                    <a href="../index.php" class="btn btn-outline-secondary">← Kembali ke Dashboard</a>
                </div> -->
            </div>
        </div>
    </div>
</div>
</body>
</html>
