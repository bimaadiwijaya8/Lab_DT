<?php
// editor-dashboard.php - Halaman Dashboard untuk Editor Laboratorium Data Technologies

// Anda bisa menambahkan logika PHP di sini, seperti:
// 1. Cek sesi/autentikasi user (apakah user sudah login sebagai editor?)
// 2. Koneksi ke database dan pengambilan data statistik (total berita, agenda, dll.)
// 3. Logika untuk memuat data awal ke tabel (berita, agenda, pengajuan)

// Contoh data simulasi (Nantinya diganti dengan data real dari database)
$total_berita = 15; // Ganti dengan hitungan dari DB
$total_agenda = 5; // Ganti dengan hitungan dari DB
$pending_content = 3; // Ganti dengan hitungan dari DB
$approved_content = 12; // Ganti dengan hitungan dari DB

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Dashboard - Laboratorium Data Technologies</title>
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
    <link href="../index.html" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out" id="sidebar">
        <div class="flex items-center justify-center h-20 bg-blue-600">
            <div class="flex items-center space-x-3">
                <h1 class="text-white text-2xl font-bold">Editor Panel</h1>
            </div>
        </div>
        
        <nav class="mt-6 px-3">
            <a href="#dashboard" class="flex items-center px-3 py-3 text-primary bg-blue-50 border-r-2 border-primary rounded-lg mb-1 sidebar-link active">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v0"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="#berita" class="flex items-center px-3 py-3 text-gray-700 hover:bg-gray-50 hover:text-primary rounded-lg mb-1 transition-all duration-200 sidebar-link">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                <span class="font-medium">Berita</span>
            </a>
            <a href="#agenda" class="flex items-center px-3 py-3 text-gray-700 hover:bg-gray-50 hover:text-primary rounded-lg mb-1 transition-all duration-200 sidebar-link">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="font-medium">Agenda</span>
            </a>
            <a href="#galeri" class="flex items-center px-3 py-3 text-gray-700 hover:bg-gray-50 hover:text-primary rounded-lg mb-1 transition-all duration-200 sidebar-link">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="font-medium">Galeri</span>
            </a>
            <a href="#fasilitas" class="flex items-center px-3 py-3 text-gray-700 hover:bg-gray-50 hover:text-primary rounded-lg mb-1 transition-all duration-200 sidebar-link">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="font-medium">Fasilitas</span>
            </a>
            <a href="#publikasi" class="flex items-center px-3 py-3 text-gray-700 hover:bg-gray-50 hover:text-primary rounded-lg mb-1 transition-all duration-200 sidebar-link">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="font-medium">Publikasi</span>
            </a>
            <a href="#status-pengajuan" class="flex items-center px-3 py-3 text-gray-700 hover:bg-gray-50 hover:text-primary rounded-lg mb-1 transition-all duration-200 sidebar-link">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">Status Pengajuan</span>
            </a>
            <a href="#edit-halaman" class="flex items-center px-3 py-3 text-gray-700 hover:bg-gray-50 hover:text-primary rounded-lg mb-1 transition-all duration-200 sidebar-link">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span class="font-medium">Edit Halaman</span>
            </a>
            <div class="border-t border-gray-200 mt-4 pt-4">
                <a href="#" onclick="logout()" class="flex items-center px-3 py-3 text-gray-700 hover:bg-red-50 hover:text-red-500 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </nav>
    </div>

    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button id="mobile-menu-btn" class="bg-primary text-white p-2 rounded-md">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="lg:ml-64 min-h-screen">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-1">
                        <span>Editor</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span id="breadcrumb-current">Dashboard</span>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-800" id="page-title">Dashboard</h2>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <img src="https://i.pinimg.com/1200x/65/aa/d4/65aad4d8622fbabced4640aa870e1174.jpg" alt="Editor" class="w-10 h-10 rounded-full border-2 border-gray-200">
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6">
            <div id="dashboard-section" class="content-section">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-white to-blue-50 rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-3xl font-bold text-gray-900" id="total-berita"><?php echo $total_berita; ?></h4>
                                <p class="text-sm font-medium text-gray-600">Total Berita</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-white to-green-50 rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-secondary to-green-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-3xl font-bold text-gray-900" id="total-agenda"><?php echo $total_agenda; ?></h4>
                                <p class="text-sm font-medium text-gray-600">Total Agenda</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-white to-yellow-50 rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-3xl font-bold text-gray-900" id="pending-content"><?php echo $pending_content; ?></h4>
                                <p class="text-sm font-medium text-gray-600">Menunggu Approval</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-white to-purple-50 rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-3xl font-bold text-gray-900" id="approved-content"><?php echo $approved_content; ?></h4>
                                <p class="text-sm font-medium text-gray-600">Disetujui</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h3>
                    <div id="recent-activities">
                        <p class="text-gray-500 text-center py-8">Belum ada aktivitas terbaru</p>
                    </div>
                </div>
            </div>

            <div id="edit-halaman-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Halaman</h2>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                        <p class="text-blue-700">Pilih halaman yang ingin diedit. Halaman akan dibuka dalam mode edit visual dengan ikon edit pada setiap elemen penting.</p>
                    </div>
                    
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <a href="../index.html?edit=true" class="group p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg hover:from-blue-100 hover:to-blue-200 transition-all duration-300 border border-blue-200 hover:shadow-lg">
                            <div class="text-3xl mb-3">🏠</div>
                            <h3 class="font-bold text-gray-900 mb-2">Beranda</h3>
                            <p class="text-sm text-gray-600 mb-4">Edit hero section, tentang lab, dan berita terbaru</p>
                            <span class="inline-flex items-center text-primary font-semibold group-hover:translate-x-1 transition-transform">
                                Edit Mode →
                            </span>
                        </a>

                        <a href="profil-lab.php?edit=true" class="group p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-lg hover:from-green-100 hover:to-green-200 transition-all duration-300 border border-green-200 hover:shadow-lg">
                            <div class="text-3xl mb-3">📋</div>
                            <h3 class="font-bold text-gray-900 mb-2">Profil Lab</h3>
                            <p class="text-sm text-gray-600 mb-4">Edit informasi profil laboratorium</p>
                            <span class="inline-flex items-center text-secondary font-semibold group-hover:translate-x-1 transition-transform">
                                Edit Mode →
                            </span>
                        </a>

                        <a href="berita.php?edit=true" class="group p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg hover:from-purple-100 hover:to-purple-200 transition-all duration-300 border border-purple-200 hover:shadow-lg">
                            <div class="text-3xl mb-3">📰</div>
                            <h3 class="font-bold text-gray-900 mb-2">Berita</h3>
                            <p class="text-sm text-gray-600 mb-4">Edit konten halaman berita</p>
                            <span class="inline-flex items-center text-purple-600 font-semibold group-hover:translate-x-1 transition-transform">
                                Edit Mode →
                            </span>
                        </a>

                        <a href="galeri.php?edit=true" class="group p-6 bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg hover:from-pink-100 hover:to-pink-200 transition-all duration-300 border border-pink-200 hover:shadow-lg">
                            <div class="text-3xl mb-3">📸</div>
                            <h3 class="font-bold text-gray-900 mb-2">Galeri</h3>
                            <p class="text-sm text-gray-600 mb-4">Edit konten halaman galeri</p>
                            <span class="inline-flex items-center text-pink-600 font-semibold group-hover:translate-x-1 transition-transform">
                                Edit Mode →
                            </span>
                        </a>

                        <a href="penelitian.php?edit=true" class="group p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg hover:from-orange-100 hover:to-orange-200 transition-all duration-300 border border-orange-200 hover:shadow-lg">
                            <div class="text-3xl mb-3">📚</div>
                            <h3 class="font-bold text-gray-900 mb-2">Publikasi</h3>
                            <p class="text-sm text-gray-600 mb-4">Edit konten halaman publikasi</p>
                            <span class="inline-flex items-center text-orange-600 font-semibold group-hover:translate-x-1 transition-transform">
                                Edit Mode →
                            </span>
                        </a>

                        <a href="fasilitas.php?edit=true" class="group p-6 bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-lg hover:from-cyan-100 hover:to-cyan-200 transition-all duration-300 border border-cyan-200 hover:shadow-lg">
                            <div class="text-3xl mb-3">🏢</div>
                            <h3 class="font-bold text-gray-900 mb-2">Fasilitas</h3>
                            <p class="text-sm text-gray-600 mb-4">Edit konten halaman fasilitas</p>
                            <span class="inline-flex items-center text-cyan-600 font-semibold group-hover:translate-x-1 transition-transform">
                                Edit Mode →
                            </span>
                        </a>

                        <a href="anggota.php?edit=true" class="group p-6 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg hover:from-indigo-100 hover:to-indigo-200 transition-all duration-300 border border-indigo-200 hover:shadow-lg">
                            <div class="text-3xl mb-3">👥</div>
                            <h3 class="font-bold text-gray-900 mb-2">Anggota</h3>
                            <p class="text-sm text-gray-600 mb-4">Edit konten halaman anggota</p>
                            <span class="inline-flex items-center text-indigo-600 font-semibold group-hover:translate-x-1 transition-transform">
                                Edit Mode →
                            </span>
                        </a>

                        <a href="kontak.php?edit=true" class="group p-6 bg-gradient-to-br from-red-50 to-red-100 rounded-lg hover:from-red-100 hover:to-red-200 transition-all duration-300 border border-red-200 hover:shadow-lg">
                            <div class="text-3xl mb-3">📞</div>
                            <h3 class="font-bold text-gray-900 mb-2">Kontak</h3>
                            <p class="text-sm text-gray-600 mb-4">Edit konten halaman kontak</p>
                            <span class="inline-flex items-center text-red-600 font-semibold group-hover:translate-x-1 transition-transform">
                                Edit Mode →
                            </span>
                        </a>
                    </div>

                    <div class="mt-8 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                        <p class="text-sm text-yellow-800">
                            <strong>💡 Tips:</strong> Klik pada salah satu halaman di atas untuk membukanya dalam mode edit. Anda akan melihat ikon edit (✎) pada setiap elemen yang dapat diedit. Klik ikon untuk mengubah konten. Mode edit ini bersifat simulasi visual - perubahan belum disimpan ke database.
                        </p>
                    </div>
                </div>
            </div>

            <div id="berita-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Kelola Berita</h3>
                        <button onclick="openAddNewsModal()" class="bg-secondary text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Tambah Berita
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="w-2/5 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                    <th class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="berita-table">
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada berita. Klik "Tambah Berita" untuk membuat berita baru.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="agenda-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Kelola Agenda</h3>
                        <button onclick="openAddAgendaModal()" class="bg-secondary text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Tambah Agenda
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Agenda</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="agenda-table">
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada agenda. Klik "Tambah Agenda" untuk membuat agenda baru.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="galeri-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Kelola Galeri</h3>
                        <button onclick="openAddGalleryModal()" class="bg-secondary text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Tambah Foto
                        </button>
                    </div>

                    <div id="galeri-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <div class="col-span-full text-center py-16">
                            <i class="fas fa-images text-4xl text-gray-400 mb-4"></i>
                            <h4 class="text-lg font-semibold text-gray-600 mb-2">Belum Ada Foto</h4>
                            <p class="text-gray-500">Klik "Tambah Foto" untuk mengunggah foto baru</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="fasilitas-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Kelola Fasilitas</h3>
                        <button onclick="openAddFacilityModal()" class="bg-secondary text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Tambah Fasilitas
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Fasilitas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="fasilitas-table">
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada fasilitas. Klik "Tambah Fasilitas" untuk menambah fasilitas baru.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="publikasi-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Kelola Publikasi</h3>
                        <button onclick="openAddPublicationModal()" class="bg-secondary text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Tambah Publikasi
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penyusun</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosen Pengampu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="publikasi-table">
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada publikasi. Klik "Tambah Publikasi" untuk menambah publikasi baru.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="status-pengajuan-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Status Pengajuan</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Konten</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Kirim</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="pengajuan-table">
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada pengajuan konten
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="add-news-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Tambah Berita Baru</h3>
                        <button onclick="closeAddNewsModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form id="add-news-form" onsubmit="return createNews(event)" class="space-y-6">
                        <div>
                            <label for="judul-berita" class="block text-sm font-medium text-gray-700 mb-2">Judul Berita</label>
                            <input type="text" id="judul-berita" name="judul_berita" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>

                        <div>
                            <label for="isi-berita" class="block text-sm font-medium text-gray-700 mb-2">Isi Berita</label>
                            <textarea id="isi-berita" name="isi_berita" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required></textarea>
                        </div>

                        <div>
                            <label for="gambar-berita" class="block text-sm font-medium text-gray-700 mb-2">Upload Gambar</label>
                            <input type="file" id="gambar-berita" name="gambar_berita" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" accept="image/*">
                        </div>

                        <div>
                            <label for="tanggal-berita" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                            <input type="date" id="tanggal-berita" name="tanggal_berita" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="closeAddNewsModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2 bg-secondary text-white rounded-md hover:bg-green-600 transition-colors duration-200">
                                Simpan Berita
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-berita-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Edit Berita</h3>
                        <button onclick="closeEditBeritaModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form id="edit-berita-form" onsubmit="return handleEditBerita(event)" class="space-y-6">
                        <input type="hidden" id="edit-id-berita" name="id_berita">
                        
                        <div>
                            <label for="edit-judul-berita" class="block text-sm font-medium text-gray-700 mb-2">Judul Berita</label>
                            <input type="text" id="edit-judul-berita" name="judul" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>

                        <div>
                            <label for="edit-isi-berita" class="block text-sm font-medium text-gray-700 mb-2">Isi Berita</label>
                            <textarea id="edit-isi-berita" name="informasi" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required></textarea>
                        </div>

                        <div>
                            <label for="edit-gambar-berita" class="block text-sm font-medium text-gray-700 mb-2">Upload Gambar (Opsional)</label>
                            <input type="file" id="edit-gambar-berita" name="gambar_berita" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" accept="image/*" onchange="previewEditImage(this)">
                            <div id="edit-gambar-preview" class="mt-2 hidden">
                                <img id="edit-gambar-preview-img" src="" alt="Preview" class="max-w-full h-32 object-cover rounded">
                                <p class="text-sm text-gray-500 mt-1">Gambar saat ini. Upload gambar baru untuk mengganti.</p>
                            </div>
                        </div>

                        <div>
                            <label for="edit-tanggal-berita" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                            <input type="date" id="edit-tanggal-berita" name="tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>

                        <div>
                            <label for="edit-author-berita" class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                            <input type="text" id="edit-author-berita" name="author" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="closeEditBeritaModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition-colors duration-200">
                                Update Berita
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="logo-edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold text-gray-900">Edit Logo</h3>
                    <button onclick="closeLogoEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="logo-upload-form" method="POST" action="../assets/php/logo_upload.php" enctype="multipart/form-data" class="p-6">
                    <div class="mb-6 text-center">
                        <p class="text-sm text-gray-600 mb-4">Logo saat ini:</p>
                        <img id="current-logo-preview" src="../assets/img/logo.png" alt="Current Logo" class="mx-auto h-32 w-auto object-contain border rounded-lg p-2">
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unggah Logo Baru (PNG only)</label>
                        <div class="mt-1 flex items-center">
                            <input type="file" id="new-logo-upload" name="logo_file" accept=".png" class="hidden" required>
                            <label for="new-logo-upload" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Pilih File
                            </label>
                            <span id="file-name" class="ml-3 text-sm text-gray-500">Tidak ada file dipilih</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Ukuran maksimal: 2MB. Format: PNG</p>
                    </div>

                    <div id="new-logo-preview-container" class="mb-6 text-center hidden">
                        <p class="text-sm text-gray-600 mb-2">Pratinjau logo baru:</p>
                        <img id="new-logo-preview" class="mx-auto h-32 w-auto object-contain border rounded-lg p-2">
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeLogoEditModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Batal
                        </button>
                        <button type="submit" id="save-logo-btn" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Mengganti nilai placeholder dengan nilai dari PHP
            document.getElementById('total-berita').textContent = '<?php echo $total_berita; ?>';
            document.getElementById('total-agenda').textContent = '<?php echo $total_agenda; ?>';
            document.getElementById('pending-content').textContent = '<?php echo $pending_content; ?>';
            document.getElementById('approved-content').textContent = '<?php echo $approved_content; ?>';

            // Logika Navigasi Sidebar (tetap menggunakan JavaScript)
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            const contentSections = document.querySelectorAll('.content-section');
            const breadcrumbCurrent = document.getElementById('breadcrumb-current');
            const pageTitle = document.getElementById('page-title');
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');

            function hideAllSections() {
                contentSections.forEach(section => {
                    section.classList.add('hidden');
                });
            }

            function setActiveLink(activeLink) {
                sidebarLinks.forEach(link => {
                    link.classList.remove('active', 'bg-blue-50', 'border-r-2', 'border-primary', 'text-primary');
                    link.classList.add('text-gray-700', 'hover:bg-gray-50', 'hover:text-primary');
                });

                activeLink.classList.add('active', 'bg-blue-50', 'border-r-2', 'border-primary', 'text-primary');
                activeLink.classList.remove('text-gray-700', 'hover:bg-gray-50', 'hover:text-primary');
            }

            function navigate(hash) {
                const sectionId = hash ? hash.substring(1) + '-section' : 'dashboard-section';
                const section = document.getElementById(sectionId);
                const link = document.querySelector(`.sidebar-link[href="${hash}"]`);

                hideAllSections();
                if (section) {
                    section.classList.remove('hidden');
                }

                if (link) {
                    setActiveLink(link);
                    const titleText = link.querySelector('span').textContent;
                    breadcrumbCurrent.textContent = titleText;
                    pageTitle.textContent = titleText;
                } else {
                    // Fallback for non-dashboard pages
                    breadcrumbCurrent.textContent = 'Dashboard';
                    pageTitle.textContent = 'Dashboard';
                }
            }

            // Inisialisasi pada load
            const initialHash = window.location.hash || '#dashboard';
            navigate(initialHash);

            // Event listener untuk klik link sidebar
            sidebarLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const hash = link.getAttribute('href');
                    window.location.hash = hash; // Update URL hash
                    navigate(hash);
                    // Close sidebar on mobile after navigation
                    sidebar.classList.add('-translate-x-full');
                });
            });
            
            // Event listener untuk tombol menu mobile
            mobileMenuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
            });
            
            // Logika Modal
            window.openAddNewsModal = function() {
                document.getElementById('add-news-modal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            window.closeAddNewsModal = function() {
                document.getElementById('add-news-modal').classList.add('hidden');
                document.body.style.overflow = '';
            }

            window.openEditBeritaModal = function(id) {
                // Di sini Anda akan mengambil data berita berdasarkan ID dari API
                // dan mengisi form edit. Ini hanya simulasi.
                console.log('Open Edit Modal for ID:', id);
                document.getElementById('edit-id-berita').value = id;
                document.getElementById('edit-judul-berita').value = "Judul Berita " + id;
                document.getElementById('edit-isi-berita').value = "Isi berita yang akan diedit untuk ID " + id + ".";
                document.getElementById('edit-tanggal-berita').value = "2025-12-10";
                document.getElementById('edit-author-berita').value = "Admin Editor";
                document.getElementById('edit-gambar-preview').classList.remove('hidden'); // Tampilkan preview gambar
                document.getElementById('edit-gambar-preview-img').src = "placeholder_image.jpg"; // Ganti dengan path gambar asli

                document.getElementById('edit-berita-modal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            window.closeEditBeritaModal = function() {
                document.getElementById('edit-berita-modal').classList.add('hidden');
                document.body.style.overflow = '';
            }

            window.handleEditBerita = function(event) {
                event.preventDefault();
                alert('Simulasi: Berita berhasil diperbarui (ID: ' + document.getElementById('edit-id-berita').value + ')');
                closeEditBeritaModal();
                return false;
            }

            window.createNews = function(event) {
                event.preventDefault();
                alert('Simulasi: Berita baru berhasil disimpan. Menunggu approval.');
                closeAddNewsModal();
                return false;
            }

            // Logika Modal Edit Logo
            window.openLogoEditModal = function() {
                document.getElementById('logo-edit-modal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            window.closeLogoEditModal = function() {
                document.getElementById('logo-edit-modal').classList.add('hidden');
                document.body.style.overflow = '';
                document.getElementById('logo-upload-form').reset(); // Reset form
                document.getElementById('file-name').textContent = 'Tidak ada file dipilih';
                document.getElementById('new-logo-preview-container').classList.add('hidden');
                document.getElementById('save-logo-btn').disabled = true;
            }

            const newLogoUpload = document.getElementById('new-logo-upload');
            const fileNameSpan = document.getElementById('file-name');
            const newLogoPreviewContainer = document.getElementById('new-logo-preview-container');
            const newLogoPreview = document.getElementById('new-logo-preview');
            const saveLogoBtn = document.getElementById('save-logo-btn');

            newLogoUpload.addEventListener('change', (event) => {
                if (event.target.files.length > 0) {
                    const file = event.target.files[0];
                    fileNameSpan.textContent = file.name;
                    saveLogoBtn.disabled = false;

                    // Pratinjau gambar
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        newLogoPreview.src = e.target.result;
                        newLogoPreviewContainer.classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                } else {
                    fileNameSpan.textContent = 'Tidak ada file dipilih';
                    newLogoPreviewContainer.classList.add('hidden');
                    saveLogoBtn.disabled = true;
                }
            });
            
            // Tambahkan fungsi-fungsi modal untuk Agenda, Galeri, Fasilitas, Publikasi
            // (Hanya placeholder karena logikanya mirip dengan Add News)

            window.openAddAgendaModal = function() {
                alert("Simulasi: Membuka modal Tambah Agenda.");
                // Logika modal actual...
            }
            window.openAddGalleryModal = function() {
                alert("Simulasi: Membuka modal Tambah Foto Galeri.");
                // Logika modal actual...
            }
            window.openAddFacilityModal = function() {
                alert("Simulasi: Membuka modal Tambah Fasilitas.");
                // Logika modal actual...
            }
            window.openAddPublicationModal = function() {
                alert("Simulasi: Membuka modal Tambah Publikasi.");
                // Logika modal actual...
            }
        });
    </script>
</body>
</html>