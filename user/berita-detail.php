<?php
require '../config/config.php';
session_start();

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$nama_user = $_SESSION['nama'] ?? 'Warga';
$id = intval($_GET['id'] ?? 0);
$query = mysqli_query($koneksi, "SELECT * FROM berita_desa WHERE id = $id");
$berita = mysqli_fetch_assoc($query);

// Berita terbaru untuk sidebar
$sidebar = mysqli_query($koneksi, "SELECT id, judul, tanggal, gambar FROM berita_desa ORDER BY tanggal DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Detail Berita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">
    <nav class="bg-blue-600 shadow py-4 px-6 flex justify-between items-center text-white">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-blue-600 font-bold">SD</div>
            <div>
                <h1 class="text-xl font-semibold">Desa Rajeg</h1>
                <p class="text-xs text-blue-100">Kecamatan Rajeg, Kabupaten Tangerang</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <a href="../index.php" class="hover:underline">Beranda</a>
            <a href="berita.php" class="border-b-2 border-white font-semibold">Berita</a>
            <a href="#" class="hover:underline">Galeri</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row gap-6">
        <!-- Konten Utama -->
        <main class="flex-1 bg-white rounded-xl shadow p-6">
            <a href="berita.php" class="text-sm text-blue-600 hover:underline mb-2 block">← Kembali ke Daftar Berita</a>
            <?php if ($berita): ?>
                <h1 class="text-2xl font-bold text-gray-800 mb-3"><?= htmlspecialchars($berita['judul']) ?></h1>
                <p class="text-sm text-gray-500 mb-4">📅 <?= htmlspecialchars($berita['tanggal']) ?> | ✍️ <?= htmlspecialchars($berita['penulis']) ?></p>
                <?php if (!empty($berita['gambar']) && file_exists('../' . $berita['gambar'])): ?>
                    <img src="../<?= $berita['gambar'] ?>" alt="Gambar Berita" class="rounded-lg mb-4 w-full max-h-[450px] object-cover">
                <?php endif; ?>
                <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($berita['isi'])) ?></p>
            <?php else: ?>
                <p class="text-red-600 font-semibold">Berita tidak ditemukan.</p>
            <?php endif; ?>
        </main>

        <!-- Sidebar Berita Terbaru -->
        <aside class="w-full md:w-1/3">
            <div class="bg-white rounded-xl shadow p-4">
                <h2 class="text-lg font-bold mb-4">🗞️ Berita Terbaru</h2>
                <?php while ($row = mysqli_fetch_assoc($sidebar)): ?>
                    <a href="berita-detail.php?id=<?= $row['id'] ?>" class="flex items-center mb-3 hover:bg-gray-100 p-2 rounded transition">
                        <?php if (!empty($row['gambar']) && file_exists('../' . $row['gambar'])): ?>
                            <img src="../<?= $row['gambar'] ?>" alt="" class="w-12 h-12 object-cover rounded mr-3">
                        <?php else: ?>
                            <div class="w-12 h-12 bg-gray-300 rounded mr-3"></div>
                        <?php endif; ?>
                        <div>
                            <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($row['judul']) ?></p>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($row['tanggal']) ?></p>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </aside>
    </div>
</body>

</html>