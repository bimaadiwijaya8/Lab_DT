<?php
$active_page = 'penelitian';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Penelitian & Publikasi – Lab Data Technologies</title>
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
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
              </path>
            </svg>
            <span>Penelitian & Publikasi</span>
          </div>

          <h1 class="text-4xl lg:text-6xl font-bold text-white-900 mb-6 leading-tight">
            Penelitian<br>
            <span class="text-gradient">& Publikasi</span>
          </h1>

          <p class="text-xl text-white-600 mb-8 leading-relaxed max-w-4xl mx-auto">
            Artikel, jurnal, proceeding, dan riset aktif dari <span class="font-semibold text-[#00A0D6]">Laboratorium Data Technologies</span>
          </p>

          <div class="flex flex-col sm:flex-row gap-4 mb-12 justify-center">
            <a href="#riset-list"
              class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              Publikasi Jurnal
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
            <a href="#riset-list"
              class="group inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-[#00A0D6] hover:text-[#00A0D6] transition-all duration-300 hover:scale-105 shadow-sm hover:shadow-md">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              Penelitian Aktif
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Premium Research Section -->
    <section class="py-24 bg-gradient-to-br from-gray-50/50 via-white to-blue-50/30">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <!-- Section Header -->
        <div class="mb-12">
          <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Riset Aktif</h2>
            <p class="text-gray-600">Proyek penelitian yang sedang berjalan</p>
          </div>
        </div>

        <!-- Research Grid -->
        <div id="riset-list" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Data riset akan dimuat dari API -->
          <div class="col-span-full">
            <div class="empty-state text-center py-16 bg-white/60 backdrop-blur-xl border border-white/40 rounded-3xl shadow-lg">
              <div class="w-20 h-20 bg-gradient-to-br from-[#00A0D6]/10 to-[#6AC259]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-[#00A0D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Riset Aktif</h3>
              <p class="text-gray-600 mb-6">Data riset akan ditampilkan setelah tersedia dari sistem</p>
              <div class="inline-flex items-center gap-2 text-sm text-[#00A0D6] font-medium">
                <div class="w-2 h-2 bg-[#00A0D6] rounded-full animate-pulse"></div>
                Menunggu data dari API
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Premium Publications Section -->
    <section class="py-24 bg-white">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <!-- Section Header with Filters -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12">
          <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Publikasi Ilmiah</h2>
            <p class="text-gray-600">Artikel, jurnal, dan proceeding terpublikasi</p>
          </div>
          
          <!-- Premium Filter Controls -->
          <div class="flex flex-wrap items-center gap-3">
            <select id="pub-filter" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:border-[#00A0D6] focus:border-[#00A0D6] focus:ring-2 focus:ring-[#00A0D6]/20 transition-all duration-200">
              <option value="all">Semua Tipe</option>
              <option value="Jurnal">Jurnal</option>
              <option value="Proceeding">Proceeding</option>
              <option value="Artikel">Artikel</option>
            </select>
            <select id="pub-sort" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:border-[#00A0D6] focus:border-[#00A0D6] focus:ring-2 focus:ring-[#00A0D6]/20 transition-all duration-200">
              <option value="year-desc">Tahun Terbaru</option>
              <option value="year-asc">Tahun Terlama</option>
            </select>
          </div>
        </div>

        <!-- Publications Grid -->
        <div id="publikasi-list" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Data publikasi akan dimuat dari API -->
          <div class="col-span-full">
            <div class="empty-state text-center py-16 bg-gradient-to-br from-gray-50/50 to-blue-50/30 border border-gray-100 rounded-3xl">
              <div class="w-20 h-20 bg-gradient-to-br from-[#00A0D6]/10 to-[#6AC259]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-[#00A0D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Publikasi</h3>
              <p class="text-gray-600 mb-6">Data publikasi akan ditampilkan setelah tersedia dari sistem</p>
              <div class="inline-flex items-center gap-2 text-sm text-[#00A0D6] font-medium">
                <div class="w-2 h-2 bg-[#00A0D6] rounded-full animate-pulse"></div>
                Menunggu data dari API
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
