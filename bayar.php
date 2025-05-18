<?php
include 'koneksi.php';

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$total_harga = 0;
$error = '';
$success = '';

// Calculate total price from keranjang table
$sql = "SELECT SUM(harga) as total FROM keranjang WHERE bayar = 'belum'";
$result = $koneksi->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_harga = $row['total'];
}

// Process payment form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Initialize statement variable
    $stmt = null;
    
    try {
        // Validate and sanitize inputs
        $nama = isset($_POST['nama']) ? $koneksi->real_escape_string($_POST['nama']) : '';
        $telepon = isset($_POST['telepon']) ? $koneksi->real_escape_string($_POST['telepon']) : '';
        $email = isset($_POST['email']) ? $koneksi->real_escape_string($_POST['email']) : '';
        $alamat = isset($_POST['alamat']) ? $koneksi->real_escape_string($_POST['alamat']) : '';
        
        // Handle file upload
        $bukti_pembayaran = null;
        if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] === UPLOAD_ERR_OK) {
            // Validate file type and size
            $allowed_types = ['image/jpeg', 'image/png'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            if (in_array($_FILES['bukti_pembayaran']['type'], $allowed_types) && 
                $_FILES['bukti_pembayaran']['size'] <= $max_size) {
                $file = $_FILES['bukti_pembayaran'];
                $bukti_pembayaran = file_get_contents($file['tmp_name']);
            } else {
                throw new Exception("File harus berupa JPG/PNG dan maksimal 2MB");
            }
        } else {
            throw new Exception("Bukti pembayaran harus diupload");
        }
        
        // Start transaction
        $koneksi->begin_transaction();
        
        // Check if total_harga column exists
        $column_check = $koneksi->query("SHOW COLUMNS FROM bayar LIKE 'total_harga'");
        $has_total_harga = ($column_check && $column_check->num_rows > 0);
        
        // Check if alamat column exists
        $alamat_column_check = $koneksi->query("SHOW COLUMNS FROM bayar LIKE 'alamat'");
        $has_alamat = ($alamat_column_check && $alamat_column_check->num_rows > 0);
        
        // Insert payment data into database
        if ($has_total_harga && $has_alamat) {
            $stmt = $koneksi->prepare("INSERT INTO bayar (customer, telephone, email, alamat, bukti_pembayaran, total_harga) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssd", $nama, $telepon, $email, $alamat, $bukti_pembayaran, $total_harga);
        } elseif ($has_total_harga) {
            // If alamat column doesn't exist, insert without it
            $stmt = $koneksi->prepare("INSERT INTO bayar (customer, telephone, email, bukti_pembayaran, total_harga) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssd", $nama, $telepon, $email, $bukti_pembayaran, $total_harga);
        } elseif ($has_alamat) {
            // If total_harga column doesn't exist but alamat does
            $stmt = $koneksi->prepare("INSERT INTO bayar (customer, telephone, email, alamat, bukti_pembayaran) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nama, $telepon, $email, $alamat, $bukti_pembayaran);
        } else {
            // If neither column exists
            $stmt = $koneksi->prepare("INSERT INTO bayar (customer, telephone, email, bukti_pembayaran) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nama, $telepon, $email, $bukti_pembayaran);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan data pembayaran: " . $stmt->error);
        }
        
        // Update payment status in keranjang table
        $update_sql = "UPDATE keranjang SET bayar='sudah' WHERE bayar='belum'";
        if (!$koneksi->query($update_sql)) {
            throw new Exception("Gagal update status pembayaran: " . $koneksi->error);
        }
        
        // Commit transaction
        $koneksi->commit();
        $success = "Pembayaran berhasil! Status pembayaran telah diperbarui.";
        
    } catch (Exception $e) {
        // Rollback transaction if error occurs
        if ($koneksi) {
            $koneksi->rollback();
        }
        $error = $e->getMessage();
    } finally {
        // Close statement if it exists
        if ($stmt) {
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - mYGuitar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .receipt-icon::before {
            content: "\f543"; /* Unicode for fa-receipt */
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        
        /* Tambahkan gaya untuk tombol yang dinonaktifkan */
        .btn-disabled {
            background-color: #9CA3AF !important; /* gray-400 */
            cursor: not-allowed;
            opacity: 0.7;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <img src="logo.png" alt="mYGuitar Logo" class="h-12 mr-4">
                <h1 class="text-3xl font-bold text-green-700">mYGuitar</h1>
            </div>
            <a href="faktur.php" class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                <i class="fas fa-receipt mr-2"></i>Faktur Belanja
            </a>
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Pembayaran</h2>
            
            <div class="mb-6 p-4 bg-green-100 rounded-lg border border-green-200">
                <p class="text-lg font-medium text-gray-800">Silakan bayar: <span class="font-bold text-green-700">Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></span></p>
            </div>

            <!-- Success/Error Messages -->
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Payment Method -->
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Pembayaran via DANA</h3>
                    <div class="bg-white p-4 border border-gray-200 rounded-lg flex flex-col items-center">
                        <img src="qr2.jpg" alt="QR Code DANA" class="w-64 h-64 mb-4">
                        <p class="text-gray-600 mb-2">Scan QR code di atas untuk melakukan pembayaran</p>
                        <p class="text-gray-600 font-medium">Nomor DANA: 0812 XXXX XXXX</p>
                    </div>
                </div>

                <!-- Payment Form -->
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Formulir Pembayaran</h3>
                    <form id="paymentForm" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label for="nama" class="block text-gray-700 font-medium mb-2">Nama Customer</label>
                            <input type="text" id="nama" name="nama" required autocomplete="off" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="telepon" class="block text-gray-700 font-medium mb-2">Nomor HP</label>
                            <input type="tel" id="telepon" name="telepon" required autocomplete="off" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                value="<?php echo isset($_POST['telepon']) ? htmlspecialchars($_POST['telepon']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                            <input type="email" id="email" name="email" required autocomplete="off" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="alamat" class="block text-gray-700 font-medium mb-2">Alamat Pengiriman <span class="text-gray-500 text-sm">(wajib)</span></label>
                            <textarea id="alamat" name="alamat" rows="3" required autocomplete="off" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"><?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>
                        </div>
                        
                        <div>
                            <label for="bukti_pembayaran" class="block text-gray-700 font-medium mb-2">Upload Bukti Pembayaran</label>
                            <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/jpeg,image/png" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG (Maks. 2MB)</p>
                        </div>
                        
                        <button id="submitBtn" type="submit" class="w-full bg-gray-400 text-white font-bold py-3 px-4 rounded-lg transition btn-disabled" disabled>
                            <i class="fas fa-money-bill-wave mr-2"></i>Bayar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript untuk validasi form dan aktivasi tombol bayar
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('paymentForm');
            const submitBtn = document.getElementById('submitBtn');
            const nama = document.getElementById('nama');
            const telepon = document.getElementById('telepon');
            const email = document.getElementById('email');
            const buktiPembayaran = document.getElementById('bukti_pembayaran');
            
            // Fungsi untuk memeriksa validitas form
            function validateForm() {
                if (nama.value.trim() !== '' && 
                    telepon.value.trim() !== '' && 
                    email.value.trim() !== '' && 
                    buktiPembayaran.files.length > 0) {
                    
                    // Aktifkan tombol bayar
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('btn-disabled');
                    submitBtn.classList.remove('bg-gray-400');
                    submitBtn.classList.add('bg-green-600');
                    submitBtn.classList.add('hover:bg-green-700');
                } else {
                    // Nonaktifkan tombol bayar
                    submitBtn.disabled = true;
                    submitBtn.classList.add('btn-disabled');
                    submitBtn.classList.add('bg-gray-400');
                    submitBtn.classList.remove('bg-green-600');
                    submitBtn.classList.remove('hover:bg-green-700');
                }
            }
            
            // Tambahkan event listener untuk setiap input
            nama.addEventListener('input', validateForm);
            telepon.addEventListener('input', validateForm);
            email.addEventListener('input', validateForm);
            buktiPembayaran.addEventListener('change', validateForm);
            
            // Panggil validasi saat halaman dimuat untuk menangani nilai yang sudah ada
            validateForm();
        });
    </script>
</body>
</html>