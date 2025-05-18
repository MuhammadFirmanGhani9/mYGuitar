<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Dipesan - mYGuitar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body {
    height: 100%; /* Pastikan html dan body memiliki tinggi 100% */
    margin: 0; /* Hapus margin default */
}

body {
    display: flex;
    flex-direction: column; /* Mengatur arah flex menjadi kolom */
}

.container {
    flex-grow: 1; /* Membuat konten utama mengisi ruang yang tersedia */
}

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <?php
    // Include koneksi database
    include "koneksi.php";
    
    // Periksa koneksi
    if (!$koneksi) {
        die("Koneksi gagal: " . mysqli_connect_error());
    }
    ?>

    <!-- Header -->
    <header class="bg-black text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="logo.png" alt="mYGuitar Logo" class="h-12 mr-3">
                <h1 class="text-3xl font-bold text-green-500">mYGuitar</h1>
            </div>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="container mx-auto py-8 px-4">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Produk yang Dipesan</h2>
            <a href="pesanan.php" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            // Query untuk mengambil data dari tabel dipesan
            $query = "SELECT merk, harga FROM dipesan";
            $result = mysqli_query($koneksi, $query);

            // Periksa apakah ada data
            if (mysqli_num_rows($result) > 0) {
                // Output data dari setiap baris
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg border border-gray-200 transition-all duration-300 card-hover">
                        <div class="p-6 bg-gradient-to-br from-green-500 to-green-700">
                            <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($row["merk"]); ?></h3>
                        </div>
                        <div class="p-6 flex flex-col">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-700 font-medium">Harga:</span>
                                <span class="text-green-600 font-bold">Rp <?php echo number_format($row["harga"], 0, ',', '.'); ?></span>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex justify-center">
                                    <span class="text-black font-semibold bg-gray-100 rounded-full px-4 py-1 text-sm">
                                        Produk Dipesan
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-span-full text-center p-8 bg-white rounded-lg shadow">
                        <p class="text-gray-600 text-lg">Tidak ada produk yang dipesan.</p>
                      </div>';
            }

            // Tutup koneksi
            mysqli_close($koneksi);
            ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-6">
        <div class="container mx-auto px-4 text-center">
            <div class="flex justify-center mb-4 space-x-6">
                <a href="#" class="text-gray-400 hover:text-white">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white">
                    <i class="fab fa-youtube"></i>
                </a>
            </div>
            <p class="text-sm">&copy; <?php echo date('Y'); ?> mYGuitar. All rights reserved.</p>
            <p class="text-xs text-gray-500 mt-2">Sistem Manajemen Pesanan Guitar Terbaik</p>
        </div>
    </footer>
</body>
</html>