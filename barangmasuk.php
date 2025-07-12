<?php
require 'function.php';
require 'cek.php';

// Tambah barang masuk
if (isset($_POST['barangmasuk'])) {
    if ($_SESSION['role'] != 'gudang') {
        echo "<script>alert('❌ Akses tambah hanya untuk gudang'); window.location.href='barangmasuk.php';</script>";
        exit();
    }

    $idbarang = $_POST['barang'];
    $jumlah = $_POST['quantity'];
    $keterangan = $_POST['keterangan'];

    // Tambah ke tabel barangmasuk
    $masuk = mysqli_query($conn, "INSERT INTO barangmasuk (idbarang, quantity, keterangan) VALUES ('$idbarang', '$jumlah', '$keterangan')");

    // Update stok barang
    $updateStok = mysqli_query($conn, "UPDATE barang SET stock = stock + $jumlah WHERE idbarang='$idbarang'");

    if ($masuk && $updateStok) {
        echo "<script>alert('✅ Barang masuk berhasil ditambahkan'); window.location.href='barangmasuk.php';</script>";
    } else {
        echo "<script>alert('❌ Gagal menambahkan barang masuk');</script>";
    }
}

// Edit barang masuk
if (isset($_POST['editbarangmasuk'])) {
    if ($_SESSION['role'] != 'gudang') {
        echo "<script>alert('❌ Akses edit hanya untuk gudang'); window.location.href='barangmasuk.php';</script>";
        exit();
    }

    $idmasuk = $_POST['idmasuk'];
    $quantityBaru = $_POST['quantity'];
    $keterangan = $_POST['keterangan'];

    $ambil = mysqli_query($conn, "SELECT * FROM barangmasuk WHERE idmasuk='$idmasuk'");
    $data = mysqli_fetch_array($ambil);
    $quantityLama = $data['quantity'];
    $idbarang = $data['idbarang'];

    $selisih = $quantityBaru - $quantityLama;

    $updateStok = mysqli_query($conn, "UPDATE barang SET stock = stock + $selisih WHERE idbarang = '$idbarang'");
    $updateMasuk = mysqli_query($conn, "UPDATE barangmasuk SET quantity='$quantityBaru', keterangan='$keterangan' WHERE idmasuk='$idmasuk'");

    if ($updateStok && $updateMasuk) {
        echo "<script>alert('✅ Data berhasil diupdate'); window.location.href='barangmasuk.php';</script>";
    } else {
        echo "<script>alert('❌ Gagal mengupdate data');</script>";
    }
}

// Hapus barang masuk
if (isset($_POST['hapusbarangmasuk'])) {
    if ($_SESSION['role'] != 'gudang') {
        echo "<script>alert('❌ Akses hapus hanya untuk gudang'); window.location.href='barangmasuk.php';</script>";
        exit();
    }

    $idmasuk = $_POST['idmasuk'];
    $ambil = mysqli_query($conn, "SELECT * FROM barangmasuk WHERE idmasuk='$idmasuk'");
    $data = mysqli_fetch_array($ambil);
    $jumlah = $data['quantity'];
    $idbarang = $data['idbarang'];

    // Kembalikan stok
    $updateStok = mysqli_query($conn, "UPDATE barang SET stock = stock - $jumlah WHERE idbarang = '$idbarang'");
    $hapus = mysqli_query($conn, "DELETE FROM barangmasuk WHERE idmasuk='$idmasuk'");

    if ($hapus && $updateStok) {
        echo "<script>alert('Data berhasil dihapus'); window.location.href='barangmasuk.php';</script>";
    } else {
        echo "<script>alert('❌ Gagal menghapus data');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Barang Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">Sinar Jaya</a>
    <button class="btn btn-link btn-sm" id="sidebarToggle"><i class="fas fa-bars"></i></button>
</nav>

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <a class="nav-link" href="index.php"><div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>Dashboard</a>
                    <a class="nav-link" href="barangmasuk.php"><div class="sb-nav-link-icon"><i class="fas fa-arrow-circle-down"></i></div>Barang Masuk</a>
                    <a class="nav-link" href="barangkeluar.php"><div class="sb-nav-link-icon"><i class="fas fa-arrow-circle-up"></i></div>Barang Keluar</a>
                    <a class="nav-link" href="logout.php"><div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>Logout</a>
                </div>
            </div>
        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Barang Masuk</h1>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <?php if ($_SESSION['role'] == 'gudang') { ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">Tambah Barang</button>
                        <?php } ?>
                    </div>

                    <div class="card-body">
                        <table id="datatablesSimple" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID Barang</th>
                                    <th>Nama</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                    <th>Tanggal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $ambil = mysqli_query($conn, "SELECT bm.idmasuk, bm.idbarang, b.namabarang, b.harga, bm.quantity, bm.keterangan, bm.tanggal 
                                                          FROM barangmasuk bm 
                                                          JOIN barang b ON bm.idbarang = b.idbarang 
                                                          ORDER BY bm.tanggal DESC");
                            while ($data = mysqli_fetch_array($ambil)) {
                            ?>
                                <tr>
                                    <td><?= $data['idbarang']; ?></td>
                                    <td><?= $data['namabarang']; ?></td>
                                    <td>Rp<?= number_format($data['harga']); ?></td>
                                    <td><?= $data['quantity']; ?></td>
                                    <td><?= $data['keterangan']; ?></td>
                                    <td><?= $data['tanggal']; ?></td>
                                    <td>
                                        <?php if ($_SESSION['role'] == 'gudang') { ?>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $data['idmasuk']; ?>">Edit</button>
                                        <form method="post" style="display:inline;" onsubmit="return confirm('Yakin ingin hapus data ini?');">
                                            <input type="hidden" name="idmasuk" value="<?= $data['idmasuk']; ?>">
                                            <button type="submit" name="hapusbarangmasuk" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                        <?php } else { ?>
                                            <span class="text-muted">Hanya lihat</span>
                                        <?php } ?>
                                    </td>
                                </tr>

                                <!-- Modal Edit -->
                                <div class="modal fade" id="editModal<?= $data['idmasuk']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Barang Masuk</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="idmasuk" value="<?= $data['idmasuk']; ?>">
                                                    <label>Jumlah</label>
                                                    <input type="number" name="quantity" class="form-control" value="<?= $data['quantity']; ?>" required><br>
                                                    <label>Keterangan</label>
                                                    <input type="text" name="keterangan" class="form-control" value="<?= $data['keterangan']; ?>" required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="editbarangmasuk" class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Tambah Barang Masuk -->
<?php if ($_SESSION['role'] == 'gudang') { ?>
<div class="modal fade" id="myModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Barang Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select name="barang" class="form-control" required>
                        <option value="">Pilih Barang</option>
                        <?php
                        $barang = mysqli_query($conn, "SELECT * FROM barang");
                        while ($b = mysqli_fetch_array($barang)) {
                            echo "<option value='{$b['idbarang']}'>{$b['namabarang']}</option>";
                        }
                        ?>
                    </select><br>
                    <input type="number" name="quantity" class="form-control" placeholder="Jumlah" required><br>
                    <input type="text" name="keterangan" class="form-control" placeholder="Keterangan" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="barangmasuk" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
<script>
    const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {
        perPageSelect: false
    });
</script>
</body>
</html>
