<?php
require_once "koneksi.php";

// Query untuk mengambil data dari tabel keranjang
// Perbaikan: Query diubah untuk mendapatkan semua data yang diperlukan dan filter berdasarkan bayar = 'belum'
$query = "SELECT k.product_id AS id, k.merk, k.vendor, k.harga, k.tanggal_ditambahkan AS tanggal_pesanan 
          FROM keranjang k
          WHERE k.bayar = 'belum'";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}

// Hitung total item dan total harga
$total_items = mysqli_num_rows($result); // Perbaikan: Menghitung jumlah baris dengan bayar = 'belum'
$total_harga = 0;
$items = [];
$no_faktur = "INV-" . date('Ymd') . rand(1000, 9999);

// Ambil semua data hasil query ke dalam array
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
    $total_harga += $row['harga']; // Perbaikan: Menambahkan harga ke total
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mYGuitar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10B981',
                        dark: '#1F2937',
                        light: '#FFFFFF'
                    }
                }
            }
        }
    </script>
    <style>
        .hover-effect:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        .shadow-custom {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-dark text-light shadow-lg">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-guitar text-2xl text-primary"></i>
                    <h1 class="text-2xl font-bold text-primary">mYGuitar</h1>
                </div>
                <nav>
                    <ul class="flex space-x-4">
                        <li><a href="faktur.php" class="text-light hover:text-primary transition-colors"><i class="fas fa-sync-alt mr-1"></i> Refresh</a></li>
                        <li><a href="keranjang.php" class="text-light hover:text-primary transition-colors"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></li>
                        <li><a href="tampil_produk.php" class="text-primary font-bold hover:underline"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-custom overflow-hidden">
                    <!-- Invoice Header -->
                    <div class="px-6 py-5 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl font-bold text-dark">Faktur Pemesanan</h2>
                                <div class="mt-2 flex space-x-4">
                                    <p class="text-gray-700"><span class="font-semibold">Tanggal:</span> <?= date('d-m-Y') ?></p>
                                    <p class="text-gray-700"><span class="font-semibold">No. Faktur:</span> <?= $no_faktur ?></p>
                                </div>
                            </div>
                            <div class="bg-primary/10 px-4 py-2 rounded-full">
                                <span class="text-primary font-semibold">Status: Belum Dibayar</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Items Table -->
                    <div class="px-6 py-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($items as $index => $item): ?>
                                    <tr class="hover-effect hover:bg-gray-50/50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= $index + 1 ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item['merk']) ?></div>
                                            <div class="text-sm text-gray-500">ID: <?= htmlspecialchars($item['id']) ?></div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($item['vendor']) ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            Rp <?= number_format($item['harga'], 0, ',', '.') ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($item['tanggal_pesanan']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Total</td>
                                        <td class="px-4 py-3 text-sm font-bold text-primary">
                                            Rp <?= number_format($total_harga, 0, ',', '.') ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Payment Summary -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-700">
                                    <span class="font-semibold">Total Item:</span> 
                                    <span class="text-primary font-bold"><?= $total_items ?></span>
                                </p>
                            </div>
                            <form action="bayar.php" method="POST">
                                <input type="hidden" name="total_harga" value="<?= $total_harga ?>">
                                <input type="hidden" name="total_items" value="<?= $total_items ?>">
                                <input type="hidden" name="no_faktur" value="<?= $no_faktur ?>">
                                <button type="submit" class="bg-primary hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition-all hover:shadow-md flex items-center">
                                    <i class="fas fa-credit-card mr-2"></i> Bayar Sekarang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-dark text-light py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p>&copy; <?= date('Y') ?> mYGuitar | Marketplace Gitar Terbaik</p>
                <div class="mt-2 flex justify-center space-x-4">
                    <a href="#" class="text-light hover:text-primary transition-colors"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-light hover:text-primary transition-colors"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-light hover:text-primary transition-colors"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>