<?php
$host = "localhost";       // Ganti jika bukan localhost
$user = "root";            // Ganti sesuai username MySQL
$pass = "";                // Ganti sesuai password MySQL
$db   = "mygitar";         // Nama database

// Membuat koneksi
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
