<?php
// login.php

// 1. MEMULAI SESI DAN KONFIGURASI
session_start();

// Menyertakan file koneksi database (menggunakan PDO)
// Pastikan file db_connect.php ada di direktori yang sama
include '../assets/php/db_connect.php';

// Variabel untuk Pesan Status
$errorMessage = '';
$successMessage = '';
$showError = 'hidden';
$showSuccess = 'hidden';


// 2. LOGIKA PEMROSESAN FORM
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $input = htmlspecialchars($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  // Cek apakah input tidak kosong
  if (empty($input) || empty($password)) {
    $errorMessage = 'Silakan isi email/username/nama dan password.';
    $showError = '';
  } else {
    try {
      $pdo = Database::getConnection();

      // QUERY UNTUK CEK DI DUA TABEL (users dan member)
      // Users: login dengan email atau username
      // Members: login dengan email atau nama
      $sql = "
                SELECT id AS user_id, username AS identity, password, role FROM public.users 
                WHERE email = :input OR username = :input
                UNION ALL
                SELECT id_member AS user_id, 
                       CASE 
                           WHEN email = :input THEN email 
                           ELSE nama 
                       END AS identity, 
                       password, 
                       'member' AS role 
                FROM public.member 
                WHERE email = :input OR nama = :input
            ";

      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':input', $input, PDO::PARAM_STR);
      $stmt->execute();

      $user = $stmt->fetch();

      // Verifikasi Password
      if ($user) {
        $is_password_valid = false;
        
        // Check password hashing method and verify accordingly
        if ($user['role'] === 'member') {
          // Members use MD5 hashing
          $is_password_valid = (md5($password) === $user['password']);
        } else {
          // Users (admin/editor) use password_hash()
          $is_password_valid = password_verify($password, $user['password']);
        }
        
        if ($is_password_valid) {
          // Simpan data ke Sesi
          $_SESSION['loggedin'] = true;
          $_SESSION['user_id'] = $user['user_id']; // Mengambil ID (bisa id_user atau id_member)
          $_SESSION['username'] = $user['identity']; // Mengambil Email/Username
          $_SESSION['role'] = $user['role'];

          $successMessage = 'Login berhasil! Mengarahkan...';
          $showSuccess = '';

          // Redirect sesuai role
          if ($user['role'] === 'admin') {
            header("refresh:1;url=admin-dashboard.php");
          } elseif ($user['role'] === 'editor') {
            header("refresh:1;url=editor-dashboard.php");
          } else {
            header("refresh:1;url=member-dashboard.php");
          }
        } else {
          $errorMessage = 'Email/Username/Nama atau password salah.';
          $showError = '';
        }
      } else {
        $errorMessage = 'Email/Username/Nama atau password salah.';
        $showError = '';
      }
    } catch (PDOException $e) {
      // Tangani error koneksi atau query database
      // Di lingkungan produksi, log error ini, jangan tampilkan ke pengguna.
      error_log("Login PDO Error: " . $e->getMessage());
      $errorMessage = "Terjadi masalah sistem. Silakan coba lagi nanti. (Kode: " . $e->getCode() . ")";
      $showError = '';
    }
  }
}

// Get logo from database settings
$logo_path = '../assets/img/logo.png'; // Default fallback
try {
  $pdo = Database::getConnection();
  $stmt = $pdo->prepare("SELECT value FROM vw_settings_users WHERE key = 'logo_utama'");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($result && !empty($result['value']) && file_exists($result['value'])) {
    $logo_path = $result['value'];
  }
} catch (Exception $e) {
  // Keep default logo if there's an error
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal Internal – Lab Data Technologies</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Styling tambahan untuk gradient text dan accent color */
    .text-gradient {
      background-image: linear-gradient(to right, #00A0D6, #6AC259);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .gradient-accent {
      background-image: linear-gradient(to right, #00A0D6, #6AC259);
    }
  </style>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#00A0D6',
            secondary: '#6AC259',
          }
        }
      }
    }
  </script>
</head>

<body class="bg-white text-gray-900" style="font-family: 'Inter', sans-serif;">
  <section class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 relative overflow-hidden">
    <div class="absolute inset-0 opacity-5">
      <div class="absolute top-20 left-20 w-64 h-64 bg-gradient-to-br from-[#00A0D6] to-[#6AC259] rounded-full blur-3xl"></div>
      <div class="absolute bottom-20 right-20 w-96 h-96 bg-gradient-to-br from-[#6AC259] to-[#00A0D6] rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 min-h-screen flex items-center justify-center px-6 lg:px-8">
      <div class="w-full max-w-6xl">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
          <div class="text-center lg:text-left">

            <div class="mb-12">
              <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                Selamat Datang di<br>
                <span class="text-gradient">Portal Internal</span>
              </h1>

              <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                Akses eksklusif untuk anggota laboratorium. Kelola penelitian, publikasi, dan kolaborasi dalam satu platform terintegrasi.
              </p>

            </div>
          </div>

          <div class="max-w-md mx-auto w-full">
            <div class="bg-white/80 backdrop-blur rounded-3xl shadow-xl border border-gray-100 p-8">
              <div class="text-center mb-4">
                <span class="inline-flex h-12 w-12 rounded-xl to-blue-600 items-center justify-center group-hover:shadow-xl transition-all duration-300">
                  <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Lab Data Technologies Logo" class="w-full h-full object-cover rounded-xl">
                </span>
              </div>

              <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Masuk ke Portal</h2>
                <p class="text-gray-600">Laboratorium Data Technologies</p>
                <p class="text-sm text-gray-500 mt-1">Politeknik Negeri Malang</p>
              </div>

              <form id="loginForm" method="POST" action="login.php" class="space-y-6">
                <div class="space-y-4">
                  <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email, Username, atau Nama</label>
                    <input id="email"
                      name="email"
                      type="text"
                      placeholder="Masukkan email, username, atau nama"
                      class="input-focus w-full h-12 border border-gray-200 rounded-xl px-4 focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                      required>
                  </div>

                  <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input id="password"
                      name="password"
                      type="password"
                      placeholder="Masukkan password"
                      class="input-focus w-full h-12 border border-gray-200 rounded-xl px-4 focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                      required>
                  </div>
                </div>

                <button type="submit"
                  id="loginBtn"
                  class="w-full h-12 gradient-accent text-white font-semibold rounded-xl hover:scale-[1.02] hover:shadow-lg transition-all duration-200">
                  <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Masuk ke Portal
                  </span>
                </button>
              </form>

              <div id="loginError" class="<?php echo $showError; ?> mt-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span class="text-sm text-red-700" id="loginErrorMessage"><?php echo $errorMessage ?: 'Email/Username/Nama atau Password salah'; ?></span>
                </div>
              </div>

              <div id="loginSuccess" class="<?php echo $showSuccess; ?> mt-4 p-3 bg-green-50 border border-green-200 rounded-xl">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <span class="text-sm text-green-700" id="loginSuccessMessage"><?php echo $successMessage ?: 'Login berhasil, mengarahkan...'; ?></span>
                </div>
              </div>

              <div id="loginLoading" class="hidden mt-4 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                <div class="flex items-center">
                  <svg class="animate-spin w-5 h-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span class="text-sm text-blue-700">Memproses login...</span>
                </div>
              </div>

              <div class="mt-8 text-center">
                <a href="../index.html" class="inline-flex items-center text-gray-600 hover:text-primary transition-colors text-sm font-medium">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  Kembali ke Situs Utama
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>

</html>