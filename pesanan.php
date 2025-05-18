<?php
// Memulai session
session_start();

// Include koneksi ke database
include 'koneksi.php';

// Periksa apakah vendor sudah diset di session
if (!isset($_SESSION['vendor'])) {
    header("Location: findpesanan.php");
    exit();
}

$vendor = $_SESSION['vendor'];

// Query untuk mendapatkan data pesanan vendor (hanya merk dan harga)
$query = "SELECT id, merk, harga, status FROM keranjang WHERE vendor = '$vendor'";

$result = mysqli_query($koneksi, $query);

// Inisialisasi variabel total harga
$total_harga = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Vendor - mYGuitar</title>
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
                <h2 class="text-2xl font-semibold text-gray-800">Daftar Pesanan</h2>
                <p class="text-lg text-gray-600 mt-1">Vendor: <?php echo htmlspecialchars($vendor); ?></p>
            </div>
        </div>

        <!-- Tombol Kembali -->
        <div class="mb-6">
            <a href="vendor.php" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Kembali ke Vendor
            </a>
        </div>

        <!-- Card untuk daftar pesanan -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden mb-8 border border-gray-200">
            <!-- Header card -->
            <div class="bg-gradient-to-r from-green-700 to-green-800 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-white">
                        <i class="fas fa-shopping-cart mr-2"></i>Detail Pesanan
                    </h3>
                    <span class="bg-white text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        <?php echo mysqli_num_rows($result); ?> Pesanan
                    </span>
                </div>
            </div>

            <!-- Tabel pesanan -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">Merk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <?php $total_harga += $row['harga']; ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo $row['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo htmlspecialchars($row['merk']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if ($row['status'] == 'belum_dikirim'): ?>
    <button 
        onclick="confirmDelivery(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['merk'], ENT_QUOTES); ?>', <?php echo $row['harga']; ?>, this)" 
        class="bg-green-700 hover:bg-green-800 text-white py-1 px-3 rounded-md text-xs transition-colors">
        Kirim Barang
    </button>
<?php else: ?>
    <span class="text-gray-500">Barang Sudah Dikirim</span>
<?php endif; ?>
                                    

                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    <i class="fas fa-box-open text-gray-400 text-2xl mb-2"></i>
                                    <p>Tidak ada pesanan ditemukan</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-gray-100 border-t-2 border-gray-200">
                        <tr>
                            <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total Harga</td>
                            <td class="px-6 py-3 text-lg font-bold text-green-700">Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Info Boxes (Disederhanakan) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Box Ringkasan -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-700">
                <h3 class="text-lg font-semibold text-gray-800 mb-3"><i class="fas fa-chart-pie text-green-700 mr-2"></i>Ringkasan Pesanan</h3>
                <div class="space-y-2">
                    <p class="text-sm text-gray-700"><span class="font-medium">Total Pesanan:</span> <?php echo mysqli_num_rows($result); ?></p>
                    <p class="text-sm text-gray-700"><span class="font-medium">Total Nilai:</span> Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></p>
                </div>
            </div>

            <!-- Box Aksi -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-gray-600">
                <h3 class="text-lg font-semibold text-gray-800 mb-3"><i class="fas fa-tools text-gray-600 mr-2"></i>Aksi</h3>
                <div class="space-y-3">
                    <button onclick="window.location.href='pesanan.php';" class="w-full bg-gray-800 hover:bg-gray-900 text-white py-2 px-4 rounded-md text-sm transition-colors">
    <i class="fas fa-sync-alt mr-2"></i>Refresh Data
</button>

                    <a href="dipesan.php" class="block w-full bg-green-700 hover:bg-green-800 text-white py-2 px-4 rounded-md text-sm text-center transition-colors mt-2">
                        <i class="fas fa-list-check mr-2"></i>Lihat Barang Terkirim
                    </a>
                </div>
            </div>
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

    <!-- Script untuk handling pengiriman barang dan Ajax request -->
    <script>
async function confirmDelivery(orderId, merk, harga, buttonElement) {
    try {
        if (!confirm(`Kirim ${merk} (Rp ${harga.toLocaleString('id-ID')})?`)) {
            return;
        }

        const response = await fetch('process_delivery.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                order_id: orderId,
                merk: merk,
                harga: harga,
                action: 'kirim_barang'
            })
        });

        const rawResponse = await response.text();
        const data = JSON.parse(rawResponse);
        
        if (data.success) {
            alert('✅ Berhasil dikirim!');
            buttonElement.style.display = 'none'; // Menyembunyikan tombol
            window.location.reload(); // Reload halaman untuk memperbarui data
        } else {
            throw new Error(data.message || 'Unknown error');
        }
    } catch (error) {
        console.error('Error details:', error);
        alert(`❌ Gagal: ${error.message}\nLihat konsol untuk detail`);
    }
}

</script>
</body>
</html>