<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    $query = "DELETE FROM keranjang WHERE id = $id";
    
    if (mysqli_query($koneksi, $query)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($koneksi);
    }
} else {
    echo "error: Invalid request";
}

mysqli_close($koneksi);
?>