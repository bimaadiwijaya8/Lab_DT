<?php
// berita.php

// 1. Variabel Konfigurasi Dasar
$page_title = "Berita – Lab Data Technologies";
$current_year = date('Y'); // Menggunakan tahun saat ini secara dinamis

$active_page = 'berita';

// --- Bagian Koneksi Database dan Ambil Data ---
$pdo = null;
$db_error = false;
// Path relatif dari folder 'pages' ke file 'db_connect.php' di 'assets/php/'
$db_connect_path = '../assets/php/db_connect.php'; 
$news_items = [];

if (file_exists($db_connect_path)) {
    require_once $db_connect_path;
    
    try {
        // Panggil metode static dari class Database
        $pdo = Database::getConnection(); 

        // Query untuk mengambil data berita yang hanya status 'approved'
        // Menggunakan LEFT(informasi, 150) untuk ringkasan singkat (summary)
        $sql = "SELECT 
                    id_berita, 
                    judul, 
                    gambar, 
                    LEFT(informasi, 150) as summary, 
                    tanggal, 
                    status 
                FROM berita 
                WHERE status = 'approved'
                -- Urutkan dari tanggal terbaru
                ORDER BY tanggal DESC"; 
        
        $stmt = $pdo->query($sql);
        $db_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mapping hasil DB ke array $news_items
        foreach ($db_results as $row) {
            
            // --- Logika Penentuan Kategori ---
            // Karena tabel DB sebelumnya hanya memiliki 'status' (pending/publish),
            // kita gunakan logika sederhana untuk mapping ke kategori frontend.
            $category = 'berita'; // Default
            if (strtoupper($row['status']) === 'PENDING') {
                $category = 'berita'; 
            } 
            // Catatan: Untuk mendukung kategori 'agenda', Anda perlu menambahkan kolom 'kategori' di tabel 'berita'.
            
            // Catatan Path Gambar:
            // Path gambar yang disimpan di DB adalah relatif dari root proyek (misalnya: 'assets/img/berita/...')
            // Karena file berita.php berada di dalam subfolder (misalnya 'pages/'), kita perlu menambahkan '../' di depannya
            // agar bisa mengakses folder 'assets'.
            $gambar_path = $row['gambar'];
            
            // Jika path gambar di DB sudah benar-benar kosong atau invalid (walaupun seharusnya tidak), 
            // beri path default agar tidak error.
            if (empty($row['gambar']) || !file_exists($gambar_path)) {
                $gambar_path = '../assets/img/berita/default-news.jpg'; // Path gambar default jika tidak ada/gagal
            }


            $news_items[] = [
                'id' => $row['id_berita'],
                'category' => $category,
                'image_thumb' => $gambar_path, // Path gambar dari database
                'title' => htmlspecialchars($row['judul']),
                // Tambahkan elipsis (...) untuk menandakan ini adalah ringkasan
                'summary' => htmlspecialchars($row['summary']) . '...', 
                'date' => date('d M Y', strtotime($row['tanggal'])), // Format tanggal
            ];
        }

    } catch (PDOException $e) {
        $db_error = true;
        // Opsional: Tampilkan pesan error di log, tapi biarkan array $news_items kosong
    } catch (Exception $e) {
        $db_error = true;
    }
}
// --- END Bagian Koneksi Database dan Ambil Data ---


// Fungsi untuk mendapatkan kelas dan nama kategori (TETAP SAMA)
function get_category_style($category)
{
  switch ($category) {
    case 'berita':
      return ['class' => 'bg-green-100 text-[#6AC259]', 'name' => 'Berita'];
    case 'pengumuman':
      return ['class' => 'bg-blue-100 text-[#00A0D6]', 'name' => 'Pengumuman'];
    case 'agenda':
      return ['class' => 'bg-orange-100 text-orange-600', 'name' => 'Agenda'];
    default:
      return ['class' => 'bg-gray-100 text-gray-600', 'name' => 'Lainnya'];
  }
}

