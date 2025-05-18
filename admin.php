<?php
include 'koneksi.php'; // pastikan file koneksi udah benar

// Inisialisasi filter
$jenis_filter = isset($_GET['jenis']) ? $_GET['jenis'] : '';
$vendor_filter = isset($_GET['vendor']) ? $_GET['vendor'] : '';

// Query ambil data dari tabel produk dengan filter jika ada
$query = "SELECT * FROM produk";

// Tambahkan kondisi WHERE jika ada filter
if (!empty($jenis_filter) || !empty($vendor_filter)) {
    $query .= " WHERE ";
    
    if (!empty($jenis_filter)) {
        $query .= "jenis_gitar = '" . mysqli_real_escape_string($koneksi, $jenis_filter) . "'";
    }
    
    if (!empty($jenis_filter) && !empty($vendor_filter)) {
        $query .= " AND ";
    }
    
    if (!empty($vendor_filter)) {
        $query .= "vendor = '" . mysqli_real_escape_string($koneksi, $vendor_filter) . "'";
    }
}

$result = mysqli_query($koneksi, $query);

// Hitung jumlah stok
$total_stok = 0;
$vendors = array();

// Simpan hasil query dalam array untuk penggunaan beberapa kali
$all_products = array();
while ($row = mysqli_fetch_assoc($result)) {
    $all_products[] = $row;
    $total_stok += $row['stok'];
    $vendors[$row['vendor']] = 1; // Menghitung vendor unik
}

// Hitung jumlah vendor unik
$total_vendor = count($vendors);

// Reset pointer result
mysqli_data_seek($result, 0);

