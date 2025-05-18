<?php
// Memulai sesi untuk menghapusnya
session_start();

// Koneksi ke database untuk menghapus remember_token jika ada
if(isset($_SESSION['user_id'])) {
    $host = 'localhost';
    $db = 'mygitar';
    $user = 'root';
    $pass = '';
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Hapus remember_token dari database
        $stmt = $conn->prepare("UPDATE daftar SET remember_token = NULL WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
    } catch(PDOException $e) {
        // Catat error tapi lanjutkan proses logout
        error_log("Logout Error: " . $e->getMessage());
    }
}

// Hapus semua data session
$_SESSION = array();

// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hapus sesi
session_destroy();

// Hapus cookie remember_token jika ada
if(isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}
if(isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, '/');
}

// Alihkan ke halaman login
header('Location: signin.html');
exit;
?>