// Menghitung jumlah konten per kategori (TETAP SAMA)
$category_counts = [
  'all' => count($news_items),
  'berita' => 0,
  'pengumuman' => 0,
  'agenda' => 0,
];

foreach ($news_items as $item) {
  if (isset($category_counts[$item['category']])) {
    $category_counts[$item['category']]++;
  }
}

// --- LOGIKA PAGINATION BARU: 4 ITEM PER HALAMAN (TETAP SAMA) ---
$items_per_page = 4;
$total_items = count($news_items);
$total_pages = ceil($total_items / $items_per_page);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($current_page < 1) {
    $current_page = 1;
} elseif ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
} elseif ($total_pages === 0) {
    $current_page = 1;
}

$start_index = ($current_page - 1) * $items_per_page;
$paginated_items = array_slice($news_items, $start_index, $items_per_page);
$is_news_available = count($news_items) > 0;
// --- END LOGIKA PAGINATION BARU ---
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
  <style>
    .text-gradient {
      background-image: linear-gradient(to right, #00A0D6, #6AC259);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-fill-color: transparent;
    }

    .hero-section {
      background-image: linear-gradient(180deg, #1f2937, #1f2937 50%, rgba(31, 41, 55, 0.9) 100%), url('../assets/img/hero-bg-dark.jpg');
      background-size: cover;
      background-position: center;
      background-blend-mode: multiply;
    }

    .news-card-image {
      position: relative;
      height: 200px;
      /* Atur tinggi gambar */
      overflow: hidden;
    }

    .news-card-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    @keyframes slide-down {
      0% {
        opacity: 0;
        transform: translateY(-10px);
      }

      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-slide-down {
      animation: slide-down 0.3s ease-out;
    }
  </style>
  <script>
    // Simple client-side filtering script (Optional, but good for UX)
    document.addEventListener('DOMContentLoaded', () => {
      const newsList = document.getElementById('news-list');
      const filterButtons = document.querySelectorAll('.filter-btn');

      filterButtons.forEach(button => {
        button.addEventListener('click', (e) => {
          const filter = e.target.dataset.filter;

          // Update active button style
          filterButtons.forEach(btn => {
            btn.classList.remove('bg-[#00A0D6]', 'text-white');
            btn.classList.add('bg-white', 'text-gray-700', 'hover:bg-[#00A0D6]-50');
          });
          e.target.classList.add('bg-[#00A0D6]', 'text-white');
          e.target.classList.remove('bg-white', 'text-gray-700', 'hover:bg-[#00A0D6]-50');

          // Filter news items
          const newsItems = newsList.querySelectorAll('.news-item');
          newsItems.forEach(item => {
            const category = item.dataset.category;
            // Catatan: Filter ini hanya akan memfilter 4 item yang saat ini ada di halaman.
            // Untuk filtering lengkap, Anda perlu menerapkan logika filtering pada PHP sebelum pagination.
            if (filter === 'all' || category === filter) {
              item.style.display = 'block';
            } else {
              item.style.display = 'none';
            }
          });
        });
      });

      // Mobile navigation toggle
      const navToggle = document.querySelector('[data-nav-toggle]');
      const mnav = document.getElementById('mnav');
      if (navToggle && mnav) {
        navToggle.addEventListener('click', () => {
          const isExpanded = mnav.classList.contains('hidden');
          if (isExpanded) {
            mnav.classList.remove('hidden');
          } else {
            mnav.classList.add('hidden');
          }
        });
      }
    });
  </script>
</head>

<body class="bg-white text-gray-900">
  <?php 
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
                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
              </path>
            </svg>
            <span>Berita & Informasi</span>
          </div>

          <h1 class="text-4xl lg:text-6xl font-bold text-white-900 mb-6 leading-tight">
            Berita<br>
            <span class="text-gradient">& Pengumuman</span>
          </h1>

          <p class="text-xl text-white-400 mb-8 leading-relaxed max-w-4xl mx-auto">
            Informasi terkini, penelitian terdepan, dan pengumuman resmi dari <span class="font-semibold text-[#00A0D6]">Laboratorium Data Technologies</span>
          </p>

          <div class="flex flex-col sm:flex-row gap-4 mb-12 justify-center">
            <a href="#berita-terbaru"
              class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
              </svg>
              Semua Berita
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
            <a href="#berita-terbaru"
              class="group inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-[#00A0D6] hover:text-[#00A0D6] transition-all duration-300 hover:scale-105 shadow-sm hover:shadow-md">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
              </svg>
              Pengumuman Resmi
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <section id="berita-terbaru" class="py-16 bg-gradient-to-br from-gray-50/50 via-white to-blue-50/30">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">


        <div class="grid lg:grid-cols-[1fr_380px] gap-16">
          <div class="space-y-12">
            <div>
              <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">
                Berita & <span class="bg-gradient-to-r from-[#00A0D6] to-[#6AC259] bg-clip-text text-transparent">Pengumuman</span>
              </h2>
              <p class="text-lg text-gray-600 font-light">Informasi resmi dan terkini dari laboratorium</p>
            </div>

            <div class="flex flex-wrap gap-3 mb-8">
              <button class="filter-btn px-6 py-2 rounded-full text-sm font-medium transition-colors bg-[#00A0D6] text-white" data-filter="all">
                Semua (<?php echo $category_counts['all']; ?>)
              </button>
              <button class="filter-btn px-6 py-2 rounded-full text-sm font-medium transition-colors bg-white text-gray-700 hover:bg-[#00A0D6]-50" data-filter="berita">
                Berita (<?php echo $category_counts['berita']; ?>)
              </button>
              <button class="filter-btn px-6 py-2 rounded-full text-sm font-medium transition-colors bg-white text-gray-700 hover:bg-[#00A0D6]-50" data-filter="pengumuman">
                Pengumuman (<?php echo $category_counts['pengumuman']; ?>)
              </button>
            </div>

            <div id="news-list" class="grid sm:grid-cols-2 lg:grid-cols-2 gap-8">
              <?php
              // Cek apakah ada item di halaman ini (dari $paginated_items)
              if (count($paginated_items) === 0) {
                // Tampilkan Empty State jika tidak ada berita di halaman ini
              ?>
                <div class="sm:col-span-2 text-center py-20">
                  <div class="bg-white/80 backdrop-blur-xl border border-white/50 shadow-2xl rounded-3xl p-16 max-w-2xl mx-auto">
                    <?php if ($db_error): ?>
                        <div class="relative mb-8">
                            <div class="w-32 h-32 bg-red-500/10 rounded-3xl flex items-center justify-center mx-auto shadow-lg">
                                <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.503-1.657 1.636-2.954L13.636 4.79C12.77 3.5 10.23 3.5 9.364 4.79L3.364 16.046c-.867 1.297.096 2.954 1.636 2.954z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 mb-4">Kesalahan Database</h3>
                        <p class="text-lg text-gray-600 mb-8 leading-relaxed max-w-lg mx-auto">
                           Gagal mengambil data dari database. Pastikan koneksi (`db_connect.php`) dan tabel `berita` sudah benar.
                        </p>
                    <?php else: ?>
                        <div class="relative mb-8">
                          <div class="w-32 h-32 bg-gradient-to-br from-[#00A0D6]/10 to-[#6AC259]/10 rounded-3xl flex items-center justify-center mx-auto shadow-lg">
                            <svg class="w-16 h-16 text-[#00A0D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                          </div>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 mb-4">Konten Tidak Ditemukan</h3>
                        <p class="text-lg text-gray-600 mb-8 leading-relaxed max-w-lg mx-auto">
                          Tidak ada berita yang tersedia untuk ditampilkan di halaman ini.
                          <span class="font-semibold text-[#00A0D6]">Coba Tambahkan Berita Baru</span> melalui halaman Admin Dashboard.
                        </p>
                    <?php endif; ?>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                      <a href="berita.php?page=1" class="inline-flex items-center gap-3 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white px-8 py-4 rounded-2xl font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Coba Muat Ulang</span>
                      </a>
                    </div>
                  </div>
                </div>
                <?php
              } else {
                // Loop untuk menampilkan setiap item berita di halaman ini
                foreach ($paginated_items as $item) {
                  $style = get_category_style($item['category']);
                ?>
                  <article class="news-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                    <a href="detail-berita.php?id=<?php echo $item['id']; ?>" class="group bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden block hover:shadow-2xl hover:scale-[1.02] transition-all duration-300">
                      <div class="news-card-image">
                        <img src="<?php echo htmlspecialchars($item['image_thumb']); ?>" 
                             alt="<?php echo htmlspecialchars($item['title']); ?>" 
                             class="transition-transform duration-500 group-hover:scale-110" 
                             loading="lazy">
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors"></div>
                      </div>
                      <div class="p-6">
                        <div class="inline-block px-3 py-1 text-xs font-semibold rounded-full mb-3 <?php echo $style['class']; ?>">
                          <?php echo $style['name']; ?>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3 leading-snug group-hover:text-[#00A0D6] transition-colors duration-300">
                          <?php echo htmlspecialchars($item['title']); ?>
                        </h3>
                        <p class="text-gray-600 mb-4 text-sm line-clamp-3">
                          <?php echo htmlspecialchars($item['summary']); ?>
                        </p>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                          <p class="text-xs text-gray-500 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <?php echo $item['date']; ?>
                          </p>
                          <span class="text-sm font-semibold text-[#00A0D6] group-hover:text-[#6AC259] transition-colors">
                            Baca Selengkapnya
                            <svg class="w-4 h-4 inline-block ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                          </span>
                        </div>
                      </div>
                    </a>
                  </article>
              <?php
                }
              }
              ?>
            </div>
            
            <div class="flex justify-center items-center mt-12 space-x-2" id="pagination-controls">
                
              <?php if ($current_page > 1): ?>
                <a href="?page=<?php echo $current_page - 1; ?>" class="px-4 py-2 text-sm font-medium rounded-xl bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
                  &larr; Sebelumnya
                </a>
              <?php else: ?>
                <button class="px-4 py-2 text-sm font-medium rounded-xl border border-gray-200 text-gray-400 cursor-not-allowed">
                  &larr; Sebelumnya
                </button>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $current_page): ?>
                  <button class="px-4 py-2 text-sm font-bold rounded-xl bg-[#00A0D6] text-white shadow-md">
                    <?php echo $i; ?>
                  </button>
                <?php else: ?>
                  <a href="?page=<?php echo $i; ?>" class="px-4 py-2 text-sm font-medium rounded-xl bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
                    <?php echo $i; ?>
                  </a>
                <?php endif; ?>
              <?php endfor; ?>

              <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?php echo $current_page + 1; ?>" class="px-4 py-2 text-sm font-medium rounded-xl bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
                  Selanjutnya &rarr;
                </a>
              <?php else: ?>
                <button class="px-4 py-2 text-sm font-medium rounded-xl border border-gray-200 text-gray-400 cursor-not-allowed">
                  Selanjutnya &rarr;
                </button>
              <?php endif; ?>

            </div>
            </div>
          <div class="space-y-10">
            <div class="bg-white/80 backdrop-blur-xl border border-white/50 shadow-xl rounded-3xl p-8">
              <div class="flex items-center gap-4 mb-8">
                <div class="w-14 h-14 bg-gradient-to-br from-[#6AC259] to-green-600 rounded-2xl flex items-center justify-center shadow-lg">
                  <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900">Agenda Mendatang</h3>
                  <p class="text-sm text-gray-600 font-light">Kegiatan & acara terjadwal</p>
                </div>
              </div>

              <div class="space-y-6" id="upcoming-agenda">
                <div class="text-center py-12">
                  <div class="w-20 h-20 bg-gradient-to-br from-[#6AC259]/10 to-green-500/10 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <svg class="w-10 h-10 text-[#6AC259]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <h4 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Agenda</h4>
                  <p class="text-gray-600 text-sm leading-relaxed">Agenda kegiatan akan ditampilkan di sini ketika tersedia</p>
                </div>
              </div>
            </div>

            <div id="kategori" class="bg-white/80 backdrop-blur-xl border border-white/50 shadow-xl rounded-3xl p-8">
              <div class="flex items-center gap-4 mb-8">
                <div class="w-14 h-14 bg-gradient-to-br from-[#00A0D6] to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                  <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900">Kategori Konten</h3>
                  <p class="text-sm text-gray-600 font-light">Jelajahi berdasarkan topik</p>
                </div>
              </div>

              <div class="space-y-3">
                <a href="berita.php?filter=all" class="group flex items-center justify-between px-6 py-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors duration-200">
                  <span class="text-gray-800 font-medium">🔍 Semua Konten</span>
                  <span class="text-blue-600 font-semibold bg-blue-100 px-3 py-1 rounded-full text-sm"><?php echo $category_counts['all']; ?></span>
                </a>
                <a href="berita.php?filter=berita" class="group flex items-center justify-between px-6 py-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors duration-200">
                  <span class="text-gray-800 font-medium">📰 Berita</span>
                  <span class="text-green-600 font-semibold bg-green-100 px-3 py-1 rounded-full text-sm"><?php echo $category_counts['berita']; ?></span>
                </a>
                <a href="berita.php?filter=pengumuman" class="group flex items-center justify-between px-6 py-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors duration-200">
                  <span class="text-gray-800 font-medium">📢 Pengumuman</span>
                  <span class="text-purple-600 font-semibold bg-purple-100 px-3 py-1 rounded-full text-sm"><?php echo $category_counts['pengumuman']; ?></span>
                </a>
                <a href="berita.php?filter=agenda" class="group flex items-center justify-between px-6 py-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition-colors duration-200">
                  <span class="text-gray-800 font-medium">📅 Agenda</span>
                  <span class="text-orange-600 font-semibold bg-orange-100 px-3 py-1 rounded-full text-sm"><?php echo $category_counts['agenda']; ?></span>
                </a>
              </div>
            </div>

            <div class="bg-white/80 backdrop-blur-xl border border-white/50 shadow-xl rounded-3xl p-8">
              <div class="flex items-center gap-4 mb-8">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                  <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900">Tautan Cepat</h3>
                  <p class="text-sm text-gray-600 font-light">Akses langsung ke halaman penting</p>
                </div>
              </div>

              <div class="space-y-3">
                <a href="profil-lab.html" class="flex items-center gap-4 p-4 bg-gradient-to-r from-gray-50 to-blue-50/50 rounded-2xl hover:from-blue-50 hover:to-blue-100/50 transition-all duration-300 group">
                  <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center group-hover:bg-blue-500/20 transition-colors">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                  <div>
                    <div class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">Profil Lab</div>
                    <div class="text-xs text-gray-600">Tentang laboratorium</div>
                  </div>
                </a>
                <a href="penelitian.html" class="flex items-center gap-4 p-4 bg-gradient-to-r from-gray-50 to-green-50/50 rounded-2xl hover:from-green-50 hover:to-green-100/50 transition-all duration-300 group">
                  <div class="w-10 h-10 bg-green-500/10 rounded-xl flex items-center justify-center group-hover:bg-green-500/20 transition-colors">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                  </div>
                  <div>
                    <div class="font-semibold text-gray-900 group-hover:text-green-600 transition-colors">Publikasi</div>
                    <div class="text-xs text-gray-600">Penelitian & jurnal</div>
                  </div>
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