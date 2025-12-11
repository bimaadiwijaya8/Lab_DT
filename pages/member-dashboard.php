<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard - Laboratorium Data Technologies</title>
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
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out" id="sidebar">
        <div class="flex items-center justify-center h-20 bg-blue-600">
            <div class="flex items-center space-x-3">
                <h1 class="text-white text-2xl font-bold">Member Panel</h1>
            </div>
        </div>
        
        <nav class="mt-6 px-3">
            <a href="#dashboard" class="flex items-center px-3 py-3 text-primary bg-blue-50 border-r-2 border-primary rounded-lg mb-1 sidebar-link active">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v0"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="#profil-saya" class="flex items-center px-3 py-3 text-gray-700 hover:bg-gray-50 hover:text-primary rounded-lg mb-1 transition-all duration-200 sidebar-link">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="font-medium">Profil Saya</span>
            </a>
            <a href="#publikasi-saya" class="flex items-center px-3 py-3 text-gray-700 hover:bg-gray-50 hover:text-primary rounded-lg mb-1 transition-all duration-200 sidebar-link">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="font-medium">Publikasi Saya</span>
            </a>
            <a href="#status-pengajuan" class="flex items-center px-3 py-3 text-gray-700 hover:bg-gray-50 hover:text-primary rounded-lg mb-1 transition-all duration-200 sidebar-link">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">Status Pengajuan</span>
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

    <!-- Mobile menu button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button id="mobile-menu-btn" class="bg-primary text-white p-2 rounded-md">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-64 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4 flex items-center justify-between">
                <!-- Breadcrumb & Title -->
                <div>
                    <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-1">
                        <span>Member</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span id="breadcrumb-current">Dashboard</span>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-800" id="page-title">Dashboard</h2>
                </div>
                
                <!-- User Info -->
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <img src="https://via.placeholder.com/40x40/00A0D6/FFFFFF?text=MB" alt="Member" class="w-10 h-10 rounded-full border-2 border-gray-200">
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="p-6">
            <!-- Dashboard Section -->
            <div id="dashboard-section" class="content-section">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-white to-blue-50 rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-3xl font-bold text-gray-900" id="total-publikasi">0</h4>
                                <p class="text-sm font-medium text-gray-600">Total Publikasi</p>
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
                                <h4 class="text-3xl font-bold text-gray-900" id="pending-publikasi">0</h4>
                                <p class="text-sm font-medium text-gray-600">Menunggu Approval</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-white to-green-50 rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-secondary to-green-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-3xl font-bold text-gray-900" id="approved-publikasi">0</h4>
                                <p class="text-sm font-medium text-gray-600">Disetujui</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h3>
                    <div id="recent-activities">
                        <!-- Data aktivitas akan dimuat dari API -->
                        <p class="text-gray-500 text-center py-8">Belum ada aktivitas terbaru</p>
                    </div>
                </div>
            </div>

            <!-- Profil Saya Section -->
            <div id="profil-saya-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Edit Profil Saya</h3>
                    
                    <form id="profile-form" onsubmit="return updateMemberProfile(event)" class="space-y-6">
                        <!-- Photo Upload Section -->
                        <div class="flex flex-col items-center mb-6">
                            <div class="relative">
                                <img src="https://via.placeholder.com/120x120/00A0D6/FFFFFF?text=JD" alt="Foto Profil" class="w-24 h-24 rounded-full object-cover border-4 border-gray-200" id="profile-preview">
                                <label for="profile-photo" class="absolute bottom-0 right-0 bg-primary text-white p-2 rounded-full cursor-pointer hover:bg-blue-600 transition-colors duration-200">
                                    <i class="fas fa-camera text-sm"></i>
                                </label>
                                <input type="file" id="profile-photo" name="photo" class="hidden" accept="image/*" onchange="previewPhoto(event)">
                            </div>
                            <p class="text-sm text-gray-600 mt-2">Klik ikon kamera untuk mengubah foto</p>
                        </div>

                        <!-- Form Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nama-lengkap" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" id="nama-lengkap" name="nama_lengkap" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" value="John Doe" required>
                            </div>
                            
                            <div>
                                <label for="nim" class="block text-sm font-medium text-gray-700 mb-2">NIM</label>
                                <input type="text" id="nim" name="nim" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" value="123456789" required>
                            </div>
                            
                            <div>
                                <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-2">Jurusan</label>
                                <select id="jurusan" name="jurusan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                                    <option value="">Pilih Jurusan</option>
                                    <option value="Teknik Informatika" selected>Teknik Informatika</option>
                                    <option value="Sistem Informasi">Sistem Informasi</option>
                                    <option value="Teknik Komputer">Teknik Komputer</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="program-studi" class="block text-sm font-medium text-gray-700 mb-2">Program Studi</label>
                                <select id="program-studi" name="program_studi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                                    <option value="">Pilih Program Studi</option>
                                    <option value="S1 Teknik Informatika" selected>S1 Teknik Informatika</option>
                                    <option value="S1 Sistem Informasi">S1 Sistem Informasi</option>
                                    <option value="D3 Teknik Informatika">D3 Teknik Informatika</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                                <input type="text" id="kelas" name="kelas" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" value="TI-3A" required>
                            </div>
                            
                            <div>
                                <label for="tahun-angkatan" class="block text-sm font-medium text-gray-700 mb-2">Tahun Angkatan</label>
                                <select id="tahun-angkatan" name="tahun_angkatan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                                    <option value="">Pilih Tahun</option>
                                    <option value="2024">2024</option>
                                    <option value="2023">2023</option>
                                    <option value="2022">2022</option>
                                    <option value="2021" selected>2021</option>
                                    <option value="2020">2020</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="nomor-telepon" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                                <input type="tel" id="nomor-telepon" name="nomor_telepon" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" value="081234567890" required>
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" value="john.doe@student.polinema.ac.id" required>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition-colors duration-200">
                                <i class="fas fa-save mr-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Publikasi Saya Section -->
            <div id="publikasi-saya-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Publikasi Saya</h3>
                        <button onclick="openAddPublicationModal()" class="bg-secondary text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Tambah Publikasi
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Upload</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosen Pengampu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="publikasi-table">
                                <!-- Data publikasi akan dimuat dari API -->
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada publikasi. Klik "Tambah Publikasi" untuk menambah publikasi baru.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Status Pengajuan Section -->
            <div id="status-pengajuan-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Status Pengajuan</h3>
                    
                    <div id="pengajuan-cards" class="space-y-4">
                        <!-- Data pengajuan akan dimuat dari API -->
                        <div class="text-center py-16">
                            <i class="fas fa-clock text-4xl text-gray-400 mb-4"></i>
                            <h4 class="text-lg font-semibold text-gray-600 mb-2">Belum Ada Pengajuan</h4>
                            <p class="text-gray-500">Pengajuan publikasi Anda akan ditampilkan di sini</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Publication Modal -->
    <div id="add-publication-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Tambah Publikasi Baru</h3>
                        <button onclick="closeAddPublicationModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form id="add-publication-form" onsubmit="return createPublication(event)" class="space-y-6">
                        <div>
                            <label for="judul-publikasi" class="block text-sm font-medium text-gray-700 mb-2">Judul Publikasi</label>
                            <input type="text" id="judul-publikasi" name="judul_publikasi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>

                        <div>
                            <label for="tanggal-upload" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Upload</label>
                            <input type="date" id="tanggal-upload" name="tanggal_upload" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>

                        <div>
                            <label for="penyusun" class="block text-sm font-medium text-gray-700 mb-2">Penyusun</label>
                            <input type="text" id="penyusun" name="penyusun" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" value="John Doe" readonly>
                            <p class="text-xs text-gray-500 mt-1">Otomatis menggunakan nama Anda</p>
                        </div>

                        <div>
                            <label for="link-jurnal" class="block text-sm font-medium text-gray-700 mb-2">Link Jurnal</label>
                            <input type="url" id="link-jurnal" name="link_jurnal" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="https://" required>
                        </div>

                        <div>
                            <label for="dosen-pengampu" class="block text-sm font-medium text-gray-700 mb-2">Dosen Pengampu</label>
                            <input type="text" id="dosen-pengampu" name="dosen_pengampu" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>

                        <div>
                            <label for="deskripsi-singkat" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat</label>
                            <textarea id="deskripsi-singkat" name="deskripsi_singkat" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Deskripsi singkat tentang publikasi..." required></textarea>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="closeAddPublicationModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2 bg-secondary text-white rounded-md hover:bg-green-600 transition-colors duration-200">
                                Simpan Publikasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Publication Modal -->
    <div id="edit-publication-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Edit Publikasi</h3>
                        <button onclick="closeEditPublicationModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form id="edit-publication-form" onsubmit="return updatePublication(event)" class="space-y-6">
                        <input type="hidden" id="edit-publication-id" name="publication_id">
                        
                        <div>
                            <label for="edit-judul-publikasi" class="block text-sm font-medium text-gray-700 mb-2">Judul Publikasi</label>
                            <input type="text" id="edit-judul-publikasi" name="judul_publikasi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>

                        <div>
                            <label for="edit-tanggal-upload" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Upload</label>
                            <input type="date" id="edit-tanggal-upload" name="tanggal_upload" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>

                        <div>
                            <label for="edit-penyusun" class="block text-sm font-medium text-gray-700 mb-2">Penyusun</label>
                            <input type="text" id="edit-penyusun" name="penyusun" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" readonly>
                        </div>

                        <div>
                            <label for="edit-link-jurnal" class="block text-sm font-medium text-gray-700 mb-2">Link Jurnal</label>
                            <input type="url" id="edit-link-jurnal" name="link_jurnal" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>

                        <div>
                            <label for="edit-dosen-pengampu" class="block text-sm font-medium text-gray-700 mb-2">Dosen Pengampu</label>
                            <input type="text" id="edit-dosen-pengampu" name="dosen_pengampu" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        </div>

                        <div>
                            <label for="edit-deskripsi-singkat" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat</label>
                            <textarea id="edit-deskripsi-singkat" name="deskripsi_singkat" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required></textarea>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="closeEditPublicationModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition-colors duration-200">
                                Update Publikasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
