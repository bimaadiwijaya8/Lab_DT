<?php
$active_page = 'beranda';
$current_year = date('Y');

// Include database connection
require_once '../assets/php/db_connect.php';

try {
    $conn = Database::getConnection();
    
    // Query counts for each category
    $publikasi_count = $conn->query("SELECT COUNT(*) FROM publikasi")->fetchColumn();
    $berita_count = $conn->query("SELECT COUNT(*) FROM berita WHERE status = 'approved'")->fetchColumn();
    $fasilitas_count = $conn->query("SELECT COUNT(*) FROM fasilitas")->fetchColumn();
    $anggota_count = $conn->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
    
    // Add member count to anggota if needed
    $member_count = $conn->query("SELECT COUNT(*) FROM member WHERE status = 'aktif'")->fetchColumn();
    $total_anggota = $anggota_count + $member_count;
    
    // Get latest 4 approved publications for Riset Unggulan section
    $latest_publikasi = [];
    try {
        $stmt = $conn->query("SELECT id_publikasi, judul, penulis, tanggal_terbit, deskripsi, file_publikasi 
                             FROM publikasi 
                             WHERE status = 'approved' 
                             ORDER BY tanggal_terbit DESC 
                             LIMIT 4");
        $latest_publikasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching latest publications: " . $e->getMessage());
    }
    
} catch (PDOException $e) {
    // Fallback to default values if database fails
    $publikasi_count = 50;
    $berita_count = 20;
    $fasilitas_count = 500;
    $total_anggota = 15;
}
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
  <?php include '../includes/header.php'; ?>
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
              <a href="profil-lab.php" class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Pelajari Lebih Lanjut
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </a>
              <a href="penelitian.php" class="group inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-[#00A0D6] hover:text-[#00A0D6] transition-all duration-300 hover:scale-105 shadow-sm hover:shadow-md">
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
            <div class="text-3xl font-bold text-gray-900 mb-2"><?php echo $publikasi_count; ?></div>
            <div class="text-sm font-medium text-gray-600">Publikasi Ilmiah</div>
          </div>

          <!-- Kolaborasi Industri -->
          <div class="stat-card rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-lg border border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-[#6AC259] to-[#4BAE45] rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
              </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-2"><?php echo $berita_count; ?></div>
            <div class="text-sm font-medium text-gray-600">Berita Lab</div>
          </div>

          <!-- Alumni Sukses -->
          <div class="stat-card rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-lg border border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-2"><?php echo $fasilitas_count; ?></div>
            <div class="text-sm font-medium text-gray-600">Fasilitas Lab</div>
          </div>

          <!-- Penghargaan -->
          <div class="stat-card rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-lg border border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-2"><?php echo $total_anggota; ?></div>
            <div class="text-sm font-medium text-gray-600">Anggota Lab</div>
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

            <a href="profil-lab.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg">
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
              <a href="profil-lab.php" class="inline-flex items-center text-[#00A0D6] font-bold hover:text-blue-700 transition-all duration-300 group">
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
              <a href="penelitian.php" class="inline-flex items-center text-[#6AC259] font-bold hover:text-green-700 transition-all duration-300 group">
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
              <a href="anggota.php" class="inline-flex items-center text-purple-600 font-bold hover:text-purple-700 transition-all duration-300 group">
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
          <a href="fasilitas.php" class="group p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl hover:from-blue-100 hover:to-blue-200 transition-all duration-300 text-center hover:scale-105 shadow-sm hover:shadow-lg">
            <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">🏢</div>
            <div class="font-bold text-gray-900 mb-2">Fasilitas</div>
            <div class="text-sm text-gray-600">Peralatan & infrastruktur modern</div>
          </a>
          <a href="galeri.php" class="group p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl hover:from-green-100 hover:to-green-200 transition-all duration-300 text-center hover:scale-105 shadow-sm hover:shadow-lg">
            <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">📸</div>
            <div class="font-bold text-gray-900 mb-2">Galeri</div>
            <div class="text-sm text-gray-600">Dokumentasi kegiatan lab</div>
          </a>
          <a href="berita.php" class="group p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl hover:from-purple-100 hover:to-purple-200 transition-all duration-300 text-center hover:scale-105 shadow-sm hover:shadow-lg">
            <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">📰</div>
            <div class="font-bold text-gray-900 mb-2">Berita</div>
            <div class="text-sm text-gray-600">Update & pengumuman terbaru</div>
          </a>
          <a href="kontak.php" class="group p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl hover:from-orange-100 hover:to-orange-200 transition-all duration-300 text-center hover:scale-105 shadow-sm hover:shadow-lg">
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
          <a href="berita.php" class="text-[#00A0D6] font-semibold hover:text-blue-700 transition-colors">Lihat semua</a>
        </div>

        <!-- News Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12" id="news-container">
          <?php
          try {
              $conn = Database::getConnection();
              // Get 3 most recent approved news with author information
              $stmt = $conn->query("SELECT b.*, a.nama_gelar as author_name FROM berita b 
                                   LEFT JOIN anggota a ON b.author = a.id_anggota 
                                   WHERE b.status = 'approved' ORDER BY b.tanggal DESC LIMIT 3");
              $berita = $stmt->fetchAll(PDO::FETCH_ASSOC);
              
              if (count($berita) > 0) {
                  foreach ($berita as $item) {
                      // Get image from gambar field first, then fallback to content extraction
                      if (!empty($item['gambar'])) {
                          $image_src = $item['gambar'];
                      } else {
                          // Try to extract first image from content
                          preg_match('/<img.+?src=[\'"](?P<src>[^\'"]+)[\'"].*>/i', $item['informasi'], $matches);
                          $image_src = !empty($matches['src']) ? $matches['src'] : 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?q=80&w=800&auto=format&fit=crop';
                      }
                      
                      // Get first paragraph as excerpt
                      $excerpt = strip_tags($item['informasi']);
                      $excerpt = strlen($excerpt) > 150 ? substr($excerpt, 0, 150) . '...' : $excerpt;
                      
                      // Format date
                      $tanggal = date('d F Y', strtotime($item['tanggal']));
                      
                      // Output the news card
                      ?>
                      <article class="card-magic rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
                        <div class="relative">
                          <img src="<?php echo htmlspecialchars($image_src); ?>"
                            alt="<?php echo htmlspecialchars($item['judul']); ?>"
                            class="w-full h-48 object-cover">
                        </div>
                        <div class="p-6">
                          <div class="text-sm text-gray-500 mb-2"><?php echo $tanggal; ?></div>
                          <?php if (!empty($item['author_name'])): ?>
                          <div class="text-sm text-gray-600 mb-2">Oleh <?php echo htmlspecialchars($item['author_name']); ?></div>
                          <?php endif; ?>
                          <h3 class="font-bold text-lg text-gray-900 mb-3 line-clamp-2">
                            <?php echo htmlspecialchars($item['judul']); ?>
                          </h3>
                          <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            <?php echo htmlspecialchars($excerpt); ?>
                          </p>
                          <button onclick="showNewsDetail(<?php echo htmlspecialchars(json_encode($item)); ?>)" 
                                  class="inline-flex items-center text-[#00A0D6] font-semibold hover:text-blue-700 transition-colors">
                            Lihat Selengkapnya
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                          </button>
                        </div>
                      </article>
                      <?php
                  }
              } else {
                  // Fallback if no news found
                  echo '<p class="col-span-3 text-center text-gray-500">Tidak ada berita tersedia saat ini.</p>';
              }
          } catch (PDOException $e) {
              // Log error and show fallback message
              error_log("Error fetching news: " . $e->getMessage());
              echo '<p class="col-span-3 text-center text-red-500">Gagal memuat berita. Silakan coba lagi nanti.</p>';
          }
          ?>
        </div>
      </div>
    </section>

    <!-- News Detail Modal -->
    <div id="newsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
      <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-start mb-4">
            <h3 id="modalTitle" class="text-2xl font-bold text-gray-900"></h3>
            <button onclick="closeNewsModal()" class="text-gray-400 hover:text-gray-500">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="mb-4">
            <span id="modalDate" class="text-sm text-gray-500"></span>
            <span id="modalAuthor" class="ml-2 text-sm text-gray-600"></span>
          </div>
          <div class="mb-6">
            <img id="modalImage" src="" alt="" class="w-full h-64 object-cover rounded-lg mb-4">
            <div id="modalContent" class="prose max-w-none"></div>
          </div>
          <div class="flex justify-end">
            <button onclick="closeNewsModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

    <script>
      function showNewsDetail(news) {
        // Set modal content
        document.getElementById('modalTitle').textContent = news.judul;
        document.getElementById('modalDate').textContent = new Date(news.tanggal).toLocaleDateString('id-ID', {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
        
        // Set author if available
        const modalAuthor = document.getElementById('modalAuthor');
        if (news.author_name && news.author_name.trim() !== '') {
          modalAuthor.textContent = 'Oleh ' + news.author_name;
          modalAuthor.style.display = 'inline';
        } else {
          modalAuthor.style.display = 'none';
        }
        
        // Set image (prioritize gambar field, then extract from content, then use default)
        let imgSrc;
        if (news.gambar && news.gambar.trim() !== '') {
          imgSrc = news.gambar;
        } else {
          const content = news.informasi || '';
          const imgMatch = content.match(/<img.+?src=["'](.+?)["'].*?>/i);
          imgSrc = imgMatch ? imgMatch[1] : 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?q=80&w=800&auto=format&fit=crop';
        }
        document.getElementById('modalImage').src = imgSrc;
        document.getElementById('modalImage').alt = news.judul;
        
        // Set content (sanitize if needed)
        document.getElementById('modalContent').innerHTML = news.informasi || '<p>Tidak ada konten yang tersedia.</p>';
        
        // Show modal
        document.getElementById('newsModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }
      
      function closeNewsModal() {
        document.getElementById('newsModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
      }
      
      // Close modal when clicking outside content
      document.getElementById('newsModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeNewsModal();
        }
      });
      
      // Close modal with Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeNewsModal();
        }
      });
    </script>

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
              <span class="text-gradient">Riset Terbaru</span>
            </h2>
            <p class="text-lg text-gray-600 mb-8 leading-relaxed">
              Topik terbaru terkait pengembangan dan penelitian terapan yang menjadi fokus utama laboratorium kami.
            </p>

            <div class="grid sm:grid-cols-2 gap-4">
              <?php if (!empty($latest_publikasi)): ?>
                <?php foreach ($latest_publikasi as $index => $publikasi): ?>
                  <div class="p-6 rounded-2xl border border-gray-200 hover:shadow-lg hover:scale-105 transition-all duration-300 bg-white">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[<?php echo $index % 2 == 0 ? '#00A0D6' : '#6AC259'; ?>] to-[<?php echo $index % 2 == 0 ? '#0078A6' : '#4BAE45'; ?>] flex items-center justify-center text-white font-bold mb-4">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                      </svg>
                    </div>
                    <div class="font-bold text-gray-900 mb-2 line-clamp-2"><?php echo htmlspecialchars($publikasi['judul']); ?></div>
                    <div class="text-sm text-gray-600 mb-2">Oleh <?php echo htmlspecialchars($publikasi['penulis']); ?></div>
                    <div class="text-xs text-gray-500"><?php echo date('d M Y', strtotime($publikasi['tanggal_terbit'])); ?></div>
                    <?php if (!empty($publikasi['file_publikasi'])): ?>
                      <a href="<?php echo htmlspecialchars($publikasi['file_publikasi']); ?>" target="_blank" class="inline-flex items-center text-[#00A0D6] text-sm font-semibold hover:text-blue-700 transition-colors mt-2">
                        Lihat Dokumen
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                      </a>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <!-- Fallback dummy cards if no publications -->
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
              <?php endif; ?>
            </div>
          </div>

          <!-- Right Column - Research Image -->
          <div class="relative flex items-end justify-center">
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

  <?php include '../includes/footer.php'; ?>
</body>

</html>