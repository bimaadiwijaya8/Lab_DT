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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* Gradient Utama untuk Judul di Hero dan Konten (dari file lain) */
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
      position: relative;
    }
    
    /* Style untuk Publikasi Card, disesuaikan agar lebih premium dan konsisten */
    .publication-card {
        transition: all 0.3s ease;
    }
    .publication-card:hover {
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08); /* Shadow lebih tebal saat hover */
        transform: translateY(-3px);
    }
    .publication-category {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.75rem; /* text-xs */
      font-weight: 500; /* font-medium */
    }
    
    /* Warna kategori, disamakan dengan warna brand */
    .category-journal {
        background-color: rgba(0, 160, 214, 0.1); /* #00A0D6/10 */
        color: #00A0D6; 
        border: 1px solid rgba(0, 160, 214, 0.2);
    }
    .category-conference {
        background-color: rgba(106, 194, 89, 0.1); /* #6AC259/10 */
        color: #6AC259;
        border: 1px solid rgba(106, 194, 89, 0.2);
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
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
              </path>
            </svg>
            <span>Penelitian & Publikasi</span>
          </div>

          <h1 class="text-4xl lg:text-6xl font-bold text-white mb-6 leading-tight">
            Penelitian<br>
            <span class="text-gradient">& Publikasi</span>
          </h1>

          <p class="text-xl text-white/80 mb-8 leading-relaxed max-w-4xl mx-auto font-light">
            Jelajahi karya ilmiah terbaru dan kolaborasi riset yang dihasilkan oleh <span class="font-semibold text-[#00A0D6]">Laboratorium Data Technologies</span>.
          </p>

          <div class="flex flex-col sm:flex-row gap-4 mb-12 justify-center">
            <a href="#publication-list"
              class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"></path>
              </svg>
              Lihat Publikasi
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
            <a href="#research-topics"
              class="group inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-[#00A0D6] hover:text-[#00A0D6] transition-all duration-300 hover:scale-105 shadow-sm hover:shadow-md">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1.375-6.875M16.5 15.5l-1.47 1.47M12 21h10"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.25 15.25L10 12M4 9h16"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.5a.5.5 0 100-1 .5.5 0 000 1zM3 12.5a.5.5 0 100-1 .5.5 0 000 1z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18"></path>
              </svg>
              Topik Riset
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <section class="bg-gradient-to-br from-gray-50/50 via-white to-blue-50/30 py-20 lg:py-24">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16" id="research-topics">
                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    <span class="text-gradient">Topik Penelitian Unggulan</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed font-light">
                    Fokus penelitian utama di Laboratorium Data Technologies yang sejalan dengan perkembangan teknologi data terbaru
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-300">
                    <div class="w-12 h-12 bg-[#00A0D6]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-[#00A0D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0h6"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Big Data Analytics</h3>
                    <p class="text-gray-600">Eksplorasi teknik dan algoritma untuk mengolah, menganalisis, dan memvisualisasikan data bervolume besar.</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-300">
                    <div class="w-12 h-12 bg-[#6AC259]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0h6"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 5h-7a2 2 0 00-2 2v10a2 2 0 002 2h7a2 2 0 002-2V7a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Machine Learning & AI</h3>
                    <p class="text-gray-600">Pengembangan model kecerdasan buatan untuk prediksi, klasifikasi, dan pemrosesan bahasa alami.</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-300">
                    <div class="w-12 h-12 bg-yellow-500/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10m-4-2h12m0 0h12m-12 0v-4m0 4v4m0-4h12m0 0h12m-12 0v-4m0 4v4m0-4h12"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12.5a.5.5 0 100-1 .5.5 0 000 1zM9 12.5a.5.5 0 100-1 .5.5 0 000 1zM21 12.5a.5.5 0 100-1 .5.5 0 000 1z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Cloud Computing & IoT</h3>
                    <p class="text-gray-600">Riset implementasi sistem data terdistribusi pada lingkungan *cloud* dan pemrosesan data *real-time* dari perangkat IoT.</p>
                </div>
            </div>

            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    <span class="text-gradient">Publikasi Terbaru</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed font-light">
                    Kumpulan jurnal dan prosiding konferensi terkini dari staf dan peneliti laboratorium
                </p>
            </div>

            <div id="publication-list" class="grid lg:grid-cols-2 gap-8">
                <div class="col-span-full">
                    <div class="empty-state text-center py-16 bg-white/60 backdrop-blur-xl border border-white/40 rounded-3xl shadow-lg max-w-2xl mx-auto">
                        <div class="w-20 h-20 bg-gradient-to-br from-[#00A0D6]/10 to-[#6AC259]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-[#00A0D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Data Publikasi Belum Tersedia</h3>
                        <p class="text-gray-600 mb-6">Arsip publikasi sedang dalam proses pengarsipan. Silakan cek kembali nanti.</p>
                        <div class="inline-flex items-center gap-2 text-sm text-[#00A0D6] font-medium">
                            <div class="w-2 h-2 bg-[#00A0D6] rounded-full animate-pulse"></div>
                            Menunggu data dari API
                        </div>
                    </div>
                </div>

                <template id="publication-card-template">
                    <div class="publication-card bg-white p-6 rounded-2xl border border-gray-100 shadow-md">
                        <div data-category class="publication-category category-journal mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"></path></svg>
                            <span data-category-text>Jurnal Internasional</span>
                        </div>
                        <h3 data-title class="text-lg font-semibold text-gray-900 mb-2 leading-snug hover:text-[#00A0D6] transition-colors">Judul Penelitian yang Sangat Panjang dan Menarik di Bidang Data Mining</h3>
                        <p data-authors class="text-sm text-gray-600 mb-3 font-medium">Penulis 1, Penulis 2, dan Penulis 3</p>
                        <div class="flex items-center justify-between text-sm text-gray-500 border-t border-gray-100 pt-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-4 10v4m-4-4h.01M4 20h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v11a2 2 0 002 2z"></path></svg>
                                <span data-year>2024</span>
                            </div>
                            <a data-link href="#" target="_blank" class="text-[#00A0D6] font-medium hover:text-blue-600 flex items-center gap-1 transition-colors">
                                Lihat Detail
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>
  </main>

  <?php
  require_once '../includes/footer.php';
  ?>
</body>

</html>