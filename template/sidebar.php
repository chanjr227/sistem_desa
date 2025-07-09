<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <!-- DASHBOARD -->
                <div class="sb-sidenav-menu-heading">Halaman Utama</div>
                <a class="nav-link" href="dashboard.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard Admin
                </a>

                <!-- PENGADUAN & BENCANA -->
                <div class="sb-sidenav-menu-heading">Menu Pengaduan dan Laporan Bencana</div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePengaduan" aria-expanded="false">
                    <div class="sb-nav-link-icon"><i class="fas fa-exclamation-circle"></i></div>
                    Menu Pengaduan
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePengaduan" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="layanan-pengaduan-admin.php">Layanan Pengaduan</a>
                        <a class="nav-link" href="laporan-bencana-admin.php">Laporan Bencana</a>
                    </nav>
                </div>

                <!-- KESEHATAN -->
                <a class="nav-link" href="kesehatan-admin.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-heartbeat"></i></div>
                    Layanan Kesehatan
                </a>

                <!-- PENDUDUK & SURAT -->
                <div class="sb-sidenav-menu-heading">Penduduk dan Surat</div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePenduduk" aria-expanded="false">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Penduduk & Surat
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePenduduk" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="tambah-penduduk-admin.php">Tambah Penduduk</a>
                        <a class="nav-link" href="pengajuan-surat-admin.php">Surat Pengantar</a>
                        <a class="nav-link" href="jadwal-kegiatan-admin.php">Jadwal Kegiatan</a>
                    </nav>
                </div>

                <!-- BERITA & LOG -->
                <div class="sb-sidenav-menu-heading">Berita & Log</div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseBerita" aria-expanded="false">
                    <div class="sb-nav-link-icon"><i class="fas fa-newspaper"></i></div>
                    Berita Desa
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseBerita" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="tambah-berita.php">Tambah Berita</a>
                        <a class="nav-link" href="review-berita.php">Review Berita</a>
                    </nav>
                </div>

                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLog" aria-expanded="false">
                    <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
                    Log & Pengaturan
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseLog" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="log-user.php">Log User</a>
                        <a class="nav-link" href="tambah_karyawan.php">Tambah Karyawan</a>
                        <a class="nav-link" href="pengaturan-akun-staff.php">Pengaturan Akun</a>
                    </nav>
                </div>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Login sebagai:</div>
            <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin Desa') ?>
        </div>
    </nav>
</div>