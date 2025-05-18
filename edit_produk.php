<?php
// Memastikan koneksi ke database
require_once 'koneksi.php';

// Inisialisasi variabel
$id = "";
$merk = "";
$jenis_gitar = "";
$harga = "";
$stok = "";
$gambar_base64 = "";
$pesan = "";
$pesan_error = "";
$is_authenticated = false;

// Memulai session untuk menyimpan status login
session_start();

// Cek apakah user sudah login
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    $is_authenticated = true;
} else {
    // Proses login jika form login di-submit
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        // Validasi input
        if (empty($username) || empty($password)) {
            $pesan_error = "Username dan password harus diisi!";
        } else {
            // Cek username dan password di database
            $query = "SELECT * FROM daftar WHERE username = ?";
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Verifikasi apakah username terdaftar sebagai vendor di tabel produk
                $vendor_query = "SELECT COUNT(*) as vendor_count FROM produk WHERE vendor = ?";
                $vendor_stmt = $koneksi->prepare($vendor_query);
                $vendor_stmt->bind_param("s", $username);
                $vendor_stmt->execute();
                $vendor_result = $vendor_stmt->get_result();
                $vendor_data = $vendor_result->fetch_assoc();
                $vendor_stmt->close();
                
                // Jika username tidak terdaftar sebagai vendor
                if ($vendor_data['vendor_count'] <= 0) {
                    $pesan_error = "Akun Anda tidak terdaftar sebagai vendor!";
                } else {
                    // Verifikasi password
                    if (password_verify($password, $user['password'])) {
                        // Password dalam bentuk hash
                        $_SESSION['authenticated'] = true;
                        $_SESSION['username'] = $username;
                        $is_authenticated = true;
                    } elseif ($password === $user['password']) {
                        // Fallback untuk password plaintext
                        $_SESSION['authenticated'] = true;
                        $_SESSION['username'] = $username;
                        $is_authenticated = true;
                    } else {
                        $pesan_error = "Password salah!";
                    }
                }
            } else {
                $pesan_error = "Username tidak ditemukan!";
            }
            
            $stmt->close();
            
            // Redirect ke vendor.php jika login gagal
            if (!$is_authenticated && empty($pesan_error)) {
                header("Location: vendor.php");
                exit;
            }
        }
    } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Jika ada request POST tapi bukan form login, redirect ke vendor.php
        if (!isset($_POST['login']) && !isset($_SESSION['authenticated'])) {
            header("Location: vendor.php");
            exit;
        }
    }
}

