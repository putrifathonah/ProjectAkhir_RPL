<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// koneksi ke database 
// 4 parameter =  host(xampp/komputer kita), username db, pw, nama db
$conn = mysqli_connect("localhost","root","","stockbarang");

// menambah barang baru 
// Handle Tambah Barang
if (isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $harga = $_POST['harga'];
    $stock = $_POST['stock'];

    // Ambil ID terakhir
    $getLastId = mysqli_query($conn, "SELECT idbarang FROM barang ORDER BY idbarang DESC LIMIT 1");
    $data = mysqli_fetch_array($getLastId);

    if ($data) {
        $lastIdNum = intval(substr($data['idbarang'], 1));
        $nextIdNum = $lastIdNum + 1;
        $newId = 'B' . str_pad($nextIdNum, 3, '0', STR_PAD_LEFT);
    } else {
        $newId = 'B001';
    }

    $addtotable = mysqli_query($conn, "INSERT INTO barang (idbarang, namabarang, harga, stock) 
                                       VALUES ('$newId', '$namabarang', '$harga', '$stock')");

    if ($addtotable) {
        header('location:index.php');
        exit();
    } else {
        echo "<script>alert('Gagal menambahkan barang!');</script>";
    }
}

// menambah barang masuk 
if (isset($_POST['barangmasuk'])) {
    $barang = $_POST['barang']; 
    $keterangan = $_POST['keterangan']; 
    $quantity = $_POST['quantity'];

    // Ambil stok sekarang dari tabel 'barang'
    $cekstocksekarang = mysqli_query($conn, "SELECT * FROM barang WHERE idbarang = '$barang'");
    $ambildata = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildata['stock']; 
    $stokbaru = $stocksekarang + $quantity; 

    // Insert ke tabel barangmasuk
    $addtomasuk = mysqli_query($conn, "INSERT INTO barangmasuk (idbarang, keterangan, quantity) VALUES('$barang','$keterangan','$quantity')");
    
    // Update stok di tabel barang
    $updatestok = mysqli_query($conn, "UPDATE barang SET stock = '$stokbaru' WHERE idbarang='$barang'");

    if ($addtomasuk && $updatestok) {
        header('Location: barangmasuk.php');
    } else {
        echo "<script>alert('Gagal menambahkan data'); window.location.href='barangmasuk.php';</script>";
    }
}

// ✅ Menambah barang keluar dengan validasi stok dan perlindungan dari input ganda
if (isset($_POST['addbarangkeluar'])) {
    $barang = $_POST['barang'];
    $quantity = $_POST['quantity'];

    // Ambil stok sekarang dari tabel 'barang'
    $cekstocksekarang = mysqli_query($conn, "SELECT stock FROM barang WHERE idbarang = '$barang'");
    $ambildata = mysqli_fetch_assoc($cekstocksekarang);

    if (!$ambildata) {
        echo "<script>alert('Barang tidak ditemukan'); window.location.href='barangkeluar.php';</script>";
        exit();
    }

    $stocksekarang = $ambildata['stock'];

    // Validasi jika jumlah yang keluar melebihi stok
    if ($quantity > $stocksekarang) {
        echo "<script>alert('❌ Jumlah barang keluar melebihi stok yang tersedia! Stok saat ini: $stocksekarang'); window.location.href='barangkeluar.php';</script>";
        exit();
    }

    // Mulai transaksi database (opsional, untuk keamanan)
    mysqli_begin_transaction($conn);

    $insertSuccess = mysqli_query($conn, "INSERT INTO barangkeluar (idbarang, quantity) VALUES('$barang', '$quantity')");
    $updateSuccess = mysqli_query($conn, "UPDATE barang SET stock = stock - $quantity WHERE idbarang = '$barang'");

    if ($insertSuccess && $updateSuccess) {
        mysqli_commit($conn);
        echo "<script>alert('✅ Barang keluar berhasil ditambahkan'); window.location.href='barangkeluar.php';</script>";
        exit(); // ⛔ penting agar mencegah proses ganda
    } else {
        mysqli_rollback($conn);
        echo "<script>alert('❌ Gagal menambahkan barang keluar'); window.location.href='barangkeluar.php';</script>";
        exit();
    }
}

// supplier 
// menambah supplier
if (isset($_POST['addnewsupplier'])) {
  $kode = $_POST['kodesupplier'];
  $perusahaan = $_POST['namaperusahaan'];
  $notelp = $_POST['notelp'];
  $alamat = $_POST['alamat'];

  $insert = mysqli_query($conn, "INSERT INTO supplier (Kode, Perusahaan, No_Telp, Alamat) 
                                 VALUES ('$kode', '$perusahaan', '$notelp', '$alamat')");
  
  if ($insert) {
    echo "<script>window.location.href='supplier.php';</script>";
  } else {
    echo "<script>alert('Gagal menambah data!');</script>";
  }
}

?>