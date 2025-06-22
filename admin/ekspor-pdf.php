<?php
require '../config/config.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Konfigurasi Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);

// Ambil data laporan dari database
$query = "SELECT * FROM laporan ORDER BY tanggal_laporan DESC";
$result = $koneksi->query($query);

// Buat HTML untuk isi PDF
$html = '
<style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #999; padding: 5px; text-align: left; vertical-align: top; }
    th { background-color: #f2f2f2; }
    h2 { text-align: center; margin-bottom: 20px; }
</style>

<h2>Laporan Bencana Desa</h2>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Pelapor</th>
            <th>Jenis Bencana</th>
            <th>Deskripsi</th>
            <th>Tanggal</th>
            <th>Kota</th>
            <th>Lokasi</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
while ($row = $result->fetch_assoc()) {
    $html .= "<tr>
        <td>{$no}</td>
        <td>" . htmlspecialchars($row['nama_pelapor']) . "</td>
        <td>" . htmlspecialchars($row['jenis_bencana']) . "</td>
        <td>" . nl2br(htmlspecialchars($row['deskripsi'])) . "</td>
        <td>" . htmlspecialchars($row['tanggal_laporan']) . "</td>
        <td>" . htmlspecialchars($row['kota']) . "</td>
        <td>" . htmlspecialchars($row['lokasi']) . "</td>
    </tr>";
    $no++;
}

$html .= '</tbody></table>';

// Generate PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("laporan_bencana.pdf", ["Attachment" => false]); // true = download langsung
