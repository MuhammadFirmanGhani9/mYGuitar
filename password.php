<?php
include 'koneksi.php';
session_start();

$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vendor = $_POST['vendor'];
    $password = $_POST['password'];

    // Cek apakah vendor ada di tabel produk
    $query_produk = "SELECT * FROM produk WHERE vendor = '$vendor'";
    $hasil_produk = mysqli_query($koneksi, $query_produk);

    if (mysqli_num_rows($hasil_produk) > 0) {
        // Cek apakah username dan password cocok di tabel daftar
        $query_daftar = "SELECT * FROM daftar WHERE username = '$vendor' AND password = '$password'";
        $hasil_daftar = mysqli_query($koneksi, $query_daftar);

        if (mysqli_num_rows($hasil_daftar) > 0) {
            // Login berhasil
            $_SESSION['vendor'] = $vendor;
            header("Location: edit_produk.php");
            exit();
        } else {
            $pesan_error = "Password salah.";
        }
    } else {
        $pesan_error = "Vendor tidak ditemukan di tabel produk.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>mYGuitar - Login Vendor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-black font-sans">
    <div class="flex items-center justify-center min-h-screen bg-green-100">
        <div class="bg-white shadow-xl rounded-xl p-8 w-full max-w-md border border-green-700">
            <div class="flex flex-col items-center mb-6">
                <img src="logo.png" alt="Logo mYGuitar" class="h-16 mb-2">
                <h1 class="text-2xl font-bold text-green-700">mYGuitar</h1>
                <p class="text-gray-600">Login untuk edit produk</p>
            </div>
            <?php if ($pesan_error): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    <?= $pesan_error ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="vendor" class="block text-sm font-medium">Nama Vendor</label>
                    <input type="text" id="vendor" name="vendor" required class="mt-1 w-full px-3 py-2 border border-green-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium">Password</label>
                    <input type="password" id="password" name="password" required class="mt-1 w-full px-3 py-2 border border-green-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="flex justify-between items-center mt-6">
                    <a href="vendor.php" class="text-green-700 hover:underline">← Kembali</a>
                    <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
