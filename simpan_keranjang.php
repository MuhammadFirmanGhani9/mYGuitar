<?php
header('Content-Type: application/json');

// Sertakan file koneksi database
include 'koneksi.php';

// Ambil data yang dikirim dari JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Validasi data yang diterima
if (!isset($data['id'], $data['merk'], $data['harga'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

// Bersihkan data
$product_id = intval($data['id']);
$merk = mysqli_real_escape_string($koneksi, $data['merk']);
$harga = floatval($data['harga']);
$vendor = isset($data['vendor']) ? mysqli_real_escape_string($koneksi, $data['vendor']) : 'mYGuitar';
$konfirmasi = isset($data['konfirmasi']) ? mysqli_real_escape_string($koneksi, $data['konfirmasi']) : 'belum';

// Query untuk menyimpan ke keranjang
$query = "INSERT INTO keranjang (product_id, merk, harga, vendor, konfirmasi) 
          VALUES ('$product_id', '$merk', '$harga', '$vendor', '$konfirmasi')";

if (mysqli_query($koneksi, $query)) {
    echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan ke keranjang']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan produk: ' . mysqli_error($koneksi)]);
}

// Tutup koneksi
mysqli_close($koneksi);
?>