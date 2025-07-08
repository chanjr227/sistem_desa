<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$success = '';
$error = '';

// Tambah Akun
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email tidak valid.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } else {
        $cek = $koneksi->prepare("SELECT * FROM users WHERE email = ?");
        $cek->bind_param("s", $email);
        $cek->execute();
        $result = $cek->get_result();

        if ($result->num_rows > 0) {
            $error = "Email sudah terdaftar.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $koneksi->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hash, $role);
            if ($stmt->execute()) {
                $success = "Akun berhasil ditambahkan.";
            } else {
                $error = "Gagal menambahkan akun.";
            }
            $stmt->close();
        }
    }
}
?>

<?php include '../template/header.php'; ?>
<?php include '../template/navbar.php'; ?>
<div id="layoutSidenav">
    <?php include '../template/sidebar.php'; ?>
    <div id="layoutSidenav_content">
        <main class="container-fluid px-4 py-4">
            <h2 class="mb-4">👤 Pengaturan Akun Staff & RT</h2>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label>Role</label>
                    <select name="role" class="form-select" required>
                        <option value="staff_desa">Staff Desa</option>
                        <option value="rt">RT</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100">+ Tambah</button>
                </div>
            </form>

            <!-- Daftar Staff & RT -->
            <div class="card mb-4">
                <div class="card-header bg-light">📋 Daftar Akun Staff & RT</div>
                <div class="table-responsive p-3">
                    <table class="table table-bordered table-hover text-center">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Tanggal Buat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $data = $koneksi->query("SELECT * FROM users WHERE role IN ('staff_desa', 'rt') ORDER BY created_at DESC");
                            while ($row = $data->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><span class="badge bg-<?= $row['role'] === 'staff_desa' ? 'info' : 'success' ?>">
                                            <?= $row['role'] === 'staff_desa' ? 'Staff Desa' : 'RT' ?>
                                        </span></td>
                                    <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
        <?php include '../template/footer.php'; ?>
    </div>
</div>