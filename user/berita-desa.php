<?php
require '../config/config.php';
session_start();

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$nama_user = $_SESSION['nama'] ?? 'Warga';

$query = mysqli_query($koneksi, "SELECT * FROM berita_desa ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Semua Berita Desa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 overflow-x-hidden">

    <!-- Navbar -->
    <nav class="bg-blue-600 text-white px-4 py-3 shadow-md">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center">
            <a href="../index.php" class="text-lg font-bold">Sistem Informasi Desa</a>
            <div class="flex items-center space-x-4 mt-2 sm:mt-0">
                <span>Halo, <?= htmlspecialchars($nama_user) ?></span>
                <a href="../index.php" class="bg-white text-blue-600 px-3 py-1 rounded text-sm hover:bg-gray-100 transition">← Kembali</a>
                <a href="logout.php" class="bg-red-500 px-3 py-1 rounded text-sm hover:bg-red-600 transition">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-800 mb-8">📰 Semua Berita Desa</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (mysqli_num_rows($query) > 0): ?>
                <?php while ($berita = mysqli_fetch_assoc($query)): ?>
                    <div class="bg-white rounded-2xl shadow hover:shadow-lg transition duration-300 flex flex-col">
                        <?php
                        $gambarPath = "../" . ltrim($berita['gambar'], '/');
                        if (!empty($berita['gambar']) && file_exists($gambarPath)): ?>
                            <img src="<?= htmlspecialchars($gambarPath) ?>" alt="Gambar Berita" class="h-48 w-full object-cover rounded-t-2xl">
                        <?php else: ?>
                            <div class="h-48 w-full bg-gray-400 flex items-center justify-center text-white text-sm rounded-t-2xl">
                                Gambar tidak tersedia
                            </div>
                        <?php endif; ?>

                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <h5 class="text-lg font-semibold text-gray-900 mb-2"><?= htmlspecialchars($berita['judul']) ?></h5>
                                <p class="text-gray-700 text-sm mb-4"><?= substr(htmlspecialchars($berita['isi']), 0, 100) ?>...</p>
                                <p class="text-xs text-gray-500">📅 <?= htmlspecialchars($berita['tanggal']) ?></p>
                                <span class="text-xs text-gray-600">✍️ <?= htmlspecialchars($berita['penulis']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-500">Belum ada berita tersedia.</p>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>