<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Komisi - mYGuitar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --color-primary: #10B981; /* Hijau */
            --color-secondary: #111827; /* Hitam */
            --color-tertiary: #FFFFFF; /* Putih */
        }
        .qr-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 63vh; /* agar tinggi kontainer full layar */
        background-color: #f9f9f9; /* opsional, buat latar terang */
        }

        .qr-img {
        width: 300px;
        max-width: 90%;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-emerald-900 to-gray-900">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <div class="flex items-center justify-center mb-6">
                <h1 class="text-3xl font-bold text-emerald-600">mYGuitar</h1>
            </div>
            
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Pembayaran Komisi</h2>
            
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h3 class="font-semibold text-lg text-gray-800 mb-3">Informasi Pembayaran</h3>
                <p class="text-sm text-gray-600 mb-3">Silakan transfer komisi marketplace dengan rincian sebagai berikut:</p>
                
                <div class="space-y-2 bg-white p-3 rounded border border-gray-200">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nominal Komisi:</span>
                        <span class="font-semibold">Rp 45.000</span>
                    </div>
                </div>
            </div>
            <div class="qr-container">
            <img src="qr.jpg" alt="QR DANA" class="qr-img">
            </div>


            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 mb-3">Setelah melakukan pembayaran, silakan lanjut ke halaman tambah produk dan upload bukti pembayaran Anda.</p>
                </div>
                
                <a href="tambah_produk.php" class="block w-full px-4 py-2 bg-emerald-600 text-white text-center font-medium rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
                    Kembali ke Tambah Produk
                </a>
            </div>
        </div>
    </div>
</body>
</html>