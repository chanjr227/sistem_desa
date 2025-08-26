<?php
session_start();
require '../config/config.php';
require '../helpers/auth_helpers.php';
check_access(['admin']);

$result = $koneksi->query("SELECT user_log.*, users.email FROM user_log JOIN users ON user_log.userid = users.userid ORDER BY waktu DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Log Aktivitas Pengguna</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h2 class="mb-3">Log Aktivitas Pengguna</h2>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Aktivitas</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['aktivitas']) ?></td>
                        <td><?= $row['waktu'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>