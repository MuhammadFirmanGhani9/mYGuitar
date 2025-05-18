<?php
// Koneksi ke database
$host = "localhost";
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$database = "mygitar"; // Ganti dengan nama database Anda

$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (mysqli_connect_errno()) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Inisialisasi variabel pesan
$pesan = "";
$bukti_pembayaran_terupload = false;

// Proses form jika ada data yang dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $merk = htmlspecialchars($_POST["merk"]);
    $jenis_gitar = htmlspecialchars($_POST["jenis_gitar"]);
    $harga = (int)$_POST["harga"];
    $stok = (int)$_POST["stok"];
    $vendor = htmlspecialchars($_POST["vendor"]);
    $deskripsi_gambar = htmlspecialchars($_POST["deskripsi_gambar"]);
    
    // Validasi data
    $valid = true;
    if (empty($merk) || empty($jenis_gitar) || empty($harga) || empty($stok) || empty($vendor)) {
        $pesan = "Error: Semua field harus diisi!";
        $valid = false;
    }

    // Proses upload gambar produk
    $data_gambar = null;
    if(isset($_FILES['data_gambar']) && $_FILES['data_gambar']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['data_gambar']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(!in_array(strtolower($ext), $allowed)) {
            $pesan = "Error: Format file tidak didukung. Gunakan format JPG, JPEG, PNG, atau GIF.";
            $valid = false;
        } else {
            $max_size = 2 * 1024 * 1024; // 2MB
            if($_FILES['data_gambar']['size'] > $max_size) {
                $pesan = "Error: Ukuran file terlalu besar. Maksimal 2MB.";
                $valid = false;
            } else {
                // Baca file gambar sebagai binary data
                $data_gambar = file_get_contents($_FILES['data_gambar']['tmp_name']);
                // Prepare binary data untuk query SQL
                $data_gambar = mysqli_real_escape_string($koneksi, $data_gambar);
            }
        }
    }

    // Proses upload bukti pembayaran (tidak disimpan ke database)
    if(isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
        $filename = $_FILES['bukti_pembayaran']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(!in_array(strtolower($ext), $allowed)) {
            $pesan = "Error: Format bukti pembayaran tidak didukung. Gunakan format JPG, JPEG, PNG, GIF, atau PDF.";
            $valid = false;
        } else {
            $max_size = 3 * 1024 * 1024; // 3MB
            if($_FILES['bukti_pembayaran']['size'] > $max_size) {
                $pesan = "Error: Ukuran bukti pembayaran terlalu besar. Maksimal 3MB.";
                $valid = false;
            } else {
                // Bukti pembayaran berhasil diunggah
                $bukti_pembayaran_terupload = true;
            }
        }
    }

    // Jika data valid, simpan ke database
    if ($valid) {
        // Cek apakah ada bukti pembayaran
        if (!$bukti_pembayaran_terupload) {
            $pesan = "Error: Anda harus mengunggah bukti pembayaran sebelum menjual produk!";
        } else {
            $query = "INSERT INTO produk (merk, jenis_gitar, harga, stok, vendor, data_gambar, deskripsi_gambar) 
                    VALUES ('$merk', '$jenis_gitar', $harga, $stok, '$vendor', '" . ($data_gambar ? $data_gambar : "NULL") . "', '$deskripsi_gambar')";
            
            if (mysqli_query($koneksi, $query)) {
                $pesan = "Produk berhasil ditambahkan! Bukti pembayaran telah diterima.";
            } else {
                $pesan = "Error: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - mYGuitar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --color-primary: #10B981; /* Hijau */
            --color-secondary: #111827; /* Hitam */
            --color-tertiary: #FFFFFF; /* Putih */
        }
    </style>
    <script>
        function handleFileUpload() {
            const fileInput = document.getElementById('bukti_pembayaran');
            const submitButton = document.getElementById('submit_button');
            
            if (fileInput.files.length > 0) {
                // Jika file telah dipilih, aktifkan tombol dan ubah warna menjadi hijau
                submitButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                submitButton.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                submitButton.disabled = false;
            } else {
                // Jika tidak ada file, tombol tetap dinonaktifkan dan berwarna abu-abu
                submitButton.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                submitButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                submitButton.disabled = true;
            }
        }
        
        function bayarKomisi() {
            // Redirect ke halaman komisi.html
            window.location.href = 'komisi.php';
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-emerald-900 to-gray-900">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <div class="flex items-center justify-center mb-6">
                <h1 class="text-3xl font-bold text-emerald-600">mYGuitar</h1>
            </div>
            
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Tambah Produk Baru</h2>
            
            <?php if (!empty($pesan)) : ?>
                <div class="mb-4 p-3 <?php echo strpos($pesan, "Error") !== false ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?> rounded">
                    <?php echo $pesan; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="merk" class="block text-sm font-medium text-gray-700 mb-1">Merk Gitar</label>
                    <input type="text" id="merk" name="merk" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                
                <div>
                    <label for="jenis_gitar" class="block text-sm font-medium text-gray-700 mb-1">Jenis Gitar</label>
                    <select id="jenis_gitar" name="jenis_gitar" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Pilih Jenis Gitar</option>
                        <option value="Akustik">Akustik</option>
                        <option value="Elektrik">Elektrik</option>
                        <option value="transkustik">Transkustik</option>
                        <option value="Ukulele">Ukulele</option>
                    </select>
                </div>
                
                <div>
                    <label for="harga" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" id="harga" name="harga" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                
                <div>
                    <label for="stok" class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                    <input type="number" id="stok" name="stok" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                
                <div>
                    <label for="vendor" class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                    <input type="text" id="vendor" name="vendor" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <p class="text-xs text-gray-500 mt-1">Harap masukkan nama vendor sesuai dengan username anda, Tindakan penyalahggunaan nama vendor akan berakibat pada penghapusan produk pada marketplace atas nama vendor terkait tindakan penipuan tanpa pengembalian dana komisi.</p>
                </div>
                
                <!-- Input untuk upload gambar -->
                <div>
                    <label for="data_gambar" class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
                    <input type="file" id="data_gambar" name="data_gambar" accept="image/jpeg,image/png,image/gif" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maks: 2MB</p>
                </div>
                
                <!-- Input untuk deskripsi gambar -->
                <div>
                    <label for="deskripsi_gambar" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Gambar</label>
                    <textarea id="deskripsi_gambar" name="deskripsi_gambar" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
                
                <!-- Tombol Bayar Komisi -->
                <div class="pt-3 border-t border-gray-200">
                    <button type="button" onclick="bayarKomisi()" class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Bayar Komisi
                    </button>
                    <p class="text-xs text-gray-500 mt-1">Bayar komisi Rp45.000,00 per produk</p>
                </div>

                <!-- Input untuk upload bukti pembayaran (tidak disimpan ke database) -->
                <div>
                    <label for="bukti_pembayaran" class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti Pembayaran</label>
                    <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/jpeg,image/png,image/gif,application/pdf" onchange="handleFileUpload()" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF, PDF. Maks: 3MB</p>
                </div>
                
                <div class="pt-4">
                    <button type="submit" id="submit_button" class="w-full px-4 py-2 bg-gray-400 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors cursor-not-allowed" disabled>
                        Jual Produk
                    </button>
                    <p class="text-xs text-gray-500 mt-1 text-center">Unggah bukti pembayaran untuk mengaktifkan tombol ini</p>
                </div>

                <div class="pt-2">
                    <a href="vendor.php" class="block w-full px-4 py-2 bg-gray-800 text-white text-center font-medium rounded-md hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>