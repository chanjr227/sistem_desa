<?php
session_start();
require '../config/config.php';

// Cek apakah user sudah login dan merupakan admin
if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../login.php');
  exit;
}
?>



<?php include '../template/header.php'; ?>
<?php include '../template/navbar.php'; ?>
<div id="layoutSidenav">
  <?php include '../template/sidebar.php'; ?>

  <div id="layoutSidenav_content">
    <div class="container-fluid px-4">
      <h2 class="my-4 text-center">Data Laporan Bencana</h2>

      <div class="table-responsive shadow rounded bg-white p-3">
        <table class="table table-hover table-bordered align-middle">
          <thead class="table-primary text-center">
            <tr>
              <th>No</th>
              <th>Nama Pelapor</th>
              <th>Jenis Bencana</th>
              <th>Deskripsi</th>
              <th>Tanggal Laporan</th>
              <th>Kota</th>
              <th>Lokasi</th>
              <th>Foto</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $query = "SELECT * FROM laporan ORDER BY tanggal_laporan DESC";
            $result = $koneksi->query($query);
            while ($row = $result->fetch_assoc()) {
              $jenisBencana = htmlspecialchars($row['jenis_bencana']);
              $badgeColor = match (strtolower($jenisBencana)) {
                'banjir' => 'primary',
                'gempa' => 'danger',
                'kebakaran' => 'warning',
                'longsor' => 'success',
                default => 'secondary'
              };

              echo "<tr>
                    <td class='text-center'>{$no}</td>
                    <td>" . htmlspecialchars($row['nama_pelapor']) . "</td>
                    <td><span class='badge bg-{$badgeColor}'>" . $jenisBencana . "</span></td>
                    <td>" . nl2br(htmlspecialchars($row['deskripsi'])) . "</td>
                    <td class='text-center'>" . htmlspecialchars($row['tanggal_laporan']) . "</td>
                    <td>" . htmlspecialchars($row['kota']) . "</td>
                    <td>" . htmlspecialchars($row['lokasi']) . "</td>
                    <td class='text-center'>";
              if (!empty($row['foto'])) {
                echo "<img src='../user/uploads/" . htmlspecialchars($row['foto']) . "' class='img-thumbnail' width='100'>";
              } else {
                echo "-";
              }
              echo "</td>
                  </tr>";
              $no++;
            }
            ?>
          </tbody>
        </table>

        <div class="mb-3 text-end">
          <a href="ekspor-pdf.php" class="btn btn-danger" target="_blank">🖨️ Ekspor PDF</a>
          <!-- <a href="dashboard.php" class="btn btn-secondary">← Kembali</a>
    </div> -->
        </div>
      </div>

      <?php include '../template/footer.php'; ?>
    </div>
  </div>