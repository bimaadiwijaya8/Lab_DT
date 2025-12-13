<?php
// admin-dashboard.php (All-in-One: Dashboard Ringkasan + Kelola Berita + Kelola Galeri)

// --- Bagian Logika PHP Awal ---
session_start(); 
$is_authenticated = true; // Ganti dengan logika otentikasi sesungguhnya (misal: isset($_SESSION['user_id']))

if (!$is_authenticated) {
    header('Location: login.php'); 
    exit;
}

// 1. Variabel Konfigurasi Dasar
$current_year = date('Y');
$username = "AdminLDT"; // Ganti dengan nama user yang login
$active_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard'; 
$admin_user_id = 1; // HARDCODED: Ganti dengan ID user yang login (untuk kolom 'author' pada tabel berita)
$message = ''; // Untuk notifikasi sukses/gagal

// 2. Koneksi Database
$pdo = null;
$db_error = false;
$db_connect_path = '../assets/php/db_connect.php'; 

if (file_exists($db_connect_path)) {
    require_once $db_connect_path;
    
    try {
        // Panggil metode static dari class Database
        $pdo = Database::getConnection(); 
        
    } catch (PDOException $e) {
        $db_error = true;
        // Tampilkan pesan error detail dari database
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                        Kesalahan Koneksi Database: <b>PDOException</b>. Pastikan host, port, dbname, user, dan password di <b>db_connect.php</b> sudah benar. 
                        Detail Error: " . htmlspecialchars($e->getMessage()) . "
                    </div>";
        $pdo = null;
    } catch (Exception $e) {
        $db_error = true;
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                        Kesalahan Sistem: " . htmlspecialchars($e->getMessage()) . "
                    </div>";
        $pdo = null;
    }
} else {
    $db_error = true;
    $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Kesalahan Koneksi: File '{$db_connect_path}' tidak ditemukan. CRUD tidak dapat berjalan.</div>";
}

