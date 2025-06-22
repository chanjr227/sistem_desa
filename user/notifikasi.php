<?php
session_start();
require '../config/config.php';

$stmt = $pdo->prepare("SELECT * FROM notifikasi WHERE user_id = :uid ORDER BY tanggal DESC");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$notifikasi = $stmt->fetchAll();
$pdo->prepare("UPDATE notifikasi SET status_baca = 'sudah' WHERE user_id = :uid")->execute([':uid' => $_SESSION['user_id']]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Notifikasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
    <h3 class="mb-4">Notifikasi Anda</h3>
    <ul class="list-group">
        <?php if (empty($notifikasi)): ?>
            <li class="list-group-item">Tidak ada notifikasi.</li>
        <?php else: ?>
            <?php foreach ($notifikasi as $n): ?>
                <li class="list-group-item">
                    <?= htmlspecialchars($n['pesan']) ?><br>
                    <small class="text-muted"><?= $n['tanggal'] ?></small>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>
