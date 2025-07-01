<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Menu Utama</div>
                <a class="nav-link" href="../admin/dashboard.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>

                <div class="sb-sidenav-menu-heading">Layanan</div>
                <a class="nav-link" href="../admin/layanan-pengaduan-admin.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-comments"></i></div>
                    Layanan Pengaduan
                </a>
                <a class="nav-link" href="../admin/laporan-bencana-admin.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    Laporan Bencana
                </a>
                <a class="nav-link" href="../admin/kesehatan-admin.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Jadwal Kesehatan
                </a>

                <div class="sb-sidenav-menu-heading">Data Penduduk</div>
                <a class="nav-link" href="../admin/tambah-penduduk-admin.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                    Tambah Penduduk
                </a>
                <a class="nav-link" href="../admin/pengajuan-surat-admin.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                    Surat Pengantar
                </a>
                <a class="nav-link" href="../admin/jadwal-kegiatan-admin.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Jadwal Kegiatan
                </a>

                <div class="sb-sidenav-menu-heading">Layanan Berita Desa</div>
                <a class="nav-link" href="../admin/review-berita.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Review Berita
                </a>
                <div class="sb-sidenav-menu-heading">Log aktifitas</div>
                <a class="nav-link" href="../admin/log-user.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Log user
                </a>
            </div>
        </div>
        <div class="sb-sidenav-footer">
        <div class="small">Login sebagai:</div>
            <?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin Desa') ?>
        </div>

    </nav>
</div>
