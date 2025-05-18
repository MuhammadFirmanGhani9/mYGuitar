<?php
// Mengaktifkan error reporting untuk debugging (matikan di produksi)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header untuk respon JSON
header('Content-Type: application/json');

// Cek apakah permintaan berjenis POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Metode tidak valid. Gunakan POST.'
    ]);
    exit;
}

// Mengambil data dari form
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

// Validasi basic
if (empty($username) || empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Semua field harus diisi!'
    ]);
    exit;
}

// Validasi email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Format email tidak valid!'
    ]);
    exit;
}

// Validasi password match
if ($password !== $confirm_password) {
    echo json_encode([
        'success' => false,
        'message' => 'Kata sandi dan konfirmasi kata sandi tidak cocok!'
    ]);
    exit;
}

// Koneksi database
$host = 'localhost'; // Ganti dengan host database Anda
$db_user = 'root';   // Ganti dengan username database Anda
$db_pass = '';       // Ganti dengan password database Anda
$db_name = 'mygitar'; // Ganti dengan nama database Anda

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Koneksi database gagal: ' . $conn->connect_error
    ]);
    exit;
}

// Cek apakah username sudah digunakan
$stmt = $conn->prepare("SELECT id FROM daftar WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Username sudah digunakan. Silakan pilih username lain.'
    ]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Cek apakah email sudah digunakan
$stmt = $conn->prepare("SELECT id FROM daftar WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Email sudah terdaftar. Silakan gunakan email lain.'
    ]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Hash password untuk keamanan
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Simpan data pengguna ke database
$stmt = $conn->prepare("INSERT INTO daftar (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sss", $username, $email, $hashed_password);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Pendaftaran berhasil! Anda akan dialihkan ke halaman login.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Pendaftaran gagal: ' . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>