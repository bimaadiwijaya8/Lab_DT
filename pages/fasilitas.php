<?php
// Tentukan judul halaman
$page_title = "Fasilitas – Lab Data Technologies";
$current_page = "fasilitas"; // Untuk menandai navigasi yang aktif

$active_page = 'fasilitas';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-white text-gray-900">
    <?php require_once '../includes/header.php'; ?>
    <main>
        <section class="hero-section text-white py-20 lg:py-28">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center">
                    <div
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-[#00A0D6] to-[#6AC259] text-white px-4 py-2 rounded-full font-medium mb-6 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <span>Fasilitas & Infrastruktur</span>
                    </div>

                    <h1 class="text-4xl lg:text-6xl font-bold text-white-900 mb-6 leading-tight">
                        Fasilitas<br>
                        <span class="text-gradient">& Infrastruktur</span>
                    </h1>

                    <p class="text-xl text-white-600 mb-8 leading-relaxed max-w-4xl mx-auto">
                        Perangkat keras, perangkat lunak, jaringan, dan ruang pembelajaran modern untuk mendukung kegiatan riset dan praktikum di <span class="font-semibold text-[#00A0D6]">Laboratorium Data Technologies</span>
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 mb-12 justify-center">
                        <a href="#facilities-container"
                            class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Ruang Laboratorium
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <a href="#facilities-container"
                            class="group inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-[#00A0D6] hover:text-[#00A0D6] transition-all duration-300 hover:scale-105 shadow-sm hover:shadow-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Peralatan Penelitian
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                        <span class="text-gradient">Fasilitas & Infrastruktur</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Infrastruktur lengkap dan modern untuk mendukung pembelajaran serta penelitian di bidang teknologi data
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="facilities-container">
                    <div class="col-span-full">
                        <div class="empty-state text-center py-16 bg-white/60 backdrop-blur-xl border border-white/40 rounded-3xl shadow-lg">
                            <div class="w-20 h-20 bg-gradient-to-br from-[#00A0D6]/10 to-[#6AC259]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-[#00A0D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Data Fasilitas</h3>
                            <p class="text-gray-600 mb-6">Data fasilitas akan ditampilkan setelah tersedia dari sistem</p>
                            <div class="inline-flex items-center gap-2 text-sm text-[#00A0D6] font-medium">
                                <div class="w-2 h-2 bg-[#00A0D6] rounded-full animate-pulse"></div>
                                Menunggu data dari API
                            </div>
                        </div>
                    </div>
                </div>

                <template id="facility-card-template">
                    <div class="card-fasilitas group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:scale-105 transition-all duration-300">
                        <img data-foto class="w-full h-48 object-cover" alt="Foto Fasilitas" loading="lazy">
                        <div class="p-6">
                            <h3 data-nama class="text-lg font-semibold text-gray-900 mb-2"></h3>
                            <p data-deskripsi class="text-gray-600 text-sm leading-relaxed"></p>
                        </div>
                    </div>
                </template>
            </div>
        </section>
    </main>

    <?php
    require_once '../includes/footer.php';
    ?>
</body>

</html>