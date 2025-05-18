<?php
// Mulai session di awal file sebelum output apa pun
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mYGuitar - Vendor Panel</title>
    <!-- Tailwind CSS dari Cloudflare -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #2ecc71;
            --secondary-color: #25a25a;
            --dark-color: #1a1a1a;
            --light-color: #f5f5f5;
        }

        .theme-light {
            --bg-primary: var(--light-color);
            --text-primary: #333333;
            --bg-card: #ffffff;
            --border-color: #e5e7eb;
        }

        .theme-dark {
            --bg-primary:rgb(54, 54, 54);
            --text-primary:rgb(0, 0, 0);
            --bg-card:rgb(255, 255, 255);
            --border-color: #374151;
        }

        body {
            transition: background-color 0.3s, color 0.3s;
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .btn-primary {
            background-color: var(--primary-color);
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .bg-header {
            background-color: var(--dark-color);
        }

        .logo-text {
            color: var(--primary-color);
        }

        /* Tambahan custom styling untuk tabel */
        .custom-table th {
            background-color: var(--dark-color);
            color: white;
            padding: 12px;
        }
        h1{
            color: #2ecc71;
        }
        .custom-table tr:nth-child(even) {
            background-color: var(--bg-card);
        }

        .custom-table tr:hover {
            background-color: rgba(46, 204, 113, 0.1);
        }

        /* Style untuk tombol aksi */
        .btn-edit {
            background-color: #3498db;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-edit:hover {
            background-color: #2980b9;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .btn-disabled {
            background-color: #95a5a6;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 14px;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="theme-light">
    <!-- Header dengan Navbar -->
    <header class="bg-header shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <span class="text-3xl mr-2"></span>
                    <span class="text-2xl font-bold logo-text">mYGuitar</span>
                </div>
                <nav class="flex items-center">
                    <ul class="flex space-x-6 mr-6">
                        <li><a href="vendor.php" class="text-gray-300 hover:text-white">Beranda</a></li>
                        <li><a href="findpesanan.php" class="text-gray-300 hover:text-white">Pesanan</a></li>
                        <li><a href="role.html" class="text-gray-300 hover:text-white">Ubah Role</a></li>
                        <li><a href="index.html" class="text-gray-300 hover:text-white">Log Out</a></li>
                    </ul>
                    <button id="jualBtn" class="btn-primary text-white py-2 px-4 rounded-md font-semibold shadow-md mr-4">
                        Jual
                    </button>
                    <button id="themeToggle" class="rounded-full p-2 border-2 border-green-500 text-green-500 hover:bg-green-500 hover:text-white transition-colors duration-300">
                        <i class="fas fa-moon" id="themeIcon"></i>
                    </button>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        <div class="border-b-2 border-green-500 pb-4 mb-6">
            <h1 class="text-3xl font-bold">Dashboard Vendor</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola produk gitar Anda di sini</p>
            <br>
            <p class="text-gray-600 dark:text-gray-400"><b>Kebijakan privasi </b>: Anda hanya bisa menghapus dan mengedit produk jika dan hanya jika produk tersebut adalah milik anda dan akun anda terdaftar secara resmi di database kami. Anda tidak dapat melakukan penghapusan atau edit produk jika salah memasukkan nama vendor yang anda gunakan ketika login. Tidak ada pengembalian uang komisi ketika produk dihapus. Anda juga tidak dapat melakukan penghapusan dan edit produk dari vendor lain di marketplace ini.</p>
        </div>
        
        <!-- Notifikasi -->
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">';
            echo '<p>' . $_SESSION['success_message'] . '</p>';
            echo '</div>';
            unset($_SESSION['success_message']);
        }
        
        if (isset($_SESSION['error_message'])) {
            echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">';
            echo '<p>' . $_SESSION['error_message'] . '</p>';
            echo '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <!-- Tabel Produk dari Database -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h2 class="text-xl font-semibold">Laporan Singkat Daftar Produk yang Ada di Marketplace Saat Ini</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full custom-table">
                    <thead>
                        <tr>
                            <th class="text-left">ID</th>
                            <th class="text-left">Merk</th>
                            <th class="text-left">Jenis Gitar</th>
                            <th class="text-left">Harga</th>
                            <th class="text-left">Stok</th>
                            <th class="text-left">Vendor</th>
                            <th class="text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Include file koneksi
                        include 'koneksi.php';
                        
                        // Query untuk mengambil data dari tabel produk dan memeriksa apakah vendor terdaftar di tabel daftar
                        $query = "SELECT produk.*, 
                                (SELECT COUNT(*) FROM daftar WHERE daftar.username = produk.vendor) AS is_registered 
                                FROM produk 
                                ORDER BY produk.id";
                        
                        $result = mysqli_query($koneksi, $query);
                        
                        // Cek apakah query berhasil
                        if ($result) {
                            // Loop untuk menampilkan data produk
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Cek apakah vendor terdaftar di tabel daftar (berdasarkan hasil subquery)
                                $is_registered = $row['is_registered'] > 0;
                                
                                echo "<tr class='border-b border-gray-200 dark:border-gray-700'>";
                                echo "<td class='px-6 py-4'>" . $row['id'] . "</td>";
                                echo "<td class='px-6 py-4'>" . $row['merk'] . "</td>";
                                echo "<td class='px-6 py-4'>" . $row['jenis_gitar'] . "</td>";
                                echo "<td class='px-6 py-4'>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                                echo "<td class='px-6 py-4'>" . $row['stok'] . "</td>";
                                echo "<td class='px-6 py-4'>" . $row['vendor'] . "</td>";
                                echo "<td class='px-6 py-4'>";
                                echo "<div class='flex space-x-2'>";
                                
                                // Tampilkan tombol edit dan hapus hanya jika vendor terdaftar di tabel daftar
                                if ($is_registered) {
                                    echo "<a href='edit_produk.php?id=" . $row['id'] . "' class='btn-edit'><i class='fas fa-edit mr-1'></i> Edit</a>";
                                    echo "<a href='#' onclick='konfirmasiHapus(" . $row['id'] . ")' class='btn-delete'><i class='fas fa-trash-alt mr-1'></i> Hapus</a>";
                                } else {
                                    echo "<span class='btn-disabled'><i class='fas fa-edit mr-1'></i> Edit</span>";
                                    echo "<span class='btn-disabled'><i class='fas fa-trash-alt mr-1'></i> Hapus</span>";
                                }
                                
                                echo "</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            
                            // Bebaskan hasil
                            mysqli_free_result($result);
                        } else {
                            echo "<tr><td colspan='7' class='px-6 py-4 text-center'>Error: " . mysqli_error($koneksi) . "</td></tr>";
                        }
                        
                        // Tutup koneksi
                        mysqli_close($koneksi);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // Toggle theme functionality
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        let isDarkMode = false;

        themeToggle.addEventListener('click', () => {
            isDarkMode = !isDarkMode;
            if (isDarkMode) {
                document.body.classList.remove('theme-light');
                document.body.classList.add('theme-dark');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                document.body.classList.remove('theme-dark');
                document.body.classList.add('theme-light');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        });

        // Jual button functionality
        const jualBtn = document.getElementById('jualBtn');
        jualBtn.addEventListener('click', () => {
            window.location.href = 'tambah_produk.php';
        });
        
        // Fungsi konfirmasi hapus
        function konfirmasiHapus(id) {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                window.location.href = 'hapus_produk.php?id=' + id;
            }
        }
    </script>
</body>
</html>