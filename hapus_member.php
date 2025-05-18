<?php
include 'koneksi.php';

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    // Menggunakan prepared statement untuk mencegah SQL injection
    $query = "DELETE FROM daftar WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $result = mysqli_stmt_execute($stmt);
    
    if($result) {
        header("Location: member.php?pesan=Data member berhasil dihapus");
    } else {
        header("Location: member.php?pesan=Gagal menghapus data member: " . mysqli_error($koneksi));
    }
    
    mysqli_stmt_close($stmt);
} else {
    header("Location: member.php?pesan=ID tidak valid");
}

mysqli_close($koneksi);
?>