<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_SESSION['userid'] ?? 1;
    $nama_pelapor = htmlspecialchars($_SESSION['nama'] ?? 'Anonim');
    $jenis_bencana = $_POST['jenis_bencana'] ?? '';
    $kota = htmlspecialchars(trim($_POST['kota'] ?? ''));
    $deskripsi = htmlspecialchars(trim($_POST['deskripsi'] ?? ''));
    $tanggal = $_POST['tanggal'] ?? '';
    $lokasi = htmlspecialchars(trim($_POST['lokasi'] ?? ''));
    $foto_nama = '';

    // Validasi jenis bencana
    $jenis_valid = ['Gempa Bumi', 'Banjir', 'Tanah Longsor', 'Kebakaran', 'Tsunami'];
    if (!in_array($jenis_bencana, $jenis_valid)) {
        $error = 'Jenis bencana tidak valid.';
    }

    // Validasi tanggal
    if (!$error && !DateTime::createFromFormat('Y-m-d', $tanggal)) {
        $error = 'Format tanggal tidak valid.';
    }

    // Validasi file (jika ada)
    if (!$error && !empty($_FILES['foto']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $file_type = mime_content_type($_FILES['foto']['tmp_name']);
        $file_size = $_FILES['foto']['size'];

        if (!in_array($file_type, $allowed_types)) {
            $error = "Jenis file tidak diperbolehkan. Hanya JPG, PNG, dan GIF.";
        } elseif ($file_size > 2 * 1024 * 1024) {
            $error = "Ukuran file maksimal 2MB.";
        } else {
            $upload_dir = "uploads/";
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

            $foto_nama = time() . '_' . basename($_FILES["foto"]["name"]);
            $target_file = $upload_dir . $foto_nama;

            if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $error = "Gagal mengunggah foto.";
            }
        }
    }

    // Simpan jika tidak ada error
    if (!$error) {
        $stmt = $koneksi->prepare("INSERT INTO laporan (userid, nama_pelapor, jenis_bencana, deskripsi, tanggal_laporan, kota, lokasi, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $userid, $nama_pelapor, $jenis_bencana, $deskripsi, $tanggal, $kota, $lokasi, $foto_nama);

        if ($stmt->execute()) {
            header("Location: laporan-bencana.php?sukses=1");
            exit;
        } else {
            $error = $stmt->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bencana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body>

<!-- NAVBAR -->
<nav class="bg-primary text-white px-4 py-2">
    <div class="container-fluid flex justify-between items-center">
        <!-- Kiri: Nama Sistem -->
        <a class="text-white text-lg font-semibold" href="../index.php">Sistem Informasi Desa</a>

        <!-- Kanan: Nama user, tombol kembali, logout -->
        <div class="flex items-center gap-2">
            <span class="text-sm">Halo, <?= htmlspecialchars($_SESSION['nama'] ?? 'Warga') ?></span>

            <!-- Tombol kembali -->
            <a href="../index.php" class="bg-white text-primary px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-100 transition">
                ← Kembali
            </a>

            <!-- Logout -->
            <a href="logout.php" class="bg-red-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container my-4">
    <h2 class="text-center">Laporan Bencana</h2>

  

    <?php if (isset($_GET['sukses'])): ?>
        <div class="alert alert-success text-center">Laporan berhasil dikirim</div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger text-center">Gagal menyimpan: <?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="jenisBencana" class="form-label">Jenis Bencana</label>
            <select class="form-select" id="jenisBencana" name="jenis_bencana" required>
                <option value="" disabled selected>Pilih Jenis Bencana</option>
                <option value="Gempa Bumi">Gempa Bumi</option>
                <option value="Banjir">Banjir</option>
                <option value="Tanah Longsor">Tanah Longsor</option>
                <option value="Kebakaran">Kebakaran</option>
                <option value="Tsunami">Tsunami</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="kota" class="form-label">Daerah</label>
            <input type="text" class="form-control" id="kota" name="kota" required>
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi Kejadian</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal Kejadian</label>
            <input type="date" class="form-control" name="tanggal" id="tanggal" required>
        </div>
        <div class="mb-3">
            <label for="lokasi" class="form-label">Lokasi (Koordinat)</label>
            <input type="text" class="form-control" name="lokasi" id="lokasi" required>
        </div>
        <div class="mb-3">
            <label for="foto" class="form-label">Unggah Foto (opsional)</label>
            <input type="file" class="form-control" name="foto" id="foto" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Kirim Laporan</button>
          <div class="mb-3">
        <!-- <a href="../index.php" class="btn btn-secondary">← Kembali</a> -->
    </div>
    </form>
</div>
</body>
</html>
