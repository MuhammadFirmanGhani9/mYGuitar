<?php
include 'koneksi.php';

// Ambil data dari tabel daftar
$query = "SELECT * FROM daftar";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Member Terdaftar - mYGuitar</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              light: '#4ade80', // Light green
              DEFAULT: '#22c55e', // Green
              dark: '#16a34a', // Dark green
            }
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
  <!-- Header -->
  <header class="bg-black text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <h1 class="text-2xl font-bold text-primary-light">mYGuitar</h1>
      <a href="admin.php" class="flex items-center bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md transition duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke Admin
      </a>
    </div>
  </header>

  <!-- Main Content -->
  <main class="container mx-auto my-8 px-4 flex-grow">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-800">Daftar Member</h2>
      </div>
      
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-800 text-white">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Username</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Created At</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Updated At</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $row['id']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row['username']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $row['created_at']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $row['updated_at']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <a href="hapus_member.php?id=<?= $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus member ini?')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md inline-block transition duration-300">
                    Hapus Membership
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-black text-white p-4 mt-auto">
    <div class="container mx-auto text-center">
      <p>&copy; <?= date('Y'); ?> mYGuitar. Hak Cipta Dilindungi.</p>
    </div>
  </footer>
</body>
</html>