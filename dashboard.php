<?php
// Include file check_login untuk memastikan user sudah login
require_once 'check_login.php';

// Pastikan hanya user yang sudah login yang bisa mengakses halaman ini
requireLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - mYGuitar</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            min-height: 100vh;
        }
        
        .header {
            background-color: #2ecc71;
            color: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo h1 {
            font-size: 1.8rem;
            letter-spacing: 1px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info span {
            margin-right: 15px;
        }
        
        .logout-btn {
            padding: 8px 15px;
            background-color: white;
            color: #2ecc71;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background-color: #f9f9f9;
            transform: translateY(-2px);
        }
        
        .welcome-box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-top: 30px;
            text-align: center;
        }
        
        .welcome-box h2 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .welcome-box p {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>mY<span>Guitar</span></h1>
                </div>
                <div class="user-info">
                    <span>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a href="logout.php" class="logout-btn">Keluar</a>
                </div>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="welcome-box">
            <h2>Selamat Datang di mYGuitar</h2>
            <p>
                Ini adalah halaman dashboard Anda. Di sini Anda dapat mengelola semua aktivitas dan konten terkait gitar Anda.
                Fitur lengkap akan segera hadir untuk Anda.
            </p>
        </div>
    </div>
</body>
</html>