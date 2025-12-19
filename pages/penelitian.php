<?php
// penelitian.php

$active_page = 'penelitian';

// 1. Variabel Konfigurasi Pagination
$items_per_page = 5; // Hanya 5 item per halaman
$total_items = 0;
$total_pages = 0;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$paginated_publikasi = []; // Array untuk menyimpan publikasi di halaman saat ini


// 2. Koneksi Database (Diambil dari logika admin-dashboard.php)
$pdo = null;
$db_connect_path = '../assets/php/db_connect.php';

if (file_exists($db_connect_path)) {
  require_once $db_connect_path;

  try {
    // Panggil metode static dari class Database
    $pdo = Database::getConnection();
  } catch (PDOException $e) {
    error_log("Kesalahan Koneksi Database Publikasi: " . $e->getMessage());
    $pdo = null;
  } catch (Exception $e) {
    error_log("Kesalahan Sistem Publikasi: " . $e->getMessage());
    $pdo = null;
  }
} else {
  error_log("Kesalahan Koneksi: File '{$db_connect_path}' tidak ditemukan.");
  $pdo = null;
}

// 3. Logika READ Data Publikasi LENGKAP
$publikasi_data_full = []; // Variabel baru untuk menampung SEMUA data sebelum pagination
$publikasi_error = '';
if ($pdo) {
  try {
    // Mengambil data publikasi yang sudah disetujui menggunakan view yang ada
    $sql = "SELECT 
                    id_publikasi, 
                    judul, 
                    penulis, 
                    tanggal_terbit, 
                    deskripsi,
                    file_publikasi
                FROM 
                    vw_publikasi_member
                ORDER BY 
                    tanggal_terbit DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $publikasi_data_full = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- LOGIKA PAGINATION DITERAPKAN DI SINI ---
    $total_items = count($publikasi_data_full);
    $total_pages = ceil($total_items / $items_per_page);
    
    // Validasi halaman saat ini
    if ($current_page < 1) {
        $current_page = 1;
    } elseif ($current_page > $total_pages && $total_pages > 0) {
        $current_page = $total_pages;
    } elseif ($total_pages === 0) {
        $current_page = 1;
    }

    $start_index = ($current_page - 1) * $items_per_page;
    // Potong array publikasi untuk halaman saat ini
    $paginated_publikasi = array_slice($publikasi_data_full, $start_index, $items_per_page);
    // --- END LOGIKA PAGINATION ---

  } catch (PDOException $e) {
    $publikasi_error = "Gagal mengambil data publikasi. Silakan coba lagi nanti.";
    error_log("Kesalahan Query Publikasi: " . $e->getMessage());
  }
} else {
  $publikasi_error = "Layanan database tidak tersedia.";
}

// Fungsi pembantu untuk memformat tanggal (opsional)
function format_tanggal($tanggal_db)
{
  $timestamp = strtotime($tanggal_db);
  // Contoh format: 25 November 2025
  // Gunakan 'l' untuk nama hari dalam Bahasa Inggris, atau 'F' untuk nama bulan
  // Jika ingin Bahasa Indonesia, perlu mengatur locale atau array bulan
  return date('d M Y', $timestamp); // Menggunakan format d M Y agar netral
}
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
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
      /* Shadow lebih tebal saat hover */
      transform: translateY(-3px);
    }

    .publication-category {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      /* text-xs */
      font-weight: 500;
      /* font-medium */
    }

    /* Warna kategori, disamakan dengan warna brand */
    .category-journal {
      background-color: rgba(0, 160, 214, 0.1);
      /* #00A0D6/10 */
      color: #00A0D6;
      border: 1px solid rgba(0, 160, 214, 0.2);
    }

    .category-conference {
      background-color: rgba(106, 194, 89, 0.1);
      /* #6AC259/10 */
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
            <a href="#daftar-publikasi"
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

    <section id="daftar-publikasi" class="py-16 sm:py-24 bg-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-12">Daftar Publikasi Terbaru</h2>

        <?php if ($publikasi_error) : ?>
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-8"
            role="alert">
            <strong class="font-bold">Gagal!</strong>
            <span class="block sm:inline"><?php echo htmlspecialchars($publikasi_error); ?></span>
          </div>
        <?php endif; ?>

        <?php if (empty($publikasi_data_full) && !$publikasi_error) : ?>
          <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-8" role="alert">
            <p class="font-bold">Informasi</p>
            <p>Saat ini belum ada publikasi yang tersedia.</p>
          </div>
        <?php else : ?>
          <div class="space-y-6" id="publication-list">
            <?php 
            // Loop menggunakan data yang sudah dipaginasi
            foreach ($paginated_publikasi as $publikasi) : 
            ?>
              <div
                class="bg-white p-6 rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition duration-300 publication-card">
                <div class="md:flex md:justify-between md:items-start">
                  <div class="flex-1">
                    <h3 class="text-xl font-bold mb-1">
                      <?php echo htmlspecialchars($publikasi['judul']); ?>
                    </h3>
                    <p class="text-sm text-gray-500 mb-2">
                      <span class="font-semibold">Penulis:</span> <?php echo htmlspecialchars($publikasi['penulis']); ?>
                    </p>
                    <p class="text-sm text-gray-500 mb-3">
                      <span class="font-semibold">Terbit:</span> <?php echo format_tanggal($publikasi['tanggal_terbit']); ?>
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                      <?php
                      // Tampilkan deskripsi, batasi panjang jika terlalu panjang
                      $deskripsi_singkat = strlen($publikasi['deskripsi']) > 200 ?
                        substr($publikasi['deskripsi'], 0, 200) . '...' :
                        $publikasi['deskripsi'];
                      echo nl2br(htmlspecialchars($deskripsi_singkat));
                      ?>
                    </p>
                  </div>
                  <div class="mt-4 md:mt-0 md:ml-6 flex-shrink-0">
                    <?php if (!empty($publikasi['file_publikasi'])) : ?>
                      <a href="<?php echo htmlspecialchars($publikasi['file_publikasi']); ?>" target="_blank"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Lihat Dokumen
                      </a>
                    <?php else : ?>
                      <span class="text-sm text-red-500">File tidak tersedia</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <?php if ($total_pages > 1): // Tampilkan kontrol pagination hanya jika lebih dari 1 halaman ?>
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
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </section>

  </main>

  <?php include '../includes/footer.php'; // Ganti dengan path ke footer Anda 
  ?>

</body>

</html>