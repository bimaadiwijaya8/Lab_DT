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

// Variabel untuk Admin Panel Design
$username = "EditorLDT"; // Ganti dengan nama user yang login
// Tentukan Judul Halaman berdasarkan hash (Client-side)
$page_title_base = "Editor Panel - ";
$page_title = $page_title_base . "Dashboard"; 

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
        /* Gaya tambahan untuk menyesuaikan dengan admin-dashboard */
        #sidebar.translate-x-0 {
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .modal { 
            transition: opacity 0.3s ease, visibility 0.3s ease; 
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <div id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-xl">
        <div class="flex items-center justify-center h-20 border-b border-gray-100 px-6">
            <h1 class="text-2xl font-bold text-gray-800">Editor Panel</h1>
        </div>
        <nav class="mt-6 px-4 space-y-2">
            <a href="#dashboard" class="flex items-center p-3 text-gray-700 rounded-lg font-semibold sidebar-link hover:bg-gray-100 transition-colors duration-200" data-page="Dashboard">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v0"></path>
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="#berita" class="flex items-center p-3 text-gray-700 rounded-lg font-semibold sidebar-link hover:bg-gray-100 transition-colors duration-200" data-page="Kelola Berita">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                <span>Berita</span>
            </a>
            <a href="#agenda" class="flex items-center p-3 text-gray-700 rounded-lg font-semibold sidebar-link hover:bg-gray-100 transition-colors duration-200" data-page="Kelola Agenda">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span>Agenda</span>
            </a>
            <a href="#galeri" class="flex items-center p-3 text-gray-700 rounded-lg font-semibold sidebar-link hover:bg-gray-100 transition-colors duration-200" data-page="Kelola Galeri">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Galeri</span>
            </a>
            <a href="#fasilitas" class="flex items-center p-3 text-gray-700 rounded-lg font-semibold sidebar-link hover:bg-gray-100 transition-colors duration-200" data-page="Kelola Fasilitas">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Fasilitas</span>
            </a>
            <a href="#publikasi" class="flex items-center p-3 text-gray-700 rounded-lg font-semibold sidebar-link hover:bg-gray-100 transition-colors duration-200" data-page="Kelola Publikasi">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span>Publikasi</span>
            </a>
            <a href="#status-pengajuan" class="flex items-center p-3 text-gray-700 rounded-lg font-semibold sidebar-link hover:bg-gray-100 transition-colors duration-200" data-page="Status Pengajuan">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Status Pengajuan</span>
            </a>
            <a href="#edit-halaman" class="flex items-center p-3 text-gray-700 rounded-lg font-semibold sidebar-link hover:bg-gray-100 transition-colors duration-200" data-page="Edit Halaman Utama">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Edit Halaman</span>
            </a>
        </nav>
        <div class="absolute bottom-0 w-full border-t border-gray-100 p-4">
            <a href="#" onclick="logout()" class="flex items-center p-3 text-red-600 bg-red-50 rounded-lg font-semibold hover:bg-red-100 transition-colors duration-200">
                <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i> Logout
            </a>
        </div>
    </div>

    <div class="lg:ml-64 transition-all duration-300 ease-in-out p-4 md:p-8">
        <header class="flex items-center justify-between bg-white p-4 shadow-md rounded-xl mb-8">
            <button class="lg:hidden text-gray-600 hover:text-primary transition-colors" onclick="toggleSidebar()">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <h1 class="text-2xl font-semibold text-gray-800 hidden lg:block" id="page-title"><?php echo $page_title; ?></h1>
            <div class="flex items-center space-x-4">
                <div class="text-gray-600">Selamat datang, <span class="font-medium text-primary"><?php echo $username; ?></span> </div>
                <div class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-primary font-bold">
                    <?php echo strtoupper(substr($username, 0, 1)); ?> 
                </div>
            </div>
        </header>

        <main>
            <div id="dashboard-section" class="content-section">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Ringkasan Statistik Konten</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 transition-transform hover:scale-[1.02]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Berita</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1" id="total-berita"><?php echo $total_berita; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 transition-transform hover:scale-[1.02]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Agenda</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1" id="total-agenda"><?php echo $total_agenda; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center text-secondary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 transition-transform hover:scale-[1.02]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Menunggu Approval</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1" id="pending-content"><?php echo $pending_content; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-500/10 rounded-full flex items-center justify-center text-yellow-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 transition-transform hover:scale-[1.02]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Disetujui</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1" id="approved-content"><?php echo $approved_content; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-purple-500/10 rounded-full flex items-center justify-center text-purple-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
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
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Halaman Utama</h2>
                    <div class="bg-blue-50 border-l-4 border-primary p-4 mb-6">
                        <p class="text-blue-700">Pilih halaman yang ingin diedit. Halaman akan dibuka dalam mode edit visual dengan ikon edit pada setiap elemen penting.</p>
                    </div>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <a href="../index.html?edit=true" class="group p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg hover:from-blue-100 hover:to-blue-200 transition-all duration-300 border border-blue-200 hover:shadow-lg">
                            <div class="text-3xl mb-3">🏠</div>
                            <h3 class="font-bold text-gray-900 mb-2">Beranda</h3>
                            <p class="text-sm text-gray-600 mb-4">Edit hero section, tentang lab, dan berita terbaru</p>
                            <span class="inline-flex items-center text-primary font-semibold group-hover:translate-x-1 transition-transform"> Edit Mode → </span>
                        </a>
                        <a href="profil-lab.php?edit=true" class="group p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-lg hover:from-green-100 hover:to-green-200 transition-all duration-300 border border-green-200 hover:shadow-lg">
                            <div class="text-3xl mb-3">🔬</div>
                            <h3 class="font-bold text-gray-900 mb-2">Profil Lab</h3>
                            <p class="text-sm text-gray-600 mb-4">Edit sejarah, visi misi, dan tujuan lab</p>
                            <span class="inline-flex items-center text-secondary font-semibold group-hover:translate-x-1 transition-transform"> Edit Mode → </span>
                        </a>
                        <a href="anggota.php?edit=true" class="group p-6 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg hover:from-indigo-100 hover:to-indigo-200 transition-all duration-300 border border-indigo-200 hover:shadow-lg">
                            <div class="text-3xl mb-3">👥</div>
                            <h3 class="font-bold text-gray-900 mb-2">Anggota</h3>
                            <p class="text-sm text-gray-600 mb-4">Edit konten halaman anggota</p>
                            <span class="inline-flex items-center text-indigo-600 font-semibold group-hover:translate-x-1 transition-transform"> Edit Mode → </span>
                        </a>
                        <a href="kontak.php?edit=true" class="group p-6 bg-gradient-to-br from-red-50 to-red-100 rounded-lg hover:from-red-100 hover:to-red-200 transition-all duration-300 border border-red-200 hover:shadow-lg">
                            <div class="text-3xl mb-3">📞</div>
                            <h3 class="font-bold text-gray-900 mb-2">Kontak</h3>
                            <p class="text-sm text-gray-600 mb-4">Edit konten halaman kontak</p>
                            <span class="inline-flex items-center text-red-600 font-semibold group-hover:translate-x-1 transition-transform"> Edit Mode → </span>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="berita-table-body">
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada data berita.
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="agenda-table-body">
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada data agenda.
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="fasilitas-table-body">
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada data fasilitas.
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="publikasi-table-body">
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada data publikasi.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="status-pengajuan-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Status Pengajuan Konten</h3>
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

    <div id="add-news-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Tambah Berita Baru</h3>
                <button type="button" onclick="closeAddNewsModal()" class="text-gray-400 hover:text-gray-600">
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
                    <label for="gambar-berita" class="block text-sm font-medium text-gray-700 mb-2">Upload Gambar (Wajib)</label>
                    <input type="file" id="gambar-berita" name="gambar_berita" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" accept="image/*" required onchange="previewImage(this)">
                    <div id="gambar-preview" class="mt-2 hidden">
                        <img id="gambar-preview-img" src="" alt="Preview" class="max-w-full h-32 object-cover rounded">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal-berita" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input type="date" id="tanggal-berita" name="tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                    <div>
                        <label for="author-berita" class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                        <input type="text" id="author-berita" name="author" value="<?php echo $username; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                </div>
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeAddNewsModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-200"> Batal </button>
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition-colors duration-200"> Tambah Berita </button>
                </div>
            </form>
        </div>
    </div>

    <div id="edit-berita-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Edit Berita</h3>
                <button type="button" onclick="closeEditBeritaModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="edit-news-form" onsubmit="return updateNews(event)" class="space-y-6">
                <input type="hidden" id="edit-id-berita" name="id_berita">
                <input type="hidden" id="edit-current-gambar" name="current_gambar">
                
                <div>
                    <label for="edit-judul-berita" class="block text-sm font-medium text-gray-700 mb-2">Judul Berita</label>
                    <input type="text" id="edit-judul-berita" name="judul_berita" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                </div>
                <div>
                    <label for="edit-isi-berita" class="block text-sm font-medium text-gray-700 mb-2">Isi Berita</label>
                    <textarea id="edit-isi-berita" name="isi_berita" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit-gambar-berita" class="block text-sm font-medium text-gray-700 mb-2">Ganti Gambar (Kosongkan jika tidak diubah)</label>
                        <input type="file" id="edit-gambar-berita" name="gambar_berita" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" accept="image/*" onchange="previewEditImage(this)">
                        <p class="text-xs text-gray-500 mt-1">Gambar saat ini: <span id="current-image-name" class="font-semibold"></span></p>
                        <div id="edit-gambar-preview" class="mt-2 hidden">
                            <img id="edit-gambar-preview-img" src="" alt="Preview" class="max-w-full h-32 object-cover rounded">
                            <p class="text-sm text-gray-500 mt-1">Preview gambar baru.</p>
                        </div>
                    </div>
                    <div>
                        <label for="edit-tanggal-berita" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input type="date" id="edit-tanggal-berita" name="tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                </div>
                
                <div>
                    <label for="edit-author-berita" class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                    <input type="text" id="edit-author-berita" name="author" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeEditBeritaModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-200"> Batal </button>
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition-colors duration-200"> Update Berita </button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="logo-edit-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Ubah Logo Situs</h3>
                <button type="button" onclick="closeLogoEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="logo-upload-form" onsubmit="return saveLogo(event)" enctype="multipart/form-data">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo Saat Ini:</label>
                    <img src="../assets/img/logo.png" alt="Current Logo" class="h-16 w-auto object-contain border rounded-lg p-2">
                </div>
                
                <div class="mb-6">
                    <label for="logo-file" class="block text-sm font-medium text-gray-700 mb-2">Pilih Logo Baru (PNG)</label>
                    <div class="flex items-center">
                        <input type="file" id="logo-file" name="logo_file" accept="image/png" class="hidden" onchange="displayFileName(this)">
                        <label for="logo-file" class="cursor-pointer bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors duration-200 text-sm font-medium">
                            Upload File
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


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mengganti nilai placeholder dengan nilai dari PHP
            document.getElementById('total-berita').textContent = '<?php echo $total_berita; ?>';
            document.getElementById('total-agenda').textContent = '<?php echo $total_agenda; ?>';
            document.getElementById('pending-content').textContent = '<?php echo $pending_content; ?>';
            document.getElementById('approved-content').textContent = '<?php echo $approved_content; ?>';

            // Logika Navigasi Sidebar (tetap menggunakan JavaScript dan Hash)
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            const contentSections = document.querySelectorAll('.content-section');
            const pageTitleElement = document.getElementById('page-title'); // Diganti dari breadcrumb ke h1
            const sidebar = document.getElementById('sidebar');

            function hideAllSections() {
                contentSections.forEach(section => {
                    section.classList.add('hidden');
                });
            }

            // Fungsi untuk mengatur link aktif dengan gaya admin-dashboard.php
            function setActiveLink(activeLink) {
                sidebarLinks.forEach(link => {
                    // Hapus kelas aktif lama (gaya editor lama) dan kelas aktif admin
                    link.classList.remove('active', 'text-white', 'bg-primary', 'hover:bg-blue-700', 'bg-blue-50', 'border-r-2', 'border-primary', 'text-primary'); 
                    // Terapkan kelas default admin
                    link.classList.add('text-gray-700', 'hover:bg-gray-100');
                });
                
                // Terapkan kelas aktif admin
                activeLink.classList.add('active', 'text-white', 'bg-primary', 'hover:bg-blue-700');
                // Hapus kelas default admin dari yang aktif
                activeLink.classList.remove('text-gray-700', 'hover:bg-gray-100');
            }

            function navigate(hash) {
                const sectionId = hash ? hash.substring(1) + '-section' : 'dashboard-section';
                const section = document.getElementById(sectionId);
                const link = hash ? document.querySelector(`.sidebar-link[href="${hash}"]`) : document.querySelector(`.sidebar-link[href="#dashboard"]`);

                hideAllSections();
                if (section) {
                    section.classList.remove('hidden');
                }

                if (link) {
                    setActiveLink(link);
                    // Update Page Title (menggunakan data-page)
                    const pageName = link.getAttribute('data-page');
                    pageTitleElement.textContent = pageName;
                } else {
                    // Default to Dashboard title
                    pageTitleElement.textContent = 'Dashboard';
                }

                // Tutup sidebar di mobile setelah navigasi
                if (!sidebar.classList.contains('-translate-x-full')) {
                    toggleSidebar();
                }
            }

            // Inisialisasi: Baca hash saat ini atau default ke dashboard
            navigate(window.location.hash);

            // Listener untuk perubahan hash
            window.addEventListener('hashchange', function() {
                navigate(window.location.hash);
            });

            // Listener klik untuk link sidebar
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Tidak perlu mencegah default karena hashchange akan menangani navigasi
                    // e.preventDefault();
                    // navigate(link.getAttribute('href'));
                });
            });

            // --- Fungsi untuk Toggle Sidebar (Diambil dari admin-dashboard.php) ---
            window.toggleSidebar = function() {
                const sidebar = document.getElementById('sidebar');
                if (sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                } else {
                    sidebar.classList.add('-translate-x-full');
                }
            }
            // --- Akhir Fungsi Toggle Sidebar ---


            // --- Fungsi Simulasi CRUD & Modal ---
            window.logout = function() {
                if (confirm("Apakah Anda yakin ingin keluar?")) {
                    window.location.href = '../login.php'; // Ganti dengan halaman login yang sebenarnya
                }
            }
            
            // Logika Modal Add News
            window.openAddNewsModal = function() {
                document.getElementById('add-news-modal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            window.closeAddNewsModal = function() {
                document.getElementById('add-news-modal').classList.add('hidden');
                document.body.style.overflow = '';
                document.getElementById('add-news-form').reset();
                document.getElementById('gambar-preview').classList.add('hidden');
            }
            window.createNews = function(event) {
                event.preventDefault();
                alert("Simulasi: Berita baru berhasil ditambahkan (Pending Approval).");
                closeAddNewsModal();
                return false;
            }
            window.previewImage = function(input) {
                const previewContainer = document.getElementById('gambar-preview');
                const previewImg = document.getElementById('gambar-preview-img');
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    }
                    reader.readAsDataURL(input.files[0]);
                } else {
                    previewContainer.classList.add('hidden');
                }
            }
            
            // Logika Modal Edit News
            window.openEditBeritaModal = function(id, judul, isi, tanggal, gambarPath) {
                document.getElementById('edit-id-berita').value = id;
                document.getElementById('edit-judul-berita').value = judul;
                document.getElementById('edit-isi-berita').value = isi;
                document.getElementById('edit-tanggal-berita').value = tanggal;
                document.getElementById('edit-current-gambar').value = gambarPath;

                const filename = gambarPath.substring(gambarPath.lastIndexOf('/') + 1);
                document.getElementById('current-image-name').textContent = filename;

                // Reset preview
                document.getElementById('edit-gambar-preview').classList.add('hidden');
                document.getElementById('edit-news-form').elements['gambar_berita'].value = '';

                document.getElementById('edit-berita-modal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            window.closeEditBeritaModal = function() {
                document.getElementById('edit-berita-modal').classList.add('hidden');
                document.body.style.overflow = '';
                document.getElementById('edit-news-form').reset();
            }
            window.updateNews = function(event) {
                event.preventDefault();
                alert("Simulasi: Berita ID " + document.getElementById('edit-id-berita').value + " berhasil diupdate (Pending Approval).");
                closeEditBeritaModal();
                return false;
            }
            window.previewEditImage = function(input) {
                const previewContainer = document.getElementById('edit-gambar-preview');
                const previewImg = document.getElementById('edit-gambar-preview-img');
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    }
                    reader.readAsDataURL(input.files[0]);
                } else {
                    previewContainer.classList.add('hidden');
                }
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
            window.displayFileName = function(input) {
                const fileNameSpan = document.getElementById('file-name');
                const newLogoPreview = document.getElementById('new-logo-preview-img');
                const newLogoPreviewContainer = document.getElementById('new-logo-preview-container');
                const saveLogoBtn = document.getElementById('save-logo-btn');

                if (input.files && input.files.length > 0) {
                    const file = input.files[0];
                    fileNameSpan.textContent = file.name;
                    saveLogoBtn.disabled = false;

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
            }
            window.saveLogo = function(event) {
                event.preventDefault();
                alert("Simulasi: Logo berhasil diubah.");
                closeLogoEditModal();
            }
            
            // Tambahkan fungsi-fungsi modal placeholder untuk halaman lain (sesuai admin-dashboard)
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