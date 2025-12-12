<?php
$active_page = 'anggota';
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal Web Profil Laboratorium Data Technologies – Politeknik Negeri Malang</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-white text-gray-900">
  <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <a href="index.html" class="group">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-12 w-12 rounded-xl to-blue-600 items-center justify-center group-hover:shadow-xl transition-all duration-300">
              <img src="assets/img/logo.png" alt="" class="w-full h-full object-cover rounded-xl">
            </span>
            <div>
              <div class="font-bold text-[#1f2937] text-lg">Lab Data Technologies</div>
              <div class="text-sm text-gray-500 font-medium">Politeknik Negeri Malang</div>
            </div>
          </div>
        </a>

        <!-- Navigation -->
        <nav class="hidden lg:flex items-center gap-0,5">
          <a class="relative px-6 py-2 text-[15px] font-semibold text-[#00A0D6] transition-all duration-300 group" href="index.php">
            <span class="relative z-10">Beranda</span>
            <div class="absolute inset-0 bg-blue-50 rounded-lg opacity-100 group-hover:opacity-80 transition-opacity duration-300"></div>
            <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-8 h-0.5 bg-[#00A0D6] rounded-full"></div>
          </a>
          <a class="relative px-6 py-2 text-[15px] font-medium text-[#1f2937] hover:text-[#00A0D6] transition-all duration-300 group" href="pages/profil-lab.php">
            <span class="relative z-10">Profil Lab</span>
            <div class="absolute inset-0 bg-gray-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </a>
          <a class="relative px-6 py-2 text-[15px] font-medium text-[#1f2937] hover:text-[#00A0D6] transition-all duration-300 group" href="pages/berita.php">
            <span class="relative z-10">Berita</span>
            <div class="absolute inset-0 bg-gray-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </a>
          <a class="relative px-6 py-2 text-[15px] font-medium text-[#1f2937] hover:text-[#00A0D6] transition-all duration-300 group" href="pages/galeri.php">
            <span class="relative z-10">Galeri</span>
            <div class="absolute inset-0 bg-gray-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </a>
          <a class="relative px-6 py-2 text-[15px] font-medium text-[#1f2937] hover:text-[#00A0D6] transition-all duration-300 group" href="pages/penelitian.php">
            <span class="relative z-10">Publikasi</span>
            <div class="absolute inset-0 bg-gray-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </a>
          <a class="relative px-6 py-2 text-[15px] font-medium text-[#1f2937] hover:text-[#00A0D6] transition-all duration-300 group" href="pages/fasilitas.php">
            <span class="relative z-10">Fasilitas</span>
            <div class="absolute inset-0 bg-gray-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </a>
          <a class="relative px-6 py-2 text-[15px] font-medium text-[#1f2937] hover:text-[#00A0D6] transition-all duration-300 group" href="pages/anggota.php">
            <span class="relative z-10">Anggota</span>
            <div class="absolute inset-0 bg-gray-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </a>
          <a class="relative px-6 py-2 text-[15px] font-medium text-[#1f2937] hover:text-[#00A0D6] transition-all duration-300 group" href="pages/kontak.php">
            <span class="relative z-10">Kontak</span>
            <div class="absolute inset-0 bg-gray-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </a>
        </nav>

        <!-- Mobile Menu Button -->
        <button class="lg:hidden inline-flex items-center justify-center h-11 w-11 rounded-xl border border-gray-200 hover:border-[#00A0D6] hover:bg-blue-50 transition-all duration-300 group" data-nav-toggle="#mnav">
          <svg class="w-5 h-5 text-gray-600 group-hover:text-[#00A0D6] transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
      </div>

      <!-- Mobile Navigation -->
      <div id="mnav" class="lg:hidden hidden pb-6 border-t border-gray-100 mt-4 animate-slide-down">
        <div class="grid gap-2 text-[15px] font-medium pt-6">
          <a class="py-3 px-4 text-[#00A0D6] font-semibold bg-gradient-to-r from-blue-50 to-blue-50/50 rounded-xl border border-blue-100" href="index.php">
            <div class="flex items-center gap-3">
              <div class="w-2 h-2 bg-[#00A0D6] rounded-full"></div>
              Beranda
            </div>
          </a>
          <a class="py-3 px-4 text-[#1f2937] hover:text-[#00A0D6] hover:bg-gray-50 rounded-xl transition-all duration-300" href="pages/profil-lab.php">
            <div class="flex items-center gap-3">
              <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
              Profil Lab
            </div>
          </a>
          <a class="py-3 px-4 text-[#1f2937] hover:text-[#00A0D6] hover:bg-gray-50 rounded-xl transition-all duration-300" href="pages/berita.php">
            <div class="flex items-center gap-3">
              <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
              Berita
            </div>
          </a>
          <a class="py-3 px-4 text-[#1f2937] hover:text-[#00A0D6] hover:bg-gray-50 rounded-xl transition-all duration-300" href="pages/galeri.php">
            <div class="flex items-center gap-3">
              <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
              Galeri
            </div>
          </a>
          <a class="py-3 px-4 text-[#1f2937] hover:text-[#00A0D6] hover:bg-gray-50 rounded-xl transition-all duration-300" href="pages/penelitian.php">
            <div class="flex items-center gap-3">
              <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
              Publikasi
            </div>
          </a>
          <a class="py-3 px-4 text-[#1f2937] hover:text-[#00A0D6] hover:bg-gray-50 rounded-xl transition-all duration-300" href="pages/fasilitas.php">
            <div class="flex items-center gap-3">
              <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
              Fasilitas
            </div>
          </a>
          <a class="py-3 px-4 text-[#1f2937] hover:text-[#00A0D6] hover:bg-gray-50 rounded-xl transition-all duration-300" href="pages/anggota.php">
            <div class="flex items-center gap-3">
              <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
              Anggota
            </div>
          </a>
          <a class="py-3 px-4 text-[#1f2937] hover:text-[#00A0D6] hover:bg-gray-50 rounded-xl transition-all duration-300" href="pages/kontak.php">
            <div class="flex items-center gap-3">
              <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
              Kontak
            </div>
          </a>
        </div>
      </div>
    </div>
  </header>
  <main>
    <!-- Hero Section - 2 Column Layout -->
    <section class="bg-gradient-to-br from-blue-50 via-white to-green-50 py-20 lg:py-28">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
          <!-- Left Column - Text Content -->
          <div class="text-left">
            <div class="inline-flex items-center gap-2 bg-gradient-to-r from-[#00A0D6] to-[#6AC259] text-white px-4 py-2 rounded-full font-medium mb-6 text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
              <span>Laboratorium Terdepan</span>
            </div>

            <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight" data-editable="heading" data-element-id="hero-title">
              Laboratorium<br>
              <span class="text-gradient">Data Technologies</span>
            </h1>

            <p class="text-xl text-gray-600 mb-8 leading-relaxed" data-editable="text" data-element-id="hero-description">
              Pusat penelitian dan pengembangan teknologi data terdepan di Politeknik Negeri Malang.
              Mengintegrasikan <span class="font-semibold text-[#00A0D6]">AI</span>,
              <span class="font-semibold text-[#6AC259]">Machine Learning</span>, dan
              <span class="font-semibold text-purple-600">Big Data Analytics</span> untuk solusi masa depan.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 mb-12">
              <a href="pages/profil-lab.html" class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Pelajari Lebih Lanjut
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </a>
              <a href="pages/penelitian.html" class="group inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-[#00A0D6] hover:text-[#00A0D6] transition-all duration-300 hover:scale-105 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Lihat Penelitian
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </a>
            </div>
          </div>

          <!-- Right Column - Lab Image -->
          <div class="relative">
            <div class="relative rounded-3xl overflow-hidden shadow-2xl">
              <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=1200&auto=format&fit=crop"
                alt="Lab Data Technologies"
                class="w-full h-96 lg:h-[500px] object-cover" data-editable="image" data-element-id="hero-image">
              <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>

              <!-- Floating Elements -->
              <div class="absolute top-6 right-6 bg-white/90 backdrop-blur rounded-2xl p-4 shadow-lg">
                <div class="flex items-center gap-3">
                  <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                  <span class="text-sm font-medium text-gray-700">Lab Aktif</span>
                </div>
              </div>

              <div class="absolute bottom-6 left-6 bg-white/90 backdrop-blur rounded-2xl p-4 shadow-lg">
                <div class="text-sm text-gray-600">Fasilitas Modern</div>
                <div class="font-bold text-gray-900">25+ Workstation</div>
              </div>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute -top-4 -left-4 w-24 h-24 bg-[#00A0D6]/10 rounded-full blur-xl"></div>
            <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-[#6AC259]/10 rounded-full blur-xl"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
          <!-- Publikasi Ilmiah -->
          <div class="stat-card rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-lg border border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-[#00A0D6] to-[#0078A6] rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-2">50+</div>
            <div class="text-sm font-medium text-gray-600">Publikasi Ilmiah</div>
          </div>

          <!-- Kolaborasi Industri -->
          <div class="stat-card rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-lg border border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-[#6AC259] to-[#4BAE45] rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
              </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-2">20+</div>
            <div class="text-sm font-medium text-gray-600">Kolaborasi Industri</div>
          </div>

          <!-- Alumni Sukses -->
          <div class="stat-card rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-lg border border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
              </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-2">500+</div>
            <div class="text-sm font-medium text-gray-600">Alumni Sukses</div>
          </div>

          <!-- Penghargaan -->
          <div class="stat-card rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-lg border border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
              </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-2">15+</div>
            <div class="text-sm font-medium text-gray-600">Penghargaan</div>
          </div>
        </div>
      </div>
    </section>

    <!-- About Lab Section -->
    <section class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
          <!-- Left Column - Lab Image -->
          <div class="relative">
            <div class="relative rounded-3xl overflow-hidden shadow-xl">
              <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?q=80&w=1200&auto=format&fit=crop"
                alt="Ruangan Lab Data Technologies"
                class="w-full h-80 lg:h-96 object-cover" data-editable="image" data-element-id="about-image">
              <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>

              <!-- Lab Info Overlay -->
              <div class="absolute bottom-6 left-6 right-6">
                <div class="bg-white/90 backdrop-blur rounded-2xl p-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="font-bold text-gray-900">Fasilitas Modern</div>
                      <div class="text-sm text-gray-600">Teknologi Terkini</div>
                    </div>
                    <div class="flex items-center gap-2">
                      <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                      <span class="text-xs font-medium text-gray-700">Aktif 24/7</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute -top-6 -right-6 w-32 h-32 bg-gradient-to-br from-[#00A0D6]/20 to-[#6AC259]/20 rounded-full blur-2xl"></div>
          </div>

          <!-- Right Column - Text Content -->
          <div>
            <div class="inline-flex items-center gap-2 bg-[#00A0D6]/10 text-[#00A0D6] px-4 py-2 rounded-full font-medium mb-6 text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              <span>Tentang Laboratorium</span>
            </div>

            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6" data-editable="heading" data-element-id="about-title">
              Pusat Inovasi <span class="text-gradient">Teknologi Data</span>
            </h2>

            <p class="text-lg text-gray-600 mb-6 leading-relaxed" data-editable="text" data-element-id="about-description">
              Laboratorium Data Technologies merupakan pusat unggulan penelitian dan pengembangan di bidang teknologi data.
              Kami berkomitmen menghasilkan lulusan yang kompeten dan penelitian berkualitas tinggi.
            </p>

            <div class="space-y-4 mb-8">
              <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-[#00A0D6]/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-[#00A0D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div>
                  <div class="font-semibold text-gray-900">Penelitian Terdepan</div>
                  <div class="text-gray-600">Fokus pada AI, Machine Learning, dan Big Data Analytics</div>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-[#6AC259]/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div>
                  <div class="font-semibold text-gray-900">Kolaborasi Industri</div>
                  <div class="text-gray-600">Kerjasama dengan perusahaan teknologi terkemuka</div>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-purple-500/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div>
                  <div class="font-semibold text-gray-900">Fasilitas Modern</div>
                  <div class="text-gray-600">Peralatan dan infrastruktur teknologi terkini</div>
                </div>
              </div>
            </div>

            <a href="pages/profil-lab.html" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg">
              Pelajari Lebih Lanjut
              <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Mengapa Memilih Lab Data Technologies Section -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
          <div class="inline-flex items-center gap-2 bg-gradient-to-r from-[#00A0D6] to-[#6AC259] text-white px-4 py-2 rounded-full font-medium mb-6 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Keunggulan Laboratorium</span>
          </div>
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4" data-editable="heading" data-element-id="why-choose-title">
            Mengapa Memilih <span class="text-gradient">Lab Data Technologies</span>
          </h2>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto" data-editable="text" data-element-id="why-choose-description">
            Keunggulan dan keunikan yang menjadikan laboratorium kami pilihan terbaik untuk pengembangan teknologi data
          </p>
        </div>

        <!-- Features Grid -->
        <div class="grid md:grid-cols-3 gap-8">
          <!-- Visi Inovasi -->
          <div class="card-magic rounded-2xl p-8 text-center hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-lg border border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-[#00A0D6] to-[#0078A6] rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Visi Inovasi</h3>
            <p class="text-gray-600 leading-relaxed mb-6">
              Mengembangkan solusi teknologi data yang inovatif dan berkelanjutan untuk masa depan yang lebih baik.
            </p>
            <div class="flex items-center justify-between">
              <a href="pages/profil-lab.html" class="inline-flex items-center text-[#00A0D6] font-bold hover:text-blue-700 transition-all duration-300 group">
                Pelajari visi kami
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
              </a>
              <div class="text-3xl opacity-20 group-hover:opacity-60 transition-opacity">💡</div>
            </div>
          </div>

          <!-- Kegiatan Riset -->
          <div class="card-magic rounded-2xl p-8 text-center hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-lg border border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-[#6AC259] to-[#4BAE45] rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Kegiatan Riset</h3>
            <p class="text-gray-600 leading-relaxed mb-6">
              Program penelitian aktif dengan fokus pada AI, Machine Learning, dan Big Data Analytics yang berkelanjutan.
            </p>
            <div class="flex items-center justify-between">
              <a href="pages/penelitian.html" class="inline-flex items-center text-[#6AC259] font-bold hover:text-green-700 transition-all duration-300 group">
                Lihat penelitian
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
              </a>
              <div class="text-3xl opacity-20 group-hover:opacity-60 transition-opacity">🔬</div>
            </div>
          </div>

          <!-- Tim Ahli -->
          <div class="card-magic rounded-2xl p-8 text-center hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-lg border border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Tim Ahli</h3>
            <p class="text-gray-600 leading-relaxed mb-6">
              Didukung oleh tim dosen, peneliti, dan mahasiswa berpengalaman yang berdedikasi dalam pengembangan teknologi data.
            </p>
            <div class="flex items-center justify-between">
              <a href="pages/anggota.html" class="inline-flex items-center text-purple-600 font-bold hover:text-purple-700 transition-all duration-300 group">
                Kenali tim kami
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
              </a>
              <div class="text-3xl opacity-20 group-hover:opacity-60 transition-opacity">👥</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Jelajahi Lebih Lanjut Section -->
    <section class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4" data-editable="heading" data-element-id="explore-title">
            🚀 Jelajahi Lebih Lanjut
          </h2>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto" data-editable="text" data-element-id="explore-description">
            Temukan lebih banyak informasi tentang fasilitas, galeri, berita, dan cara menghubungi kami
          </p>
        </div>

        <!-- Quick Access Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <a href="pages/fasilitas.html" class="group p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl hover:from-blue-100 hover:to-blue-200 transition-all duration-300 text-center hover:scale-105 shadow-sm hover:shadow-lg">
            <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">🏢</div>
            <div class="font-bold text-gray-900 mb-2">Fasilitas</div>
            <div class="text-sm text-gray-600">Peralatan & infrastruktur modern</div>
          </a>
          <a href="pages/galeri.html" class="group p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl hover:from-green-100 hover:to-green-200 transition-all duration-300 text-center hover:scale-105 shadow-sm hover:shadow-lg">
            <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">📸</div>
            <div class="font-bold text-gray-900 mb-2">Galeri</div>
            <div class="text-sm text-gray-600">Dokumentasi kegiatan lab</div>
          </a>
          <a href="pages/berita.html" class="group p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl hover:from-purple-100 hover:to-purple-200 transition-all duration-300 text-center hover:scale-105 shadow-sm hover:shadow-lg">
            <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">📰</div>
            <div class="font-bold text-gray-900 mb-2">Berita</div>
            <div class="text-sm text-gray-600">Update & pengumuman terbaru</div>
          </a>
          <a href="pages/kontak.html" class="group p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl hover:from-orange-100 hover:to-orange-200 transition-all duration-300 text-center hover:scale-105 shadow-sm hover:shadow-lg">
            <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">📞</div>
            <div class="font-bold text-gray-900 mb-2">Kontak</div>
            <div class="text-sm text-gray-600">Hubungi tim laboratorium</div>
          </a>
        </div>
      </div>
    </section>

    <!-- Berita Terbaru Section -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <!-- Section Header -->
        <div class="flex items-end justify-between gap-6 mb-12">
          <div>
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2" data-editable="heading" data-element-id="news-title">Berita Terbaru</h2>
            <p class="text-lg text-gray-600" data-editable="text" data-element-id="news-description">Informasi kegiatan, rilis, dan pengumuman terbaru</p>
          </div>
          <a href="pages/berita.html" class="text-[#00A0D6] font-semibold hover:text-blue-700 transition-colors">Lihat semua</a>
        </div>

        <!-- News Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
          <!-- News Item 1 -->
          <article class="card-magic rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
            <div class="relative">
              <img src="https://images.unsplash.com/photo-1677442136019-21780ecad995?q=80&w=800&auto=format&fit=crop"
                alt="AI Research"
                class="w-full h-48 object-cover">
              <div class="absolute top-4 left-4">
                <span class="bg-[#00A0D6] text-white px-3 py-1 rounded-full text-xs font-medium">Penelitian</span>
              </div>
            </div>
            <div class="p-6">
              <div class="text-sm text-gray-500 mb-2">15 November 2024</div>
              <h3 class="font-bold text-lg text-gray-900 mb-3 line-clamp-2">
                Breakthrough dalam Algoritma Machine Learning untuk Prediksi Data
              </h3>
              <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                Tim peneliti lab berhasil mengembangkan algoritma baru yang meningkatkan akurasi prediksi hingga 95%...
              </p>
              <a href="pages/berita.html" class="inline-flex items-center text-[#00A0D6] font-semibold hover:text-blue-700 transition-colors">
                Lihat Selengkapnya
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </a>
            </div>
          </article>

          <!-- News Item 2 -->
          <article class="card-magic rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
            <div class="relative">
              <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=800&auto=format&fit=crop"
                alt="Data Analytics"
                class="w-full h-48 object-cover">
              <div class="absolute top-4 left-4">
                <span class="bg-[#6AC259] text-white px-3 py-1 rounded-full text-xs font-medium">Publikasi</span>
              </div>
            </div>
            <div class="p-6">
              <div class="text-sm text-gray-500 mb-2">12 November 2024</div>
              <h3 class="font-bold text-lg text-gray-900 mb-3 line-clamp-2">
                Publikasi Jurnal Internasional tentang Big Data Analytics
              </h3>
              <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                Paper penelitian kami diterima di jurnal IEEE dengan impact factor tinggi, membahas inovasi dalam analisis big data...
              </p>
              <a href="pages/penelitian.html" class="inline-flex items-center text-[#6AC259] font-semibold hover:text-green-700 transition-colors">
                Lihat Selengkapnya
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </a>
            </div>
          </article>

          <!-- News Item 3 -->
          <article class="card-magic rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
            <div class="relative">
              <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=800&auto=format&fit=crop"
                alt="Workshop"
                class="w-full h-48 object-cover">
              <div class="absolute top-4 left-4">
                <span class="bg-purple-500 text-white px-3 py-1 rounded-full text-xs font-medium">Kegiatan</span>
              </div>
            </div>
            <div class="p-6">
              <div class="text-sm text-gray-500 mb-2">10 November 2024</div>
              <h3 class="font-bold text-lg text-gray-900 mb-3 line-clamp-2">
                Workshop Machine Learning untuk Mahasiswa dan Industri
              </h3>
              <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                Kegiatan workshop intensif selama 3 hari dengan peserta dari berbagai universitas dan perusahaan teknologi...
              </p>
              <a href="pages/galeri.html" class="inline-flex items-center text-purple-500 font-semibold hover:text-purple-700 transition-colors">
                Lihat Selengkapnya
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </a>
            </div>
          </article>
        </div>

      </div>
    </section>

    <!-- Riset Unggulan Section -->
    <section class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
          <!-- Left Column - Research Categories -->
          <div>
            <div class="inline-flex items-center gap-2 bg-gradient-to-r from-[#00A0D6] to-[#6AC259] text-white px-4 py-2 rounded-full font-medium mb-6 text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span>Bidang Penelitian</span>
            </div>

            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
              <span class="text-gradient">Riset Unggulan</span>
            </h2>
            <p class="text-lg text-gray-600 mb-8 leading-relaxed">
              Topik prioritas pengembangan dan penelitian terapan yang menjadi fokus utama laboratorium kami.
            </p>

            <div class="grid sm:grid-cols-2 gap-4">
              <div class="p-6 rounded-2xl border border-gray-200 hover:shadow-lg hover:scale-105 transition-all duration-300 bg-white">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#00A0D6] to-[#0078A6] flex items-center justify-center text-white font-bold mb-4">AI</div>
                <div class="font-bold text-gray-900 mb-2">AI Terapan</div>
                <div class="text-sm text-gray-600">Computer vision, NLP, MLOps</div>
              </div>

              <div class="p-6 rounded-2xl border border-gray-200 hover:shadow-lg hover:scale-105 transition-all duration-300 bg-white">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#6AC259] to-[#4BAE45] flex items-center justify-center text-white font-bold mb-4">IoT</div>
                <div class="font-bold text-gray-900 mb-2">IoT & Edge</div>
                <div class="text-sm text-gray-600">Sensor, data streaming, integrasi</div>
              </div>

              <div class="p-6 rounded-2xl border border-gray-200 hover:shadow-lg hover:scale-105 transition-all duration-300 bg-white">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#00A0D6] to-[#0078A6] flex items-center justify-center text-white font-bold mb-4">DB</div>
                <div class="font-bold text-gray-900 mb-2">Data Engineering</div>
                <div class="text-sm text-gray-600">ETL, warehousing, governance</div>
              </div>

              <div class="p-6 rounded-2xl border border-gray-200 hover:shadow-lg hover:scale-105 transition-all duration-300 bg-white">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#6AC259] to-[#4BAE45] flex items-center justify-center text-white font-bold mb-4">WEB</div>
                <div class="font-bold text-gray-900 mb-2">Smart Web</div>
                <div class="text-sm text-gray-600">Aplikasi cerdas dan interaktif</div>
              </div>
            </div>
          </div>

          <!-- Right Column - Research Image -->
          <div class="relative">
            <div class="rounded-3xl overflow-hidden shadow-xl border border-gray-200">
              <img class="w-full h-96 object-cover" src="https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1500&auto=format&fit=crop" alt="Research highlight" />
            </div>

            <!-- Decorative Elements -->
            <div class="absolute -top-6 -left-6 w-32 h-32 bg-gradient-to-br from-[#00A0D6]/20 to-[#6AC259]/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-gradient-to-br from-[#6AC259]/20 to-[#00A0D6]/20 rounded-full blur-2xl"></div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="bg-[#0F1B2D] border-t border-gray-700">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-16">
      <div class="grid lg:grid-cols-3 gap-12">
        <div>
          <div class="flex items-center gap-3 mb-6">
            <span class="inline-flex h-12 w-12 rounded-xl to-blue-600 items-center justify-center group-hover:shadow-xl transition-all duration-300">
              <img src="assets/img/logo.png" alt="" class="w-full h-full object-cover rounded-xl"></span>
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
            <span>Jl. Soekarno Hatta No. 9, Malang, Jawa Timur</span>
          </div>
        </div>

        <!-- Center Column - Quick Navigation -->
        <div>
          <h3 class="font-bold text-lg text-white mb-6">Navigasi Cepat</h3>
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-3">
              <a href="pages/profil-lab.html" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Profil Lab</span>
              </a>
              <a href="pages/fasilitas.html" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Fasilitas</span>
              </a>
              <a href="pages/penelitian.html" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Publikasi</span>
              </a>
              <a href="pages/berita.html" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Berita</span>
              </a>
            </div>
            <div class="space-y-3">
              <a href="pages/galeri.html" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Galeri</span>
              </a>
              <a href="pages/anggota.html" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Anggota</span>
              </a>
              <a href="pages/kontak.html" class="flex items-center gap-2 text-gray-300 hover:text-[#00A0D6] transition-colors group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Kontak</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Right Column - Contact & Social Media -->
        <div>
          <h3 class="font-bold text-lg text-white mb-6">Kontak & Media Sosial</h3>

          <!-- Contact Info -->
          <div class="space-y-4 mb-8">
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 bg-[#00A0D6]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-[#00A0D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
              </div>
              <div>
                <div class="font-medium text-white">Email</div>
                <div class="text-gray-300">lab.dt@polinema.ac.id</div>
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
                <div class="text-gray-300">(0341) 404040</div>
              </div>
            </div>
          </div>

          <!-- Social Media -->
          <div>
            <div class="font-medium text-white mb-4">Ikuti Kami</div>
            <div class="flex gap-3">
              <a href="#" class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                </svg>
              </a>
              <a href="#" class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-white hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                </svg>
              </a>
              <a href="#" class="w-10 h-10 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center text-white hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Border -->
      <div class="mt-12 pt-8 border-t border-gray-700 text-center">
        <p class="text-sm text-gray-400">
          © 2025 Laboratorium Data Technologies – Politeknik Negeri Malang. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
  <?php
  require_once 'includes/footer.php';
  ?>
</body>

</html>