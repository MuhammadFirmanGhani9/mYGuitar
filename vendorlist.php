<?php
include 'koneksi.php';

// Cek apakah ada permintaan untuk mengurutkan stok descending
$urutan = isset($_GET['urut']) && $_GET['urut'] === 'desc' ? 'DESC' : 'ASC';
$tombolLabel = $urutan === 'ASC' ? 'Urutkan dari terbanyak' : 'Urutkan dari paling sedikit';

// Query ambil vendor dan total stok dari tabel produk
$query = "
    SELECT vendor, SUM(stok) AS total_stok
    FROM produk
    GROUP BY vendor
    ORDER BY total_stok $urutan
";

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Vendor | mYGuitar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-black text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-green-400">mYGuitar</h1>
            <a href="admin.php" class="bg-white text-black px-4 py-2 rounded-md flex items-center hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto p-4 flex-grow">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6 text-center">Daftar Vendor dan Jumlah Produk yang Dijual</h2>
            
            <!-- Tombol untuk mengubah urutan -->
            <div class="flex justify-end mb-6">
                <a href="?urut=<?php echo $urutan === 'ASC' ? 'desc' : 'asc'; ?>" 
                   class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition shadow-sm flex items-center">
                    <i class="fas fa-sort mr-2"></i> <?php echo $tombolLabel; ?>
                </a>
            </div>

            <!-- Header Card -->
            <div class="bg-black text-white p-4 rounded-t-lg grid grid-cols-12 gap-4 font-semibold mb-2">
                <div class="col-span-1 flex items-center justify-center">#</div>
                <div class="col-span-7">Vendor</div>
                <div class="col-span-4">Total Stok Produk</div>
            </div>
            
            <!-- Data Cards -->
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                $bgColor = $no % 2 == 0 ? 'bg-gray-50' : 'bg-white';
            ?>
                <div class="<?php echo $bgColor; ?> border border-gray-200 p-4 grid grid-cols-12 gap-4 mb-2 rounded-md shadow-sm hover:shadow-md transition">
                    <div class="col-span-1 flex items-center justify-center font-semibold"><?php echo $no++; ?></div>
                    <div class="col-span-7 flex items-center"><?php echo htmlspecialchars($row['vendor']); ?></div>
                    <div class="col-span-4 flex items-center">
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-semibold">
                            <?php echo (int)$row['total_stok']; ?>
                        </span>
                    </div>
                </div>
            <?php } ?>
            
            <?php if (mysqli_num_rows($result) == 0) { ?>
                <div class="text-center p-6 text-gray-500">
                    <p>Tidak ada data vendor yang tersedia.</p>
                </div>
            <?php } ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-black text-white p-4 mt-auto">
        <div class="container mx-auto text-center">
            <p>&copy; <?php echo date('Y'); ?> mYGuitar. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>