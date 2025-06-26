<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pengaduan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style-pengaduan.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">📝 Form Pengaduan Kinerja Karyawan</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="mb-3">
            <label for="nama_karyawan" class="form-label">Nama Karyawan</label>
            <input type="text" name="nama_karyawan" id="nama_karyawan" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="jabatan_karyawan" class="form-label">Jabatan Karyawan</label>
            <input type="text" name="jabatan_karyawan" id="jabatan_karyawan" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="isi_pengaduan" class="form-label">Isi Pengaduan</label>
            <textarea name="isi_pengaduan" id="isi_pengaduan" rows="5" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">🚀 Kirim Pengaduan</button>
    </form>

    <a href="../index.php" class="btn btn-secondary mt-3">← Kembali ke Dashboard</a>
</div>
</body>
</html>
