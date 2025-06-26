<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM notifikasi WHERE user_id = :uid ORDER BY tanggal DESC");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$notifikasi = $stmt->fetchAll();

// Tandai semua sebagai sudah dibaca
$pdo->prepare("UPDATE notifikasi SET status_baca = 'sudah' WHERE user_id = :uid")->execute([':uid' => $_SESSION['user_id']]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Notifikasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .unread {
            background-color: #e9f7ef;
        }
    </style>
</head>
<body class="bg-light p-4">
<div class="container">
    <h3 class="mb-4">🔔 Notifikasi Anda</h3>
    <div class="list-group">
        <?php if (empty($notifikasi)): ?>
            <div class="alert alert-info">Tidak ada notifikasi untuk Anda saat ini.</div>
        <?php else: ?>
            <?php foreach ($notifikasi as $n): ?>
                <div class="list-group-item <?= $n['status_baca'] === 'belum' ? 'unread' : '' ?>">
                    <div class="d-flex justify-content-between">
                        <div><?= htmlspecialchars($n['pesan']) ?></div>
                        <small class="text-muted"><?= date('d-m-Y H:i', strtotime($n['tanggal'])) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <a href="../index.php" class="btn btn-secondary mt-4">← Kembali ke Dashboard</a>
</div>
</body>
</html>
