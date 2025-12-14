<?php
$active_page = 'galeri';

// --- Bagian Logika PHP untuk Koneksi & Ambil Data ---
$pdo = null;
$db_connect_path = '../assets/php/db_connect.php';
$galeri_data = [];
$message = ''; // Untuk notifikasi error

if (file_exists($db_connect_path)) {
    require_once $db_connect_path;

    try {
        // Panggil metode static dari class Database
        $pdo = Database::getConnection();
    } catch (PDOException $e) {
        $message = "Kesalahan Koneksi Database.";
        $pdo = null;
    } catch (Exception $e) {
        $message = "Kesalahan Sistem.";
        $pdo = null;
    }
} else {
    $message = "Kesalahan Koneksi: File '{$db_connect_path}' tidak ditemukan. Galeri tidak dapat dimuat.";
}

// 2. Data Fetching (READ) - Diambil dari DB jika koneksi berhasil
if ($pdo) {
    try {
        // READ: Mengambil data galeri yang sudah disetujui dari database
        // Join ke tabel anggota/user untuk mendapatkan nama uploader (id_anggota)
        $sql = "SELECT g.id_foto, g.nama_foto, g.deskripsi, g.file_foto, g.id_anggota, a.nama_gelar AS anggota_name 
                FROM galeri g 
                LEFT JOIN anggota a ON g.id_anggota = a.id_anggota 
                WHERE g.status = 'approved'
                ORDER BY g.id_foto DESC";
        $stmt = $pdo->query($sql);
        $galeri_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "Gagal mengambil data galeri dari database.";
    }
}
// --- Akhir Bagian Logika PHP ---
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
              Lihat Galeri
            </a>
            <a href="#"
              class="group inline-flex items-center justify-center px-8 py-4 bg-white/10 text-white font-semibold rounded-xl hover:bg-white/20 transition-all duration-300 hover:scale-105 border border-white/20">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zM9 10l12-3"></path>
              </svg>
              Dokumentasi Acara
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
              Kategori <span class="bg-gradient-to-r from-[#00A0D6] to-[#6AC259] bg-clip-text text-transparent">Foto & Video</span>
            </h2>
            <p class="text-gray-500 mb-6">Jelajahi berbagai momen yang kami dokumentasikan.</p>
            <div class="flex flex-wrap justify-center gap-2">
              <button class="px-4 py-2 text-sm font-semibold rounded-full bg-[#00A0D6] text-white shadow-md">Semua</button>
              <button class="px-4 py-2 text-sm font-semibold rounded-full bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 transition-colors">Seminar</button>
              <button class="px-4 py-2 text-sm font-semibold rounded-full bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 transition-colors">Pelatihan</button>
              <button class="px-4 py-2 text-sm font-semibold rounded-full bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 transition-colors">Kunjungan</button>
            </div>
          </div>
        </div>

        <div id="galeri-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
          
          <?php if (!empty($galeri_data)): ?>
            <?php foreach ($galeri_data as $galeri): ?>
              <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group">
                <a href="<?php echo htmlspecialchars($galeri['file_foto']); ?>" target="_blank" class="block">
                  <img src="<?php echo htmlspecialchars($galeri['file_foto']); ?>" alt="<?php echo htmlspecialchars($galeri['nama_foto']); ?>"
                    class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110">
                </a>
                <div class="p-4">
                  <h3 class="text-lg font-semibold text-gray-900 mb-1"><?php echo htmlspecialchars($galeri['nama_foto']); ?></h3>
                  <p class="text-sm text-gray-500 line-clamp-2"><?php echo htmlspecialchars($galeri['deskripsi']); ?></p>
                  <?php if (isset($galeri['anggota_name']) && !empty($galeri['anggota_name'])): ?>
                      <p class="text-xs text-gray-400 mt-2">Diunggah oleh: <?php echo htmlspecialchars($galeri['anggota_name']); ?></p>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-span-full text-center py-10">
              <p class="text-lg font-semibold text-gray-500">Tidak ada foto di galeri saat ini.</p>
              <?php if (!empty($message)): ?>
                 <p class="text-sm text-red-500 mt-2"><?php echo $message; ?></p>
              <?php endif; ?>
            </div>
          <?php endif; ?>

        </div>

        <div class="text-center mt-16">
          <?php if (empty($galeri_data)): ?>
            <button onclick="window.location.reload()" class="inline-flex items-center gap-3 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white px-8 py-4 rounded-2xl font-semibold shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>Refresh Halaman</span>
            </button>
          <?php endif; ?>
        </div>
      </div>
    </section>

  </main>
  <?php require_once '../includes/footer.php'; ?>
</body>

</html>