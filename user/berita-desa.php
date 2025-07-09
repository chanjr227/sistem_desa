<?php
require '../config/config.php';
session_start();

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$nama_user = $_SESSION['nama'] ?? 'Warga';
$query = mysqli_query($koneksi, "SELECT * FROM berita_desa ORDER BY tanggal DESC");
$berita_utama = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Berita Desa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body class="bg-gray-100 font-sans">

    <!-- Header -->
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
            <span class="border-b-2 border-white font-semibold">Berita</span>
            <a href="#" class="hover:underline">Galeri</a>
        </div>
    </nav>

    <div class="flex flex-col md:flex-row max-w-7xl mx-auto mt-6 px-4 gap-6">

        <!-- Sidebar -->
        <aside class="w-full md:w-1/4 bg-gradient-to-b from-blue-700 to-blue-600 text-white rounded-xl p-6 shadow">
            <h2 class="text-lg font-bold mb-2"><i class="fa-solid fa-newspaper mr-2"></i>Berita Desa</h2>
            <p class="text-sm text-blue-100">Informasi dan berita terbaru dari Desa Rajeg</p>
        </aside>

        <!-- Konten Berita -->
        <main class="flex-1">

            <!-- Berita Utama -->
            <?php if ($berita_utama): ?>
                <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
                    <?php
                    $gambarUtama = "../" . ltrim($berita_utama['gambar'], '/');
                    $judul = htmlspecialchars($berita_utama['judul']);
                    $isi = strip_tags($berita_utama['isi']);
                    ?>
                    <img src="<?= $gambarUtama ?>" class="w-full h-64 object-cover" alt="Gambar Utama">
                    <div class="p-5">
                        <p class="text-sm text-blue-600 mb-2">📅 <?= htmlspecialchars($berita_utama['tanggal']) ?></p>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2"><?= $judul ?></h2>
                        <p class="text-gray-600 mb-3"><?= substr($isi, 0, 120) ?>...</p>
                        <a href="#" class="text-blue-700 hover:underline">Baca Selengkapnya →</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Berita Lainnya -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($berita = mysqli_fetch_assoc($query)): ?>
                    <div class="bg-white rounded-xl shadow hover:shadow-md transition overflow-hidden flex flex-col">
                        <?php
                        $gambar = "../" . ltrim($berita['gambar'], '/');
                        $isi = strip_tags($berita['isi']);
                        ?>
                        <img src="<?= $gambar ?>" class="h-40 w-full object-cover" alt="Gambar">
                        <div class="p-4 flex flex-col flex-grow">
                            <span class="text-xs text-blue-600 mb-1">📅 <?= htmlspecialchars($berita['tanggal']) ?></span>
                            <h5 class="font-semibold text-gray-900"><?= htmlspecialchars($berita['judul']) ?></h5>
                            <p class="text-sm text-gray-600 mt-2 flex-grow"><?= substr($isi, 0, 80) ?>...</p>
                            <a href="#" class="mt-3 text-blue-700 hover:underline text-sm">Baca Selengkapnya</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>

</body>

</html>