// 3. Penanganan Operasi CRUD Berita (Hanya jika koneksi berhasil)
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $active_page === 'berita') {
    $action = $_POST['action'] ?? '';

    // --- CREATE (Tambah Berita Baru) ---
    if ($action === 'add_news') {
        $judul = trim($_POST['judul']);
        $informasi = trim($_POST['informasi']);
        $tanggal = trim($_POST['tanggal']);
        
        $upload_ok = true;
        $gambar_path_for_db = '';
        $upload_message = '';

        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
            
            // 1. Tentukan direktori dan nama file
            $target_dir = '../assets/img/berita/';
            // Pastikan folder target ada dan memiliki izin tulis (write permission)
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true); 
            }
            
            // Ambil nama file dan buat target path lengkap
            $file_name = basename($_FILES['gambar']['name']);
            // Untuk keamanan, ganti spasi/karakter spesial dengan underscore
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name); 
            $target_file = $target_dir . $safe_file_name;
            $gambar_path_for_db = $target_file; 
            
            // Cek jika nama file sudah ada, tambahkan timestamp
            if (file_exists($target_file)) {
                $unique_name = time() . '_' . $safe_file_name;
                $target_file = $target_dir . $unique_name;
                $gambar_path_for_db = $target_file;
            }

            // 2. Lakukan proses upload
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $upload_ok = false;
                $upload_message = "Gagal mengupload gambar. Pastikan folder '{$target_dir}' memiliki izin tulis (0777).";
            }
        } else if ($_FILES['gambar']['error'] === UPLOAD_ERR_NO_FILE) {
             $upload_ok = false;
             $upload_message = "Harap unggah gambar utama untuk berita ini.";
        } else {
             $upload_ok = false;
             $upload_message = "Terjadi error saat upload file. Kode error: " . $_FILES['gambar']['error'];
        }
        
        // 3. Simpan ke database jika upload berhasil
        if ($upload_ok) {
            try {
                $sql = "INSERT INTO berita (judul, gambar, informasi, tanggal, author, status) 
                        VALUES (:judul, :gambar, :informasi, :tanggal, :author, 'pending')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':judul' => $judul,
                    ':gambar' => $gambar_path_for_db, // Path yang sudah tersimpan di server
                    ':informasi' => $informasi,
                    ':tanggal' => $tanggal,
                    ':author' => $admin_user_id 
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Berita baru berhasil ditambahkan!</div>";
            } catch (Exception $e) {
                // Jika gagal simpan DB, hapus file yang sudah terupload (optional cleanup)
                if (file_exists($gambar_path_for_db)) {
                    @unlink($gambar_path_for_db);
                }
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan berita (DB Error): " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
             $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan berita (Upload Error): {$upload_message}</div>";
        }
    }

    // --- UPDATE (Edit Berita) ---
    if ($action === 'edit_news') {
        $id_berita = (int)$_POST['id_berita'];
        $judul = trim($_POST['judul']);
        $informasi = trim($_POST['informasi']);
        $tanggal = trim($_POST['tanggal']);
        
        $new_gambar_name = $_FILES['gambar']['name'] ?? '';
        $current_gambar_path = $_POST['current_gambar']; // Path gambar lama
        $gambar_path_for_db = $current_gambar_path;      // Default: gunakan gambar lama
        $upload_ok = true;
        
        // Cek apakah ada file baru yang diupload
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK && !empty($new_gambar_name)) {
            
            $target_dir = '../assets/img/berita/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true); 
            }
            
            $file_name = basename($_FILES['gambar']['name']);
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name); 
            $target_file = $target_dir . $safe_file_name;
            
            if (file_exists($target_file)) {
                $unique_name = time() . '_' . $safe_file_name;
                $target_file = $target_dir . $unique_name;
            }

            // Lakukan proses upload file baru
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $gambar_path_for_db = $target_file; 
                
                // Opsional: Hapus gambar lama di server
                if ($current_gambar_path && file_exists($current_gambar_path)) {
                    // Pastikan file lama adalah file yang valid dan bukan folder
                    @unlink($current_gambar_path); 
                }
            } else {
                $upload_ok = false;
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupload gambar baru. Perubahan DB dibatalkan.</div>";
            }
        }
        
        // Lakukan update DB hanya jika tidak ada error upload fatal
        if ($upload_ok) {
            try {
                $sql = "UPDATE berita SET 
                            judul = :judul, 
                            informasi = :informasi, 
                            tanggal = :tanggal, 
                            gambar = :gambar,
                            status = 'pending' 
                        WHERE id_berita = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':judul' => $judul,
                    ':informasi' => $informasi,
                    ':tanggal' => $tanggal,
                    ':gambar' => $gambar_path_for_db, // Path baru atau lama
                    ':id' => $id_berita
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Berita ID {$id_berita} berhasil diupdate!</div>";
            } catch (Exception $e) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupdate berita: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}

