<?php
$active_page = 'galeri';
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Galeri & Dokumentasi – Lab Data Technologies</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-white text-gray-900">
  <?php require_once '../includes/header.php'; ?>
  <main>
    <!-- Hero Section -->
    <section class="hero-section text-white py-20 lg:py-28">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center">
          <div
            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#00A0D6] to-[#6AC259] text-white px-4 py-2 rounded-full font-medium mb-6 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
              </path>
            </svg>
            <span>Galeri & Dokumentasi</span>
          </div>

          <h1 class="text-4xl lg:text-6xl font-bold text-white-100 mb-6 leading-tight">
            Galeri<br>
            <span class="text-gradient">& Dokumentasi</span>
          </h1>

          <p class="text-xl text-white-600 mb-8 leading-relaxed max-w-4xl mx-auto">
            Koleksi foto dan dokumentasi kegiatan resmi dari <span class="font-semibold text-[#00A0D6]">Laboratorium Data Technologies</span>
          </p>

          <div class="flex flex-col sm:flex-row gap-4 mb-12 justify-center">
            <a href="#galeri-grid"
              class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              Foto Kegiatan
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
            <a href="#galeri-grid"
              class="group inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-[#00A0D6] hover:text-[#00A0D6] transition-all duration-300 hover:scale-105 shadow-sm hover:shadow-md">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
              </svg>
              Dokumentasi Acara
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Premium Main Content -->
    <section class="py-24 bg-gradient-to-br from-gray-50/50 via-white to-blue-50/30">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <!-- Premium Category Filter -->
        <div class="backdrop-blur-xl bg-white/60 border border-white/40 shadow-lg rounded-2xl p-8 mb-16">
          <div class="text-center mb-8">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
              Kategori <span class="bg-gradient-to-r from-[#00A0D6] to-[#6AC259] bg-clip-text text-transparent">Foto</span>
            </h2>
            <p class="text-lg text-gray-600 font-light">Jelajahi koleksi berdasarkan kategori</p>
          </div>

          <div class="flex flex-wrap justify-center gap-4">
            <button class="px-8 py-3 bg-gradient-to-r from-[#00A0D6]/10 to-[#00A0D6]/5 text-[#00A0D6] rounded-full font-semibold shadow-md hover:shadow-lg hover:from-[#00A0D6]/20 hover:to-[#00A0D6]/10 transition-all duration-300">
              🔍 Semua Foto
            </button>
            <button class="px-8 py-3 bg-gradient-to-r from-[#6AC259]/10 to-[#6AC259]/5 text-[#6AC259] rounded-full font-semibold shadow-md hover:shadow-lg hover:from-[#6AC259]/20 hover:to-[#6AC259]/10 transition-all duration-300">
              🎆 Kegiatan
            </button>
            <button class="px-8 py-3 bg-gradient-to-r from-orange-500/10 to-orange-500/5 text-orange-600 rounded-full font-semibold shadow-md hover:shadow-lg hover:from-orange-500/20 hover:to-orange-500/10 transition-all duration-300">
              📷 Dokumentasi
            </button>
          </div>
        </div>

        <!-- Premium Gallery Grid -->
        <div id="galeri-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
          <!-- Premium Empty State -->
          <div class="col-span-full text-center py-20">
            <div class="bg-white/80 backdrop-blur-xl border border-white/50 shadow-2xl rounded-3xl p-16 max-w-2xl mx-auto">
              <!-- Premium Icon -->
              <div class="relative mb-8">
                <div class="w-32 h-32 bg-gradient-to-br from-[#00A0D6]/10 to-[#6AC259]/10 rounded-3xl flex items-center justify-center mx-auto shadow-lg">
                  <svg class="w-16 h-16 text-[#00A0D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
              </div>

              <!-- Premium Content -->
              <h3 class="text-3xl font-bold text-gray-900 mb-4">Galeri Sedang Dipersiapkan</h3>
              <p class="text-lg text-gray-600 mb-8 leading-relaxed max-w-lg mx-auto">
                Tim dokumentasi sedang mengorganisir koleksi foto dan video terbaru.
                <span class="font-semibold text-[#00A0D6]">Segera hadir</span> dengan tampilan yang menakjubkan.
              </p>

              <!-- Premium Actions -->
              <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="location.reload()" class="inline-flex items-center gap-3 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white px-8 py-4 rounded-2xl font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                  <span>Refresh Halaman</span>
                </button>
                <a href="../index.html" class="inline-flex items-center gap-3 bg-white/80 backdrop-blur-xl border border-white/60 text-gray-700 px-8 py-4 rounded-2xl font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                  </svg>
                  <span>Kembali ke Beranda</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php
  require_once '../includes/footer.php';
  ?>
</body>

</html>