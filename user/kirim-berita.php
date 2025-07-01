<?php
session_start();
require '../config/config.php';
require '../helpers/log_helpers.php';

// A01 - Broken Access Control & Identification
if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;

}
// CSRF Token (jika belum ada, buat)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cek token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed.");
    }

    $userid = $_SESSION['userid'];
    $judul = htmlspecialchars($_POST['judul'], ENT_QUOTES, 'UTF-8');
    $isi = htmlspecialchars($_POST['isi'], ENT_QUOTES, 'UTF-8');
    $gambar = null;

    // Validasi & Upload Gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2MB

        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_name = basename($_FILES['gambar']['name']);
        $file_type = mime_content_type($file_tmp);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['berita_success'] = "Format gambar tidak valid (harus .jpg / .png)";
            header("Location: kirim-berita.php");
            exit;
        }

        if ($_FILES['gambar']['size'] > $max_size) {
            $_SESSION['berita_success'] = "Ukuran gambar terlalu besar (maks. 2MB)";
            header("Location: kirim-berita.php");
            exit;
        }

        $new_filename = uniqid('berita_', true) . '.' . $file_ext;
        $upload_dir = '../uploads/berita/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
            $gambar = $new_filename;
        }
    }

    // Simpan ke DB
    $stmt = $koneksi->prepare("INSERT INTO berita_pending (userid, judul, isi, gambar) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userid, $judul, $isi, $gambar);
    $stmt->execute();
    $stmt->close();

    $_SESSION['berita_success'] = "Berita berhasil dikirim dan akan ditinjau oleh Admin.";
    header("Location: kirim-berita.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kirim Berita Desa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen px-4">

<nav class="bg-blue-600 text-white px-4 py-3 shadow mb-6">
    <div class="flex justify-between items-center max-w-6xl mx-auto">
        <a href="../index.php" class="text-lg font-bold">Sistem Informasi Desa</a>
        <div class="flex items-center space-x-3 text-sm">
            <span>👋 <?= htmlspecialchars($_SESSION['nama'] ?? 'Warga', ENT_QUOTES, 'UTF-8') ?></span>
            <a href="../index.php" class="bg-white text-blue-600 px-3 py-1 rounded hover:bg-blue-100 transition">← Kembali</a>
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white transition">Logout</a>
        </div>
    </div>
</nav>

<div class="flex justify-center">
    <div class="w-full max-w-xl">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-blue-600 mb-4">📝 Kirim Berita Desa</h2>

            <?php if (isset($_SESSION['berita_success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Info:</strong>
                    <span class="block sm:inline"><?= $_SESSION['berita_success']; ?></span>
                    <?php unset($_SESSION['berita_success']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Berita</label>
                    <input type="text" name="judul" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Isi Berita</label>
                    <textarea name="isi" rows="6" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 resize-none"
                        placeholder="Tulis berita Anda..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Gambar (opsional)</label>
                    <input type="file" name="gambar"
                        accept=".jpg,.jpeg,.png"
                        class="w-full px-2 py-2 border border-gray-300 rounded-md file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <small class="text-gray-500 text-xs">Maks: 2MB | Format: .jpg, .jpeg, .png</small>
                </div>

                <div>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition">
                        Kirim Berita
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
