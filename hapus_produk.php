<?php
// Mulai session untuk keamanan (jika sistem login diimplementasikan)
session_start();

// Include file koneksi database
include 'koneksi.php';

// Cek apakah parameter ID tersedia di URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    // Langkah 1: Validasi ID dan cek keberadaan produk
    $check_query = "SELECT * FROM produk WHERE id = $id";
    $check_result = mysqli_query($koneksi, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $product = mysqli_fetch_assoc($check_result);
        
        // Langkah 2: Untuk keamanan, pastikan vendor yang mencoba menghapus adalah pemilik produk
        // Jika menggunakan sistem login, bisa ditambahkan cek session vendor
        // if($_SESSION['username'] != $product['vendor']) {
        //     $_SESSION['error_message'] = "Anda tidak berhak menghapus produk ini!";
        //     header("Location: vendor.php");
        //     exit();
        // }
        
        // Langkah 3: Eksekusi query hapus data
        $delete_query = "DELETE FROM produk WHERE id = $id";
        $delete_result = mysqli_query($koneksi, $delete_query);
        
        if ($delete_result) {
            // Jika berhasil dihapus, tampilkan pesan sukses
            $_SESSION['success_message'] = "Produk berhasil dihapus!";
            header("Location: vendor.php");
            exit();
        } else {
            // Jika gagal, tampilkan pesan error
            $_SESSION['error_message'] = "Gagal menghapus produk: " . mysqli_error($koneksi);
            header("Location: vendor.php");
            exit();
        }
    } else {
        // Jika produk tidak ditemukan
        $_SESSION['error_message'] = "Produk dengan ID tersebut tidak ditemukan!";
        header("Location: vendor.php");
        exit();
    }
} else {
    // Jika parameter ID tidak valid
    $_SESSION['error_message'] = "Parameter ID tidak valid!";
    header("Location: vendor.php");
    exit();
}

// Tutup koneksi database
mysqli_close($koneksi);
?>