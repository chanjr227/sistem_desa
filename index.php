<?php
//A01: Broken Access Control
session_start();
require 'config/config.php';
//A01 + A03 Gunakan default value + tidak bergantung pada input user ( A01: Broken Access Control)
$nama_user = $_SESSION['nama'] ?? 'Warga';
// A03: Injection (SQL Injection)
$result = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penduduk");
$row = mysqli_fetch_assoc($result);
$jumlah_penduduk = $row['total'];

$jadwal_kegiatan_umum = mysqli_query($koneksi, "SELECT * FROM jadwal_kegiatan ORDER BY tanggal DESC LIMIT 3");
$jadwal_posyandu = mysqli_query($koneksi, "SELECT * FROM jadwal_posyandu ORDER BY tanggal DESC LIMIT 3");

$statistik_tahun = [];
$statistik_jumlah = [];

$sql = "SELECT YEAR(tanggal_lahir) AS tahun, COUNT(*) AS jumlah 
        FROM penduduk 
        GROUP BY tahun 
        ORDER BY tahun ASC";
$query = mysqli_query($koneksi, $sql);

while ($row = mysqli_fetch_assoc($query)) {
    $statistik_tahun[] = $row['tahun'];
    $statistik_jumlah[] = $row['jumlah'];
}

$berita = mysqli_query($koneksi, "SELECT * FROM berita_desa ORDER BY tanggal DESC LIMIT 3");


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <!---  A05: Security Misconfiguration -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Desa</title>
    <!---A06: Vulnerable and Outdated Components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="css/user.css" rel="stylesheet" />
    <link href="css/hero.css" rel="stylesheet" />
</head>
<body>

<?php if (isset($_SESSION['login_success'])): ?>
    <div class="toast-notif" id="loginToast">
        <?= $_SESSION['login_success'] ?>
    </div>
    <?php unset($_SESSION['login_success']); ?>
<?php endif; ?>
<!---- navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center">
        <a class="navbar-brand" href="#">Sistem Informasi Desa</a>
        <div class="d-flex flex-wrap align-items-center">
            <span class="navbar-text text-white me-3">Halo, <?= htmlspecialchars($nama_user) ?></span>

            <?php if (isset($_SESSION['log']) && $_SESSION['role'] === 'user'): ?>
                <a href="user/kirim-berita.php" class="btn btn-light btn-sm me-2">
                    <i class="fa-solid fa-pen-to-square"></i> Kirim Berita
                </a>
            <?php endif; ?>

            <?php if (isset($_SESSION['log']) && $_SESSION['log'] === true): ?>
                <a href="user/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light btn-sm">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<section class="hero-cover text-center p-5">
    <h1 class="hero-title" data-text="Desa Rajeg">Desa Rajeg</h1>
    <p class="hero-subtitle animate-shine">Menuju Desa Digital, Maju dan Sejahtera</p>
</section>

