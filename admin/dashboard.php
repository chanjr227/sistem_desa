<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$struktur = $koneksi->query("SELECT id, nama, jabatan, nip, jenis_kelamin, tanggal_lahir, alamat, foto FROM karyawan ORDER BY id");


require_once '../config/config.php'; // pastikan koneksi di-include

// Lanjutkan dengan query data
$pengaduanPerBulan = array_fill(1, 12, 0);

$query = $koneksi->query("SELECT MONTH(tanggal_pengaduan) AS bulan, COUNT(*) AS total FROM pengaduan GROUP BY bulan");
while ($row = $query->fetch_assoc()) {
    $pengaduanPerBulan[(int)$row['bulan']] = (int)$row['total'];
}

$jsonData = json_encode(array_values($pengaduanPerBulan));

// Query jumlah penduduk berdasarkan jenis kelamin
$jenisKelaminQuery = $koneksi->query("SELECT jenis_kelamin, COUNT(*) AS total FROM penduduk GROUP BY jenis_kelamin");

$dataJenisKelamin = [];
while ($row = $jenisKelaminQuery->fetch_assoc()) {
    $dataJenisKelamin[$row['jenis_kelamin']] = (int)$row['total'];
}

// Jika data kosong, isi default 0
$jkLaki = $dataJenisKelamin['Laki-laki'] ?? 0;
$jkPerempuan = $dataJenisKelamin['Perempuan'] ?? 0;

// Encode ke JSON untuk JavaScript
$jsonBarChart = json_encode([$jkLaki, $jkPerempuan]);


