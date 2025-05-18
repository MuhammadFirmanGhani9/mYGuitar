<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mYGuitar | Keranjang Belanja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#27ae60',
                            dark: '#1e8449',
                        },
                        dark: {
                            DEFAULT: '#212121',
                            light: '#121212',
                        },
                        light: {
                            DEFAULT: '#f5f5f5',
                            dark: '#e0e0e0',
                        },
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .theme-transition {
                @apply transition-colors duration-300 ease-in-out;
            }
        }
    </style>
</head>
<body class="bg-light dark:bg-dark-light theme-transition flex flex-col min-h-screen">
    <!-- Header -->
    <header class="bg-primary text-white shadow-md py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-guitar text-2xl"></i>
                <h1 class="text-2xl font-bold tracking-wide">mYGuitar | Keranjang</h1>
            </div>
            
            <div class="flex flex-wrap justify-center gap-2 sm:gap-3">
                <a href="katalog.html" class="bg-white/20 hover:bg-white/30 text-white font-bold py-2 px-4 rounded flex items-center gap-2 transition">
                    <i class="fas fa-arrow-left"></i> Katalog
                </a>
                <a href="tampil_produk.php" class="bg-white/20 hover:bg-white/30 text-white font-bold py-2 px-4 rounded flex items-center gap-2 transition">
                    <i class="fas fa-store"></i> Belanja
                </a>
                <a href="role.html" class="bg-white/20 hover:bg-white/30 text-white font-bold py-2 px-4 rounded flex items-center gap-2 transition">
                    <i class="fas fa-user-cog"></i> Role
                </a>
                <a href="index.html" class="bg-white/20 hover:bg-white/30 text-white font-bold py-2 px-4 rounded flex items-center gap-2 transition">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <button id="theme-toggle" class="w-10 h-10 flex items-center justify-center border-2 border-white rounded-full hover:bg-white/20 transition" title="Ubah tema">
                    <i class="fas fa-moon text-white"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow w-full max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-8 pl-3 border-l-4 border-primary dark:text-white">Keranjang Belanja</h2>
        
        <div class="bg-white dark:bg-dark rounded-lg shadow-md overflow-hidden w-full">
            <?php
            include 'koneksi.php';
            
            $query = "SELECT * FROM keranjang WHERE konfirmasi = 'belum' ORDER BY tanggal_ditambahkan DESC";
            $result = mysqli_query($koneksi, $query);
            $total = 0;
            
            if (mysqli_num_rows($result) > 0): ?>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="p-4 sm:p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold dark:text-white"><?= htmlspecialchars($row['merk']) ?></h3>
                                <p class="text-primary font-bold mt-1">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                            </div>
                            <button class="remove-btn bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded flex items-center gap-2 transition" data-id="<?= $row['id'] ?>">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                        <?php $total += $row['harga']; ?>
                    <?php endwhile; ?>
                </div>
                
                <div class="p-4 sm:p-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold dark:text-white">Total Belanja:</span>
                        <span class="text-2xl font-bold text-primary">Rp <?= number_format($total, 0, ',', '.') ?></span>
                    </div>
                    <a href="faktur.php">
                    <button class="checkout-btn w-full mt-6 bg-primary hover:bg-primary-dark text-white font-bold py-3 px-4 rounded flex items-center justify-center gap-2 transition">
                        <i class="fas fa-credit-card"></i> Finalisasi pesanan dan cetak faktur
                    </button>
                </a>

                </div>
            <?php else: ?>
                <div class="p-8 text-center">
                    <i class="fas fa-shopping-cart text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-medium dark:text-white">Keranjang belanja kosong</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Silakan tambahkan produk dari halaman belanja</p>
                    <a href="tampil_produk.php" class="inline-block mt-4 bg-primary hover:bg-primary-dark text-white font-bold py-2 px-6 rounded transition">
                        Belanja Sekarang
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            &copy; <?= date('Y') ?> mYGuitar | Marketplace - Temukan Gitar Impianmu
        </div>
    </footer>

    <script>
        // Theme Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = themeToggle.querySelector('i');
            const html = document.documentElement;
            
            // Check saved theme
            const savedTheme = localStorage.getItem('myguitar-theme');
            if (savedTheme) {
                html.setAttribute('data-theme', savedTheme);
                updateIcon(savedTheme);
            }
            
            // Toggle theme
            themeToggle.addEventListener('click', () => {
                const currentTheme = html.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('myguitar-theme', newTheme);
                updateIcon(newTheme);
            });
            
            function updateIcon(theme) {
                if (theme === 'dark') {
                    themeIcon.classList.replace('fa-moon', 'fa-sun');
                } else {
                    themeIcon.classList.replace('fa-sun', 'fa-moon');
                }
            }
            
            // Remove item from cart
            document.querySelectorAll('.remove-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const itemId = this.dataset.id;
                    
                    if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                        fetch('hapus_keranjang.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'id=' + itemId
                        })
                        .then(response => response.text())
                        .then(result => {
                            if (result.includes('success')) {
                                location.reload();
                            } else {
                                alert('Gagal menghapus item');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan');
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>