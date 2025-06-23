<?php
function simpan_log($koneksi, $userid, $nama, $aktivitas) {
    $stmt = $koneksi->prepare("INSERT INTO user_log (userid, nama, aktivitas) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userid, $nama, $aktivitas);
    $stmt->execute();
}
