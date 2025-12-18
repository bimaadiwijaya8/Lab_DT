<?php
// footer.php

// Include database connection
include '../assets/php/db_connect.php';

// Get all settings from database
$settings = [];
try {
  $pdo = Database::getConnection();
  $stmt = $pdo->prepare("SELECT key, value FROM settings");
  $stmt->execute();
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($results as $result) {
    $settings[$result['key']] = $result['value'];
  }
} catch (Exception $e) {
  // Keep default values if there's an error
  $settings = [];
}

// Get logo from database settings
// Get logo from settings or use default
$logo_path = isset($settings['logo_utama']) && !empty($settings['logo_utama']) && file_exists($settings['logo_utama']) ? $settings['logo_utama'] : '../assets/img/logo.png';

// Variabel $current_year harus didefinisikan di file utama sebelum memanggil footer.
if (!isset($current_year)) {
    // Jika lupa didefinisikan, gunakan tahun saat ini sebagai default
    $current_year = date('Y');
}
?>

<footer class="bg-[#0F1B2D] border-t border-gray-700">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-16">
      <div class="grid lg:grid-cols-3 gap-12">
        <div>
          <div class="flex items-center gap-3 mb-6">
            <span class="inline-flex h-12 w-12 rounded-xl items-center justify-center">
              <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="" class="w-full h-full object-cover rounded-xl">
            </span>
            <div>
              <div class="font-bold text-xl text-white">Lab Data Technologies</div>
              <div class="text-sm text-gray-300">Politeknik Negeri Malang</div>
            </div>
          </div>
          <p class="text-gray-300 leading-relaxed mb-6">
            Pusat penelitian dan pengembangan teknologi data terdepan yang mengintegrasikan AI, Machine Learning, dan Big Data Analytics untuk solusi masa depan.
          </p>
          <div class="flex items-center gap-2 text-sm text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span><?php echo htmlspecialchars($settings['alamat'] ?? 'Jl. Soekarno Hatta No. 9, Malang, Jawa Timur'); ?></span>
          </div>
        </div>

        <div>
          <h3 class="font-bold text-lg text-white mb-6">Navigasi Cepat</h3>
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-3">
              <a href="profil-lab.php" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Profil Lab</span>
              </a>
              <a href="fasilitas.php" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Fasilitas</span>
              </a>
              <a href="penelitian.php" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Publikasi</span>
              </a>
              <a href="berita.php" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Berita</span>
              </a>
            </div>
            <div class="space-y-3">
              <a href="galeri.php" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Galeri</span>
              </a>
              <a href="anggota.php" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Anggota</span>
              </a>
              <a href="kontak.php" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Kontak</span>
              </a>
            </div>
          </div>
        </div>

        <div>
          <h3 class="font-bold text-lg text-white mb-6">Kontak & Media Sosial</h3>

          <div class="space-y-4 mb-8">
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 bg-[#00A0D6]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-[#00A0D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
              </div>
              <div>
                <div class="font-medium text-white">Email</div>
                <div class="text-gray-300"><?php echo htmlspecialchars($settings['email'] ?? 'lab.dt@polinema.ac.id'); ?></div>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="w-8 h-8 bg-[#6AC259]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
              </div>
              <div>
                <div class="font-medium text-white">Telepon</div>
                <div class="text-gray-300"><?php echo htmlspecialchars($settings['no_telepon'] ?? '(0341) 404040'); ?></div>
              </div>
            </div>
          </div>

          <div>
            <div class="font-medium text-white mb-4">Ikuti Kami</div>
            <div class="flex gap-3">
              <a href="<?php echo htmlspecialchars($settings['medsos_linkedin'] ?? '#'); ?>" class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white hover:scale-110 transition-transform" target="_blank" rel="noopener noreferrer">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                </svg>
              </a>
              <a href="<?php echo htmlspecialchars($settings['medsos_youtube'] ?? '#'); ?>" class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-white hover:scale-110 transition-transform" target="_blank" rel="noopener noreferrer">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                </svg>
              </a>
              <a href="<?php echo htmlspecialchars($settings['medsos_instagram'] ?? '#'); ?>" class="w-10 h-10 bg-gradient-to-br from-red-500 to-purple-600 rounded-xl flex items-center justify-center text-white hover:scale-110 transition-transform" target="_blank" rel="noopener noreferrer">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1 1 12.324 0 6.162 6.162 0 0 1-12.324 0zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm4.965-10.405a1.44 1.44 0 1 1 2.881.001 1.44 1.44 0 0 1-2.881-.001z" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-12 pt-8 border-t border-gray-700 text-center">
        <p class="text-sm text-gray-400">
          &copy; <?php echo $current_year; ?> Laboratorium Data Technologies – Politeknik Negeri Malang. All rights reserved.
        </p>
      </div>
    </div>
</footer>