<?php
include 'koneksi.php';

// Proses konfirmasi
if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == 'confirm') {
    $id = $_GET['id'];
    // First check if the product has been paid
    $check_sql = "SELECT k.bayar, k.product_id, p.stok FROM keranjang k 
                 JOIN produk p ON k.product_id = p.id 
                 WHERE k.id=$id";
    $check_result = $koneksi->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $row = $check_result->fetch_assoc();
        if ($row['bayar'] == 'sudah') {
            // Begin transaction
            $koneksi->begin_transaction();
            
            try {
                // Update konfirmasi status
                $sql1 = "UPDATE keranjang SET konfirmasi='confirmed' WHERE id=$id";
                $koneksi->query($sql1);
                
                // Get current stock
                $product_id = $row['product_id'];
                $current_stock = $row['stok'];
                
                // Make sure stock is at least 1 before decrementing
                if ($current_stock > 0) {
                    // Update product stock (decrease by 1)
                    $new_stock = $current_stock - 1;
                    $sql2 = "UPDATE produk SET stok=$new_stock WHERE id=$product_id";
                    $koneksi->query($sql2);
                    
                    // Commit transaction
                    $koneksi->commit();
                    $message = "Pesanan telah dikonfirmasi dan stok produk berkurang 1!";
                } else {
                    // Rollback if stock is 0
                    $koneksi->rollback();
                    $error = "Error: Stok produk sudah habis, tidak dapat mengurangi stok";
                }
            } catch (Exception $e) {
                // Rollback on error
                $koneksi->rollback();
                $error = "Error: " . $e->getMessage();
            }
        } else {
            $error = "Error: Produk belum dibayar, tidak dapat dikonfirmasi";
        }
    }
}

// Ambil data dari database
$sql = "SELECT k.*, p.stok FROM keranjang k 
       LEFT JOIN produk p ON k.product_id = p.id 
       ORDER BY k.tanggal_ditambahkan DESC";
$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan - mYGuitar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .content {
            flex: 1 0 auto;
        }
        footer {
            flex-shrink: 0;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="content">
        <div class="container mx-auto px-4 py-8">
            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="admin.php" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Admin
                </a>
            </div>

            <!-- Pesan Sukses/Error -->
            <?php if (isset($message)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Judul Section -->
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Konfirmasi Pesanan</h2>
                <p class="text-gray-600">Daftar pesanan yang perlu dikonfirmasi</p>
            </div>

            <!-- Daftar Pesanan -->
            <div class="space-y-4">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                            <div class="p-6">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                    <div class="mb-4 md:mb-0">
                                        <h3 class="text-lg font-semibold text-gray-800"><?php echo $row['merk']; ?></h3>
                                        <p class="text-gray-600">ID Produk: <?php echo $row['product_id']; ?></p>
                                        <p class="text-gray-600">Vendor: <?php echo $row['vendor']; ?></p>
                                        <p class="text-gray-600">Tanggal: <?php echo $row['tanggal_ditambahkan']; ?></p>
                                        <p class="text-gray-600">Stok Tersedia: <?php echo isset($row['stok']) ? $row['stok'] : 'Tidak diketahui'; ?></p>
                                    </div>
                                    <div class="flex flex-col md:items-end">
                                        <span class="text-xl font-bold text-green-700 mb-2">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
                                        <div class="flex flex-col items-end space-y-2">
                                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                                <?php echo $row['konfirmasi'] == 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                <?php echo $row['konfirmasi'] == 'confirmed' ? 'Confirmed' : 'Belum dikonfirmasi'; ?>
                                            </span>
                                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                                <?php echo $row['bayar'] == 'sudah' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo $row['bayar'] == 'sudah' ? 'Sudah Bayar' : 'Belum Bayar'; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if ($row['konfirmasi'] == 'belum' && $row['bayar'] == 'sudah'): ?>
                                    <div class="mt-4 flex justify-end">
                                        <?php if (isset($row['stok']) && $row['stok'] > 0): ?>
                                            <a href="konfirmasi.php?id=<?php echo $row['id']; ?>&action=confirm" 
                                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                                                <i class="fas fa-check mr-2"></i>Konfirmasi
                                            </a>
                                        <?php else: ?>
                                            <button class="bg-red-400 text-white px-4 py-2 rounded-lg cursor-not-allowed" disabled>
                                                <i class="fas fa-exclamation-triangle mr-2"></i>Stok Habis
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif ($row['konfirmasi'] == 'belum' && $row['bayar'] == 'belum'): ?>
                                    <div class="mt-4 flex justify-end">
                                        <button class="bg-gray-400 text-white px-4 py-2 rounded-lg cursor-not-allowed" disabled>
                                            <i class="fas fa-times mr-2"></i>Belum Dibayar
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <p class="text-gray-600">Tidak ada pesanan yang perlu dikonfirmasi</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <img src="logo.png" alt="mYGuitar Logo" class="h-10 mr-4">
                    <h2 class="text-xl font-bold text-green-400">mYGuitar</h2>
                </div>
                <div class="text-center md:text-right">
                    <p>&copy; <?php echo date('Y'); ?> mYGuitar. Hak Cipta Dilindungi.</p>
                    <p class="text-gray-400 text-sm mt-1">Tempat terbaik untuk menemukan gitar impian Anda</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>