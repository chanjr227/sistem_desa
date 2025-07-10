<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$success = '';
$error = '';
$filter = $_GET['filter'] ?? 'staff_rt';
$edit = false;
$edit_data = null;

// Edit mode
if (isset($_GET['edit_id'])) {
    $id = intval($_GET['edit_id']);
    $result = $koneksi->query("SELECT * FROM users WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
        $edit = true;
    }
}

// Tambah atau Update Akun
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $id = $_POST['edit_id'] ?? null;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email tidak valid.";
    } elseif (!$id && strlen($_POST['password']) < 6) {
        $error = "Password minimal 6 karakter.";
    } else {
        if ($id) {
            // Update akun
            $stmt = $koneksi->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $email, $role, $id);
            if ($stmt->execute()) {
                $success = "Akun berhasil diupdate.";
                header("Location: pengaturan-akun.php?filter=$filter");
                exit;
            } else {
                $error = "Gagal mengupdate akun.";
            }
        } else {
            // Tambah akun
            $password = $_POST['password'];
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
}

// Hapus akun
if (isset($_GET['hapus'])) {
    $hapus_id = intval($_GET['hapus']);
    $koneksi->query("DELETE FROM users WHERE id = $hapus_id");
    header("Location: pengaturan-akun.php?filter=$filter");
    exit;
}
?>

<?php include '../template/header.php'; ?>
<?php include '../template/navbar.php'; ?>

<div id="layoutSidenav">
    <?php include '../template/sidebar.php'; ?>
    <div id="layoutSidenav_content">
        <main class="container-fluid px-4 py-4">
            <h2 class="mb-4">👥 Pengaturan Akun</h2>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <!-- Form Tambah / Edit Akun -->
            <form method="POST" class="row g-3 mb-4">
                <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?? '' ?>">
                <div class="col-md-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($edit_data['email'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <?php if (!$edit): ?>
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    <?php else: ?>
                        <label class="form-label text-muted">Password</label>
                        <input type="password" class="form-control" placeholder="(tidak diubah)" disabled>
                    <?php endif; ?>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="staff_desa" <?= ($edit_data['role'] ?? '') == 'staff_desa' ? 'selected' : '' ?>>Staff Desa</option>
                        <option value="rt" <?= ($edit_data['role'] ?? '') == 'rt' ? 'selected' : '' ?>>RT</option>
                        <option value="user" <?= ($edit_data['role'] ?? '') == 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= ($edit_data['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-<?= $edit ? 'success' : 'primary' ?> w-100">
                        <?= $edit ? '💾 Update' : '+ Tambah' ?>
                    </button>
                </div>
            </form>

            <!-- Filter Role -->
            <form method="GET" class="mb-3">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="filter" class="col-form-label">Filter Role:</label>
                    </div>
                    <div class="col-auto">
                        <select name="filter" id="filter" class="form-select" onchange="this.form.submit()">
                            <option value="staff_rt" <?= $filter == 'staff_rt' ? 'selected' : '' ?>>Staff & RT</option>
                            <option value="admin" <?= $filter == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="user" <?= $filter == 'user' ? 'selected' : '' ?>>User</option>
                            <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Semua</option>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Tabel Akun -->
            <div class="card mb-4">
                <div class="card-header bg-light">📋 Daftar Akun</div>
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
                            if ($filter === 'staff_rt') {
                                $sql = "SELECT * FROM users WHERE role IN ('staff_desa', 'rt') ORDER BY created_at DESC";
                            } elseif ($filter === 'all') {
                                $sql = "SELECT * FROM users ORDER BY created_at DESC";
                            } else {
                                $sql = "SELECT * FROM users WHERE role = '$filter' ORDER BY created_at DESC";
                            }

                            $data = $koneksi->query($sql);
                            while ($row = $data->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= match ($row['role']) {
                                                                    'admin' => 'danger',
                                                                    'user' => 'secondary',
                                                                    'rt' => 'success',
                                                                    default => 'info'
                                                                } ?>">
                                            <?= ucfirst(str_replace('_', ' ', $row['role'])) ?>
                                        </span>
                                    </td>
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