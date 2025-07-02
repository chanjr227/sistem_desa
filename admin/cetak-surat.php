<?php
require '../config/config.php';

// Cegah SQL injection
$id = intval($_GET['id']);
$result = $koneksi->query("SELECT * FROM pengajuan_surat WHERE id = $id LIMIT 1");
$data = $result->fetch_assoc();

$jenis = $data['jenis_surat'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Cetak Surat <?= htmlspecialchars($jenis) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
        }

        .header {
            text-align: center;
        }

        .header h2,
        .header p {
            margin: 0;
        }

        .line {
            margin-bottom: 10px;
        }

        .content {
            margin-top: 20px;
        }

        table td {
            vertical-align: top;
            padding: 4px 8px;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <h2>PEMERINTAH DESA RAJEG</h2>
        <p><strong>SURAT KETERANGAN <?= strtoupper(htmlspecialchars($jenis)) ?></strong></p>
        <hr>
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>
        <table>
            <tr>
                <td>Nama</td>
                <td>: <?= htmlspecialchars($data['nama_lengkap']) ?></td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>: <?= htmlspecialchars($data['jenis_kelamin']) ?></td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>: <?= htmlspecialchars($data['tempat_lahir']) ?>, <?= date('d-m-Y', strtotime($data['tanggal_lahir'])) ?></td>
            </tr>
            <tr>
                <td>NIK / Nomor KTP</td>
                <td>: <?= htmlspecialchars($data['nik']) ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>: <?= htmlspecialchars($data['alamat']) ?></td>
            </tr>
            <tr>
                <td>Pekerjaan</td>
                <td>: <?= htmlspecialchars($data['pekerjaan']) ?></td>
            </tr>
        </table>

        <p>
            Adalah benar-benar warga RT. 02 – RW. 03 Kelurahan Rajeg, Kecamatan Rajeg, Kota Tangerang
            yang bermaksud memohon/mengurus <strong><?= htmlspecialchars($data['keperluan']) ?></strong>.
        </p>

        <p>Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

        <div style="text-align: right; margin-top: 50px;">
            Tangerang, <?= date('d-m-Y', strtotime($data['Tanggal_pengajuan'])) ?><br>
            TTD RT<br><br><br><br>
            <strong><u>Nama King Sepuh Suhu Yanyan Julyandi</u></strong>
        </div>
    </div>

</body>

</html>