// --- DELETE (Hapus Berita - Menggunakan GET request) ---
if ($pdo && $active_page === 'berita' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_berita = (int)$_GET['id'];
    try {
        $sql = "DELETE FROM berita WHERE id_berita = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_berita]);
        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Berita ID {$id_berita} berhasil dihapus!</div>";
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menghapus berita: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    // Redirect untuk menghilangkan parameter GET dari URL
    header("Location: admin-dashboard.php?page=berita");
    exit;
}


// 4. Data Dashboard (Ringkasan - Diambil dari DB atau default 0)
$total_news = 0; 
$total_users = 0; 
$total_pages = 0; 

if ($pdo) {
    try {
        // Query COUNT untuk Dashboard
        $total_news = $pdo->query("SELECT COUNT(id_berita) FROM berita")->fetchColumn();
        // ASUMSI: Tabel Anggota adalah 'anggota'
        $total_users = $pdo->query("SELECT COUNT(id_anggota) FROM anggota")->fetchColumn(); 
        $total_pages = 8; // Nilai statis sementara
    } catch (Exception $e) {
        // Biarkan nilai tetap 0 jika query count gagal
    }
}

// 5. Data Berita (READ - Diambil dari DB jika koneksi berhasil)
$news_data = [];
$galeri_data = []; 

if ($active_page === 'berita' && $pdo) {
    try {
        // READ: Mengambil semua data berita dari database
        $sql = "SELECT id_berita, judul, tanggal, informasi, gambar, author, status 
                FROM berita 
                ORDER BY tanggal DESC";
        $stmt = $pdo->query($sql);
        $news_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Kesalahan Query Database saat mengambil data berita: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
} 

// 6. Pengaturan Judul Halaman
$page_title = "Admin Panel - ";
switch ($active_page) {
    case 'verifikasi-member':
        $page_title .= "Verifikasi Member";
        break;
    case 'persetujuan-konten':
        $page_title .= "Persetujuan Konten";
        break;
    case 'berita':
        $page_title .= "Kelola Berita";
        break;
    case 'publikasi':
        $page_title .= "Kelola Publikasi";
        break;
    case 'agenda':
        $page_title .= "Kelola Agenda";
        break;
    case 'galeri':
        $page_title .= "Kelola Galeri";
        break;
    case 'anggota':
        $page_title .= "Kelola Anggota";
        break;
    case 'fasilitas':
        $page_title .= "Kelola Fasilitas";
        break;
    case 'pengumuman':
        $page_title .= "Kelola Pengumuman";
        break;
    case 'edit-halaman':
        $page_title .= "Edit Halaman";
        break;
    default:
        $page_title .= "Dashboard Utama";
}
// --- Akhir Bagian Logika PHP Awal ---
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
      #sidebar.translate-x-0 {
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
      }
      .modal {
        transition: opacity 0.3s ease, visibility 0.3s ease;
      }
    </style>
</head>
<body class="bg-gray-50">
    
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out" id="sidebar">
        <div class="flex items-center justify-center h-20 bg-primary">
            <div class="flex items-center space-x-3">
                <h1 class="text-white text-2xl font-bold">Admin Panel</h1>
            </div>
        </div>
        
        <nav class="p-4 space-y-2">
            <a href="admin-dashboard.php?page=dashboard" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'dashboard' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                <i class="fas fa-tachometer-alt w-5 h-5 mr-3 <?php echo ($active_page === 'dashboard' ? 'text-primary' : ''); ?>"></i>
                Dashboard
            </a>
            <a href="admin-dashboard.php?page=verifikasi-member" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'verifikasi-member' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                <i class="fas fa-user-check w-5 h-5 mr-3 <?php echo ($active_page === 'verifikasi-member' ? 'text-primary' : ''); ?>"></i>
                Verifikasi Member
            </a>
            <a href="admin-dashboard.php?page=persetujuan-konten" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'persetujuan-konten' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                <i class="fas fa-check-circle w-5 h-5 mr-3 <?php echo ($active_page === 'persetujuan-konten' ? 'text-primary' : ''); ?>"></i>
                Persetujuan Konten
            </a>
            <a href="admin-dashboard.php?page=berita" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'berita' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                <i class="fas fa-newspaper w-5 h-5 mr-3 <?php echo ($active_page === 'berita' ? 'text-primary' : ''); ?>"></i>
                Kelola Berita
            </a>
            <a href="admin-dashboard.php?page=publikasi" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'publikasi' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                <i class="fas fa-book w-5 h-5 mr-3 <?php echo ($active_page === 'publikasi' ? 'text-primary' : ''); ?>"></i>
                Kelola Publikasi
            </a>
            <a href="admin-dashboard.php?page=agenda" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'agenda' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                <i class="fas fa-calendar-alt w-5 h-5 mr-3 <?php echo ($active_page === 'agenda' ? 'text-primary' : ''); ?>"></i>
                Kelola Agenda
            </a>
            <a href="admin-dashboard.php?page=galeri" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'galeri' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                <i class="fas fa-image w-5 h-5 mr-3 <?php echo ($active_page === 'galeri' ? 'text-primary' : ''); ?>"></i>
                Kelola Galeri
            </a>
            <a href="admin-dashboard.php?page=anggota" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'anggota' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                <i class="fas fa-users w-5 h-5 mr-3 <?php echo ($active_page === 'anggota' ? 'text-primary' : ''); ?>"></i>
                Kelola Anggota
            </a>
            <a href="admin-dashboard.php?page=fasilitas" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'fasilitas' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                <i class="fas fa-building w-5 h-5 mr-3 <?php echo ($active_page === 'fasilitas' ? 'text-primary' : ''); ?>"></i>
                Kelola Fasilitas
            </a>
            <a href="admin-dashboard.php?page=pengumuman" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'pengumuman' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                <i class="fas fa-bullhorn w-5 h-5 mr-3 <?php echo ($active_page === 'pengumuman' ? 'text-primary' : ''); ?>"></i>
                Kelola Pengumuman
            </a>
            <a href="admin-dashboard.php?page=edit-halaman" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'edit-halaman' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                <i class="fas fa-edit w-5 h-5 mr-3 <?php echo ($active_page === 'edit-halaman' ? 'text-primary' : ''); ?>"></i>
                Edit Halaman
            </a>
        </nav>
        
        <div class="absolute bottom-0 w-full p-4">
            <a href="logout.php" class="flex items-center p-3 text-red-600 bg-red-50 rounded-lg font-semibold hover:bg-red-100 transition-colors duration-200">
                <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                Logout
            </a>
        </div>
    </div>
    
    <div class="lg:ml-64 transition-all duration-300 ease-in-out p-4 md:p-8">
        
        <header class="flex items-center justify-between bg-white p-4 shadow-md rounded-xl mb-8">
            <button class="lg:hidden text-gray-600 hover:text-primary transition-colors" onclick="toggleSidebar()">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <h1 class="text-2xl font-semibold text-gray-800 hidden lg:block"><?php echo $page_title; ?></h1>
            
            <div class="flex items-center space-x-4">
                <div class="text-gray-600">Selamat datang, 
                    <span class="font-medium text-primary"><?php echo $username; ?></span>
                </div>
                <div class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-primary font-bold">
                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                </div>
            </div>
        </header>
        
        <?php echo $message; // Menampilkan pesan notifikasi, termasuk error DB ?>

        <?php if ($active_page === 'dashboard'): ?>
            <h2 class="text-xl font-bold text-gray-800 mb-4">Ringkasan Statistik Laboratorium</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 transition-transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Berita</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo number_format($total_news); ?></p>
                        </div>
                        <div class="p-3 bg-primary/10 rounded-full text-primary">
                            <i class="fas fa-newspaper text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 transition-transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pengguna</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo number_format($total_users); ?></p>
                        </div>
                        <div class="p-3 bg-secondary/10 rounded-full text-secondary">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 transition-transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Halaman Publik</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo number_format($total_pages); ?></p>
                        </div>
                        <div class="p-3 bg-purple-500/10 rounded-full text-purple-600">
                            <i class="fas fa-pager text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 min-h-64 flex flex-col items-center justify-center">
                <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-600 font-semibold text-lg">Area Grafik & Analitik</p>
                <p class="text-sm text-gray-500">Tambahkan grafik performa di sini.</p>
            </div>

        <?php elseif ($active_page === 'berita'): ?>
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Daftar Semua Berita</h2>
                    <button onclick="openAddNewsModal()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center space-x-2" <?php echo ($db_error ? 'disabled title="Koneksi DB Gagal"' : ''); ?>>
                        <i class="fas fa-plus"></i>
                        <span>Tambah Baru</span>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Informasi (Konten)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($news_data)): ?>
                                <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    <?php echo ($db_error ? 'Gagal mengambil data karena masalah koneksi database.' : 'Belum ada data berita yang tersedia.'); ?>
                                </td></tr>
                            <?php else: ?>
                                <?php foreach ($news_data as $news): ?>
                                    <tr class="hover:bg-gray-50" 
                                        data-news-id="<?php echo $news['id_berita']; ?>" 
                                        data-judul="<?php echo htmlspecialchars($news['judul']); ?>" 
                                        data-informasi="<?php echo htmlspecialchars($news['informasi']); ?>" 
                                        data-tanggal="<?php echo $news['tanggal']; ?>" 
                                        data-gambar="<?php echo htmlspecialchars($news['gambar']); ?>">
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $news['id_berita']; ?></td>
                                        <td class="px-6 py-4 whitespace-normal text-sm font-medium text-gray-900 w-48"><?php echo htmlspecialchars($news['judul']); ?></td>
                                        <td class="px-6 py-4 whitespace-normal text-sm text-gray-500 truncate max-w-sm" title="<?php echo htmlspecialchars($news['informasi']); ?>"><?php echo substr(strip_tags($news['informasi']), 0, 100) . '...'; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d M Y', strtotime($news['tanggal'])); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo ($news['status'] === 'approved' ? 'bg-green-100 text-green-800' : ($news['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')); ?>">
                                                <?php echo ucfirst($news['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button onclick="openEditNewsModal(<?php echo $news['id_berita']; ?>)" class="text-primary hover:text-blue-800 mr-3" <?php echo ($db_error ? 'disabled' : ''); ?>>Edit</button>
                                            <a href="admin-dashboard.php?page=berita&action=delete&id=<?php echo $news['id_berita']; ?>" onclick="return confirm('Anda yakin ingin menghapus berita: <?php echo htmlspecialchars($news['judul']); ?>?')" class="text-red-600 hover:text-red-900" <?php echo ($db_error ? 'onclick="return false;"' : ''); ?>>Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($active_page === 'galeri'): ?>
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Daftar Foto Galeri</h2>
                    <button onclick="openAddGaleriModal()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Foto</span>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50"> 
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama/Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Foto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Anggota ID</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($galeri_data)): ?>
                            <tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada foto di galeri.</td></tr>
                            <?php else: ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($active_page === 'verifikasi-member'): ?>
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Verifikasi Member</h2>
                    <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-sync"></i>
                        <span>Refresh Data</span>
                    </button>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-blue-800"><i class="fas fa-info-circle mr-2"></i>Halaman untuk verifikasi pendaftaran member baru yang menunggu persetujuan.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada member yang menunggu verifikasi.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($active_page === 'persetujuan-konten'): ?>
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Persetujuan Konten</h2>
                    <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-sync"></i>
                        <span>Refresh Data</span>
                    </button>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <p class="text-yellow-800"><i class="fas fa-exclamation-triangle mr-2"></i>Halaman untuk menyetujui atau menolak konten yang diajukan oleh editor.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Konten</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengaju</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada konten yang menunggu persetujuan.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($active_page === 'publikasi'): ?>
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Kelola Publikasi</h2>
                    <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Publikasi</span>
                    </button>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-green-800"><i class="fas fa-book mr-2"></i>Kelola jurnal, paper, dan publikasi ilmiah laboratorium.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data publikasi.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($active_page === 'agenda'): ?>
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Kelola Agenda</h2>
                    <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Agenda</span>
                    </button>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                    <p class="text-purple-800"><i class="fas fa-calendar-alt mr-2"></i>Kelola jadwal kegiatan, seminar, dan acara laboratorium.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Agenda</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada agenda terjadwal.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($active_page === 'anggota'): ?>
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Kelola Anggota</h2>
                    <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-user-plus"></i>
                        <span>Tambah Anggota</span>
                    </button>
                </div>
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
                    <p class="text-indigo-800"><i class="fas fa-users mr-2"></i>Kelola data anggota laboratorium, peneliti, dan staf.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posisi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data anggota.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($active_page === 'fasilitas'): ?>
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Kelola Fasilitas</h2>
                    <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Fasilitas</span>
                    </button>
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                    <p class="text-orange-800"><i class="fas fa-building mr-2"></i>Kelola data fasilitas, laboratorium, dan peralatan penelitian.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Fasilitas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapasitas</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data fasilitas.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($active_page === 'pengumuman'): ?>
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Kelola Pengumuman</h2>
                    <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Pengumuman</span>
                    </button>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-red-800"><i class="fas fa-bullhorn mr-2"></i>Kelola pengumuman penting untuk seluruh anggota laboratorium.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Isi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada pengumuman.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($active_page === 'edit-halaman'): ?>
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Edit Halaman</h2>
                    <button class="px-4 py-2 bg-secondary text-white rounded-lg hover:bg-green-600 transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-save"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                    <p class="text-gray-800"><i class="fas fa-edit mr-2"></i>Edit konten halaman statis seperti About, Contact, dll.</p>
                </div>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Halaman</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            <option>Halaman Utama (Home)</option>
                            <option>Tentang Kami (About)</option>
                            <option>Kontak (Contact)</option>
                            <option>Layanan (Services)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Halaman</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary" placeholder="Masukkan judul halaman">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konten Halaman</label>
                        <textarea rows="10" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary" placeholder="Masukkan konten halaman"></textarea>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <div id="add-news-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Tambah Berita Baru</h3>
                <button type="button" onclick="closeAddNewsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="admin-dashboard.php?page=berita" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_news">
                
                <div class="mb-4">
                    <label for="judul" class="block text-sm font-medium text-gray-700">Judul Berita</label>
                    <input type="text" id="judul" name="judul" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                </div>
                <div class="mb-4">
                    <label for="informasi" class="block text-sm font-medium text-gray-700">Isi Berita (Informasi)</label>
                    <textarea id="informasi" name="informasi" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                        <input type="date" id="tanggal" name="tanggal" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar Utama (.jpg/.png)</label>
                        <input type="file" id="gambar" name="gambar" accept="image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeAddNewsModal()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium"> Batal </button>
                    <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 font-medium shadow-md"> Simpan Berita </button>
                </div>
            </form>
        </div>
    </div>

    <div id="edit-news-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Edit Berita</h3>
                <button type="button" onclick="closeEditNewsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="edit-news-form" action="admin-dashboard.php?page=berita" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_news">
                <input type="hidden" name="id_berita" id="edit_id_berita">
                <input type="hidden" name="current_gambar" id="edit_current_gambar">
                
                <div class="mb-4">
                    <label for="edit_judul" class="block text-sm font-medium text-gray-700">Judul Berita</label>
                    <input type="text" id="edit_judul" name="judul" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                </div>
                <div class="mb-4">
                    <label for="edit_informasi" class="block text-sm font-medium text-gray-700">Isi Berita (Informasi)</label>
                    <textarea id="edit_informasi" name="informasi" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="edit_tanggal" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                        <input type="date" id="edit_tanggal" name="tanggal" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label for="edit_gambar" class="block text-sm font-medium text-gray-700">Ganti Gambar (Kosongkan jika tidak diubah)</label>
                        <input type="file" id="edit_gambar" name="gambar" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                        <p class="text-xs text-gray-500 mt-1">Gambar saat ini: <span id="current-image-name" class="font-semibold"></span></p>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeEditNewsModal()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium"> Batal </button>
                    <button type="submit" class="px-6 py-3 bg-secondary text-white rounded-lg hover:bg-green-600 transition-colors duration-200 font-medium shadow-md"> Simpan Perubahan </button>
                </div>
            </form>
        </div>
    </div>

    <div id="add-galeri-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Tambah Foto Galeri</h3>
                <button onclick="closeAddGaleriModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="#" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="galeri_nama" class="block text-sm font-medium text-gray-700">Nama/Judul Foto</label>
                    <input type="text" id="galeri_nama" name="nama_foto" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                </div>
                <div class="mb-4">
                    <label for="galeri_file" class="block text-sm font-medium text-gray-700">File Foto</label>
                    <input type="file" id="galeri_file" name="file_foto" required accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeAddGaleriModal()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium"> Batal </button>
                    <button type="submit" class="px-6 py-3 bg-secondary text-white rounded-lg hover:bg-green-600 transition-colors duration-200 font-medium shadow-md"> Simpan Foto </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Fungsi Sidebar (Tidak Berubah)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('translate-x-0');
        }

        // --- Fungsi Modal Berita (CRUD) ---

        // CREATE
        function openAddNewsModal() {
            document.getElementById('add-news-modal').classList.remove('hidden');
        }

        function closeAddNewsModal() {
            document.getElementById('add-news-modal').classList.add('hidden');
            document.getElementById('add-news-modal').querySelector('form').reset();
        }

        // UPDATE (Mengisi data ke modal edit)
        function openEditNewsModal(id) {
            const row = document.querySelector(`[data-news-id="${id}"]`);
            if (!row) return;

            // Ambil data dari atribut data-* di baris tabel
            const judul = row.getAttribute('data-judul');
            const informasi = row.getAttribute('data-informasi');
            const tanggal = row.getAttribute('data-tanggal');
            const gambar = row.getAttribute('data-gambar');
            const filename = gambar.substring(gambar.lastIndexOf('/') + 1); // Ambil nama file saja

            // Isi form modal
            document.getElementById('edit_id_berita').value = id;
            document.getElementById('edit_judul').value = judul;
            document.getElementById('edit_informasi').value = informasi;
            document.getElementById('edit_tanggal').value = tanggal;
            document.getElementById('edit_current_gambar').value = gambar; // Simpan path gambar saat ini
            document.getElementById('current-image-name').textContent = filename;

            // Tampilkan modal
            document.getElementById('edit-news-modal').classList.remove('hidden');
        }

        function closeEditNewsModal() {
            document.getElementById('edit-news-modal').classList.add('hidden');
            document.getElementById('edit-news-modal').querySelector('form').reset();
        }
        
        // --- Fungsi Modal Galeri (Existing) ---
        function openAddGaleriModal() {
            document.getElementById('add-galeri-modal').classList.remove('hidden');
        }

        function closeAddGaleriModal() {
            document.getElementById('add-galeri-modal').classList.add('hidden');
        }
    </script>
</body>
</html>