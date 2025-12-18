<?php
$active_page = 'kontak';

// Include database connection and get settings
include '../assets/php/db_connect.php';
$settings = [];
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = Database::getConnection();
        
        $form_type = $_POST['form_type'] ?? '';

        if ($form_type === 'ask' && $pdo) {
            $nama_lengkap = trim($_POST['nama'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $pesan = trim($_POST['pesan'] ?? '');

            if (!empty($nama_lengkap) && !empty($email) && !empty($pesan)) {
                try {
                    $sql = "INSERT INTO pertanyaan (nama_lengkap, email, pesan) VALUES (:nama, :email, :pesan)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':nama' => $nama_lengkap,
                        ':email' => $email,
                        ':pesan' => $pesan
                    ]);
                    $success_message = "Pertanyaan Anda telah berhasil dikirim!";
                } catch (Exception $e) {
                    $error_message = "Gagal mengirim pertanyaan: " . $e->getMessage();
                }
            } else {
                $error_message = "Semua field harus diisi!";
            }
        }
        
        if ($form_type === 'cooperation' && $pdo) {
            $nama = trim($_POST['nama'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $no_telp = trim($_POST['no_telp'] ?? '');
            $nama_perusahaan = trim($_POST['nama_perusahaan'] ?? '');
            $kontak_perusahaan = trim($_POST['kontak_perusahaan'] ?? '');
            $deskripsi = trim($_POST['deskripsi'] ?? '');
            
            if (!empty($nama) && !empty($email) && !empty($no_telp) && !empty($nama_perusahaan) && !empty($deskripsi)) {
                $file_proposal = null;
                
                if (isset($_FILES['proposal']) && $_FILES['proposal']['error'] == UPLOAD_ERR_OK) {
                    $target_dir = '../assets/files/publikasi/';
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    
                    $file_name = basename($_FILES['proposal']['name']);
                    $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
                    $unique_name = 'proposal_' . time() . '_' . $safe_file_name;
                    $target_file = $target_dir . $unique_name;
                    
                    if (move_uploaded_file($_FILES['proposal']['tmp_name'], $target_file)) {
                        $file_proposal = '../assets/files/publikasi/' . $unique_name;
                    }
                }
                
                try {
                    $sql = "INSERT INTO kerjasama (nama, email, no_telp, nama_perusahaan, kontak_perusahaan, deskripsi_tujuan, file_proposal) VALUES (:nama, :email, :no_telp, :nama_perusahaan, :kontak_perusahaan, :deskripsi, :file_proposal)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':nama' => $nama,
                        ':email' => $email,
                        ':no_telp' => $no_telp,
                        ':nama_perusahaan' => $nama_perusahaan,
                        ':kontak_perusahaan' => $kontak_perusahaan,
                        ':deskripsi' => $deskripsi,
                        ':file_proposal' => $file_proposal
                    ]);
                    $success_message = "Pengajuan kerja sama Anda telah berhasil dikirim!";
                } catch (Exception $e) {
                    $error_message = "Gagal mengirim pengajuan: " . $e->getMessage();
                }
            } else {
                $error_message = "Semua field wajib harus diisi!";
            }
        }
    } catch (Exception $e) {
        $error_message = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

try {
  $pdo = Database::getConnection();
  $stmt = $pdo->prepare("SELECT key, value FROM settings");
  $stmt->execute();
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($results as $result) {
    $settings[$result['key']] = $result['value'];
  }
} catch (Exception $e) {
  // Keep default values if there's an error
  $settings = [];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kontak – Lab Data Technologies</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* Gradient Utama untuk Judul di Hero dan Konten (dari berita.php) */
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

    /* Style Pill Switch dipindahkan ke sini */
    .pill-switch-btn {
      color: #6b7280;
      background: transparent;
      border-radius: 9999px;
    }

    .pill-switch-btn.active {
      background: #00A0D6;
      color: #ffffff;
      /* Menggunakan shadow yang konsisten dengan warna biru */
      box-shadow: 0 4px 10px rgba(0, 160, 214, 0.12); 
    }

    .pill-switch-btn:not(.active):hover {
      color: #374151;
      background: rgba(0, 0, 0, 0.02);
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
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
              </path>
            </svg>
            <span>Kontak & Kerja Sama</span>
          </div>

          <h1 class="text-4xl lg:text-6xl font-bold text-white mb-6 leading-tight">
            Kontak<br>
            <span class="text-gradient">& Kerja Sama</span>
          </h1>

          <p class="text-xl text-white/80 mb-8 leading-relaxed max-w-4xl mx-auto font-light">
            Hubungi kami untuk informasi lebih lanjut atau ajukan kerja sama melalui formulir dinamis yang tersedia.
          </p>

          <div class="flex flex-col sm:flex-row gap-4 mb-12 justify-center">
            <a href="#contact-info"
              class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              Lokasi Laboratorium
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
            <a href="#contact-info"
              class="group inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-[#00A0D6] hover:text-[#00A0D6] transition-all duration-300 hover:scale-105 shadow-sm hover:shadow-md">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
              </svg>
              Hubungi Kami
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
      <div class="max-w-7xl mx-auto px-6 lg:px-8" id="contact-info">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-12">
          <div class="space-y-6">
            <div class="bg-white p-6 lg:p-8 rounded-2xl border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-300">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-[#00A0D6] to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900">Informasi Kontak</h3>
              </div>
              <div class="space-y-4">
                <div class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-[#00A0D6] mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                  <div>
                    <p class="font-medium text-gray-900">Email</p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($settings['email'] ?? 'lab.dt@polinema.ac.id'); ?></p>
                  </div>
                </div>
                <div class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-[#00A0D6] mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                  </svg>
                  <div>
                    <p class="font-medium text-gray-900">Telepon</p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($settings['no_telepon'] ?? '(0341) 404040'); ?></p>
                  </div>
                </div>
                <div class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-[#00A0D6] mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  <div>
                    <p class="font-medium text-gray-900">Alamat</p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($settings['alamat'] ?? 'Jl. Soekarno Hatta No. 9, Malang'); ?></p>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-white p-6 lg:p-8 rounded-2xl border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-300">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-[#6AC259] to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900">Jam Layanan</h3>
              </div>
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="font-medium text-gray-900">Senin – Jumat</p>
                    <p class="text-gray-600">08.00 – 16.00 WIB</p>
                  </div>
                  <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                </div>
                <div class="flex items-center justify-between">
                  <div>
                    <p class="font-medium text-gray-900">Sabtu – Minggu</p>
                    <p class="text-gray-600">Tutup</p>
                  </div>
                  <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                </div>
              </div>
            </div>

            <div class="bg-white p-6 lg:p-8 rounded-2xl border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-300">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                  </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900">Media Sosial</h3>
              </div>
              <div class="flex gap-3">
              <a href="<?php echo htmlspecialchars($settings['medsos_linkedin'] ?? '#'); ?>" class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white hover:scale-110 transition-transform" target="_blank" rel="noopener noreferrer">
                  <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                  </svg>
                </a>
              <a href="<?php echo htmlspecialchars($settings['medsos_youtube'] ?? '#'); ?>" class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-white hover:scale-110 transition-transform" target="_blank" rel="noopener noreferrer">
                  <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                  </svg>
                </a>
              <a href="<?php echo htmlspecialchars($settings['medsos_instagram'] ?? '#'); ?>" class="w-10 h-10 bg-gradient-to-br from-red-500 to-purple-600 rounded-xl flex items-center justify-center text-white hover:scale-110 transition-transform" target="_blank" rel="noopener noreferrer">
                  <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1 1 12.324 0 6.162 6.162 0 0 1-12.324 0zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm4.965-10.405a1.44 1.44 0 1 1 2.881.001 1.44 1.44 0 0 1-2.881-.001z" />
                  </svg>
                </a>
              </div>
            </div>
          </div>

          <div class="bg-white p-6 lg:p-8 rounded-2xl border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-300">
            <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-8">Formulir Kontak</h2>

            <?php if (!empty($success_message)) : ?>
              <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success_message; ?>
              </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)) : ?>
              <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error_message; ?>
              </div>
            <?php endif; ?>
            
            <div class="w-full flex justify-center mb-8">
              <div class="inline-flex rounded-full bg-gray-100 p-1 shadow-sm" role="tablist" aria-label="Mode Form">
                <button type="button" data-form-type="ask" class="pill-switch-btn active flex-1 text-center px-6 py-3 rounded-full text-sm font-medium transition-all" role="tab" aria-selected="true">
                  Pertanyaan
                </button>
                <button type="button" data-form-type="coop" class="pill-switch-btn flex-1 text-center px-6 py-3 rounded-full text-sm font-medium transition-all" role="tab" aria-selected="false">
                  Kerja Sama
                </button>
              </div>
            </div>
            
            <form id="contact-form" class="space-y-5" method="POST" enctype="multipart/form-data" novalidate>
              <input type="hidden" name="form_type" id="form_type" value="ask">
              <div id="form-ask" class="space-y-5">
                <div>
                  <input name="nama" id="ask-name" required class="w-full h-12 rounded-lg border border-gray-300 px-4 focus:outline-none focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent transition-all" placeholder="Nama Lengkap" />
                </div>
                <div>
                  <input name="email" id="ask-email" type="email" required class="w-full h-12 rounded-lg border border-gray-300 px-4 focus:outline-none focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent transition-all" placeholder="Email Aktif" />
                </div>
                <div>
                  <textarea name="pesan" id="ask-message" required class="w-full min-h-[120px] rounded-lg border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent transition-all resize-none" placeholder="Pesan Anda"></textarea>
                </div>
              </div>

              <div id="form-coop" class="space-y-5 hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                  <div>
                    <input name="nama" id="coop-name" required class="w-full h-12 rounded-lg border border-gray-300 px-4 focus:outline-none focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent transition-all" placeholder="Nama Lengkap" />
                  </div>
                  <div>
                    <input name="email" id="coop-email" type="email" required class="w-full h-12 rounded-lg border border-gray-300 px-4 focus:outline-none focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent transition-all" placeholder="Email Aktif" />
                  </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                  <div>
                    <input name="no_telp" id="coop-phone" required class="w-full h-12 rounded-lg border border-gray-300 px-4 focus:outline-none focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent transition-all" placeholder="Nomor Telepon" />
                  </div>
                  <div>
                    <input name="nama_perusahaan" id="coop-company" required class="w-full h-12 rounded-lg border border-gray-300 px-4 focus:outline-none focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent transition-all" placeholder="Nama Perusahaan" />
                  </div>
                </div>
                <div>
                  <textarea name="deskripsi" id="coop-purpose" required class="w-full min-h-[100px] rounded-lg border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent transition-all resize-none" placeholder="Deskripsi Tujuan Kerja Sama"></textarea>
                </div>
                <div>
                  <input name="kontak_perusahaan" id="coop-contact" class="w-full h-12 rounded-lg border border-gray-300 px-4 focus:outline-none focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent transition-all" placeholder="Kontak Perusahaan (Opsional)" />
                </div>
                <div class="space-y-2">
                  <label for="coop-proposal" class="block text-sm font-medium text-gray-700">Unggah Proposal Kerja Sama (PDF/DOC/DOCX, maks. 5MB)</label>
                  <div class="mt-1 flex items-center">
                    <label for="coop-proposal" class="cursor-pointer w-full">
                      <div class="w-full h-12 px-4 py-2 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center hover:border-[#00A0D6] hover:bg-gray-50 transition-colors duration-200">
                        <span class="text-sm text-gray-500" id="file-name">Klik untuk memilih file</span>
                        <input type="file" id="coop-proposal" name="proposal" class="hidden" accept=".pdf,.doc,.docx" required>
                      </div>
                    </label>
                  </div>
                  <p class="text-xs text-gray-500">Format: PDF, DOC, DOCX (Maksimal 5MB)</p>
                </div>
              </div>

              <button id="contact-submit" class="w-full h-12 rounded-full bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold hover:shadow-xl hover:scale-[1.01] transition-all duration-300">
                Kirim Pesan
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php
  require_once '../includes/footer.php';
  ?>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Get all pill switch buttons
      const pillButtons = document.querySelectorAll('.pill-switch-btn');
      const formAsk = document.getElementById('form-ask');
      const formCoop = document.getElementById('form-coop');
      const contactForm = document.getElementById('contact-form');
      const submitButton = document.getElementById('contact-submit');
      const fileInput = document.getElementById('coop-proposal');
      const fileName = document.getElementById('file-name');

      // Handle pill switch button clicks
      pillButtons.forEach(button => {
        button.addEventListener('click', function() {
          // Remove active class from all buttons
          pillButtons.forEach(btn => {
            btn.classList.remove('active');
            btn.setAttribute('aria-selected', 'false');
          });

          // Add active class to clicked button
          this.classList.add('active');
          this.setAttribute('aria-selected', 'true');

          // Show/hide forms based on selection
          const formType = this.getAttribute('data-form-type');
          if (formType === 'ask') {
            formAsk.classList.remove('hidden');
            formCoop.classList.add('hidden');
            submitButton.textContent = 'Kirim Pesan';
            document.getElementById('form_type').value = 'ask';
          } else if (formType === 'coop') {
            formAsk.classList.add('hidden');
            formCoop.classList.remove('hidden');
            submitButton.textContent = 'Kirim Proposal Kerja Sama';
            document.getElementById('form_type').value = 'cooperation';
          }
        });
      });

      // Handle file input change
      if (fileInput) {
        fileInput.addEventListener('change', function() {
          if (this.files && this.files[0]) {
            fileName.textContent = this.files[0].name;
          } else {
            fileName.textContent = 'Klik untuk memilih file';
          }
        });
      }

      // Handle form submission validation
      contactForm.addEventListener('submit', function(e) {
        const activeForm = document.querySelector('.pill-switch-btn.active').getAttribute('data-form-type');
        
        // Disable fields in hidden form to prevent submission
        const askInputs = document.querySelectorAll('#form-ask input, #form-ask textarea');
        const coopInputs = document.querySelectorAll('#form-coop input, #form-coop textarea');
        
        if (activeForm === 'ask') {
          // Enable ask form fields, disable coop form fields
          askInputs.forEach(input => input.disabled = false);
          coopInputs.forEach(input => input.disabled = true);
          
          const name = document.getElementById('ask-name').value;
          const email = document.getElementById('ask-email').value;
          const message = document.getElementById('ask-message').value;
          
          if (!name || !email || !message) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang diperlukan.');
            return false;
          }
        } else if (activeForm === 'coop') {
          // Enable coop form fields, disable ask form fields
          coopInputs.forEach(input => input.disabled = false);
          askInputs.forEach(input => input.disabled = true);
          
          const name = document.getElementById('coop-name').value;
          const email = document.getElementById('coop-email').value;
          const phone = document.getElementById('coop-phone').value;
          const company = document.getElementById('coop-company').value;
          const purpose = document.getElementById('coop-purpose').value;
          
          if (!name || !email || !phone || !company || !purpose) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang diperlukan.');
            return false;
          }
        }
      });
    });
  </script>
</body>

</html>