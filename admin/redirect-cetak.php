<?php
require '../config/config.php';

$id = intval($_GET['id']);
$result = mysqli_query($koneksi, "SELECT * FROM pengajuan_surat WHERE id = $id LIMIT 1");
$data = mysqli_fetch_assoc($result);
$jenis = $data['jenis_surat'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Disetujui</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            padding: 40px;
            text-align: center;
        }
        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>✅ Surat <?= htmlspecialchars($jenis) ?> Telah Disetujui</h2>
        <p>Silakan klik tombol di bawah ini untuk mencetak surat.</p>
        <button onclick="window.open('cetak-surat.php?id=<?= $id ?>', '_blank'); window.location.href='pengajuan-surat-admin.php?pesan=berhasil';">
            Cetak Surat
        </button>
    </div>
</body>
</html>
