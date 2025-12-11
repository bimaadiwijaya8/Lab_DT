<?php

$active_page = 'anggota';

/**
 * anggota.php - Halaman Daftar Anggota Laboratorium Data Technologies
 *
 * Catatan:
 * Bagian ini adalah contoh. Di lingkungan produksi, Anda akan:
 * 1. Menghubungkan ke database.
 * 2. Mengambil data Dosen/Staff dan Mahasiswa dari database.
 */

// Contoh variabel simulasi data (nantinya diganti dengan data database)
// Untuk menampilkan data, hapus tanda komentar pada data simulasi di bawah.

$dosen_staff = [
    /*
    [
        'nama' => 'Dr. Budi Santoso', 
        'jabatan' => 'Kepala Laboratorium', 
        'foto' => 'budi.jpg'
    ],
    [
        'nama' => 'Prof. Dr. Dewi Lestari', 
        'jabatan' => 'Pembimbing Penelitian', 
        'foto' => 'dewi.jpg'
    ],
    [
        'nama' => 'Ir. Chandra Wijaya, M.T.', 
        'jabatan' => 'Koordinator Teknis', 
        'foto' => 'chandra.jpg'
    ],
    */
];

$mahasiswa = [
    /*
    [
        'nama' => 'Siti Aminah', 
        'jabatan' => 'Asisten Laboratorium Data Mining', 
        'foto' => 'siti.jpg'
    ],
    [
        'nama' => 'Rizky Pratama', 
        'jabatan' => 'Asisten Laboratorium Big Data', 
        'foto' => 'rizky.jpg'
    ],
    [
        'nama' => 'Anya Geraldine', 
        'jabatan' => 'Mahasiswa Peneliti - AI', 
        'foto' => 'anya.jpg'
    ],
    */
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Anggota – Lab Data Technologies</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    .text-gradient {
      background-image: linear-gradient(to right, #00A0D6, #6AC259);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      color: transparent; /* Fallback */
    }
    .hero-section {
      background-color: #0c0a09; /* Dark background for hero */
      /* Anda bisa menambahkan gambar latar belakang di sini */
    }
  </style>
</head>
<body class="bg-white text-gray-900 font-['Inter']">
  
  <?php 
    // Memuat komponen header
    // Asumsi file header.php berada di direktori induk dari direktori 'includes'
    require_once '../includes/header.php';
  ?>

  <main>
    <section class="hero-section text-white py-20 lg:py-28">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center">
          <div
            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#00A0D6] to-[#6AC259] text-white px-4 py-2 rounded-full font-medium mb-6 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
              </path>
            </svg>
            <span>Daftar Anggota</span>
          </div>

          <h1 class="text-4xl lg:text-6xl font-bold text-white-900 mb-6 leading-tight">
            Tim<br>
            <span class="text-gradient"> & Komunitas</span>
          </h1>

          <p class="text-xl text-gray-300 mb-8 leading-relaxed max-w-4xl mx-auto">
            Dosen, staff, dan mahasiswa yang berdedikasi membangun masa depan teknologi data di <span class="font-semibold text-[#00A0D6]">Laboratorium Data Technologies</span>
          </p>

          <div class="flex flex-col sm:flex-row gap-4 mb-12 justify-center">
            <a href="#dosen-staff-list"
              class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                  </path>
              </svg>
              Dosen
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
            <a href="#mahasiswa-list"
              class="group inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-[#00A0D6] hover:text-[#00A0D6] transition-all duration-300 hover:scale-105 shadow-sm hover:shadow-md">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              Asisten Laboratorium
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <section class="py-24 bg-gradient-to-br from-gray-50/50 via-white to-blue-50/30">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        
        <div class="mb-20">
          <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
              <span class="text-gradient">Dosen & Staff</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
              Tim pengajar dan pengelola laboratorium yang berpengalaman dalam bidang teknologi data
            </p>
          </div>
          
          <div id="dosen-staff-list" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($dosen_staff)) : ?>
            <div class="col-span-full">
              <div class="empty-state text-center py-16 bg-white/60 backdrop-blur-xl border border-white/40 rounded-3xl shadow-lg">
                <div class="w-20 h-20 bg-gradient-to-br from-[#00A0D6]/10 to-[#6AC259]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <svg class="w-10 h-10 text-[#00A0D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                  </path>
                  </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Data Dosen & Staff</h3>
                <p class="text-gray-600 mb-6">Data dosen dan staff akan ditampilkan setelah tersedia dari sistem</p>
                <div class="inline-flex items-center gap-2 text-sm text-[#00A0D6] font-medium">
                  <div class="w-2 h-2 bg-[#00A0D6] rounded-full animate-pulse"></div>
                  Menunggu data dari API
                </div>
              </div>
            </div>
            <?php else : ?>
            <?php foreach ($dosen_staff as $member) : ?>
            <div class="member-card group bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-lg hover:scale-[1.01] transition-all duration-300">
                <div class="text-center">
                    <div class="relative inline-block mb-6">
                        <img src="assets/img/<?php echo $member['foto']; ?>" class="w-24 h-24 rounded-xl object-cover border-4 border-white shadow-lg" alt="Foto Profil <?php echo $member['nama']; ?>" loading="lazy">
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-[#00A0D6]/10 to-[#6AC259]/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo $member['nama']; ?></h3>
                    <p class="text-gray-600 text-sm font-medium"><?php echo $member['jabatan']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <div class="mb-20">
          <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
              <span class="text-gradient">Mahasiswa</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
              Asisten laboratorium dan mahasiswa peneliti yang aktif dalam pengembangan teknologi
            </p>
          </div>
          
          <div id="mahasiswa-list" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($mahasiswa)) : ?>
            <div class="col-span-full">
              <div class="empty-state text-center py-16 bg-white/60 backdrop-blur-xl border border-white/40 rounded-3xl shadow-lg">
                <div class="w-20 h-20 bg-gradient-to-br from-[#6AC259]/10 to-[#00A0D6]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <svg class="w-10 h-10 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Data Mahasiswa</h3>
                <p class="text-gray-600 mb-6">Data mahasiswa akan ditampilkan setelah tersedia dari sistem</p>
                <div class="inline-flex items-center gap-2 text-sm text-[#6AC259] font-medium">
                  <div class="w-2 h-2 bg-[#6AC259] rounded-full animate-pulse"></div>
                  Menunggu data dari API
                </div>
              </div>
            </div>
            <?php else : ?>
            <?php foreach ($mahasiswa as $member) : ?>
            <div class="member-card group bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-lg hover:scale-[1.01] transition-all duration-300">
                <div class="text-center">
                    <div class="relative inline-block mb-6">
                        <img src="assets/img/<?php echo $member['foto']; ?>" class="w-24 h-24 rounded-xl object-cover border-4 border-white shadow-lg" alt="Foto Profil <?php echo $member['nama']; ?>" loading="lazy">
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-[#00A0D6]/10 to-[#6AC259]/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo $member['nama']; ?></h3>
                    <p class="text-gray-600 text-sm font-medium"><?php echo $member['jabatan']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
        
        <template id="member-card-template">
          <div class="member-card group bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-lg hover:scale-[1.01] transition-all duration-300">
            <div class="text-center">
              <div class="relative inline-block mb-6">
                <img data-foto class="w-24 h-24 rounded-xl object-cover border-4 border-white shadow-lg" alt="Foto Profil" loading="lazy">
                <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-[#00A0D6]/10 to-[#6AC259]/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </div>
              <h3 data-nama class="text-xl font-bold text-gray-900 mb-2"></h3>
              <p data-jabatan class="text-gray-600 text-sm font-medium"></p>
            </div>
          </div>
        </template>
      </div>
    </section>

    <section class="py-24 bg-white">
      <div class="max-w-4xl mx-auto px-6 lg:px-8">
        <div class="bg-gradient-to-br from-gray-50/50 via-white to-blue-50/30 rounded-3xl shadow-xl border border-gray-100 p-12 text-center">
          <div class="w-20 h-20 bg-gradient-to-br from-[#00A0D6] to-[#6AC259] rounded-2xl flex items-center justify-center mx-auto mb-8 shadow-lg">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
          </div>
          
          <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
            <span class="text-gradient">Bergabung dengan Tim</span>
          </h2>
          
          <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-12 leading-relaxed font-light">
            Ingin menjadi bagian dari komunitas teknologi data terdepan? Daftarkan diri Anda dan berkontribusi dalam penelitian dan pengembangan inovasi masa depan.
          </p>
          
          <button id="register-btn" data-modal-open="#registration-modal" class="inline-flex items-center gap-3 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold px-8 py-4 rounded-full hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            <span>Daftar Jadi Member</span>
          </button>
        </div>
      </div>
    </section>

    <div id="registration-modal" class="modal-backdrop hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        
        <div class="p-6 border-b">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold">Pendaftaran Member Laboratorium</h3>
            <button data-modal-close="#registration-modal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        
        <form id="registration-form" class="p-6 space-y-6">
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label for="reg-name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
              <input type="text" id="reg-name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent" placeholder="Masukkan nama lengkap">
            </div>
            
            <div>
              <label for="reg-nim" class="block text-sm font-medium text-gray-700 mb-2">NIM *</label>
              <input type="text" id="reg-nim" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent" placeholder="Contoh: 2241720001">
            </div>
          </div>

          <div>
            <label for="reg-photo" class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
            <input type="file" id="reg-photo" accept=".jpg,.jpeg,.png" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent">
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label for="reg-jurusan" class="block text-sm font-medium text-gray-700 mb-2">Jurusan *</label>
              <select id="reg-jurusan" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent">
                <option value="">Pilih Jurusan</option>
                <option value="Teknik Informatika">Teknik Informatika</option>
                <option value="Sistem Informasi Bisnis">Sistem Informasi Bisnis</option>
                <option value="Teknik Komputer dan Jaringan">Teknik Komputer dan Jaringan</option>
              </select>
            </div>
            
            <div>
              <label for="reg-prodi" class="block text-sm font-medium text-gray-700 mb-2">Program Studi *</label>
              <select id="reg-prodi" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent">
                <option value="">Pilih Program Studi</option>
                <option value="TI">Teknik Informatika (TI)</option>
                <option value="SIB">Sistem Informasi Bisnis (SIB)</option>
                <option value="TKJ">Teknik Komputer dan Jaringan (TKJ)</option>
              </select>
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label for="reg-kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas *</label>
              <input type="text" id="reg-kelas" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent" placeholder="Contoh: TI-3A">
            </div>
            
            <div>
              <label for="reg-tahun" class="block text-sm font-medium text-gray-700 mb-2">Tahun Angkatan *</label>
              <input type="number" id="reg-tahun" required min="2020" max="2030" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent" placeholder="Contoh: 2024">
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label for="reg-tel" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon *</label>
              <input type="tel" id="reg-tel" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent" placeholder="Contoh: 08123456789">
            </div>

            <div>
              <label for="reg-email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
              <input type="email" id="reg-email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent" placeholder="Contoh: user@gmail.com">
            </div>
          </div>

          <div class="flex gap-4 pt-4">
            <button type="button" data-modal-close="#registration-modal" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
              Batal
            </button>
            <button type="submit" class="flex-1 px-4 py-2 bg-[#00A0D6] text-white rounded-lg hover:bg-[#0090C0] transition">
              Kirim Pendaftaran
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <?php 
    // Memuat komponen footer
    require_once '../includes/footer.php';
  ?>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const modal = document.getElementById('registration-modal');
      const openButtons = document.querySelectorAll('[data-modal-open]');
      const closeButtons = document.querySelectorAll('[data-modal-close]');
      const form = document.getElementById('registration-form');

      // Fungsi untuk membuka modal
      openButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          modal.classList.remove('hidden');
          document.body.style.overflow = 'hidden'; // Nonaktifkan scroll body
        });
      });

      // Fungsi untuk menutup modal
      closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          modal.classList.add('hidden');
          document.body.style.overflow = ''; // Aktifkan kembali scroll body
        });
      });

      // Menutup modal ketika mengklik di luar konten modal
      modal.addEventListener('click', (e) => {
        if (e.target.id === 'registration-modal') {
          modal.classList.add('hidden');
          document.body.style.overflow = '';
        }
      });

      // Mengatasi submit form (simulasi)
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Simulasi: Pendaftaran Berhasil Dikirim!');
        // Lakukan AJAX/Fetch API di sini untuk mengirim data ke server
        
        // Tutup modal setelah submit
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        form.reset(); // Reset form
      });
    });
  </script>
</body>
</html>