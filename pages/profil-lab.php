<?php
$active_page = 'profil-lab';
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil Laboratorium – Lab Data Technologies</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-white text-gray-900">
  <?php require_once '../includes/header.php'; ?>

  <main>
    <!-- Hero Section -->
    <section class="relative py-20 lg:py-28 bg-cover bg-center"
      style="background-image: url('https://bpb-us-e1.wpmucdn.com/sites.tufts.edu/dist/8/8205/files/2015/10/Lab_Final.jpg');">
      <!-- Dark overlay for better text readability -->
      <div class="absolute inset-0 bg-black/50"></div>

      <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
        <div class="text-center text-white">
          <div
            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#00A0D6] to-[#6AC259] text-white px-4 py-2 rounded-full font-medium mb-6 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
              </path>
            </svg>
            <span>Profil Laboratorium</span>
          </div>

          <h1 class="text-4xl lg:text-6xl font-bold text-white-900 mb-6 leading-tight">
            Laboratorium<br>
            <span class="text-gradient">Data Technologies</span>
          </h1>

          <p class="text-xl text-white-600 mb-8 leading-relaxed max-w-4xl mx-auto">
            Pusat unggulan untuk riset dan pendidikan di bidang
            <span class="font-semibold text-[#21baee]">ilmu data</span>,
            <span class="font-semibold text-[#75ce64]">kecerdasan buatan</span>, dan
            <span class="font-semibold text-purple-400">teknologi informasi</span>
            di Politeknik Negeri Malang.
          </p>

          <div class="flex flex-col sm:flex-row gap-4 mb-12 justify-center">
            <a href="#visi-misi"
              class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                </path>
              </svg>
              Visi & Misi
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
            <a href="#struktur-organisasi"
              class="group inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-[#00A0D6] hover:text-[#00A0D6] transition-all duration-300 hover:scale-105 shadow-sm hover:shadow-md">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
              </svg>
              Struktur Organisasi
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Visi & Misi Section -->
    <section id="visi-misi" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16">
          <div
            class="inline-flex items-center gap-2 bg-[#00A0D6]/10 text-[#00A0D6] px-4 py-2 rounded-full font-medium mb-6 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
              </path>
            </svg>
            <span>Visi & Misi</span>
          </div>
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
            Landasan <span class="text-gradient">Filosofi</span>
          </h2>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Arah pengembangan dan komitmen Laboratorium Data Technologies untuk masa depan
          </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-start">
          <!-- Visi Card -->
          <div
            class="card-magic rounded-2xl p-8 text-left hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-lg border border-gray-100">
            <div
              class="w-16 h-16 bg-gradient-to-br from-[#00A0D6] to-[#0078A6] rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                </path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-4 text-center">Visi</h3>
            <p class="text-gray-600 leading-relaxed mb-6">
              Menjadi organisasi riset terkemuka dalam penelitian maupun pengembangan untuk mendorong inovasi
              teknologi serta keilmuan di bidang <b>penyimpanan</b>, <b>pengolahan</b>, dan <b>rekayasa sistem data</b>
              yang <b>berkelanjutan</b>.
            </p>
          </div>

          <!-- Misi Card -->
          <div
            class="card-magic rounded-2xl p-8 text-left hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-lg border border-gray-100">
            <div
              class="w-16 h-16 bg-gradient-to-br from-[#6AC259] to-[#4BAE45] rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                </path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-4 text-center">Misi</h3>
            <div class="space-y-4">
              <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-[#6AC259]/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-gray-600"><b>Mendukung visi dan misi Jurusan Teknologi Informasi Polinema</b> melalui
                    penelitian dan pengembangan di bidang penyimpanan, pengolahan, serta rekayasa sistem data.</div>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-[#6AC259]/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-gray-600"><b>Melakukan penelitian berkualitas tinggi</b> yang berkontribusi pada
                    kemajuan ilmu pengetahuan dan teknologi di bidang data, selaras dengan agenda riset JTI Polinema.
                  </div>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-[#6AC259]/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-gray-600"><b>Mengembangkan inovasi teknologi data</b> yang dapat diterapkan dalam
                    dunia industri, pendidikan, dan pemerintahan guna meningkatkan daya saing lulusan JTI Polinema.
                  </div>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-[#6AC259]/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-gray-600"><b>Membangun infrastruktur dan sistem data yang skalabel dan efisien</b>
                    untuk mendukung kebutuhan analitik, kecerdasan buatan, dan Big Data, serta memperkuat keunggulan
                    akademik JTI Polinema.</div>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-[#6AC259]/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-gray-600"><b>Menjalin kolaborasi dengan akademisi, industri, dan pemerintah</b> dalam
                    pengembangan solusi teknologi data yang inovatif, sejalan dengan misi JTI Polinema dalam memperkuat
                    sinergi dengan dunia kerja.</div>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-[#6AC259]/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-gray-600"><b>Meningkatkan kapasitas dan kompetensi sumber daya manusia</b> di
                    lingkungan JTI Polinema melalui pelatihan, penelitian, seminar, dan publikasi ilmiah di bidang
                    teknologi data.</div>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-[#6AC259]/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-gray-600"><b>Menyediakan layanan dan rekomendasi berbasis riset</b> bagi JTI Polinema
                    serta mitra industri dan akademik untuk mengoptimalkan pengelolaan dan pemanfaatan data.</div>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-[#6AC259]/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-gray-600"><b>Menjaga etika dan keamanan data</b> dalam setiap penelitian dan
                    pengembangan teknologi, mendukung prinsip tata kelola data yang baik dalam lingkungan akademik dan
                    industri.</div>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-[#6AC259]/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-gray-600"><b>Mengembangkan praktik riset dan infrastruktur teknologi data yang
                      berkelanjutan</b> melalui penerapan prinsip efisiensi energi, optimalisasi sumber daya, serta
                    pengelolaan siklus hidup data yang ramah lingkungan.</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Sejarah Laboratorium -->
    <section class="relative py-20 bg-gradient-to-br from-[#00A0D6]/5 via-white to-[#6AC259]/5 overflow-hidden">
      <!-- Background Decorations -->
      <div class="absolute top-20 right-10 w-32 h-32 bg-[#00A0D6]/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-20 left-10 w-40 h-40 bg-[#6AC259]/10 rounded-full blur-3xl"></div>

      <div class="max-w-7xl mx-auto px-6 lg:px-8 relative">
        <div class="text-center mb-16">
          <div
            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#00A0D6] to-[#6AC259] text-white px-4 py-2 rounded-full font-medium mb-6 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Sejarah & Pencapaian</span>
          </div>
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
            Perjalanan <span class="text-gradient">Laboratorium</span>
          </h2>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Dari visi sederhana hingga menjadi pusat unggulan teknologi data di Indonesia
          </p>
        </div>

        <!-- Main Content Card -->
        <div class="relative">
          <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl border border-gray-100 p-8 lg:p-12 mb-12">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
              <!-- Left Content -->
              <div class="space-y-6">
                <div class="space-y-4">
                  <div class="flex items-start gap-4">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-[#00A0D6] to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-xl font-bold text-gray-900 mb-2">Berdiri 2018</h3>
                      <p class="text-gray-600 leading-relaxed">
                        Laboratorium Data Technologies didirikan sebagai pusat kegiatan akademik dan penelitian di
                        bidang data engineering dan machine learning.
                      </p>
                    </div>
                  </div>

                  <div class="flex items-start gap-4">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-[#6AC259] to-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-xl font-bold text-gray-900 mb-2">Evolusi Digital</h3>
                      <p class="text-gray-600 leading-relaxed">
                        Berkembang pesat mengikuti era digital dengan memperluas penelitian ke machine learning, AI, dan
                        big data analytics.
                      </p>
                    </div>
                  </div>

                  <div class="flex items-start gap-4">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-xl font-bold text-gray-900 mb-2">Kolaborasi Industri</h3>
                      <p class="text-gray-600 leading-relaxed">
                        Aktif berkolaborasi dengan industri dan mengabdi kepada masyarakat melalui pelatihan teknologi
                        data untuk UMKM.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right - Floating Stats -->
              <div class="relative">
                <div class="grid grid-cols-1 gap-6">
                  <!-- Stat Card 1 -->
                  <div
                    class="card-magic rounded-2xl bg-white/70 backdrop-blur border border-gray-200 p-6 text-center hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                    <div
                      class="w-16 h-16 bg-gradient-to-br from-[#00A0D6] to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                      <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                      </svg>
                    </div>
                    <div class="text-3xl font-bold text-[#00A0D6] mb-2">50+</div>
                    <div class="text-sm font-medium text-gray-600">Publikasi Ilmiah</div>
                  </div>

                  <!-- Stat Card 2 -->
                  <div
                    class="card-magic rounded-2xl bg-white/70 backdrop-blur border border-gray-200 p-6 text-center hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                    <div
                      class="w-16 h-16 bg-gradient-to-br from-[#6AC259] to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                      <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6">
                        </path>
                      </svg>
                    </div>
                    <div class="text-3xl font-bold text-[#6AC259] mb-2">20+</div>
                    <div class="text-sm font-medium text-gray-600">Proyek Industri</div>
                  </div>

                  <!-- Stat Card 3 -->
                  <div
                    class="card-magic rounded-2xl bg-white/70 backdrop-blur border border-gray-200 p-6 text-center hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                    <div
                      class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                      <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                        </path>
                      </svg>
                    </div>
                    <div class="text-3xl font-bold text-purple-600 mb-2">500+</div>
                    <div class="text-sm font-medium text-gray-600">Alumni Kompeten</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Tujuan -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <!-- Left Column - Title & Description -->
          <div class="space-y-8">
            <div>
              <div
                class="inline-flex items-center gap-2 bg-[#6AC259]/10 text-[#6AC259] px-4 py-2 rounded-full font-medium mb-6 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                  </path>
                </svg>
                <span>Tujuan Strategis</span>
              </div>
              <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
                Mencapai <span class="text-gradient">Keunggulan</span><br>
                dalam Teknologi Data
              </h2>
              <p class="text-lg text-gray-600 leading-relaxed">
                Laboratorium Data Technologies didirikan dengan tujuan strategis untuk menjadi pusat unggulan yang
                mengintegrasikan pendidikan, penelitian, dan kolaborasi industri dalam bidang teknologi data.
              </p>
            </div>

            <!-- Key Points -->
            <div class="space-y-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-[#00A0D6]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-[#00A0D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <span class="text-gray-700 font-medium">Menghasilkan lulusan yang kompeten dan siap kerja</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-[#6AC259]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <span class="text-gray-700 font-medium">Mendorong inovasi melalui penelitian berkualitas tinggi</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-purple-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <span class="text-gray-700 font-medium">Membangun ekosistem kolaborasi yang berkelanjutan</span>
              </div>
            </div>
          </div>

          <!-- Right Column - Vertical Feature Pills -->
          <div class="space-y-4">
            <!-- Pendidikan Pill -->
            <div
              class="group bg-white rounded-xl px-6 py-4 shadow-sm hover:shadow-md border-l-4 border-[#00A0D6] hover:border-[#0078A6] transition-all duration-300 hover:-translate-y-1">
              <div class="flex items-center gap-4">
                <div
                  class="w-12 h-12 bg-gradient-to-br from-[#00A0D6] to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                    </path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 mb-1">Pendidikan Berkualitas</h3>
                  <p class="text-gray-600 text-sm leading-relaxed">
                    Menyediakan fasilitas pembelajaran modern dan kurikulum yang relevan dengan kebutuhan industri
                    teknologi data.
                  </p>
                </div>
              </div>
            </div>

            <!-- Penelitian Pill -->
            <div
              class="group bg-white rounded-xl px-6 py-4 shadow-sm hover:shadow-md border-l-4 border-[#6AC259] hover:border-[#4BAE45] transition-all duration-300 hover:-translate-y-1">
              <div class="flex items-center gap-4">
                <div
                  class="w-12 h-12 bg-gradient-to-br from-[#6AC259] to-green-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                    </path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 mb-1">Penelitian Inovatif</h3>
                  <p class="text-gray-600 text-sm leading-relaxed">
                    Melakukan penelitian fundamental dan terapan di bidang AI, machine learning, dan big data analytics.
                  </p>
                </div>
              </div>
            </div>

            <!-- Kolaborasi Pill -->
            <div
              class="group bg-white rounded-xl px-6 py-4 shadow-sm hover:shadow-md border-l-4 border-purple-500 hover:border-purple-600 transition-all duration-300 hover:-translate-y-1">
              <div class="flex items-center gap-4">
                <div
                  class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 mb-1">Kolaborasi Strategis</h3>
                  <p class="text-gray-600 text-sm leading-relaxed">
                    Membangun kemitraan dengan industri, pemerintah, dan institusi untuk pengembangan teknologi.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Area Fokus Penelitian -->
    <section class="relative py-20 bg-gradient-to-br from-gray-50 via-white to-blue-50 overflow-hidden">
      <!-- Background Decorations -->
      <div class="absolute top-10 left-10 w-24 h-24 bg-[#6AC259]/10 rounded-full blur-2xl"></div>
      <div class="absolute bottom-10 right-10 w-32 h-32 bg-[#00A0D6]/10 rounded-full blur-2xl"></div>

      <div class="max-w-7xl mx-auto px-6 lg:px-8 relative">
        <div class="text-center mb-16">
          <div
            class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-2 rounded-full font-medium mb-6 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
              </path>
            </svg>
            <span>Area Penelitian</span>
          </div>
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
            Fokus <span class="text-gradient">Penelitian</span> Unggulan
          </h2>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Empat pilar utama penelitian yang mendorong inovasi teknologi data untuk masa depan
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Data Mining Card -->
          <div
            class="group bg-white/60 backdrop-blur border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-2">
            <div
              class="w-16 h-16 bg-gradient-to-br from-[#00A0D6] to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                </path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Data Mining</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
              Eksplorasi dan analisis data untuk menemukan pola tersembunyi dan insight berharga yang mendukung
              pengambilan keputusan.
            </p>
            <div
              class="flex items-center text-[#00A0D6] font-medium text-sm group-hover:translate-x-1 transition-transform">
              <span>Pelajari lebih lanjut</span>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
          </div>

          <!-- Machine Learning Card -->
          <div
            class="group bg-white/60 backdrop-blur border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-2">
            <div
              class="w-16 h-16 bg-gradient-to-br from-[#6AC259] to-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                </path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Machine Learning</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
              Pengembangan algoritma pembelajaran mesin canggih untuk prediksi, klasifikasi, dan otomatisasi proses
              bisnis.
            </p>
            <div
              class="flex items-center text-[#6AC259] font-medium text-sm group-hover:translate-x-1 transition-transform">
              <span>Pelajari lebih lanjut</span>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
          </div>

          <!-- Computer Vision Card -->
          <div
            class="group bg-white/60 backdrop-blur border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-2">
            <div
              class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                </path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Computer Vision</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
              Pengolahan citra dan video untuk aplikasi pengenalan pola visual, deteksi objek, dan analisis visual
              otomatis.
            </p>
            <div
              class="flex items-center text-purple-600 font-medium text-sm group-hover:translate-x-1 transition-transform">
              <span>Pelajari lebih lanjut</span>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
          </div>

          <!-- Big Data Analytics Card -->
          <div
            class="group bg-white/60 backdrop-blur border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-2">
            <div
              class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                </path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Big Data Analytics</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
              Analisis data skala besar menggunakan teknologi distributed computing dan cloud untuk insight bisnis
              real-time.
            </p>
            <div
              class="flex items-center text-orange-600 font-medium text-sm group-hover:translate-x-1 transition-transform">
              <span>Pelajari lebih lanjut</span>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Struktur Organisasi -->
    <section id="struktur-organisasi" class="bg-gray-50 py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Struktur Organisasi</h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Struktur kepemimpinan dan manajemen Laboratorium Data Technologies
          </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-12 px-8">
          <div class="flex flex-col lg:flex-row items-start lg:items-center justify-center gap-8 lg:gap-16">

            <div class="org-line text-center flex-shrink-0">
              <div
                class="w-24 h-24 rounded-2xl bg-gradient-to-br from-[#00A0D6] to-blue-600 mx-auto mb-4 flex items-center justify-center shadow-lg">
                <img src="../assets/img/KepalaLab.jpg" alt="" class="w-full h-full object-cover rounded-xl">
              </div>
              <h3 class="font-bold text-gray-900 mb-2">Kepala Laboratorium</h3>
              <p class="text-sm text-gray-600 font-medium">Dr. Ahmad Saikhu, S.T., M.T.</p>
              <p class="text-xs text-gray-500 mt-2 mx-auto max-w-36">Menyusun kebijakan dan strategi pengembangan</p>
            </div>

            <div class="org-line text-center flex-shrink-0">
              <div
                class="w-24 h-24 rounded-2xl bg-gradient-to-br from-[#6AC259] to-green-600 mx-auto mb-4 flex items-center justify-center shadow-lg">
                <img src="../assets/img/KoordinatorLab.jpg" alt="" class="w-full h-full object-cover rounded-xl">
              </div>
              <h3 class="font-bold text-gray-900 mb-2">Koordinator Lab</h3>
              <p class="text-sm text-gray-600 font-medium">Ir. Sari Widya, S.T., M.T.</p>
              <p class="text-xs text-gray-500 mt-2 mx-auto max-w-36">Mengelola operasional harian dan sumber daya</p>
            </div>

            <div class="org-line text-center flex-shrink-0">
              <div
                class="w-24 h-24 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 mx-auto mb-4 flex items-center justify-center shadow-lg">
                <img src="../assets/img/Sekretaris.jpg" alt="" class="w-full h-full object-cover rounded-xl">
              </div>
              <h3 class="font-bold text-gray-900 mb-2">Sekretaris</h3>
              <p class="text-sm text-gray-600 font-medium">Dian Pratiwi, S.Kom., M.T.</p>
              <p class="text-xs text-gray-500 mt-2 mx-auto max-w-36">Administrasi dan dokumentasi kegiatan</p>
            </div>

            <div class="text-center flex-shrink-0">
              <div
                class="w-24 h-24 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 mx-auto mb-4 flex items-center justify-center shadow-lg">
                <img src="../assets/img/TimLaboran.jpg" alt="" class="w-full h-full object-cover rounded-xl">
              </div>
              <h3 class="font-bold text-gray-900 mb-2">Tim Laboran & Asisten</h3>
              <p class="text-sm text-gray-600 font-medium">5 Laboran & 12 Asisten</p>
              <p class="text-xs text-gray-500 mt-2 mx-auto max-w-36">Mendukung praktikum dan penelitian</p>
            </div>
          </div>

          <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="text-center">
              <div class="inline-flex items-center px-6 py-3 bg-blue-50 rounded-xl">
                <svg class="w-5 h-5 text-[#00A0D6] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                  </path>
                </svg>
                <p class="text-sm text-gray-700 font-medium">
                  Laboratorium Data Technologies berada di bawah naungan <span class="font-semibold">Jurusan Teknik
                    Informatika</span> Politeknik Negeri Malang
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Fasilitas Utama -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="text-center mb-16">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">Fasilitas Utama</h2>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
          Infrastruktur dan peralatan modern untuk mendukung kegiatan penelitian dan pembelajaran
        </p>
      </div>

      <div class="grid md:grid-cols-3 gap-8">
        <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <div
            class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#00A0D6] to-blue-600 flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
              </path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-3">Workstation High-End</h3>
          <p class="text-gray-600 leading-relaxed">
            Komputer workstation dengan spesifikasi tinggi untuk machine learning, data processing, dan simulasi
            kompleks.
          </p>
        </div>

        <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <div
            class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#6AC259] to-green-600 flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h6m-7 8a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h2a2 2 0 012 2v4a2 2 0 01-2 2H9a2 2 0 01-2-2V6a2 2 0 012-2h2m7 0V4a2 2 0 00-2-2H9a2 2 0 00-2 2v2m8 0V4a2 2 0 00-2-2H9a2 2 0 00-2 2v2">
              </path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-3">Server Cluster</h3>
          <p class="text-gray-600 leading-relaxed">
            Infrastruktur server untuk big data processing, distributed computing, dan cloud-based applications.
          </p>
        </div>

        <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <div
            class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
              </path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-3">Research Tools</h3>
          <p class="text-gray-600 leading-relaxed">
            Software dan tools penelitian seperti MATLAB, Python, R, TensorFlow, dan platform analisis data lainnya.
          </p>
        </div>
      </div>
    </section>

    <!-- Kontak & Lokasi -->
    <section class="bg-gray-50 py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Kontak & Lokasi</h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Informasi kontak dan lokasi Laboratorium Data Technologies
          </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 lg:p-12">
          <div class="grid lg:grid-cols-2 gap-12">
            <!-- Contact Info -->
            <div>
              <h3 class="text-2xl font-bold text-gray-900 mb-8">Informasi Kontak</h3>
              <div class="space-y-6">
                <div class="flex items-start gap-4">
                  <div
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#00A0D6] to-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </div>
                  <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Alamat</h4>
                    <p class="text-gray-600">Jl. Soekarno Hatta No. 9, Malang<br>Jawa Timur 65141, Indonesia</p>
                  </div>
                </div>

                <div class="flex items-start gap-4">
                  <div
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#6AC259] to-green-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                      </path>
                    </svg>
                  </div>
                  <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Email</h4>
                    <p class="text-gray-600">lab.dt@polinema.ac.id<br>info@datatechlab.polinema.ac.id</p>
                  </div>
                </div>

                <div class="flex items-start gap-4">
                  <div
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                      </path>
                    </svg>
                  </div>
                  <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Telepon</h4>
                    <p class="text-gray-600">(0341) 404040 ext. 1234<br>Fax: (0341) 404040</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Map Placeholder -->
            <div>
              <h3 class="text-2xl font-bold text-gray-900 mb-8">Lokasi</h3>
              <div class="bg-gray-100 rounded-2xl h-80 flex items-center justify-center overflow-hidden shadow-lg">
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.502683161686!2d112.61354597536744!3d-7.946891192077398!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78827687d272e7%3A0x789ce9a636cd3aa2!2sPoliteknik%20Negeri%20Malang!5e0!3m2!1sid!2sid!4v1764178273637!5m2!1sid!2sid"
                  width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade">
                </iframe>
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