// Tambah anggota
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $foto = $_FILES['foto'];

    $namaFoto = '';
    if ($foto['name'] !== '') {
        $namaFoto = uniqid() . '-' . basename($foto['name']);
        move_uploaded_file($foto['tmp_name'], '../uploads/' . $namaFoto);
    }

    $stmt = $koneksi->prepare("INSERT INTO struktur_organisasi (nama, jabatan, foto) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $jabatan, $namaFoto);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

// Edit anggota
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $fotoBaru = $_FILES['foto'];

    if ($fotoBaru['name'] !== '') {
        $namaFoto = uniqid() . '-' . basename($fotoBaru['name']);
        move_uploaded_file($fotoBaru['tmp_name'], '../uploads/' . $namaFoto);
        $stmt = $koneksi->prepare("UPDATE struktur_organisasi SET nama=?, jabatan=?, foto=? WHERE id=?");
        $stmt->bind_param("sssi", $nama, $jabatan, $namaFoto, $id);
    } else {
        $stmt = $koneksi->prepare("UPDATE struktur_organisasi SET nama=?, jabatan=? WHERE id=?");
        $stmt->bind_param("ssi", $nama, $jabatan, $id);
    }
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

// Hapus anggota
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $koneksi->prepare("DELETE FROM struktur_organisasi WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.html">Desa Rajeg</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <!-- DASHBOARD -->
                        <div class="sb-sidenav-menu-heading">Halaman utama</div>
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
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseKesehatan" aria-expanded="false">
                            <div class="sb-nav-link-icon"><i class="fas fa-heartbeat"></i></div>
                            Menu Layanan Kesehatan
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseKesehatan" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="kesehatan-admin.php">Layanan Kesehatan</a>
                            </nav>
                        </div>

                        <!-- PENDUDUK & SURAT -->
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePenduduk" aria-expanded="false">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Penduduk dan Surat
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePenduduk" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPenduduk">
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePendudukSub" aria-expanded="false">
                                    Tambah Penduduk
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapsePendudukSub" data-bs-parent="#sidenavAccordionPenduduk">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="tambah-penduduk-admin.php">Tambah Penduduk</a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSuratSub" aria-expanded="false">
                                    Surat Pengantar & Jadwal Kegiatan
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapseSuratSub" data-bs-parent="#sidenavAccordionPenduduk">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="pengajuan-surat-admin.php">Surat Pengantar</a>
                                        <a class="nav-link" href="jadwal-kegiatan-admin.php">Jadwal Kegiatan</a>
                                    </nav>
                                </div>
                            </nav>
                        </div>

                        <!-- LOG & BERITA -->
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLog" aria-expanded="false">
                            <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
                            Log & Berita
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLog" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionLog">
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLogSub" aria-expanded="false">
                                    Menu Log
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapseLogSub" data-bs-parent="#sidenavAccordionLog">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="log-user.php">Menu Log User</a>
                                        <a class="nav-link" href="tambah_karyawan.php">Tambah karyawan</a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseBeritaSub" aria-expanded="false">
                                    Tambah & Review Berita
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapseBeritaSub" data-bs-parent="#sidenavAccordionLog">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="tambah-berita.php">Tambah Berita</a>
                                        <a class="nav-link" href="review-berita.php">Review Berita</a>
                                    </nav>
                                </div>
                            </nav>
                        </div>
                    </div>

                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Login sebagai:</div>
                    <?= htmlspecialchars($_SESSION['name'] ?? 'Admin Desa') ?>
                </div>

            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <!-- <li class="breadcrumb-item active">Dashboard</li> -->
                    </ol>
                    <div class="row">
                        <!-- Card: Laporan Pengaduan -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col me-2">
                                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                                Laporan Pengaduan
                                            </div>
                                            <a href="layanan-pengaduan-admin.php" class="text-decoration-none small">Lihat detail</a>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Layanan Kesehatan -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col me-2">
                                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                                Layanan Kesehatan
                                            </div>
                                            <a href="kesehatan-admin.php" class="text-decoration-none small">Lihat detail</a>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-heartbeat fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Tambah Penduduk -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col me-2">
                                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                                Tambah Penduduk
                                            </div>
                                            <a href="tambah-penduduk-admin.php" class="text-decoration-none small">Lihat detail</a>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-plus fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Surat Pengantar -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col me-2">
                                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                                Surat Pengantar
                                            </div>
                                            <a href="pengajuan-surat-admin.php" class="text-decoration-none small">Lihat detail</a>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-envelope-open-text fa-2x text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-area me-1"></i>
                                    Jumlah pengaduan
                                </div>
                                <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Jumlah penduduk
                                </div>
                                <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-sitemap me-1"></i>
                            Struktur Organisasi
                            <button class="btn btn-sm btn-success float-end" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah</button>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>NIP</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Tanggal Lahir</th>
                                        <th>Alamat</th>
                                        <th>Foto</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $struktur->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['nama']) ?></td>
                                            <td><?= htmlspecialchars($row['jabatan']) ?></td>
                                            <td><?= htmlspecialchars($row['nama']) ?></td>
                                            <td><?= htmlspecialchars($row['jabatan']) ?></td>
                                            <td><?= htmlspecialchars($row['nip']) ?></td>
                                            <td><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                                            <td><?= htmlspecialchars($row['tanggal_lahir']) ?></td>
                                            <td><?= htmlspecialchars($row['alamat']) ?></td>
                                            <td>
                                                <?php if ($row['foto']): ?>
                                                    <img src="../uploads/foto_karyawan htmlspecialchars($row['foto']) ?>" width="50" height="50" style="border-radius: 50%">
                                                <?php else: ?>
                                                    <span>Tidak ada</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>

            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Sistem desa Rajeg 2025</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <!-- <script src="../assets/demo/chart-area-demo.js"></script> -->
    <!-- <script src="../assets/demo/chart-bar-demo.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/datatables-simple-demo.js"></script>
    <script>
        const dataPengaduan = <?= $jsonData ?>;

        const areaCtx = document.getElementById("myAreaChart").getContext("2d");
        const myChart = new Chart(areaCtx, {
            type: "line",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
                datasets: [{
                    label: "Jumlah Pengaduan",
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    data: dataPengaduan
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <script>
        const dataJenisKelamin = <?= $jsonBarChart ?>;

        const barCtx = document.getElementById("myBarChart").getContext("2d");
        new Chart(barCtx, {
            type: "bar",
            data: {
                labels: ["Laki-laki", "Perempuan"],
                datasets: [{
                    label: "Jumlah Penduduk",
                    backgroundColor: ["#4e73df", "#e74a3b"],
                    borderColor: ["#4e73df", "#e74a3b"],
                    borderWidth: 1,
                    data: dataJenisKelamin
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <script>
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit-id').value = this.dataset.id;
                document.getElementById('edit-nama').value = this.dataset.nama;
                document.getElementById('edit-jabatan').value = this.dataset.jabatan;
                document.getElementById('edit-preview').src = '../uploads/' + this.dataset.foto;
                document.getElementById('edit-foto-lama').value = this.dataset.foto;
            });
        });
    </script>
</body>

</html>