<div class="container mt-5">
    <h2 class="mb-4">Dashboard Utama</h2>

    <div class="col-12">
    <div class="card shadow-sm p-4">
        <div class="row align-items-center">
            <!-- Kiri: Profil Desa -->
            <div class="card shadow-sm p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <i class="fa-solid fa-map-location-dot fa-2x text-primary me-3"></i>
        <h5 class="mb-0 text-primary fw-bold">Profil Desa Rajeg</h5>
    </div>
    <p class="mb-1"><strong>Alamat:</strong></p>
    <p class="text-muted">
        Desa Rajeg, Kecamatan Rajeg,<br>
        Kabupaten Tangerang, Provinsi Banten.
    </p>
    <p class="text-secondary" style="font-size: 0.9rem;">
        Terletak di wilayah strategis, Desa Rajeg terus berkembang menuju desa digital
        yang maju, transparan, dan inklusif bagi seluruh warganya.
    </p>


            <!-- Kanan: Grafik -->
            <div class="card mt-5 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">📊 Statistik Penduduk</h5>
                    <canvas id="pendudukChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


    <div class="layanan-cepat p-4 text-center bg-white shadow rounded-3 mt-4 mx-1">
    <h4 class="text-primary mb-4"><i class="fa-solid fa-bolt"></i> Layanan Cepat</h4>
    <div class="row g-4 justify-content-center">

        <!-- Ajukan Surat -->
        <div class="col-6 col-md-3">
            <a href="user/menu-pengajuan-surat.php" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <div class="card-body">
                        <i class="fa-solid fa-file-circle-plus fa-2x text-success mb-2"></i>
                        <p class="fw-bold mb-0">Ajukan Surat</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Laporan Bencana -->
        <div class="col-6 col-md-3">
            <a href="user/laporan-bencana.php" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <div class="card-body">
                        <i class="fa-solid fa-triangle-exclamation fa-2x text-danger mb-2"></i>
                        <p class="fw-bold mb-0">Laporan Bencana</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Kesehatan Desa -->
        <div class="col-6 col-md-3">
            <a href="user/kesehatan.php" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <div class="card-body">
                        <i class="fa-solid fa-heart-pulse fa-2x text-info mb-2"></i>
                        <p class="fw-bold mb-0">Kesehatan Desa</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Surat KTP/KK -->
        <div class="col-6 col-md-3">
            <a href="user/pengaduan-kinerja.php" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <div class="card-body">
                        <i class="fa-solid fa-id-card fa-2x text-primary mb-2"></i>
                        <p class="fw-bold mb-0">Laporan pengaduan</p>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>


    <div class="mt-5">
        <h4 class="mb-3">📋 Jadwal Kegiatan Umum</h4>
        <div class="row g-3">
            <?php if ($jadwal_kegiatan_umum && mysqli_num_rows($jadwal_kegiatan_umum) > 0): ?>
                <?php while($k = mysqli_fetch_assoc($jadwal_kegiatan_umum)): ?>
                    <div class="col-12 col-md-4">
                        <div class="card shadow-sm border-secondary card-menu-animate h-100">
                            <div class="card-body">
                                <h6 class="card-subtitle text-muted mb-1">
                                    <?= date('d M Y', strtotime($k['tanggal'])) ?> - <?= htmlspecialchars($k['waktu'] ?? '-') ?>
                                </h6>
                                <h5 class="card-title"><?= htmlspecialchars($k['nama_kegiatan']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($k['keterangan'] ?? 'Tidak ada keterangan') ?></p>
                                <span class="badge bg-dark">Lokasi: <?= htmlspecialchars($k['lokasi'] ?? '-') ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">Belum ada jadwal kegiatan umum.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-5">
        <h4 class="mb-3">🩺 Jadwal Posyandu</h4>
        <div class="row g-3">
            <?php while($p = mysqli_fetch_assoc($jadwal_posyandu)): ?>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-info card-menu-animate h-100">
                        <div class="card-body">
                            <h6 class="card-subtitle text-muted mb-1">
                                <?= date('d M Y', strtotime($p['tanggal'])) ?> - <?= htmlspecialchars($p['waktu'] ?? '-') ?>
                            </h6>
                            <h5 class="card-title"><?= htmlspecialchars($p['lokasi']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($p['keterangan'] ?? 'Tidak ada keterangan') ?></p>
                            <span class="badge bg-primary">Petugas: <?= htmlspecialchars($p['petugas'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="mt-5">
    <h4 class="mb-3">📰 Berita Desa</h4>
    <div class="row g-3">
        <?php if ($berita && mysqli_num_rows($berita) > 0): ?>
            <?php while ($b = mysqli_fetch_assoc($berita)): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100 border-warning">
                        <?php if (!empty($b['gambar'])): ?>
                    <img src="<?= htmlspecialchars($b['gambar']) ?>" class="card-img-top" style="max-height: 180px; object-fit: cover;" alt="Gambar Berita">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($b['judul']) ?></h5>
                            <small class="text-muted"><?= date('d M Y', strtotime($b['tanggal'])) ?> | <?= htmlspecialchars($b['penulis']) ?></small>
                            <p class="card-text mt-2"><?= htmlspecialchars(substr($b['isi'], 0, 100)) ?>...</p>
                            <a href="user/berita-desa.php" class="btn btn-sm btn-outline-warning">Selengkapnya</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">Belum ada berita terbaru.</div>
            </div>
        <?php endif; ?>
    </div>

    <div class="text-center mt-3">
        <a href="user/berita-desa.php" class="btn btn-warning">Lihat Semua Berita</a>
    </div>
</div>


    <div class="sambutan-box fade-in mt-5" id="sambutan">
        <h5>Sambutan Kepala Desa</h5>
        <p>
            Assalamu'alaikum warahmatullahi wabarakatuh.  
            Saya selaku Kepala Desa Rajeg mengucapkan terima kasih atas partisipasi warga dalam membangun desa yang kita cintai ini.
            Dengan sistem informasi ini, kami harap pelayanan dapat lebih cepat, transparan, dan akuntabel.  
            Mari kita wujudkan desa yang maju dan sejahtera bersama!
        </p>
        <p class="text-end"><strong>– Bapak Yanyan, Kepala Desa</strong></p>
    </div>

 <div class="container py-4">
    <h2 class="text-center mb-4">Struktur Organisasi Desa</h2>

    <div class="tree">
      <ul>
        <li>
          <a href="#"><img src="aset/yanyan2.jpg">Lurah<br><small>Yanyan Julyandi</small></a>
          <ul>
            <li>
              <a href="#"><img src="aset/rojak2.jpg">Sekretaris Desa<br><small>Bintang Rogerman</small></a>
              <ul>
                <li><a href="#"><img src="aset/rojak2.jpg">Bendahara<br><small>Bintang Rogerman</small></a></li>
                <li><a href="#"><img src="aset/ucup.jpg">Kaur Umum<br><small>M. Yusuf</small></a></li>
              </ul>
            </li>
            <li>
              <a href="#"><img src="aset/yanyan2.jpg">RW 01<br><small>Yanyan Julyandi</small></a>
              <ul>
                <li><a href="#"><img src="aset/ucup.jpg">RT 01<br><small>M. Yusuf</small></a></li>
                <li><a href="#"><img src="aset/rojak2.jpg">RT 02<br><small>Bintang Rogerman</small></a></li>
              </ul>
            </li>
            <li>
              <a href="#"><img src="aset/ucup.jpg">RW 02<br><small>M. Yusuf</small></a>
              <ul>
                <li><a href="#"><img src="aset/rojak2.jpg">RT 03<br><small>Bintang Rogerman</small></a></li>
                <li><a href="#"><img src="aset/ucup.jpg">RT 04<br><small>M. Yusuf</small></a></li>
              </ul>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
<!-- Modal Detail Struktur -->
<div class="modal fade" id="strukturModal" tabindex="-1" aria-labelledby="strukturModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="strukturModalLabel">Detail Pejabat</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
        <img id="strukturFoto" src="" alt="Foto" class="img-fluid rounded mb-3" style="max-height: 200px;">
        <blockquote class="blockquote">
          <p id="strukturQuote" class="mb-0"></p>
          <footer class="blockquote-footer mt-2" id="strukturNama"></footer>
        </blockquote>
      </div>
    </div>
  </div>
</div>

    <footer class="mt-5 text-center text-muted">
        <hr>
        <small>&copy; <?= date('Y') ?> Sistem Informasi Desa - Dibuat oleh Kelompok 1</small>
    </footer>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const toast = document.getElementById("loginToast");
        if (toast) {
            setTimeout(() => { toast.remove(); }, 4000);
        }
    });
</script>
<script src="js/user.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.struktur-clickable').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        const foto = this.getAttribute('data-foto');
        const nama = this.getAttribute('data-nama');
        const quote = this.getAttribute('data-quote');

        document.getElementById('strukturFoto').src = foto;
        document.getElementById('strukturNama').textContent = nama;
        document.getElementById('strukturQuote').textContent = quote;

        const modal = new bootstrap.Modal(document.getElementById('strukturModal'));
        modal.show();
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('pendudukChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($statistik_tahun) ?>,
        datasets: [{
            label: 'Jumlah Penduduk',
            data: <?= json_encode($statistik_jumlah) ?>,
            backgroundColor: 'rgba(13, 110, 253, 0.5)',
            borderColor: '#0d6efd',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => `${ctx.parsed.y} jiwa`
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Jumlah Jiwa'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Tahun Lahir'
                }
            }
        }
    }
});
</script>

<script>
  document.querySelectorAll('.struktur-clickable').forEach(el => {
    el.addEventListener('click', function(e) {
      e.preventDefault();

      const nama = this.dataset.nama;
      const foto = this.dataset.foto;
      const quote = this.dataset.quote;

      document.getElementById('strukturNama').textContent = nama;
      document.getElementById('strukturFoto').src = foto;
      document.getElementById('strukturQuote').textContent = quote;

      const modal = new bootstrap.Modal(document.getElementById('strukturModal'));
      modal.show();
    });
  });
</script>
<script>
  const modalEl = document.getElementById('strukturModal');

  modalEl.addEventListener('hidden.bs.modal', function () {
    // Hapus class "modal-open" dari body jika masih ada
    document.body.classList.remove('modal-open');

    // Hapus backdrop yang masih tertinggal
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(el => el.remove());

    // Pulihkan scroll halaman
    document.body.style.overflow = 'auto';
  });
</script>

</body>
</html>
