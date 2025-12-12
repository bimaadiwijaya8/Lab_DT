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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    .text-gradient {
      background-image: linear-gradient(to right, #00A0D6, #6AC259);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-fill-color: transparent;
    }

    /* Hero Section Style dari berita.php: Background gelap dengan blend mode dan gambar */
    .hero-section {
      background-image: linear-gradient(180deg, #1f2937, #1f2937 50%, rgba(31, 41, 55, 0.9) 100%), url('../assets/img/hero-bg-dark.jpg');
      background-size: cover;
      background-position: center;
      background-blend-mode: multiply;
      /* Pastikan z-index dihapus karena tidak lagi diperlukan overlay terpisah */
      position: relative;
    }
  </style>
</head>

<body class="bg-white text-gray-900 font-[Inter]">
  <?php require_once '../includes/header.php'; ?>
  <main>
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

          <h1 class="text-4xl lg:text-6xl font-bold text-white mb-6 leading-tight">
            Galeri<br>
            <span class="text-gradient">& Dokumentasi</span>
          </h1>

          <p class="text-xl text-white-400 mb-8 leading-relaxed max-w-4xl mx-auto font-light">
            Koleksi foto dan dokumentasi kegiatan resmi dari <span class="font-semibold text-[#00A0D6]">Laboratorium Data Technologies</span>.
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

    <section class="py-24 bg-gradient-to-br from-gray-50/50 via-white to-blue-50/30" aria-labelledby="gallery-heading">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="backdrop-blur-xl bg-white/60 border border-white/40 shadow-xl rounded-2xl p-8 mb-16">
          <div class="text-center mb-8">
            <h2 id="gallery-heading" class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
              Kategori 
              <span class="bg-gradient-to-r from-[#00A0D6] to-[#6AC259] bg-clip-text text-transparent">Foto</span>
            </h2>
            <p class="text-lg text-gray-600 font-light">Jelajahi koleksi berdasarkan kategori</p>
          </div>

          <div role="group" aria-label="Filter Galeri" class="flex flex-wrap justify-center gap-3">
            <button class="px-6 py-2 rounded-full text-sm font-medium transition-colors bg-[#00A0D6] text-white shadow-md">
              🔍 Semua Foto
            </button>
            <button class="px-6 py-2 rounded-full text-sm font-medium transition-colors bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
              🎆 Kegiatan
            </button>
            <button class="px-6 py-2 rounded-full text-sm font-medium transition-colors bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
              📷 Dokumentasi
            </button>
          </div>
        </div>

        <div id="galeri-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 min-h-[50vh]">
          <div class="col-span-full text-center py-20">
            <div class="bg-white border border-gray-100 shadow-2xl shadow-blue-500/10 rounded-3xl p-16 max-w-2xl mx-auto">
              <div class="relative mb-8">
                <div class="w-32 h-32 bg-gradient-to-br from-[#00A0D6]/10 to-[#6AC259]/10 rounded-3xl flex items-center justify-center mx-auto shadow-lg ring-4 ring-white">
                  <svg class="w-16 h-16 text-[#00A0D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
              </div>

              <h3 class="text-3xl font-bold text-gray-900 mb-4">Galeri Sedang Dipersiapkan 🚧</h3>
              <p class="text-lg text-gray-600 mb-8 leading-relaxed max-w-lg mx-auto">
                Tim dokumentasi sedang mengorganisir koleksi foto dan video terbaru.
                <span class="font-bold text-[#00A0D6]">Segera hadir</span> dengan tampilan yang menakjubkan.
              </p>

              <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button type="button" onclick="window.location.reload()" class="inline-flex items-center gap-3 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white px-8 py-4 rounded-2xl font-semibold shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                  <span>Refresh Halaman</span>
                </button>
                <a href="../index.html" class="inline-flex items-center gap-3 bg-white border border-gray-300 text-gray-700 px-8 py-4 rounded-2xl font-semibold shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
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