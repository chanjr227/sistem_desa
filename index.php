<?php
session_start();
require 'config/config.php';

$nama_user = $_SESSION['nama'] ?? 'Warga';

$result = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penduduk");
$row = mysqli_fetch_assoc($result);
$jumlah_penduduk = $row['total'];

$jadwal_kegiatan_umum = mysqli_query($koneksi, "SELECT * FROM jadwal_kegiatan ORDER BY tanggal DESC LIMIT 3");
$jadwal_posyandu = mysqli_query($koneksi, "SELECT * FROM jadwal_posyandu ORDER BY tanggal DESC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center">
        <a class="navbar-brand" href="#">Sistem Informasi Desa</a>
        <div class="d-flex flex-wrap align-items-center">
            <span class="navbar-text text-white me-3">Halo, <?= htmlspecialchars($nama_user) ?></span>
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

    <div class="row g-4">
        <div class="col-12 col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Profil Desa</h5>
                    <p class="card-text">Desa Rajeg, Kecamatan Harapan, Kabupaten Maju Jaya, Provinsi Jawa Sejahtera.</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Jumlah Penduduk</h5>
                    <p class="card-text"><?= $jumlah_penduduk ?> jiwa</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-primary card-menu-animate">
                <div class="card-body">
                    <h5 class="card-title">Laporan Bencana</h5>
                    <p class="card-text">Laporkan kejadian bencana alam yang Anda saksikan.</p>
                    <a href="user/laporan-bencana.php" class="btn btn-primary">Laporkan Sekarang</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-success card-menu-animate">
                <div class="card-body">
                    <h5 class="card-title">Pengajuan Surat</h5>
                    <p class="card-text">Ajukan surat pengantar KTP, KK, dan lainnya secara online.</p>
                    <a href="user/menu-pengajuan-surat.php" class="btn btn-success">Ajukan Surat</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-info card-menu-animate">
                <div class="card-body">
                    <h5 class="card-title">Kesehatan Desa</h5>
                    <p class="card-text">Lihat jadwal posyandu, warga rentan, dan imunisasi desa Anda.</p>
                    <a href="user/kesehatan.php" class="btn btn-info text-white">Lihat Kesehatan</a>
                </div>
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

   <div class="tree">
    <ul>
        <li>
            <a href="#" class="struktur-clickable"
               data-nama="King Yanyan"
               data-foto=""
               data-quote="Saya siap membangun desa dengan hati dan integritas.">
               Kepala Desa<br><small>King Yanyan</small>
            </a>
            <ul>
                <li>
                    <a href="#" class="struktur-clickable"
                       data-nama="King Rojak"
                       data-foto=""
                       data-quote="Administrasi adalah fondasi pemerintahan yang tertib.">
                       Sekretaris Desa<br><small>King Rojak</small>
                    </a>
                    <ul>
                        <li>
                            <a href="#" class="struktur-clickable"
                               data-nama="King Yanyan"
                               data-foto=""
                               data-quote="Setiap rupiah harus bisa dipertanggungjawabkan.">
                               Bendahara<br><small>King Yanyan</small>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="struktur-clickable"
                               data-nama="King Rojak"
                               data-foto=""
                               data-quote="Pelayanan cepat, warga puas.">
                               Kaur Umum<br><small>King Rojak</small>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="struktur-clickable"
                       data-nama="King Yanyan"
                       data-foto=""
                       data-quote="Saya dekat dengan warga Dusun 1.">
                       Kepala Dusun 1<br><small>King Yanyan</small>
                    </a>
                    <ul>
                        <li>
                            <a href="#" class="struktur-clickable"
                               data-nama="King Rojak"
                               data-foto=""
                               data-quote="RT adalah ujung tombak desa.">
                               RT 01<br><small>King Rojak</small>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="struktur-clickable"
                               data-nama="King Yanyan"
                               data-foto=""
                               data-quote="Bersama wargaku, membangun lingkungan.">
                               RT 02<br><small>King Yanyan</small>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="struktur-clickable"
                       data-nama="King Yanyan"
                       data-foto=""
                       data-quote="Saya mendengar dan melayani Dusun 2.">
                       Kepala Dusun 2<br><small>King Yanyan</small>
                    </a>
                    <ul>
                        <li>
                            <a href="#" class="struktur-clickable"
                               data-nama="King Rojak"
                               data-foto=""
                               data-quote="Kami jaga gotong royong warga.">
                               RT 03<br><small>King Rojak</small>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="struktur-clickable"
                               data-nama="King Yanyan"
                               data-foto=""
                               data-quote="RT 04 siap jadi teladan bagi desa.">
                               RT 04<br><small>King Yanyan</small>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>
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
        <small>&copy; <?= date('Y') ?> Sistem Informasi Desa - Dibuat oleh Admin Desa</small>
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

</body>
</html>