// Dapatkan daftar vendor unik untuk dropdown
$query_vendors = "SELECT DISTINCT vendor FROM produk ORDER BY vendor";
$result_vendors = mysqli_query($koneksi, $query_vendors);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mYGuitar - Admin Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
  <div class="min-h-screen">
    <!-- Header -->
    <header class="bg-green-700 text-white shadow-lg">
      <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
          <div class="flex items-center">
            <i class="fas fa-guitar text-2xl mr-3"></i>
            <h1 class="text-2xl font-bold">mYGuitar</h1>
          </div>
          <div>
            <span class="bg-white text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
              Firghan
            </span>
          </div>
        </div>
      </div>
    </header>

    <!-- Navigation Bar -->
    <nav class="bg-green-800 text-white shadow-md">
      <div class="container mx-auto px-4">
        <div class="flex items-center justify-between flex-wrap">
          <div class="flex-1 flex items-center py-3">
            <ul class="flex space-x-6">
              <li class="font-medium">
                <a href="admin.php" class="hover:text-green-200 border-b-2 border-green-500 pb-1">
                  <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                </a>
              </li>
              <li class="font-medium">
                <a href="vendorlist.php" class="hover:text-green-200 pb-1">
                  <i class="fas fa-store mr-1"></i> Daftar Vendor
                </a>
              </li>
              <li class="font-medium">
                <a href="konfirmasi.php" class="hover:text-green-200 pb-1">
                  <i class="fas fa-check-circle mr-1"></i> Konfirmasi
                </a>
              </li>
              <li class="font-medium">
                <a href="member.php" class="hover:text-green-200 pb-1">
                  <i class="fas fa-users mr-1"></i> Member
                </a>
              </li>
            </ul>
          </div>
          <div>
            <a href="index.html" class="bg-gray-100 hover:bg-white text-green-800 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center">
              <i class="fas fa-arrow-left mr-2"></i> Kembali ke Website
            </a>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
      <!-- Dashboard Stats -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Dashboard</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-green-600 rounded-lg p-4 text-white shadow-md">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-sm opacity-80">Total Stok</p>
                <h3 class="text-3xl font-bold"><?= $total_stok ?></h3>
              </div>
              <div class="text-4xl opacity-80">
                <i class="fas fa-boxes"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-green-600 rounded-lg p-4 text-white shadow-md">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-sm opacity-80">Kategori</p>
                <h3 class="text-3xl font-bold">4</h3>
              </div>
              <div class="text-4xl opacity-80">
                <i class="fas fa-tags"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-green-600 rounded-lg p-4 text-white shadow-md">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-sm opacity-80">Vendor Berbeda</p>
                <h3 class="text-3xl font-bold"><?= $total_vendor ?></h3>
              </div>
              <div class="text-4xl opacity-80">
                <i class="fas fa-store"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Products Table Card -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-800">Daftar Produk Gitar</h2>
         
        </div>
        
        <!-- Search and Filter -->
        <div class="p-4 bg-gray-50 border-b">
          <form method="GET" action="" class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow">
              <div class="relative">
                <input type="text" name="search" placeholder="Cari produk..." 
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border focus:ring focus:ring-green-200 focus:border-green-500 transition">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="fas fa-search text-gray-400"></i>
                </div>
              </div>
            </div>
            <div class="flex gap-4">
              <select name="jenis" class="bg-white border rounded-lg px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-500 transition" onchange="this.form.submit()">
                <option value="">Semua Jenis</option>
                <option value="akustik" <?= $jenis_filter == 'akustik' ? 'selected' : '' ?>>Akustik</option>
                <option value="elektrik" <?= $jenis_filter == 'elektrik' ? 'selected' : '' ?>>Elektrik</option>
                <option value="transkustik" <?= $jenis_filter == 'transkustik' ? 'selected' : '' ?>>Transkustik</option>
                <option value="ukulele" <?= $jenis_filter == 'ukulele' ? 'selected' : '' ?>>Ukulele</option>
              </select>
              <select name="vendor" class="bg-white border rounded-lg px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-500 transition" onchange="this.form.submit()">
                <option value="">Semua Vendor</option>
                <?php while ($vendor = mysqli_fetch_assoc($result_vendors)) : ?>
                  <option value="<?= $vendor['vendor'] ?>" <?= $vendor_filter == $vendor['vendor'] ? 'selected' : '' ?>>
                    <?= $vendor['vendor'] ?>
                  </option>
                <?php endwhile; ?>
              </select>
              <?php if (!empty($jenis_filter) || !empty($vendor_filter)) : ?>
                <a href="admin.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg px-3 py-2 flex items-center">
                  <i class="fas fa-times mr-1"></i> Reset
                </a>
              <?php endif; ?>
            </div>
          </form>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Gitar</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php $i = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $row['id']; ?></td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= $row['merk']; ?></td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                      <?php
                        switch(strtolower($row['jenis_gitar'])) {
                          case 'akustik':
                            echo 'bg-green-100 text-green-800';
                            break;
                          case 'elektrik':
                            echo 'bg-green-100 text-green-800';
                            break;
                          case 'transkustik':
                            echo 'bg-green-100 text-green-800';
                            break;
                          case 'ukulele':
                            echo 'bg-green-100 text-green-800';
                            break;
                          default:
                            echo 'bg-gray-100 text-gray-800';
                        }
                      ?>
                    ">
                      <?= $row['jenis_gitar']; ?>
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Rp<?= number_format($row['harga'], 0, ',', '.'); ?></td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <?php if ($row['stok'] > 10) : ?>
                      <span class="text-green-600 font-medium"><?= $row['stok']; ?></span>
                    <?php elseif ($row['stok'] > 0) : ?>
                      <span class="text-yellow-600 font-medium"><?= $row['stok']; ?></span>
                    <?php else : ?>
                      <span class="text-red-600 font-medium">Habis</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= $row['vendor']; ?></td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                   
                  </td>
                </tr>
              <?php $i++; endwhile; ?>

              <?php if (mysqli_num_rows($result) == 0) : ?>
                <tr>
                  <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                      <i class="fas fa-box-open text-4xl mb-3 opacity-30"></i>
                      <p>Belum ada data produk yang tersedia</p>
                    
                    </div>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
          <div class="flex-1 flex justify-between sm:hidden">
            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
              Previous
            </a>
            <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
              Next
            </a>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Menampilkan <span class="font-medium">1</span> sampai <span class="font-medium"><?= mysqli_num_rows($result) ?></span> dari <span class="font-medium"><?= mysqli_num_rows($result) ?></span> hasil
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                  <span class="sr-only">Previous</span>
                  <i class="fas fa-chevron-left"></i>
                </a>
                <a href="#" aria-current="page" class="z-10 bg-green-50 border-green-500 text-green-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                  1
                </a>
                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                  <span class="sr-only">Next</span>
                  <i class="fas fa-chevron-right"></i>
                </a>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-black text-white mt-8">
      <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <div>
            <p class="text-sm">&copy; 2025 mYGuitar - All rights reserved</p>
          </div>
          <div class="mt-4 md:mt-0">
            <div class="flex space-x-4"> 
              <a href="https://www.facebook.com/profile.php?id=100055798496173"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/firghan.png/?hl=en"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/6281261017232"><i class="fab fa-whatsapp"></i></a>
                        <a href="https://www.youtube.com/@arrival4852"><i class="fab fa-youtube"></i></a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
</body>
</html>