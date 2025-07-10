<?php
session_start();
require '../config/config.php';
require '../helpers/auth_helpers.php';
check_access(['admin', 'rt', 'staff_desa']);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = htmlspecialchars(trim($_POST['judul']));
    $isi = htmlspecialchars(trim($_POST['isi']));
    $penulis = $_SESSION['nama_admin'] ?? 'Admin Desa';
    $gambarPath = null;

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = uniqid() . '-' . basename($_FILES['gambar']['name']);
        $targetFile = $uploadDir . $filename;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($fileType, $allowedTypes)) {
            move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile);
            $gambarPath = 'uploads/' . $filename;
        }
    }

    $stmt = $koneksi->prepare("INSERT INTO berita_desa (judul, isi, penulis, gambar, tanggal) VALUES (?, ?, ?, ?, CURDATE())");
    $stmt->bind_param("ssss", $judul, $isi, $penulis, $gambarPath);
    if ($stmt->execute()) {
        $success = "✅ Berita berhasil diposting.";
    } else {
        $error = "❌ Gagal menambahkan berita.";
    }
    $stmt->close();
}
?>

<?php include '../template/header.php'; ?>
<?php include '../template/navbar.php'; ?>

<div id="layoutSidenav">
    <?php include '../template/sidebar.php'; ?>

    <div id="layoutSidenav_content">
        <main class="container-fluid px-4 py-4">
            <h1 class="mb-4">📰 Tambah Berita Desa</h1>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">Form Tambah Berita</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Judul Berita</label>
                            <input type="text" name="judul" id="judulInput" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Isi Berita</label>
                            <textarea name="isi" rows="6" id="isiInput" class="form-control" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Gambar Pendukung</label>
                            <input type="file" name="gambar" accept="image/*" class="form-control" id="gambarInput">
                        </div>
                        <div class="col-12">
                            <h5 class="text-secondary mt-3">🔍 Preview:</h5>
                            <div class="p-3 border rounded bg-light">
                                <h6 id="previewJudul" class="fw-bold"></h6>
                                <p id="previewIsi" style="white-space: pre-line;"></p>
                                <img id="previewGambar" src="#" alt="Preview Gambar" class="img-fluid d-none rounded" />
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-secondary">Reset</button>
                            <button type="submit" class="btn btn-success">Posting Berita</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <?php include '../template/footer.php'; ?>
    </div>
</div>

<script>
    document.getElementById('judulInput').addEventListener('input', function() {
        document.getElementById('previewJudul').textContent = this.value;
    });
    document.getElementById('isiInput').addEventListener('input', function() {
        document.getElementById('previewIsi').textContent = this.value;
    });
    document.getElementById('gambarInput').addEventListener('change', function(e) {
        const preview = document.getElementById('previewGambar');
        const file = e.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        } else {
            preview.src = '#';
            preview.classList.add('d-none');
        }
    });
</script>