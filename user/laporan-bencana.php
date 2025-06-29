<?php
session_start();
require '../config/config.php';
require '../helpers/log_helpers.php';

// A01 - Broken Access Control dan Identification and Authentication Failures
if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

// CSRF token Cryptographic Failures
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'CSRF token tidak valid.';
    } else {
        $userid = $_SESSION['userid'] ?? 1;
        $nama_pelapor = htmlspecialchars($_SESSION['nama'] ?? 'Anonim', ENT_QUOTES, 'UTF-8');
        $jenis_bencana = $_POST['jenis_bencana'] ?? '';
        $kota = htmlspecialchars(trim($_POST['kota'] ?? ''), ENT_QUOTES, 'UTF-8');
        $deskripsi = htmlspecialchars(trim($_POST['deskripsi'] ?? ''), ENT_QUOTES, 'UTF-8');
        $tanggal = $_POST['tanggal'] ?? '';
        $lokasi = htmlspecialchars(trim($_POST['lokasi'] ?? ''), ENT_QUOTES, 'UTF-8');
        $foto_nama = '';

        $jenis_valid = ['Gempa Bumi', 'Banjir', 'Tanah Longsor', 'Kebakaran', 'Tsunami'];
        if (!in_array($jenis_bencana, $jenis_valid)) {
            $error = 'Jenis bencana tidak valid.';
        }

        if (!$error && !DateTime::createFromFormat('Y-m-d', $tanggal)) {
            $error = 'Format tanggal tidak valid.';
        }

        if (!$error && !preg_match('/^-?\d{1,3}\.\d+,\s?-?\d{1,3}\.\d+$/', $lokasi)) {
            $error = 'Format lokasi tidak valid. Contoh: -6.2, 106.8';
        }

        if (!$error && !empty($_FILES['foto']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            $file_type = mime_content_type($_FILES['foto']['tmp_name']);
            $file_size = $_FILES['foto']['size'];

            if (!in_array($file_type, $allowed_types)) {
                $error = "Jenis file tidak diperbolehkan.";
            } elseif ($file_size > 2 * 1024 * 1024) {
                $error = "Ukuran file maksimal 2MB.";
            } else { //A05 - Security Misconfiguration
                $upload_dir = __DIR__ . "/uploads/";
                if (!file_exists($upload_dir)) mkdir($upload_dir, 0755, true);

                $foto_nama = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES["foto"]["name"]));
                $target_file = $upload_dir . $foto_nama;

                if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    $error = "Gagal mengunggah foto.";
                }
            }
        }


        if (!$error) {
              // Panggil log (A09 - Security Logging and Monitoring Failures)
            simpan_log($koneksi, $_SESSION['userid'], $_SESSION['nama'], 'Mengirim laporan bencana');
            //A03 - Injection (SQL Injection)
            $stmt = $koneksi->prepare("INSERT INTO laporan (userid, nama_pelapor, jenis_bencana, deskripsi, tanggal_laporan, kota, lokasi, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $userid, $nama_pelapor, $jenis_bencana, $deskripsi, $tanggal, $kota, $lokasi, $foto_nama);

            if ($stmt->execute()) {
                unset($_SESSION['csrf_token']);
                header("Location: laporan-bencana.php?sukses=1");
                exit;
            } else {
                $error = $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bencana</title>
<!--  A06 - Vulnerable and Outdated Components-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<!-- NAVBAR -->
<nav class="bg-primary text-white px-4 py-2">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a class="text-white fw-bold" href="../index.php">Sistem Informasi Desa</a>
        <div class="d-flex align-items-center gap-2">
            <span class="small">Halo, <?= htmlspecialchars($_SESSION['nama'] ?? 'Warga', ENT_QUOTES, 'UTF-8') ?></span>
            <a href="../index.php" class="btn btn-light btn-sm">← Kembali</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-4">
    <h2 class="text-center mb-4">Laporan Bencana</h2>

    <?php if (isset($_GET['sukses'])): ?>
        <div class="alert alert-success text-center">Laporan berhasil dikirim.</div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="mb-3">
            <label for="jenisBencana" class="form-label">Jenis Bencana</label>
            <select class="form-select" id="jenisBencana" name="jenis_bencana" required>
                <option value="" disabled selected>Pilih Jenis Bencana</option>
                <?php foreach (['Gempa Bumi', 'Banjir', 'Tanah Longsor', 'Kebakaran', 'Tsunami'] as $b): ?>
                    <option value="<?= $b ?>" <?= (isset($_POST['jenis_bencana']) && $_POST['jenis_bencana'] === $b) ? 'selected' : '' ?>><?= $b ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="kota" class="form-label">Daerah</label>
            <input type="text" class="form-control" id="kota" name="kota" required value="<?= htmlspecialchars($_POST['kota'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi Kejadian</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal Kejadian</label>
            <input type="date" class="form-control" name="tanggal" id="tanggal" required value="<?= htmlspecialchars($_POST['tanggal'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="lokasi" class="form-label">Lokasi (Koordinat)</label>
            <input type="text" class="form-control" name="lokasi" id="lokasi" required value="<?= htmlspecialchars($_POST['lokasi'] ?? '') ?>" placeholder="-6.2, 106.8">
        </div>
        <div class="mb-3">
            <label for="foto" class="form-label">Unggah Foto (opsional)</label>
            <input type="file" class="form-control" name="foto" id="foto" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Kirim Laporan</button>
    </form>
</div>
</body>
</html>
