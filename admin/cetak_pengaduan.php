<?php
require '../config/config.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;

// Ambil data pengaduan
$query = "SELECT id, userid, nama_karyawan, jabatan_karyawan, isi_pengaduan, tanggal_pengaduan FROM pengaduan ORDER BY tanggal_pengaduan DESC";
$result = $koneksi->query($query);

// Siapkan HTML untuk PDF
$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengaduan</title>
    <style>
        body { font-family: sans-serif; }
        h2 { text-align: center; }
        table { border-collapse: collapse; width: 100%; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; vertical-align: top; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>Laporan Data Pengaduan Karyawan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>User ID</th>
                <th>Nama Karyawan</th>
                <th>Jabatan</th>
                <th>Isi Pengaduan</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>';

$no = 1;
while ($row = $result->fetch_assoc()) {
    $html .= '<tr>
        <td>' . $no++ . '</td>
        <td>' . htmlspecialchars($row['userid']) . '</td>
        <td>' . htmlspecialchars($row['nama_karyawan']) . '</td>
        <td>' . htmlspecialchars($row['jabatan_karyawan']) . '</td>
        <td>' . nl2br(htmlspecialchars($row['isi_pengaduan'])) . '</td>
        <td>' . $row['tanggal_pengaduan'] . '</td>
    </tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

// Buat PDF dengan Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output ke browser
$dompdf->stream("laporan_pengaduan.pdf", array("Attachment" => false));
exit;
