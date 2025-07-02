<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = htmlspecialchars(trim($_POST['judul']));
    $isi = htmlspecialchars(trim($_POST['isi']));
    $penulis = $_SESSION['nama_admin'] ?? 'Admin Desa';
    $gambarPath = null;

    // Upload file ke folder ../uploads/
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = uniqid() . '-' . basename($_FILES['gambar']['name']);
        $targetFile = $uploadDir . $filename;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($fileType, $allowedTypes)) {
            move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile);
            $gambarPath = 'uploads/' . $filename; // Simpan path relatif ke root project
        }
    }

    // Simpan berita
    $stmt = $koneksi->prepare("INSERT INTO berita_desa (judul, isi, penulis, gambar, tanggal) VALUES (?, ?, ?, ?, CURDATE())");
    $stmt->bind_param("ssss", $judul, $isi, $penulis, $gambarPath);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Berita berhasil ditambahkan!";
    header("Location: tambah-berita.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Berita Desa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
        }

        .form-label {
            font-weight: 600;
        }

        .preview-box img {
            max-height: 200px;
            margin-top: 10px;
            border-radius: 10px;
        }

        .btn-success {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="card p-4 bg-white">
                    <h4 class="mb-4 text-center text-primary">Tambah Berita Desa</h4>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $_SESSION['success'];
                            unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Judul Berita</label>
                            <input type="text" name="judul" class="form-control" id="judulInput" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Isi Berita</label>
                            <textarea name="isi" rows="6" class="form-control" id="isiInput" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar Pendukung</label>
                            <input type="file" name="gambar" accept="image/*" class="form-control" id="gambarInput">
                        </div>

                        <div class="preview-box border p-3 rounded bg-light">
                            <h5 class="text-secondary">🔍 Preview:</h5>
                            <h6 id="previewJudul" class="fw-bold"></h6>
                            <p id="previewIsi" style="white-space: pre-line;"></p>
                            <img id="previewGambar" src="#" alt="Preview Gambar" class="img-fluid d-none" />
                        </div>

                        <button type="submit" class="btn btn-success mt-4">Posting Berita</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- JS -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>