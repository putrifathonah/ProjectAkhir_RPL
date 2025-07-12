<?php
require 'function.php';
require 'cek.php';

// Tambah barang (hanya untuk gudang)
if (isset($_POST['addnewbarang'])) {
  if ($_SESSION['role'] != 'gudang') {
      echo "<script>alert('❌ Akses tambah hanya untuk gudang'); window.location.href='index.php';</script>";
      exit();
  }

  $namabarang = $_POST['namabarang'];
  $harga = $_POST['harga'];
  $stock = $_POST['stock'];

  $insert = mysqli_query($conn, "INSERT INTO barang (namabarang, harga, stock) VALUES ('$namabarang', '$harga', '$stock')");
  if ($insert) {
      echo "<script>alert('✅ Barang berhasil ditambahkan'); window.location.href='index.php';</script>";
  } else {
      echo "<script>alert('❌ Gagal menambahkan barang');</script>";
  }
}

// Hapus data hanya boleh oleh admin
if (isset($_GET['hapus'])) {
  if ($_SESSION['role'] != 'admin') {
    echo "<script>alert('❌ Akses hapus hanya untuk admin'); window.location='supplier.php';</script>";
    exit();
  }

  $kode = $_GET['hapus'];
  $hapus = mysqli_query($conn, "DELETE FROM supplier WHERE Kode='$kode'");

  if ($hapus) {
    echo "<script>alert('Data supplier berhasil dihapus'); window.location='supplier.php';</script>";
  } else {
    echo "<script>alert('Gagal menghapus data');</script>";
  }
}

// Edit data hanya boleh oleh admin
if (isset($_POST['editsupplier'])) {
  if ($_SESSION['role'] != 'admin') {
    echo "<script>alert('❌ Akses edit hanya untuk admin'); window.location='supplier.php';</script>";
    exit();
  }

  $kode = $_POST['kodesupplier'];
  $namaperusahaan = $_POST['namaperusahaan'];
  $notelp = $_POST['notelp'];
  $alamat = $_POST['alamat'];

  $update = mysqli_query($conn, "UPDATE supplier SET Perusahaan='$namaperusahaan', No_Telp='$notelp', Alamat='$alamat' WHERE Kode='$kode'");

  if ($update) {
    echo "<script>alert('Data berhasil diupdate'); window.location='supplier.php';</script>";
  } else {
    echo "<script>alert('Gagal mengupdate data');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Supplier</title>
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
  <a class="navbar-brand ps-3" href="index.php">Sinar Jaya</a>
  <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
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
        <h1 class="mt-4">Data Supplier</h1>

        <?php if ($_SESSION['role'] == 'admin') { ?>
          <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#myModal">
            Tambah
          </button>
        <?php } ?>

        <div class="card mb-4">
          <div class="card-header">
            <i class="fas fa-table me-2"></i> Tabel Supplier
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="datatablesSimple" class="table table-bordered">
                <thead>
                  <tr>
                    <th>Kode</th>
                    <th>Perusahaan</th>
                    <th>No Telp</th>
                    <th>Alamat</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $datasupplier = mysqli_query($conn, "SELECT * FROM supplier");
                  while ($data = mysqli_fetch_array($datasupplier)) {
                    $kode = $data['Kode'];
                    $perusahaan = $data['Perusahaan'];
                    $notelp = $data['No_Telp'];
                    $alamat = $data['Alamat'];
                  ?>
                    <tr>
                      <td><?= $kode ?></td>
                      <td><?= $perusahaan ?></td>
                      <td><?= $notelp ?></td>
                      <td><?= $alamat ?></td>
                      <td>
                        <?php if ($_SESSION['role'] == 'admin') { ?>
                          <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $kode ?>">Edit</button>
                          <a href="supplier.php?hapus=<?= $kode ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Delete</a>
                        <?php } else { ?>
                          <span class="text-muted">Hanya lihat</span>
                        <?php } ?>
                      </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?= $kode ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $kode ?>" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel<?= $kode ?>">Edit Supplier</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <form method="post">
                            <div class="modal-body">
                              <input type="hidden" name="kodesupplier" value="<?= $kode ?>">
                              <label>Perusahaan</label>
                              <input type="text" name="namaperusahaan" value="<?= $perusahaan ?>" class="form-control" required><br>
                              <label>No Telp</label>
                              <input type="text" name="notelp" value="<?= $notelp ?>" class="form-control" required><br>
                              <label>Alamat</label>
                              <input type="text" name="alamat" value="<?= $alamat ?>" class="form-control" required><br>
                              <button type="submit" class="btn btn-primary" name="editsupplier">Simpan Perubahan</button>
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

<!-- Modal Tambah Supplier -->
<?php if ($_SESSION['role'] == 'admin') { ?>
  <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Tambah Supplier</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post"> 
          <div class="modal-body">
            <input type="text" name="kodesupplier" placeholder="Kode" class="form-control" required><br>
            <input type="text" name="namaperusahaan" placeholder="Perusahaan" class="form-control" required><br>
            <input type="text" name="notelp" placeholder="No Telp" class="form-control" required><br>
            <input type="text" name="alamat" placeholder="Alamat" class="form-control" required><br>
            <button type="submit" class="btn btn-primary" name="addnewsupplier">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php } ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<!-- menghilangkan entries per page -->
<script>
  const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {
    perPageSelect: false
  });
</script>
</body>
</html>
