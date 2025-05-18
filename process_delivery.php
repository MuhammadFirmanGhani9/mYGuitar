<?php
// Pastikan tidak ada output sebelum tag pembuka PHP
header('Content-Type: application/json');

// Mulai session setelah set header
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php';

try {
    // Validasi session vendor
    if (!isset($_SESSION['vendor'])) {
        throw new Exception('Session vendor tidak ditemukan');
    }

    // Validasi method POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode request tidak valid');
    }

    // Validasi action
    if (!isset($_POST['action']) || $_POST['action'] !== 'kirim_barang') {
        throw new Exception('Action tidak valid');
    }

    // Validasi input
    $required = ['order_id', 'merk', 'harga'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Data $field tidak valid");
        }
    }

    $order_id = (int)$_POST['order_id'];
    $merk = mysqli_real_escape_string($koneksi, $_POST['merk']);
    $harga = (float)$_POST['harga'];

    // Simpan ke database
    $query = "INSERT INTO dipesan (merk, harga) VALUES ('$merk', $harga)";
    if (!mysqli_query($koneksi, $query)) {
        throw new Exception('Gagal menyimpan data: ' . mysqli_error($koneksi));
    }

    // Contoh query untuk memperbarui status
$query = "UPDATE keranjang SET status = 'dikirim' WHERE id = '$order_id'";
mysqli_query($koneksi, $query);

    echo json_encode([
        'success' => true,
        'message' => 'Barang berhasil dikirim'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    mysqli_close($koneksi);
}
?>