<?php
// File untuk memeriksa status login user berdasarkan session atau cookie

session_start();

function isLoggedIn() {
    // Periksa apakah user sudah login melalui session
    if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        return true;
    }
    
    // Jika tidak ada session, periksa cookie "Ingat saya"
    if(isset($_COOKIE['remember_token']) && isset($_COOKIE['user_id'])) {
        $token = $_COOKIE['remember_token'];
        $user_id = $_COOKIE['user_id'];
        
        // Koneksi ke database
        $host = 'localhost';
        $db = 'mygitar';
        $user = 'root';
        $pass = '';
        
        try {
            $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Cek token di database
            $stmt = $conn->prepare("SELECT * FROM daftar WHERE id = :id AND remember_token = :token LIMIT 1");
            $stmt->bindParam(':id', $user_id);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                // Token valid, buat session baru
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                return true;
            } else {
                // Token tidak valid, hapus cookie
                setcookie('remember_token', '', time() - 3600, '/');
                setcookie('user_id', '', time() - 3600, '/');
                return false;
            }
        } catch(PDOException $e) {
            // Error koneksi database
            error_log("Database Error (Check Login): " . $e->getMessage());
            return false;
        }
    }
    
    return false;
}

// Jika perlu mengalihkan ke halaman login saat tidak masuk
function requireLogin() {
    if(!isLoggedIn()) {
        header('Location: signin.html');
        exit;
    }
}

// Contoh penggunaan:
// 1. Sertakan file ini di halaman yang memerlukan autentikasi
// require_once 'check_login.php';
// 
// 2. Panggil fungsi requireLogin() di awal halaman
// requireLogin();
?>