// Proses form ketika di-submit (hanya jika user sudah login)
if ($is_authenticated) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cari'])) {
        // Validasi ID
        $id = trim($_POST['id']);
        
        if (empty($id)) {
            $pesan_error = "ID produk harus diisi!";
        } elseif (!is_numeric($id)) {
            $pesan_error = "ID produk harus berupa angka!";
        } else {
            // Ambil data produk berdasarkan ID
            $query = "SELECT * FROM produk WHERE id = ?";
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
                $merk = htmlspecialchars($data['merk']);
                $jenis_gitar = htmlspecialchars($data['jenis_gitar']);
                $harga = htmlspecialchars($data['harga']);
                $stok = htmlspecialchars($data['stok']);
                $gambar_base64 = $data['data_gambar'];
            } else {
                $pesan_error = "Produk dengan ID tersebut tidak ditemukan!";
            }
            
            $stmt->close();
        }
    }

    // Proses form update ketika di-submit
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan'])) {
        // Validasi dan sanitasi input
        $id = trim($_POST['id']);
        $merk = trim($_POST['merk']);
        $jenis_gitar = trim($_POST['jenis_gitar']);
        $harga = trim($_POST['harga']);
        $stok = trim($_POST['stok']);
        
        // Validasi field wajib diisi
        if (empty($id) || empty($merk) || empty($jenis_gitar) || empty($harga) || empty($stok)) {
            $pesan_error = "Semua field wajib diisi!";
        }
        
        // Validasi ID, harga dan stok harus numerik
        if (!is_numeric($id)) {
            $pesan_error = "ID produk harus berupa angka!";
        }
        
        if (!is_numeric($harga)) {
            $pesan_error = "Harga harus berupa angka!";
        }
        
        if (!is_numeric($stok)) {
            $pesan_error = "Stok harus berupa angka!";
        }
        
        // Gambar tetap menggunakan yang lama secara default
        $gambar_baru = $gambar_base64;
        
        // Cek apakah ada file gambar yang diupload
        if (!empty($_FILES['data_gambar']['name'])) {
            // Validasi file gambar
            $allowed_types = array("image/jpeg", "image/jpg", "image/png", "image/gif");
            $file_type = $_FILES["data_gambar"]["type"];
            
            // Cek apakah format file sesuai
            if (!in_array($file_type, $allowed_types)) {
                $pesan_error = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
            }
            // Cek ukuran file (maksimal 2 MB)
            else if ($_FILES["data_gambar"]["size"] > 2000000) {
                $pesan_error = "Maaf, ukuran file terlalu besar (maksimal 2MB).";
            }
            // Jika tidak ada error, konversi ke base64
            else {
                $image_data = file_get_contents($_FILES["data_gambar"]["tmp_name"]);
                $gambar_baru = 'data:' . $file_type . ';base64,' . base64_encode($image_data);
            }
        }
        
        // Update data produk ke database jika tidak ada error
        if (empty($pesan_error)) {
            // Cek apakah produk dengan ID tersebut ada
            $check_query = "SELECT id FROM produk WHERE id = ?";
            $check_stmt = $koneksi->prepare($check_query);
            $check_stmt->bind_param("i", $id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                // Produk ada, lakukan update
                $query = "UPDATE produk SET merk = ?, jenis_gitar = ?, harga = ?, stok = ?, data_gambar = ? WHERE id = ?";
                $stmt = $koneksi->prepare($query);
                $stmt->bind_param("ssdisi", $merk, $jenis_gitar, $harga, $stok, $gambar_baru, $id);
                
                if ($stmt->execute()) {
                    $pesan = "Data produk berhasil diperbarui!";
                    // Update variabel gambar_base64 agar tampilan diperbarui
                    $gambar_base64 = $gambar_baru;
                } else {
                    $pesan_error = "Gagal memperbarui data: " . $stmt->error;
                }
                
                $stmt->close();
            } else {
                $pesan_error = "Produk dengan ID tersebut tidak ditemukan!";
            }
            
            $check_stmt->close();
        }
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: vendor.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - mYGuitar</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#00a86b', // Green
                        secondary: '#333333', // Dark gray/almost black
                        light: '#ffffff', // White
                    }
                }
            }
        }
    </script>
    <style>
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <div class="bg-light shadow-lg rounded-lg overflow-hidden border border-secondary">
            <div class="bg-secondary p-4">
                <h1 class="text-2xl font-bold text-light text-center">Edit Produk</h1>
            </div>
            
            <?php if (!empty($pesan)): ?>
            <div class="bg-green-100 border-l-4 border-primary text-secondary p-4 mb-4 mx-4 mt-4">
                <p><?php echo htmlspecialchars($pesan); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($pesan_error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 mx-4 mt-4">
                <p><?php echo htmlspecialchars($pesan_error); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!$is_authenticated): ?>
            <!-- Form Login -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="p-6">
                <div class="mb-6">
                    <label for="username" class="block text-secondary text-sm font-bold mb-2">Username:</label>
                    <input type="text" id="username" name="username" required autocomplete="off"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-secondary leading-tight focus:outline-none focus:shadow-outline focus:border-primary transition-all">
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-secondary text-sm font-bold mb-2">Password:</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-secondary leading-tight focus:outline-none focus:shadow-outline focus:border-primary transition-all">
                        <button type="button" id="togglePassword" class="absolute right-3 top-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between mt-8">
                    <button type="submit" name="login" class="bg-primary hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-all">
                        Login
                    </button>
                    <a href="vendor.php" class="bg-secondary hover:bg-gray-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-all">
                        Kembali
                    </a>
                </div>
                
                <script>
                    // Toggle password visibility
                    document.getElementById('togglePassword').addEventListener('click', function() {
                        const passwordField = document.getElementById('password');
                        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordField.setAttribute('type', type);
                        
                        // Change the eye icon
                        if (type === 'text') {
                            this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>`;
                        } else {
                            this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>`;
                        }
                    });
                </script>
            </form>
            <?php elseif ($is_authenticated && empty($merk) && empty($pesan_error)): ?>
            <!-- Form untuk memasukkan ID produk -->
            <div class="flex justify-end p-4">
                <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?logout=1" class="text-primary hover:text-green-800 font-medium">
                    Logout (<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>)
                </a>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="p-6">
               <div class="mb-6">
                <label for="id" class="block text-secondary text-sm font-bold mb-2">ID Produk:</label>
                <input type="number" id="id" name="id" required autocomplete="off"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-secondary leading-tight focus:outline-none focus:shadow-outline focus:border-primary transition-all">
                 </div>

                
                <div class="flex items-center justify-center mt-8">
                    <button type="submit" name="cari" class="bg-primary hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-all">
                        Cari Produk
                    </button>
                </div>
            </form>
            <?php elseif ($is_authenticated): ?>
            <!-- Form untuk mengedit produk -->
            <div class="flex justify-end p-4">
                <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?logout=1" class="text-primary hover:text-green-800 font-medium">
                    Logout (<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>)
                </a>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="p-6">
                <div class="mb-6">
                    <label for="id" class="block text-secondary text-sm font-bold mb-2">ID Produk:</label>
                    <input type="number" id="id" name="id" value="<?php echo htmlspecialchars($id); ?>" required readonly
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-secondary leading-tight bg-gray-100 focus:outline-none focus:shadow-outline focus:border-primary transition-all">
                </div>
                
                <div class="mb-6">
                    <label for="merk" class="block text-secondary text-sm font-bold mb-2">Merk:</label>
                    <input type="text" id="merk" name="merk" value="<?php echo htmlspecialchars($merk); ?>" required 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-secondary leading-tight focus:outline-none focus:shadow-outline focus:border-primary transition-all">
                </div>
                
                <div class="mb-6">
                    <label for="jenis_gitar" class="block text-secondary text-sm font-bold mb-2">Jenis Gitar:</label>
                    <select id="jenis_gitar" name="jenis_gitar" required 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-secondary leading-tight focus:outline-none focus:shadow-outline focus:border-primary transition-all">
                        <option value="">-- Pilih Jenis Gitar --</option>
                        <option value="akustik" <?php echo ($jenis_gitar == 'akustik') ? 'selected' : ''; ?>>Akustik</option>
                        <option value="elektrik" <?php echo ($jenis_gitar == 'elektrik') ? 'selected' : ''; ?>>Elektrik</option>
                        <option value="transkustik" <?php echo ($jenis_gitar == 'transkustik') ? 'selected' : ''; ?>>Transkustik</option>
                        <option value="ukulele" <?php echo ($jenis_gitar == 'ukulele') ? 'selected' : ''; ?>>Ukulele</option>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label for="harga" class="block text-secondary text-sm font-bold mb-2">Harga (Rp):</label>
                    <div class="relative">
                        <input type="number" id="harga" name="harga" value="<?php echo htmlspecialchars($harga); ?>" required 
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-secondary leading-tight focus:outline-none focus:shadow-outline focus:border-primary transition-all">
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="stok" class="block text-secondary text-sm font-bold mb-2">Stok:</label>
                    <input type="number" id="stok" name="stok" value="<?php echo htmlspecialchars($stok); ?>" required 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-secondary leading-tight focus:outline-none focus:shadow-outline focus:border-primary transition-all">
                </div>
                
                <div class="mb-6">
                    <label for="data_gambar" class="block text-gray-700 text-sm font-bold mb-2">Gambar Produk:</label>
                    <div class="mt-1 flex items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="data_gambar" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Upload file</span>
                                    <input id="data_gambar" name="data_gambar" type="file" class="sr-only">
                                </label>
                                <p class="pl-1">atau seret dan lepas</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, GIF hingga 2MB
                            </p>
                        </div>
                    </div>
                    
                    <?php if (!empty($gambar_base64)): ?>
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Gambar saat ini:</p>
                        <div class="border rounded-lg overflow-hidden">
                            <?php if (strpos($gambar_base64, 'data:image') === 0): ?>
                                <!-- Gambar sudah dalam format base64 -->
                                <img src="<?php echo htmlspecialchars($gambar_base64); ?>" alt="Gambar Produk" class="w-full max-h-56 object-contain">
                            <?php else: ?>
                                <!-- Gambar lama perlu dikonversi dari nama file ke base64 -->
                                <?php
                                $img_path = "uploads/" . $gambar_base64;
                                if (file_exists($img_path)) {
                                    $type = pathinfo($img_path, PATHINFO_EXTENSION);
                                    $img_data = file_get_contents($img_path);
                                    $base64_img = 'data:image/' . $type . ';base64,' . base64_encode($img_data);
                                    echo '<img src="' . htmlspecialchars($base64_img) . '" alt="Gambar Produk" class="w-full max-h-56 object-contain">';
                                } else {
                                    echo '<div class="bg-gray-200 h-40 flex items-center justify-center">
                                            <p class="text-gray-500">Gambar tidak tersedia</p>
                                          </div>';
                                }
                                ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="flex items-center justify-between mt-8">
                    <button type="submit" name="simpan" class="bg-primary hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-all">
                        Simpan Perubahan
                    </button>
                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="bg-secondary hover:bg-gray-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-all">
                        Cari Produk Lain
                    </a>
                    <a href="vendor.php" class="bg-secondary hover:bg-gray-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-all">
                        Kembali
                    </a>
                </div>
            </form>
            <?php endif; ?>
        </div>
        
        <div class="mt-6 text-center text-secondary text-sm">
            &copy; <?php echo date('Y'); ?> mYGuitar. All rights reserved.
        </div>
    </div>
</body>
</html>