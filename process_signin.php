<?php
// Mulai session di awal file
session_start();

// Periksa apakah request adalah POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Metode request tidak valid!'
    ]);
    exit;
}

// Mengambil data form
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Validasi input dasar
if(empty($username) || empty($password)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Harap isi semua field yang diperlukan!'
    ]);
    exit;
}

// Koneksi ke database
$host = 'localhost';  // Ganti dengan host database Anda
$db = 'mygitar';      // Nama database
$user = 'root';       // Username database
$pass = '';           // Password database

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Menyiapkan query untuk mencari user di tabel daftar
    $stmt = $conn->prepare("SELECT * FROM daftar WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Memeriksa password
        if(password_verify($password, $user['password'])) {
            // Login berhasil
            
            // Simpan informasi user di session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Jika "Ingat saya" dicentang, buat cookie
            if($remember) {
                // Cookie bertahan 30 hari (dalam detik)
                $expiry = time() + (30 * 24 * 60 * 60);
                
                // Buat token unik untuk cookie
                $token = bin2hex(random_bytes(32));
                
                // Simpan token di database untuk validasi nanti
                $stmt = $conn->prepare("UPDATE daftar SET remember_token = :token WHERE id = :id");
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':id', $user['id']);
                $stmt->execute();
                
                // Set cookie
                setcookie('remember_token', $token, $expiry, '/');
                setcookie('user_id', $user['id'], $expiry, '/');
            }
            
            // Berhasil login - kirim respon sukses sebelum redirect
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Login berhasil!',
                'redirect' => 'role.html'
            ]);
            exit;
            
        } else {
            // Password salah
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Username atau password salah!'
            ]);
            exit;
        }
    } else {
        // User tidak ditemukan
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Username atau password salah!'
        ]);
        exit;
    }
} catch(PDOException $e) {
    // Error koneksi atau query database
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi nanti.'
    ]);
    
    // Catat error (jangan tampilkan ke user untuk keamanan)
    error_log("Database Error: " . $e->getMessage());
    exit;
}
?> 