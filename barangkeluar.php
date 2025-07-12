<?php
require 'function.php';
require 'cek.php';

// ✅ Tambah barang keluar (khusus gudang)
if (isset($_POST['addbarangkeluar'])) {
    if ($_SESSION['role'] != 'gudang') {
        echo "<script>alert('❌ Akses tambah hanya untuk gudang'); window.location.href='barangkeluar.php';</script>";
        exit();
    }

    $idbarang = $_POST['barang'];
    $jumlah = $_POST['quantity'];

    $cekstok = mysqli_query($conn, "SELECT stock FROM barang WHERE idbarang='$idbarang'");
    $data = mysqli_fetch_assoc($cekstok);
    $stoksekarang = $data['stock'];

    if ($jumlah > $stoksekarang) {
        echo "<script>alert('❌ Jumlah keluar melebihi stok yang tersedia!'); window.location.href='barangkeluar.php';</script>";
        exit();
    } else {
        mysqli_query($conn, "INSERT INTO barangkeluar (idbarang, quantity) VALUES ('$idbarang', '$jumlah')");
        mysqli_query($conn, "UPDATE barang SET stock = stock - $jumlah WHERE idbarang = '$idbarang'");
        echo "<script>alert('✅ Barang keluar berhasil ditambahkan'); window.location.href='barangkeluar.php';</script>";
    }
}

// ✅ Hapus barang keluar (khusus gudang)
if (isset($_POST['hapusbarangkeluar'])) {
    if ($_SESSION['role'] != 'gudang') {
        echo "<script>alert('❌ Akses hapus hanya untuk gudang'); window.location.href='barangkeluar.php';</script>";
        exit();
    }

    $idkeluar = $_POST['idkeluar'];
    $cek = mysqli_query($conn, "SELECT * FROM barangkeluar WHERE idkeluar='$idkeluar'");
    $data = mysqli_fetch_array($cek);
    $idbarang = $data['idbarang'];
    $jumlah = $data['quantity'];

    mysqli_query($conn, "UPDATE barang SET stock = stock + $jumlah WHERE idbarang = '$idbarang'");
    mysqli_query($conn, "DELETE FROM barangkeluar WHERE idkeluar='$idkeluar'");

    echo "<script>alert('Data berhasil dihapus'); window.location.href='barangkeluar.php';</script>";
}

// ✅ Edit barang keluar (khusus gudang)
if (isset($_POST['editbarangkeluar'])) {
    if ($_SESSION['role'] != 'gudang') {
        echo "<script>alert('❌ Akses edit hanya untuk gudang'); window.location.href='barangkeluar.php';</script>";
        exit();
    }

    $idkeluar = $_POST['idkeluar'];
    $quantityBaru = $_POST['quantity'];

    $ambil = mysqli_query($conn, "SELECT * FROM barangkeluar WHERE idkeluar='$idkeluar'");
    $data = mysqli_fetch_array($ambil);
    $quantityLama = $data['quantity'];
    $idbarang = $data['idbarang'];

    $selisih = $quantityBaru - $quantityLama;

    $cekstok = mysqli_query($conn, "SELECT stock FROM barang WHERE idbarang='$idbarang'");
    $stokdata = mysqli_fetch_assoc($cekstok);
    $stoksekarang = $stokdata['stock'];

    if ($selisih > $stoksekarang) {
        echo "<script>alert('❌ Jumlah edit melebihi stok tersedia!'); window.location.href='barangkeluar.php';</script>";
        exit();
    } else {
        mysqli_query($conn, "UPDATE barangkeluar SET quantity='$quantityBaru' WHERE idkeluar='$idkeluar'");
        mysqli_query($conn, "UPDATE barang SET stock = stock - $selisih WHERE idbarang = '$idbarang'");
        echo "<script>alert('✅ Data berhasil diupdate'); window.location.href='barangkeluar.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Barang Keluar</title>
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
                <h1 class="mt-4">Barang Keluar</h1>

                <div class="card mb-4">
                    <div class="card-header">
                        <?php if ($_SESSION['role'] == 'gudang') { ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#keluarModal">Tambah Barang</button>
                        <?php } ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Harga</th>
                                        <th>Jumlah Keluar</th>
                                        <th>Tanggal</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ambil = mysqli_query($conn, "SELECT bk.idkeluar, bk.idbarang, b.namabarang, b.harga, bk.quantity, bk.tanggal FROM barangkeluar bk JOIN barang b ON bk.idbarang = b.idbarang ORDER BY bk.tanggal DESC");
                                    while ($data = mysqli_fetch_array($ambil)) {
                                    ?>
                                    <tr>
                                        <td><?= $data['idbarang']; ?></td>
                                        <td><?= $data['namabarang']; ?></td>
                                        <td>Rp<?= number_format($data['harga']); ?></td>
                                        <td><?= $data['quantity']; ?></td>
                                        <td><?= $data['tanggal']; ?></td>
                                        <td>
                                            <?php if ($_SESSION['role'] == 'gudang') { ?>
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $data['idkeluar']; ?>">Edit</button>
                                                <form method="post" style="display:inline;" onsubmit="return confirm('Yakin ingin hapus data ini?');">
                                                    <input type="hidden" name="idkeluar" value="<?= $data['idkeluar']; ?>">
                                                    <button type="submit" name="hapusbarangkeluar" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            <?php } else { ?>
                                                <span class="text-muted">Hanya lihat</span>
                                            <?php } ?>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="editModal<?= $data['idkeluar']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="post">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Barang Keluar</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="idkeluar" value="<?= $data['idkeluar']; ?>">
                                                        <label>Jumlah</label>
                                                        <input type="number" name="quantity" class="form-control" value="<?= $data['quantity']; ?>" required>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="editbarangkeluar" class="btn btn-primary">Simpan Perubahan</button>
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
            </div>
        </main>
    </div>
</div>

<!-- Modal Tambah Barang Keluar -->
<?php if ($_SESSION['role'] == 'gudang') { ?>
<div class="modal fade" id="keluarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Barang Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select name="barang" class="form-control" required>
                        <option value="">Pilih Barang</option>
                        <?php
                        $ambilsemua = mysqli_query($conn, "SELECT * FROM barang");
                        while ($b = mysqli_fetch_array($ambilsemua)) {
                            echo '<option value="' . $b['idbarang'] . '">' . $b['namabarang'] . '</option>';
                        }
                        ?>
                    </select>
                    <br>
                    <input type="number" name="quantity" class="form-control" placeholder="Jumlah" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="addbarangkeluar" class="btn btn-primary">Simpan</button>
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
