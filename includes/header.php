<?php
// header.php

// Variabel $active_page harus didefinisikan di file yang memanggil (e.g., 'beranda', 'berita', 'profil-lab')
if (!isset($active_page)) {
    // Default: jika tidak didefinisikan, tidak ada yang aktif
    $active_page = ''; 
}

// Fungsi bantu untuk menentukan class aktif/non-aktif untuk navigasi desktop
function get_desktop_nav_classes($page_name, $active_page) {
    if ($page_name === $active_page) {
        return [
            // Kelas untuk link aktif (Berita)
            'link_class' => 'relative px-6 py-2 text-[15px] font-semibold text-[#00A0D6] transition-all duration-300 group',
            // Background & underline aktif
            'after_divs' => '<div class="absolute inset-0 bg-blue-50 rounded-lg opacity-100 group-hover:opacity-80 transition-opacity duration-300"></div><div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-8 h-0.5 bg-[#00A0D6] rounded-full"></div>',
        ];
    } else {
        return [
            // Kelas untuk link non-aktif
            'link_class' => 'relative px-6 py-2 text-[15px] font-medium text-[#1f2937] hover:text-[#00A0D6] transition-all duration-300 group',
            // Background hover non-aktif
            'after_divs' => '<div class="absolute inset-0 bg-gray-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>',
        ];
    }
}

// Fungsi bantu untuk menentukan class aktif/non-aktif untuk navigasi mobile
function get_mobile_nav_classes($page_name, $active_page) {
    if ($page_name === $active_page) {
        return [
            // Kelas untuk link aktif (Berita)
            'link_class' => 'py-3 px-4 text-[#00A0D6] font-semibold bg-gradient-to-r from-blue-50 to-blue-50/50 rounded-xl border border-blue-100',
            // Bullet aktif
            'bullet_div' => '<div class="w-2 h-2 bg-[#00A0D6] rounded-full"></div>',
        ];
    } else {
        return [
            // Kelas untuk link non-aktif
            'link_class' => 'py-3 px-4 text-[#1f2937] hover:text-[#00A0D6] hover:bg-gray-50 rounded-xl transition-all duration-300',
            // Bullet non-aktif
            'bullet_div' => '<div class="w-2 h-2 bg-gray-300 rounded-full"></div>',
        ];
    }
}
?>
<header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <a href="../index.php" class="group">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-12 w-12 rounded-xl to-blue-600 items-center justify-center group-hover:shadow-xl transition-all duration-300">
              <img src="../assets/img/logo.png" alt="" class="w-full h-full object-cover rounded-xl">
            </span>
            <div>
              <div class="font-bold text-[#1f2937] text-lg">Lab Data Technologies</div>
              <div class="text-sm text-gray-500 font-medium">Politeknik Negeri Malang</div>
            </div>
          </div>
        </a>

        <nav class="hidden lg:flex items-center gap-0,5">
          
          <?php $nav = get_desktop_nav_classes('beranda', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="../index.php">
            <span class="relative z-10">Beranda</span>
            <?php echo $nav['after_divs']; ?>
          </a>
          
          <?php $nav = get_desktop_nav_classes('profil-lab', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="profil-lab.php">
            <span class="relative z-10">Profil Lab</span>
            <?php echo $nav['after_divs']; ?>
          </a>
          
          <?php $nav = get_desktop_nav_classes('berita', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="berita.php">
            <span class="relative z-10">Berita</span>
            <?php echo $nav['after_divs']; ?>
          </a>
          
          <?php $nav = get_desktop_nav_classes('galeri', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="galeri.php">
            <span class="relative z-10">Galeri</span>
            <?php echo $nav['after_divs']; ?>
          </a>
          
          <?php $nav = get_desktop_nav_classes('publikasi', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="penelitian.php">
            <span class="relative z-10">Publikasi</span>
            <?php echo $nav['after_divs']; ?>
          </a>
          
          <?php $nav = get_desktop_nav_classes('fasilitas', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="fasilitas.php">
            <span class="relative z-10">Fasilitas</span>
            <?php echo $nav['after_divs']; ?>
          </a>
          
          <?php $nav = get_desktop_nav_classes('anggota', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="anggota.php">
            <span class="relative z-10">Anggota</span>
            <?php echo $nav['after_divs']; ?>
          </a>
          
          <?php $nav = get_desktop_nav_classes('kontak', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="kontak.php">
            <span class="relative z-10">Kontak</span>
            <?php echo $nav['after_divs']; ?>
          </a>
        </nav>

        <button class="lg:hidden inline-flex items-center justify-center h-11 w-11 rounded-xl border border-gray-200 hover:border-[#00A0D6] hover:bg-blue-50 transition-all duration-300 group" data-nav-toggle="#mnav">
          <svg class="w-5 h-5 text-gray-600 group-hover:text-[#00A0D6] transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
      </div>

      <div id="mnav" class="lg:hidden hidden pb-6 border-t border-gray-100 mt-4 animate-slide-down">
        <div class="grid gap-2 text-[15px] font-medium pt-6">
          
          <?php $nav = get_mobile_nav_classes('beranda', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="../index.php">
            <div class="flex items-center gap-3">
              <?php echo $nav['bullet_div']; ?>
              Beranda
            </div>
          </a>
          
          <?php $nav = get_mobile_nav_classes('profil-lab', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="profil-lab.php">
            <div class="flex items-center gap-3">
              <?php echo $nav['bullet_div']; ?>
              Profil Lab
            </div>
          </a>
          
          <?php $nav = get_mobile_nav_classes('berita', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="berita.php">
            <div class="flex items-center gap-3">
              <?php echo $nav['bullet_div']; ?>
              Berita
            </div>
          </a>
          
          <?php $nav = get_mobile_nav_classes('galeri', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="galeri.php">
            <div class="flex items-center gap-3">
              <?php echo $nav['bullet_div']; ?>
              Galeri
            </div>
          </a>
          
          <?php $nav = get_mobile_nav_classes('publikasi', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="penelitian.php">
            <div class="flex items-center gap-3">
              <?php echo $nav['bullet_div']; ?>
              Publikasi
            </div>
          </a>
          
          <?php $nav = get_mobile_nav_classes('fasilitas', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="fasilitas.php">
            <div class="flex items-center gap-3">
              <?php echo $nav['bullet_div']; ?>
              Fasilitas
            </div>
          </a>
          
          <?php $nav = get_mobile_nav_classes('anggota', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="anggota.php">
            <div class="flex items-center gap-3">
              <?php echo $nav['bullet_div']; ?>
              Anggota
            </div>
          </a>
          
          <?php $nav = get_mobile_nav_classes('kontak', $active_page); ?>
          <a class="<?php echo $nav['link_class']; ?>" href="kontak.php">
            <div class="flex items-center gap-3">
              <?php echo $nav['bullet_div']; ?>
              Kontak
            </div>
          </a>
        </div>
      </div>
    </div>
</header>