<?php
require 'function.php';
require 'cek.php';

// Edit barang hanya untuk gudang
if (isset($_POST['editbarang'])) {
    if ($_SESSION['role'] != 'gudang') {
        echo "<script>alert('❌ Akses edit hanya untuk gudang'); window.location.href='index.php';</script>";
        exit();
    }

    $idbarang = $_POST['idbarang'];
    $namabarang = $_POST['namabarang'];
    $harga = $_POST['harga'];
    $stock = $_POST['stock'];

    $update = mysqli_query($conn, "UPDATE barang SET namabarang='$namabarang', harga='$harga', stock='$stock' WHERE idbarang='$idbarang'");
    if ($update) {
        echo "<script>alert('✅ Data berhasil diupdate'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal mengedit barang');</script>";
    }
}

// Hapus barang hanya untuk gudang
if (isset($_POST['hapusbarang'])) {
    if ($_SESSION['role'] != 'gudang') {
        echo "<script>alert('❌ Akses hapus hanya untuk gudang'); window.location.href='index.php';</script>";
        exit();
    }

    $id = $_POST['idbarang'];
    $delete = mysqli_query($conn, "DELETE FROM barang WHERE idbarang='$id'");
    if ($delete) {
        echo "<script>window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard</title>
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
                 <!--card supplier  -->
                 <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="supplier.php" class="text-decoration-none">
                                <div class="card bg-warning text-white shadow-sm">
                                    <div class="card-body fw-semibold">
                                        Supplier
                                    </div>
                                    <div class="card-footer d-flex justify-content-between align-items-center text-white">
                                        <span class="fw-semibold" href="supplier.php">View Details </span>
                                        <i class="fas fa-angle-right"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>


                <h1 class="mt-4">Stok Barang</h1>
                <?php if ($_SESSION['role'] == 'gudang') { ?>
                <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#myModal">
                    Tambah Barang
                </button>
                <?php } ?>
                <div class="card mb-4">
                    <div class="card-header"><i class="fas fa-table me-2"></i>Data Barang</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ambilsemuadatastock = mysqli_query($conn, "SELECT * FROM barang");
                                    while ($data = mysqli_fetch_array($ambilsemuadatastock)) {
                                    ?>
                                    <tr>
                                        <td><?= $data['idbarang']; ?></td>
                                        <td><?= $data['namabarang']; ?></td>
                                        <td><?= $data['harga']; ?></td>
                                        <td><?= $data['stock']; ?></td>
                                        <td>
                                            <?php if ($_SESSION['role'] == 'gudang') { ?>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $data['idbarang']; ?>">Edit</button>
                                            <form method="post" onsubmit="return confirm('Yakin ingin menghapus data ini?');" style="display:inline;">
                                                <input type="hidden" name="idbarang" value="<?= $data['idbarang']; ?>">
                                                <button type="submit" name="hapusbarang" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                            <?php } else { ?>
                                                <span class="text-muted">Hanya lihat</span>
                                            <?php } ?>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="editModal<?= $data['idbarang']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="post">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Barang</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="idbarang" value="<?= $data['idbarang']; ?>">
                                                        <label>Nama Barang</label>
                                                        <input type="text" name="namabarang" class="form-control" value="<?= $data['namabarang']; ?>" required><br>
                                                        <label>Harga</label>
                                                        <input type="number" name="harga" class="form-control" value="<?= $data['harga']; ?>" required><br>
                                                        <label>Stok</label>
                                                        <input type="number" name="stock" class="form-control" value="<?= $data['stock']; ?>" required>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="editbarang" class="btn btn-primary">Simpan Perubahan</button>
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

                <!-- Modal Tambah Barang -->
                <?php if ($_SESSION['role'] == 'gudang') { ?>
                <div class="modal fade" id="myModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="post">
                                <div class="modal-header">
                                    <h4 class="modal-title">Tambah Barang</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="text" name="namabarang" placeholder="Nama Barang" class="form-control" required><br>
                                    <input type="number" name="harga" placeholder="Harga" class="form-control" required><br>
                                    <input type="number" name="stock" placeholder="Stock" class="form-control" required><br>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary" name="addnewbarang">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } ?>

            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script>
    const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {
        perPageSelect: false
    });
</script>
</body>
</html>
