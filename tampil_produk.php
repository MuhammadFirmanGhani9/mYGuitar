<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mYGuitar | Marketplace</title>
    <link rel="stylesheet" type="text/css" href="vendor.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #27ae60;
            --dark-color: #212121;
            --light-color: #f5f5f5;
            --accent-color: #1e8449;
            --card-bg: white;
            --text-color: #212121;
            --text-secondary: #666;
            --border-color: #eee;
            --product-type-bg: #f0f0f0;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --shadow-hover: rgba(0, 0, 0, 0.15);
            --transition-speed: 0.3s;
        }
        
        [data-theme="dark"] {
            --primary-color: #2ecc71;
            --dark-color: #121212;
            --light-color: #1e1e1e;
            --accent-color: #27ae60;
            --card-bg: #1e1e1e;
            --text-color: #f5f5f5;
            --text-secondary: #aaa;
            --border-color: #333;
            --product-type-bg: #2a2a2a;
            --shadow-color: rgba(0, 0, 0, 0.3);
            --shadow-hover: rgba(0, 0, 0, 0.4);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color var(--transition-speed), color var(--transition-speed);
        }
        
        body {
            background-color: var(--light-color);
            color: var(--text-color);
        }
        
        .header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem;
            box-shadow: 0 2px 10px var(--shadow-color);
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .theme-toggle {
            background-color: transparent;
            border: 2px solid white;
            color: white;
            border-radius: 50px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s;
        }
        
        .theme-toggle:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        /* Navbar styling */
        .nav-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .nav-button {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 0.5rem 1rem;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .nav-button:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        
        .nav-button i {
            font-size: 0.9rem;
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--text-color);
            border-left: 5px solid var(--primary-color);
            padding-left: 10px;
        }
        
        /* Ubah product-container menjadi grid dengan 3 kolom */
        .product-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.2rem;
        }
        
        .product-card {
            background-color: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 3px 10px var(--shadow-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px var(--shadow-hover);
        }
        
        .product-image {
            width: 100%;
            height: 180px;
            background-color: var(--light-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-color);
            font-size: 3rem;
            border-bottom: 1px solid var(--border-color);
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .product-info {
            padding: 1.2rem;
            flex: 1;
            position: relative;
        }
        
        .product-merk {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }
        
        .product-type {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            display: inline-block;
            padding: 0.3rem 0.7rem;
            background-color: var(--product-type-bg);
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .product-price {
            font-size: 1.5rem;
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .product-stock {
            font-size: 0.9rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 0.8rem;
        }
        
        .product-vendor {
            font-size: 0.9rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 0.8rem;
        }
        
        .product-description {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-top: 0.8rem;
            line-height: 1.4;
            max-height: 4.2em;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        
        .product-id {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--dark-color);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .buy-btn {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.5rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .buy-btn:hover {
            background-color: var(--accent-color);
        }
        
        .footer {
            background-color: var(--dark-color);
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .empty-message {
            text-align: center;
            padding: 3rem;
            background-color: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 3px 10px var(--shadow-color);
            grid-column: 1 / -1;
        }
        
        /* Responsive untuk tablet */
        @media (max-width: 992px) {
            .product-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .header {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            
            .nav-buttons {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* Responsive untuk mobile */
        @media (max-width: 576px) {
            .product-container {
                grid-template-columns: 1fr;
            }
            
            .nav-buttons {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">
        <i class="fas fa-guitar"></i> mYGuitar | Marketplace
    </div>
    
    <div class="nav-buttons">
        <a href="katalog.html" class="nav-button">
            <i class="fas fa-arrow-left"></i> Kembali ke Katalog
        </a>
        <a href="keranjang.php" class="nav-button">
            <i class="fas fa-shopping-cart"></i> Keranjang
        <a href="role.html" class="nav-button">
            <i class="fas fa-user-cog"></i> Ubah Role
        </a>
        <a href="index.html" class="nav-button">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        <button id="theme-toggle" class="theme-toggle" title="Ubah tema terang/gelap">
            <i class="fas fa-moon"></i>
        </button>
    </div>
</header>

<div class="container">
    <h2 class='title'>Daftar Produk Gitar</h2>
    <div class='product-container'>
        <!-- Produk Statis ID 1-9 -->
        <!-- Produk 1 -->
        <div class='product-card' data-id="1" data-merk="Yamaha F310" data-harga="1250000" data-jenis="akustik" data-vendor="Yamaha Indonesia">
            <div class='product-image'>
                <img src='1rev.png' alt='Yamaha F310'>
            </div>
            <div class='product-info'>
                <div class='product-id'>ID: 1</div>
                <h3 class='product-merk'>Yamaha F310</h3>
                <span class='product-type'>akustik</span>
                <div class='product-price'>Rp 1.250.000</div>
                <div class='product-stock'><i class='fas fa-box'></i> Stok: 12 unit</div>
                <div class='product-vendor'><i class='fas fa-store'></i> Vendor: Yamaha Indonesia</div>
                <div class='product-description'>Gitar akustik dengan suara jernih, cocok untuk pemula maupun profesional</div>
                <button class='buy-btn'>Tambahkan ke Keranjang</button>
            </div>
        </div>

        <!-- Produk 2 -->
        <div class='product-card' data-id="2" data-merk="Fender CD-60S" data-harga="2450000" data-jenis="akustik" data-vendor="Fender Distributor">
            <div class='product-image'>
                <img src='2.png' alt='Fender CD-60S'>
            </div>
            <div class='product-info'>
                <div class='product-id'>ID: 2</div>
                <h3 class='product-merk'>Fender CD-60S</h3>
                <span class='product-type'>akustik</span>
                <div class='product-price'>Rp 2.450.000</div>
                <div class='product-stock'><i class='fas fa-box'></i> Stok: 8 unit</div>
                <div class='product-vendor'><i class='fas fa-store'></i> Vendor: Fender Distributor</div>
                <div class='product-description'>Gitar akustik dengan top solid spruce, build berkualitas tinggi</div>
                <button class='buy-btn'>Tambahkan ke Keranjang</button>
            </div>
        </div>

        <!-- Produk 3 -->
        <div class='product-card' data-id="3" data-merk="Ibanez V50NJP" data-harga="1650000" data-jenis="akustik" data-vendor="Ibanez Official">
            <div class='product-image'>
                <img src='3.png' alt='Ibanez V50NJP'>
            </div>
            <div class='product-info'>
                <div class='product-id'>ID: 3</div>
                <h3 class='product-merk'>Ibanez V50NJP</h3>
                <span class='product-type'>akustik</span>
                <div class='product-price'>Rp 1.650.000</div>
                <div class='product-stock'><i class='fas fa-box'></i> Stok: 10 unit</div>
                <div class='product-vendor'><i class='fas fa-store'></i> Vendor: Ibanez Official</div>
                <div class='product-description'>Paket lengkap gitar akustik untuk pemula, sudah termasuk aksesoris</div>
                <button class='buy-btn'>Tambahkan ke Keranjang</button>
            </div>
        </div>

        <!-- Produk 4 -->
        <div class='product-card' data-id="4" data-merk="Cort AD810E" data-harga="2350000" data-jenis="elektrik" data-vendor="Cort Indonesia">
            <div class='product-image'>
                <img src='4.png' alt='Cort AD810E'>
            </div>
            <div class='product-info'>
                <div class='product-id'>ID: 4</div>
                <h3 class='product-merk'>Cort AD810E</h3>
                <span class='product-type'>elektrik</span>
                <div class='product-price'>Rp 2.350.000</div>
                <div class='product-stock'><i class='fas fa-box'></i> Stok: 6 unit</div>
                <div class='product-vendor'><i class='fas fa-store'></i> Vendor: Cort Indonesia</div>
                <div class='product-description'>Gitar elektrik akustik dengan equalizer bawaan dan tuner digital</div>
                <button class='buy-btn'>Tambahkan ke Keranjang</button>
            </div>
        </div>

        <!-- Produk 5 -->
        <div class='product-card' data-id="5" data-merk="Yamaha APX600" data-harga="3890000" data-jenis="transkustik" data-vendor="Yamaha Indonesia">
            <div class='product-image'>
                <img src='5.png' alt='Yamaha APX600'>
            </div>
            <div class='product-info'>
                <div class='product-id'>ID: 5</div>
                <h3 class='product-merk'>Yamaha APX600</h3>
                <span class='product-type'>transkustik</span>
                <div class='product-price'>Rp 3.890.000</div>
                <div class='product-stock'><i class='fas fa-box'></i> Stok: 5 unit</div>
                <div class='product-vendor'><i class='fas fa-store'></i> Vendor: Yamaha Indonesia</div>
                <div class='product-description'>Gitar transkustik dengan efek chorus dan reverb tambahan</div>
                <button class='buy-btn'>Tambahkan ke Keranjang</button>
            </div>
        </div>

        <!-- Produk 6 -->
        <div class='product-card' data-id="6" data-merk="Taylor GS Mini" data-harga="7990000" data-jenis="akustik" data-vendor="Taylor Guitars">
            <div class='product-image'>
                <img src='6.png' alt='Taylor GS Mini'>
            </div>
            <div class='product-info'>
                <div class='product-id'>ID: 6</div>
                <h3 class='product-merk'>Taylor GS Mini</h3>
                <span class='product-type'>akustik</span>
                <div class='product-price'>Rp 7.990.000</div>
                <div class='product-stock'><i class='fas fa-box'></i> Stok: 4 unit</div>
                <div class='product-vendor'><i class='fas fa-store'></i> Vendor: Taylor Guitars</div>
                <div class='product-description'>Gitar mini dengan suara besar, cocok untuk perjalanan</div>
                <button class='buy-btn'>Tambahkan ke Keranjang</button>
            </div>
        </div>

        <!-- Produk 7 -->
        <div class='product-card' data-id="7" data-merk="Fender Telecaster" data-harga="11000000" data-jenis="elektrik" data-vendor="Fender Official">
            <div class='product-image'>
                <img src='7.png' alt='Fender Telecaster'>
            </div>
            <div class='product-info'>
                <div class='product-id'>ID: 7</div>
                <h3 class='product-merk'>Fender Telecaster</h3>
                <span class='product-type'>elektrik</span>
                <div class='product-price'>Rp 11.000.000</div>
                <div class='product-stock'><i class='fas fa-box'></i> Stok: 3 unit</div>
                <div class='product-vendor'><i class='fas fa-store'></i> Vendor: Fender Official</div>
                <div class='product-description'>Gitar elektrik legendaris dengan suara twang khas</div>
                <button class='buy-btn'>Tambahkan ke Keranjang</button>
            </div>
        </div>

        <!-- Produk 8 -->
        <div class='product-card' data-id="8" data-merk="Cordoba 15CM" data-harga="1350000" data-jenis="ukulele" data-vendor="Cordoba Music Group">
            <div class='product-image'>
                <img src='8.png' alt='Cordoba 15CM'>
            </div>
            <div class='product-info'>
                <div class='product-id'>ID: 8</div>
                <h3 class='product-merk'>Cordoba 15CM</h3>
                <span class='product-type'>ukulele</span>
                <div class='product-price'>Rp 1.350.000</div>
                <div class='product-stock'><i class='fas fa-box'></i> Stok: 7 unit</div>
                <div class='product-vendor'><i class='fas fa-store'></i> Vendor: Cordoba Music Group</div>
                <div class='product-description'>Ukulele concert dengan body mahogany dan suara hangat</div>
                <button class='buy-btn'>Tambahkan ke Keranjang</button>
            </div>
        </div>

        <!-- Produk 9 -->
        <div class='product-card' data-id="9" data-merk="Kala KA-15S" data-harga="9500000" data-jenis="ukulele" data-vendor="Kala Brand Music">
            <div class='product-image'>
                <img src='9.png' alt='Kala KA-15S'>
            </div>
            <div class='product-info'>
                <div class='product-id'>ID: 9</div>
                <h3 class='product-merk'>Kala KA-15S</h3>
                <span class='product-type'>ukulele</span>
                <div class='product-price'>Rp 9.500.000</div>
                <div class='product-stock'><i class='fas fa-box'></i> Stok: 9 unit</div>
                <div class='product-vendor'><i class='fas fa-store'></i> Vendor: Kala Brand Music</div>
                <div class='product-description'>Ukulele soprano ideal untuk pemula, suara cerah dan nyaring</div>
                <button class='buy-btn'>Tambahkan ke Keranjang</button>
            </div>
        </div>

        <?php
        include 'koneksi.php';

        // Query untuk produk ID 10 ke atas dari database
        $query = "SELECT * FROM produk WHERE id >= 10";
        $result = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Menambahkan atribut data untuk digunakan oleh JavaScript
                echo "<div class='product-card' ";
                echo "data-id='" . $row['id'] . "' ";
                echo "data-merk='" . htmlspecialchars($row['merk'], ENT_QUOTES) . "' ";
                echo "data-harga='" . $row['harga'] . "' ";
                echo "data-jenis='" . htmlspecialchars($row['jenis_gitar'], ENT_QUOTES) . "' ";
                echo "data-vendor='" . htmlspecialchars($row['vendor'], ENT_QUOTES) . "' ";
                echo ">";
                
                echo "<div class='product-image'>";
                if (!empty($row['data_gambar'])) {
                    echo "<img src='data:image/jpeg;base64," . base64_encode($row['data_gambar']) . "' alt='" . htmlspecialchars($row['merk'], ENT_QUOTES) . "'>";
                } else {
                    echo "<i class='fas fa-guitar'></i>";
                }
                echo "</div>";
                
                echo "<div class='product-info'>";
                echo "<div class='product-id'>ID: " . $row['id'] . "</div>";
                echo "<h3 class='product-merk'>" . htmlspecialchars($row['merk'], ENT_QUOTES) . "</h3>";
                echo "<span class='product-type'>" . htmlspecialchars($row['jenis_gitar'], ENT_QUOTES) . "</span>";
                echo "<div class='product-price'>Rp " . number_format($row['harga'], 0, ',', '.') . "</div>";
                echo "<div class='product-stock'><i class='fas fa-box'></i> Stok: " . $row['stok'] . " unit</div>";
                echo "<div class='product-vendor'><i class='fas fa-store'></i> Vendor: " . htmlspecialchars($row['vendor'], ENT_QUOTES) . "</div>";
                
                if (!empty($row['deskripsi_gambar'])) {
                    echo "<div class='product-description'>" . htmlspecialchars($row['deskripsi_gambar'], ENT_QUOTES) . "</div>";
                }
                
                echo "<button class='buy-btn'>Tambahkan ke Keranjang</button>";
                echo "</div>";
                echo "</div>";
            }
        }
        ?>
    </div>
</div>

<footer class="footer">
    &copy; <?php echo date('Y'); ?> mYGuitar | Marketplace - Temukan Gitar Impianmu
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const themeToggle = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;
        const themeIcon = themeToggle.querySelector('i');

        // Cek tema tersimpan
        const savedTheme = localStorage.getItem('myguitar-theme');
        if (savedTheme) {
            htmlElement.setAttribute('data-theme', savedTheme);
            updateIcon(savedTheme);
        }

        // Ganti ikon berdasarkan tema
        function updateIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        }

        // Toggle tema saat tombol diklik
        themeToggle.addEventListener('click', function () {
            const currentTheme = htmlElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('myguitar-theme', newTheme);
            updateIcon(newTheme);
        });

        // ========================
        // Tambahkan ke Keranjang
        // ========================
        const buttons = document.querySelectorAll('.buy-btn');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const card = button.closest('.product-card');
                const productId = card.getAttribute('data-id');
                const productMerk = card.getAttribute('data-merk');
                const productHarga = card.getAttribute('data-harga');
                const productJenis = card.getAttribute('data-jenis');
                const productVendor = card.getAttribute('data-vendor');
                
                // Log untuk debugging
                console.log('Menambahkan produk ke keranjang:', {
                    id: productId,
                    merk: productMerk,
                    harga: productHarga,
                    jenis: productJenis,
                    vendor: productVendor
                });

                // Kirim data ke simpan_keranjang.php
                fetch('simpan_keranjang.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: productId,
                        merk: productMerk,
                        harga: productHarga,
                        jenis: productJenis,
                        vendor: productVendor,
                        konfirmasi: 'belum'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Server response:', data);
                    if (data.status === 'success') {
                        alert('Produk berhasil ditambahkan ke keranjang!');
                    } else {
                        alert('Gagal menambahkan produk ke keranjang: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menambahkan produk: ' + error.message);
                });
            });
        });
    });
</script>


</body>
</html>