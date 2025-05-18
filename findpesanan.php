<?php
// Include koneksi ke database
include 'koneksi.php';

// Inisialisasi variabel pesan error
$error = "";

// Proses form jika tombol submit ditekan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil input dari form
    $vendor = $_POST['vendor'];
    $password = $_POST['password'];
    
    // Validasi input tidak kosong
    if (empty($vendor) || empty($password)) {
        $error = "Nama vendor dan password tidak boleh kosong!";
    } else {
        // Query untuk memeriksa vendor di tabel daftar
        $query_daftar = "SELECT * FROM daftar WHERE username = ?";
        $stmt = mysqli_prepare($koneksi, $query_daftar);
        mysqli_stmt_bind_param($stmt, "s", $vendor);
        mysqli_stmt_execute($stmt);
        $result_daftar = mysqli_stmt_get_result($stmt);
        
        // Jika vendor ditemukan di tabel daftar
        if (mysqli_num_rows($result_daftar) > 0) {
            $row = mysqli_fetch_assoc($result_daftar);
            
            // Verifikasi password
            if (password_verify($password, $row['password'])) {
                // Query untuk memeriksa vendor di tabel keranjang
                $query_keranjang = "SELECT * FROM keranjang WHERE vendor = ?";
                $stmt_keranjang = mysqli_prepare($koneksi, $query_keranjang);
                mysqli_stmt_bind_param($stmt_keranjang, "s", $vendor);
                mysqli_stmt_execute($stmt_keranjang);
                $result_keranjang = mysqli_stmt_get_result($stmt_keranjang);
                
                // Jika vendor memiliki pesanan
                if (mysqli_num_rows($result_keranjang) > 0) {
                    // Simpan nama vendor dalam session
                    session_start();
                    $_SESSION['vendor'] = $vendor;
                    
                    // Redirect ke halaman pesanan.php
                    header("Location: pesanan.php");
                    exit();
                } else {
                    $error = "Vendor tidak memiliki pesanan!";
                }
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Nama vendor tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Pesanan - mYGuitar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .guitar-pattern {
            background-color: #f8fafc;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23e5e7eb' fill-opacity='0.2'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="guitar-pattern min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header dengan logo dan judul -->
        <div class="flex flex-col items-center mb-8">
            <a href="index.php" class="flex items-center mb-4 transform hover:scale-105 transition-transform">
                <img src="logo.png" alt="mYGuitar Logo" class="h-12 mr-3">
                <h1 class="text-4xl font-bold text-green-700">mYGuitar</h1>
            </a>
            <div class="text-center bg-white px-6 py-3 rounded-lg shadow-sm">
                <h2 class="text-2xl font-semibold text-gray-800">lihat Pesanan</h2>
                <p class="text-lg text-gray-600 mt-1">Masukkan nama vendor dan password yang sesuai</p>
            </div>
        </div>

        <!-- Card untuk form -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden mb-6 max-w-md mx-auto border border-gray-200">
            <!-- Header card -->
            <div class="bg-gradient-to-r from-green-700 to-green-800 px-6 py-4">
                <h3 class="text-xl font-semibold text-white text-center">
                    <i class="fas fa-search mr-2"></i>Verifikasi Vendor
                </h3>
            </div>

            <!-- Form content -->
            <div class="p-6">
                <?php if (!empty($error)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span><?php echo $error; ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="mb-4">
                        <label for="vendor" class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-user-tie mr-2 text-green-700"></i>Nama Vendor
                        </label>
                        <input type="text" id="vendor" name="vendor" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                               placeholder="Masukkan nama vendor" 
                               value="<?php echo isset($_POST['vendor']) ? htmlspecialchars($_POST['vendor']) : ''; ?>">
                    </div>
                    
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-lock mr-2 text-green-700"></i>Password
                        </label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                               placeholder="Masukkan password">
                    </div>
                    
                    <div class="flex justify-center">
                        <button type="submit" 
                                class="bg-green-700 hover:bg-green-800 text-white font-medium py-3 px-8 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-md transform hover:scale-105">
                            <i class="fas fa-search mr-2"></i>Pantau Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tombol Kembali ke Vendor -->
        <div class="flex justify-center mt-4">
            <a href="vendor.php" class="inline-flex items-center px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Kembali ke Vendor
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-6">
        <div class="container mx-auto px-4 text-center">
            <div class="flex justify-center mb-4 space-x-6">
                <a href="https://www.facebook.com/profile.php?id=100055798496173" class="social-icon">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://wa.me/6281261017232" class="social-icon">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="https://www.instagram.com/firghan.png/?hl=en" class="social-icon">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.youtube.com/@arrival4852" class="social-icon">
                    <i class="fab fa-youtube"></i>
                </a>
            </div>
            <p class="text-sm">&copy; <?php echo date('Y'); ?> mYGuitar. All rights reserved.</p>
            <p class="text-xs text-gray-500 mt-2">Sistem Manajemen Pesanan Guitar Terbaik</p>
        </div>
    </footer>
</body>
</html>