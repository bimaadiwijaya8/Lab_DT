<?php
session_start();
$active_page = 'anggota';

require_once '../assets/php/db_connect.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    try {
        $conn = Database::getConnection();
        
        $nama = trim($_POST['nama']);
        $nim = trim($_POST['nim']);
        $email = trim($_POST['email']);
        $jurusan = trim($_POST['jurusan']);
        $prodi = trim($_POST['prodi']);
        $kelas = trim($_POST['kelas']);
        $tahun_angkatan = (int)$_POST['tahun_angkatan'];
        $no_telp = trim($_POST['no_telp']);
        $password = md5('123456');
        
        $foto = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/img/member/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $new_filename = 'member_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                $foto = '../assets/img/member/' . $new_filename;
            }
        }
        
        $sql = "INSERT INTO member (nama, nim, email, jurusan, prodi, kelas, tahun_angkatan, no_telp, foto, status, password, approval_status) 
                VALUES (:nama, :nim, :email, :jurusan, :prodi, :kelas, :tahun_angkatan, :no_telp, :foto, 'aktif', :password, 'pending')";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nama' => $nama,
            ':nim' => $nim,
            ':email' => $email,
            ':jurusan' => $jurusan,
            ':prodi' => $prodi,
            ':kelas' => $kelas,
            ':tahun_angkatan' => $tahun_angkatan,
            ':no_telp' => $no_telp,
            ':foto' => $foto,
            ':password' => $password
        ]);
        
        $success_message = 'Pendaftaran berhasil! Data Anda akan ditampilkan setelah disetujui oleh admin.';
    } catch (Exception $e) {
        $error_message = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

try {
    $conn = Database::getConnection();
    
    $sql_anggota = "SELECT * FROM anggota ORDER BY id_anggota ASC";
    $stmt_anggota = $conn->query($sql_anggota);
    $anggota_list = $stmt_anggota->fetchAll();
    
    $sql_member = "SELECT * FROM member WHERE status = 'aktif' AND approval_status = 'approved' ORDER BY id_member ASC";
    $stmt_member = $conn->query($sql_member);
    $member_list = $stmt_member->fetchAll();
    
    $sql_settings = "SELECT key, value FROM settings";
    $stmt_settings = $conn->query($sql_settings);
    $settings_results = $stmt_settings->fetchAll();
    $settings = [];
    foreach ($settings_results as $setting) {
        $settings[$setting['key']] = $setting['value'];
    }
    
} catch (Exception $e) {
    $anggota_list = [];
    $member_list = [];
    $settings = [];
    $error_message = 'Gagal mengambil data: ' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Anggota – Lab Data Technologies</title>
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
      color: transparent; /* Fallback */
    }
    .hero-section {
      background-color: #0c0a09; /* Dark background for hero */
      /* Anda bisa menambahkan gambar latar belakang di sini */
    }
  </style>
</head>
<body class="bg-white text-gray-900 font-['Inter']">
  
  <?php 
    // Memuat komponen header
    // Asumsi file header.php berada di direktori induk dari direktori 'includes'
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
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
              </path>
            </svg>
            <span>Daftar Anggota</span>
          </div>

          <h1 class="text-4xl lg:text-6xl font-bold text-white-900 mb-6 leading-tight">
            Tim<br>
            <span class="text-gradient"> & Komunitas</span>
          </h1>

          <p class="text-xl text-gray-300 mb-8 leading-relaxed max-w-4xl mx-auto">
            Dosen, staff, dan mahasiswa yang berdedikasi membangun masa depan teknologi data di <span class="font-semibold text-[#00A0D6]">Laboratorium Data Technologies</span>
          </p>

          <div class="flex flex-col sm:flex-row gap-4 mb-12 justify-center">
            <a href="#dosen-staff-list"
              class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                  </path>
              </svg>
              Dosen
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
            <a href="#mahasiswa-list"
              class="group inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-[#00A0D6] hover:text-[#00A0D6] transition-all duration-300 hover:scale-105 shadow-sm hover:shadow-md">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              Asisten Laboratorium
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">STRUKTUR ORGANISASI</h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">Tim pengajar dan pengelola laboratorium yang berpengalaman dalam bidang teknologi data</p>
        </div>
        
        <?php if (!empty($settings['struktur_anggota'])): ?>
        <div class="mb-12 bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
          <?php echo $settings['struktur_anggota']; ?>
        </div>
        <?php endif; ?>
        
        <?php 
        $struktur_organisasi = array_filter($anggota_list, function($item) {
            $jabatan = strtolower($item['jabatan']);
            return strpos($jabatan, 'kepala') !== false || 
                   strpos($jabatan, 'koordinator') !== false || 
                   strpos($jabatan, 'ketua') !== false ||
                   strpos($jabatan, 'dosen') !== false;
        });
        ?>
        
        <?php if (empty($struktur_organisasi)) : ?>
        <div class="text-center py-12 bg-gray-50 rounded-2xl">
          <div class="w-16 h-16 bg-gradient-to-br from-[#00A0D6] to-[#6AC259] rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Data Dosen & Staff</h3>
          <p class="text-gray-600">Data dosen dan staff akan ditampilkan setelah tersedia dari sistem</p>
        </div>
        <?php else : ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($struktur_organisasi as $org) : ?>
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center hover:shadow-lg transition-all duration-300">
            <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4 overflow-hidden">
              <?php if (!empty($org['foto'])) : ?>
                <img src="<?php echo $org['foto']; ?>" alt="<?php echo htmlspecialchars($org['nama_gelar']); ?>" class="w-full h-full object-cover">
              <?php else : ?>
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#00A0D6] to-[#6AC259]">
                  <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
              <?php endif; ?>
            </div>
            <h3 class="font-bold text-gray-900 mb-1"><?php echo htmlspecialchars($org['nama_gelar']); ?></h3>
            <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($org['jabatan']); ?></p>
            <?php if (!empty($org['bidang_keahlian'])) : ?>
              <p class="text-xs text-[#00A0D6]"><?php echo htmlspecialchars($org['bidang_keahlian']); ?></p>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-8">
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">ANGGOTA & STAFF</h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto mb-6">Asisten laboratorium dan tim yang aktif dalam pengembangan teknologi</p>
          
          <div class="flex justify-center gap-3 mb-8">
            <button onclick="filterAnggota('all')" class="filter-btn px-6 py-2 rounded-full bg-gradient-to-r from-[#00A0D6] to-[#6AC259] text-white font-medium">Semua</button>
            <button onclick="filterAnggota('dosen')" class="filter-btn px-6 py-2 rounded-full bg-white border-2 border-gray-300 text-gray-700 font-medium hover:border-[#00A0D6]">Dosen</button>
            <button onclick="filterAnggota('staff')" class="filter-btn px-6 py-2 rounded-full bg-white border-2 border-gray-300 text-gray-700 font-medium hover:border-[#00A0D6]">Staff</button>
          </div>
        </div>
        
        <div id="anggota-list" class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          <?php if (empty($anggota_list)) : ?>
            <div class="col-span-full text-center py-12 bg-white rounded-2xl">
              <div class="w-16 h-16 bg-gradient-to-br from-[#00A0D6] to-[#6AC259] rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Data Anggota</h3>
              <p class="text-gray-600">Data anggota akan ditampilkan setelah tersedia</p>
            </div>
          <?php else : ?>
            <?php foreach ($anggota_list as $anggota) : ?>
            <div class="anggota-card bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center hover:shadow-lg transition-all duration-300" data-jabatan="<?php echo strtolower($anggota['jabatan']); ?>">
              <div class="w-20 h-20 bg-gray-200 rounded-full mx-auto mb-4 overflow-hidden">
                <?php if (!empty($anggota['foto'])) : ?>
                  <img src="<?php echo $anggota['foto']; ?>" alt="<?php echo htmlspecialchars($anggota['nama_gelar']); ?>" class="w-full h-full object-cover">
                <?php else : ?>
                  <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#00A0D6] to-[#6AC259]">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                  </div>
                <?php endif; ?>
              </div>
              <h3 class="font-bold text-gray-900 mb-1 text-sm"><?php echo htmlspecialchars($anggota['nama_gelar']); ?></h3>
              <p class="text-xs text-gray-600"><?php echo htmlspecialchars($anggota['jabatan']); ?></p>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">MEMBER</h2>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-2">Kenali para member yang menjadi bagian dari tim ini dan berperan dalam setiap proses pengembangan.</p>
        </div>
        
        <?php if (!empty($member_list)) : ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
          <?php foreach ($member_list as $member) : ?>
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center hover:shadow-lg transition-all duration-300">
            <div class="w-20 h-20 bg-gray-200 rounded-full mx-auto mb-4 overflow-hidden">
              <?php if (!empty($member['foto'])) : ?>
                <img src="<?php echo $member['foto']; ?>" alt="<?php echo htmlspecialchars($member['nama']); ?>" class="w-full h-full object-cover">
              <?php else : ?>
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#00A0D6] to-[#6AC259]">
                  <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
              <?php endif; ?>
            </div>
            <h3 class="font-bold text-gray-900 mb-1 text-sm"><?php echo htmlspecialchars($member['nama']); ?></h3>
            <p class="text-xs text-gray-600 mb-1"><?php echo htmlspecialchars($member['nim']); ?></p>
            <p class="text-xs text-[#00A0D6]"><?php echo htmlspecialchars($member['prodi']); ?></p>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="py-24 bg-white">
      <div class="max-w-4xl mx-auto px-6 lg:px-8">
        <div class="bg-gradient-to-br from-gray-50/50 via-white to-blue-50/30 rounded-3xl shadow-xl border border-gray-100 p-12 text-center">
          <div class="w-20 h-20 bg-gradient-to-br from-[#00A0D6] to-[#6AC259] rounded-2xl flex items-center justify-center mx-auto mb-8 shadow-lg">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
          </div>
          
          <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
            <span class="text-gradient">Bergabung dengan Tim</span>
          </h2>
          
          <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-12 leading-relaxed font-light">
            Ingin menjadi bagian dari komunitas teknologi data terdepan? Daftarkan diri Anda dan berkontribusi dalam penelitian dan pengembangan inovasi masa depan.
          </p>
          
          <button id="register-btn" data-modal-open="#registration-modal" class="inline-flex items-center gap-3 bg-gradient-to-r from-[#00A0D6] to-blue-600 text-white font-semibold px-8 py-4 rounded-full hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            <span>Daftar Jadi Member</span>
          </button>
        </div>
      </div>
    </section>

    <div id="registration-modal" class="modal-backdrop hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        
        <div class="p-6 border-b">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold">Pendaftaran Member Laboratorium</h3>
            <button data-modal-close="#registration-modal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        
        <form id="registration-form" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
          <input type="hidden" name="action" value="register">
          
          <?php if (!empty($success_message)) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
              <?php echo $success_message; ?>
            </div>
          <?php endif; ?>
          
          <?php if (!empty($error_message)) : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
              <?php echo $error_message; ?>
            </div>
          <?php endif; ?>
          
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
              <input type="text" name="nama" id="nama" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent" placeholder="Masukkan nama lengkap">
            </div>
            
            <div>
              <label for="nim" class="block text-sm font-medium text-gray-700 mb-2">NIM *</label>
              <input type="text" name="nim" id="nim" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent" placeholder="Contoh: 2241720001">
            </div>
          </div>

          <div>
            <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
            <input type="file" name="foto" id="foto" accept=".jpg,.jpeg,.png" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent">
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-2">Jurusan *</label>
              <select name="jurusan" id="jurusan" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent">
                <option value="">Pilih Jurusan</option>
                <option value="Teknik Informatika">Teknik Informatika</option>
                <option value="Sistem Informasi Bisnis">Sistem Informasi Bisnis</option>
                <option value="Teknik Komputer dan Jaringan">Teknik Komputer dan Jaringan</option>
              </select>
            </div>
            
            <div>
              <label for="prodi" class="block text-sm font-medium text-gray-700 mb-2">Program Studi *</label>
              <select name="prodi" id="prodi" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent">
                <option value="">Pilih Program Studi</option>
                <option value="TI">Teknik Informatika (TI)</option>
                <option value="SIB">Sistem Informasi Bisnis (SIB)</option>
                <option value="TKJ">Teknik Komputer dan Jaringan (TKJ)</option>
              </select>
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas *</label>
              <input type="text" name="kelas" id="kelas" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent" placeholder="Contoh: TI-3A">
            </div>
            
            <div>
              <label for="tahun_angkatan" class="block text-sm font-medium text-gray-700 mb-2">Tahun Angkatan *</label>
              <input type="number" name="tahun_angkatan" id="tahun_angkatan" required min="2020" max="2030" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent" placeholder="Contoh: 2024">
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label for="no_telp" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon *</label>
              <input type="tel" name="no_telp" id="no_telp" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent" placeholder="Contoh: 08123456789">
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
              <input type="email" name="email" id="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00A0D6] focus:border-transparent" placeholder="Contoh: user@gmail.com">
            </div>
          </div>

          <div class="flex gap-4 pt-4">
            <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
              Batal
            </button>
            <button type="submit" class="flex-1 px-4 py-2 bg-[#00A0D6] text-white rounded-lg hover:bg-[#0090C0] transition">
              Kirim Pendaftaran
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <?php 
    // Memuat komponen footer
    require_once '../includes/footer.php';
  ?>

  <script>
    function openModal() {
      const modal = document.getElementById('registration-modal');
      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      const modal = document.getElementById('registration-modal');
      modal.classList.add('hidden');
      document.body.style.overflow = '';
    }

    function filterAnggota(filter) {
      const cards = document.querySelectorAll('.anggota-card');
      const buttons = document.querySelectorAll('.filter-btn');
      
      buttons.forEach(btn => {
        btn.classList.remove('bg-gradient-to-r', 'from-[#00A0D6]', 'to-[#6AC259]', 'text-white');
        btn.classList.add('bg-white', 'border-2', 'border-gray-300', 'text-gray-700');
      });
      
      event.target.classList.remove('bg-white', 'border-2', 'border-gray-300', 'text-gray-700');
      event.target.classList.add('bg-gradient-to-r', 'from-[#00A0D6]', 'to-[#6AC259]', 'text-white');
      
      cards.forEach(card => {
        const jabatan = card.getAttribute('data-jabatan');
        if (filter === 'all') {
          card.style.display = 'block';
        } else if (jabatan && jabatan.toLowerCase().includes(filter.toLowerCase())) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      const modal = document.getElementById('registration-modal');
      const openButtons = document.querySelectorAll('[data-modal-open]');
      const closeButtons = document.querySelectorAll('[data-modal-close]');

      openButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          modal.classList.remove('hidden');
          document.body.style.overflow = 'hidden';
        });
      });

      closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          modal.classList.add('hidden');
          document.body.style.overflow = '';
        });
      });

      modal.addEventListener('click', (e) => {
        if (e.target.id === 'registration-modal') {
          modal.classList.add('hidden');
          document.body.style.overflow = '';
        }
      });

      <?php if (!empty($success_message)) : ?>
        openModal();
      <?php endif; ?>
    });
  </script>
</body>
</html>