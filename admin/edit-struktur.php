<?php
session_start();
if (!isset($_SESSION['log']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require_once '../config/config.php';

// Tambah anggota baru
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
    header("Location: struktur-admin.php");
    exit;
}

// Proses edit
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
    header("Location: struktur-admin.php");
    exit;
}

// Hapus anggota
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $koneksi->prepare("DELETE FROM struktur_organisasi WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: struktur-admin.php");
    exit;
}

// Ambil semua data struktur
$data = $koneksi->query("SELECT * FROM struktur_organisasi");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Struktur Organisasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2 class="mb-4">Struktur Organisasi</h2>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Anggota</button>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Foto</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $data->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['jabatan']) ?></td>
                <td><img src="../uploads/<?= $row['foto'] ?>" width="60"></td>
                <td>
                    <button class="btn btn-sm btn-primary btn-edit" data-id="<?= $row['id'] ?>"
                            data-nama="<?= htmlspecialchars($row['nama']) ?>"
                            data-jabatan="<?= htmlspecialchars($row['jabatan']) ?>"
                            data-foto="<?= $row['foto'] ?>"
                            data-bs-toggle="modal" data-bs-target="#modalEdit">Edit</button>
                    <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus data ini?')" class="btn btn-sm btn-danger">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Anggota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="tambah" value="1">
        <div class="mb-3">
          <label>Nama</label>
          <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Jabatan</label>
          <input type="text" name="jabatan" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Foto</label>
          <input type="file" name="foto" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Anggota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="edit" value="1">
        <input type="hidden" name="id" id="edit-id">
        <div class="mb-3">
          <label>Nama</label>
          <input type="text" name="nama" id="edit-nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Jabatan</label>
          <input type="text" name="jabatan" id="edit-jabatan" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Foto Saat Ini</label><br>
          <img id="edit-preview" src="" width="60">
        </div>
        <div class="mb-3">
          <label>Ganti Foto (opsional)</label>
          <input type="file" name="foto" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('edit-id').value = this.dataset.id;
        document.getElementById('edit-nama').value = this.dataset.nama;
        document.getElementById('edit-jabatan').value = this.dataset.jabatan;
        document.getElementById('edit-preview').src = '../uploads/' + this.dataset.foto;
    });
});
</script>
</body>
</html>
