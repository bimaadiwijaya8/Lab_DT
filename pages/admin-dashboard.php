<?php
// admin-dashboard.php (All-in-One: Dashboard Ringkasan + Kelola Berita + Kelola Galeri + Kelola Fasilitas + Kelola Publikasi)

// --- Bagian Logika PHP Awal ---
session_start();
$is_authenticated = true; // Ganti dengan logika otentikasi sesungguhnya (misal: isset($_SESSION['user_id']))

if (!$is_authenticated) {
    header('Location: login.php');
    exit;
}

// 1. Variabel Konfigurasi Dasar
$current_year = date('Y');
$username = "AdminLDT"; // Ganti dengan nama user yang login
$active_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$admin_user_id = 1; // HARDCODED: Ganti dengan ID user yang login (untuk kolom 'author' pada tabel berita / 'created_by' pada fasilitas / 'id_anggota'/'updated_by' pada galeri / 'id_anggota' pada publikasi)
$message = ''; // Untuk notifikasi sukses/gagal

// 2. Koneksi Database
$pdo = null;
$db_error = false;
$db_connect_path = '../assets/php/db_connect.php';

if (file_exists($db_connect_path)) {
    require_once $db_connect_path;

    try {
        // Panggil metode static dari class Database
        $pdo = Database::getConnection();
    } catch (PDOException $e) {
        $db_error = true;
        // Tampilkan pesan error detail dari database
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                        Kesalahan Koneksi Database: <b>PDOException</b>. Pastikan host, port, dbname, user, dan password di <b>db_connect.php</b> sudah benar. 
                        Detail Error: " . htmlspecialchars($e->getMessage()) . "
                    </div>";
        $pdo = null;
    } catch (Exception $e) {
        $db_error = true;
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                        Kesalahan Sistem: " . htmlspecialchars($e->getMessage()) . "
                    </div>";
        $pdo = null;
    }
} else {
    $db_error = true;
    $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Kesalahan Koneksi: File '{$db_connect_path}' tidak ditemukan. CRUD tidak dapat berjalan.</div>";
}

// 3. Penanganan Operasi CRUD Berita (Hanya jika koneksi berhasil)
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $active_page === 'berita') {
    $action = $_POST['action'] ?? '';

    // --- CREATE (Tambah Berita Baru) ---
    if ($action === 'add_news') {
        $judul = trim($_POST['judul']);
        $informasi = trim($_POST['informasi']);
        $tanggal = trim($_POST['tanggal']);

        $upload_ok = true;
        $gambar_path_for_db = '';
        $upload_message = '';

        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {

            // 1. Tentukan direktori dan nama file
            $target_dir = '../assets/img/berita/';
            // Pastikan folder target ada dan memiliki izin tulis (write permission)
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            // Ambil nama file dan buat target path lengkap
            $file_name = basename($_FILES['gambar']['name']);
            // Untuk keamanan, ganti spasi/karakter spesial dengan underscore
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $target_file = $target_dir . $safe_file_name;
            $gambar_path_for_db = $target_file;

            // Cek jika nama file sudah ada, tambahkan timestamp
            if (file_exists($target_file)) {
                $unique_name = time() . '_' . $safe_file_name;
                $target_file = $target_dir . $unique_name;
                $gambar_path_for_db = $target_file;
            }

            // 2. Lakukan proses upload
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $upload_ok = false;
                $upload_message = "Gagal mengupload gambar. Pastikan folder '{$target_dir}' memiliki izin tulis (0777).";
            }
        } else if ($_FILES['gambar']['error'] === UPLOAD_ERR_NO_FILE) {
            $upload_ok = false;
            $upload_message = "Harap unggah gambar utama untuk berita ini.";
        } else {
            $upload_ok = false;
            $upload_message = "Terjadi error saat upload file. Kode error: " . $_FILES['gambar']['error'];
        }

        // 3. Simpan ke database jika upload berhasil
        if ($upload_ok) {
            try {
                $sql = "INSERT INTO berita (judul, gambar, informasi, tanggal, author, status) 
                        VALUES (:judul, :gambar, :informasi, :tanggal, :author, 'pending')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':judul' => $judul,
                    ':gambar' => $gambar_path_for_db, // Path yang sudah tersimpan di server
                    ':informasi' => $informasi,
                    ':tanggal' => $tanggal,
                    ':author' => $admin_user_id
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Berita baru berhasil ditambahkan!</div>";
            } catch (Exception $e) {
                // Jika gagal simpan DB, hapus file yang sudah terupload (optional cleanup)
                if (file_exists($gambar_path_for_db)) {
                    @unlink($gambar_path_for_db);
                }
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan berita (DB Error): " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan berita (Upload Error): {$upload_message}</div>";
        }
    }

    // --- UPDATE (Edit Berita) ---
    if ($action === 'edit_news') {
        $id_berita = (int)$_POST['id_berita'];
        $judul = trim($_POST['judul']);
        $informasi = trim($_POST['informasi']);
        $tanggal = trim($_POST['tanggal']);

        $new_gambar_name = $_FILES['gambar']['name'] ?? '';
        $current_gambar_path = $_POST['current_gambar']; // Path gambar lama
        $gambar_path_for_db = $current_gambar_path;      // Default: gunakan gambar lama
        $upload_ok = true;

        // Cek apakah ada file baru yang diupload
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK && !empty($new_gambar_name)) {

            $target_dir = '../assets/img/berita/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_name = basename($_FILES['gambar']['name']);
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $target_file = $target_dir . $safe_file_name;

            if (file_exists($target_file)) {
                $unique_name = time() . '_' . $safe_file_name;
                $target_file = $target_dir . $unique_name;
            }

            // Lakukan proses upload file baru
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $gambar_path_for_db = $target_file;

                // Opsional: Hapus gambar lama di server
                if ($current_gambar_path && file_exists($current_gambar_path)) {
                    // Pastikan file lama adalah file yang valid dan bukan folder
                    @unlink($current_gambar_path);
                }
            } else {
                $upload_ok = false;
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupload gambar baru. Perubahan DB dibatalkan.</div>";
            }
        }

        // Lakukan update DB hanya jika tidak ada error upload fatal
        if ($upload_ok) {
            try {
                $sql = "UPDATE berita SET 
                            judul = :judul, 
                            informasi = :informasi, 
                            tanggal = :tanggal, 
                            gambar = :gambar,
                            status = 'pending' 
                        WHERE id_berita = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':judul' => $judul,
                    ':informasi' => $informasi,
                    ':tanggal' => $tanggal,
                    ':gambar' => $gambar_path_for_db, // Path baru atau lama
                    ':id' => $id_berita
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Berita ID {$id_berita} berhasil diupdate!</div>";
            } catch (Exception $e) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupdate berita: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }

    // --- VERIFICATION (Approve/Reject Berita) ---
    if ($action === 'verify_news') {
        $id_berita = (int)$_POST['id_berita'];
        $status = $_POST['status']; // 'approved' or 'rejected'

        if (in_array($status, ['approved', 'rejected'])) {
            try {
                $sql = "UPDATE berita SET status = :status WHERE id_berita = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':status' => $status,
                    ':id' => $id_berita
                ]);

                $status_text = $status === 'approved' ? 'disetujui' : 'ditolak';
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Berita ID {$id_berita} berhasil {$status_text}!</div>";
            } catch (Exception $e) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal memperbarui status berita: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}

// --- DELETE (Hapus Berita - Menggunakan GET request) ---
if ($pdo && $active_page === 'berita' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_berita = (int)$_GET['id'];
    try {
        // Ambil path gambar sebelum dihapus (Opsional: tambahkan logika penghapusan file di sini jika ada)
        // ...

        $sql = "DELETE FROM berita WHERE id_berita = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_berita]);
        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Berita ID {$id_berita} berhasil dihapus!</div>";
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menghapus berita: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    // Redirect untuk menghilangkan parameter GET dari URL
    header("Location: admin-dashboard.php?page=berita");
    exit;
}

// --- START: Penanganan Operasi CRUD Fasilitas (Hanya jika koneksi berhasil) ---
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $active_page === 'fasilitas') {
    $action = $_POST['action'] ?? '';

    // --- CREATE (Tambah Fasilitas Baru) ---
    if ($action === 'add_fasilitas') {
        $nama_fasilitas = trim($_POST['nama_fasilitas']);
        $deskripsi = trim($_POST['deskripsi']);

        $upload_ok = true;
        $foto_path_for_db = '';
        $upload_message = '';
        $target_dir = '../assets/img/fasilitas/';

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {

            // 1. Tentukan direktori dan nama file
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_name = basename($_FILES['foto']['name']);
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $unique_name = 'fasilitas_' . time() . '_' . $safe_file_name;
            $target_file = $target_dir . $unique_name;
            $foto_path_for_db = $target_file;

            // 2. Lakukan proses upload
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $upload_ok = false;
                $upload_message = "Gagal mengupload foto. Pastikan folder '{$target_dir}' memiliki izin tulis (0777).";
            }
        } else if ($_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            $upload_ok = false;
            $upload_message = "Harap unggah foto untuk fasilitas ini.";
        } else {
            $upload_ok = false;
            $upload_message = "Terjadi error saat upload file. Kode error: " . $_FILES['foto']['error'];
        }

        // 3. Simpan ke database jika upload berhasil
        if ($upload_ok) {
            try {
                // Menggunakan INSERT INTO eksplisit (mirip Berita)
                $sql = "INSERT INTO fasilitas (nama_fasilitas, deskripsi, foto, created_by) 
                        VALUES (:nama, :deskripsi, :foto, :created_by)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama' => $nama_fasilitas,
                    ':deskripsi' => $deskripsi,
                    ':foto' => $foto_path_for_db,
                    ':created_by' => $admin_user_id
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Fasilitas baru berhasil ditambahkan!</div>";
            } catch (Exception $e) {
                // Jika gagal simpan DB, hapus file yang sudah terupload (optional cleanup)
                if (file_exists($foto_path_for_db)) {
                    @unlink($foto_path_for_db);
                }
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan fasilitas (DB Error): " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan fasilitas (Upload Error): {$upload_message}</div>";
        }
    }

    // --- UPDATE (Edit Fasilitas) ---
    if ($action === 'edit_fasilitas') {
        $id_fasilitas = (int)$_POST['id_fasilitas'];
        $nama_fasilitas = trim($_POST['nama_fasilitas']);
        $deskripsi = trim($_POST['deskripsi']);

        $new_foto_name = $_FILES['foto']['name'] ?? '';
        $current_foto_path = $_POST['current_foto']; // Path foto lama
        $foto_path_for_db = $current_foto_path;      // Default: gunakan foto lama
        $upload_ok = true;
        $target_dir = '../assets/img/fasilitas/';

        // Cek apakah ada file baru yang diupload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK && !empty($new_foto_name)) {

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_name = basename($_FILES['foto']['name']);
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $unique_name = 'fasilitas_' . time() . '_' . $safe_file_name;
            $target_file = $target_dir . $unique_name;

            // Lakukan proses upload file baru
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $foto_path_for_db = $target_file;

                // Opsional: Hapus foto lama di server
                if ($current_foto_path && file_exists($current_foto_path)) {
                    // Pastikan file lama adalah file yang valid dan bukan folder
                    @unlink($current_foto_path);
                }
            } else {
                $upload_ok = false;
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupload foto baru. Perubahan DB dibatalkan.</div>";
            }
        }

        // Lakukan update DB hanya jika tidak ada error upload fatal
        if ($upload_ok) {
            try {
                $sql = "UPDATE fasilitas SET 
                            nama_fasilitas = :nama, 
                            deskripsi = :deskripsi, 
                            foto = :foto
                        WHERE id_fasilitas = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama' => $nama_fasilitas,
                    ':deskripsi' => $deskripsi,
                    ':foto' => $foto_path_for_db, // Path baru atau lama
                    ':id' => $id_fasilitas
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Fasilitas ID {$id_fasilitas} berhasil diupdate!</div>";
            } catch (Exception $e) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupdate fasilitas: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}

// --- DELETE (Hapus Fasilitas - Menggunakan GET request) ---
if ($pdo && $active_page === 'fasilitas' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_fasilitas = (int)$_GET['id'];

    // 1. Ambil path foto untuk dihapus dari server
    $foto_to_delete = '';
    try {
        $sql_select = "SELECT foto FROM fasilitas WHERE id_fasilitas = :id";
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->execute([':id' => $id_fasilitas]);
        $result = $stmt_select->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $foto_to_delete = $result['foto'];
        }

        // 2. Hapus dari database
        $sql = "DELETE FROM fasilitas WHERE id_fasilitas = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_fasilitas]);

        // 3. Hapus foto dari server (jika ada)
        if ($foto_to_delete && file_exists($foto_to_delete)) {
            @unlink($foto_to_delete);
        }

        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Fasilitas ID {$id_fasilitas} berhasil dihapus!</div>";
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menghapus fasilitas: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    // Redirect untuk menghilangkan parameter GET dari URL
    header("Location: admin-dashboard.php?page=fasilitas");
    exit;
}
// --- END: Penanganan Operasi CRUD Fasilitas ---

// --- START: Penanganan Operasi CRUD Galeri (Hanya jika koneksi berhasil) ---
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $active_page === 'galeri') {
    $action = $_POST['action'] ?? '';
    $target_dir = '../assets/img/galeri/'; // Direktori Galeri

    // Pastikan direktori ada
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal membuat folder upload: {$target_dir}. Pastikan izin tulis sudah diatur.</div>";
            $pdo = null; // Hentikan operasi DB jika gagal buat folder
        }
    }

    // --- CREATE (Tambah Foto Galeri Baru) ---
    if ($action === 'add_galeri' && $pdo) {
        $nama_foto = trim($_POST['nama_foto']);
        $deskripsi = trim($_POST['deskripsi']);

        $upload_ok = true;
        $file_foto_path_for_db = '';
        $upload_message = '';

        if (isset($_FILES['file_foto']) && $_FILES['file_foto']['error'] == UPLOAD_ERR_OK) {

            // 1. Tentukan nama file
            $file_name = basename($_FILES['file_foto']['name']);
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $unique_name = 'galeri_' . time() . '_' . $safe_file_name;
            $target_file = $target_dir . $unique_name;
            $file_foto_path_for_db = $target_file;

            // 2. Lakukan proses upload
            if (!move_uploaded_file($_FILES['file_foto']['tmp_name'], $target_file)) {
                $upload_ok = false;
                $upload_message = "Gagal mengupload foto. Pastikan folder '{$target_dir}' memiliki izin tulis (0777).";
            }
        } else if ($_FILES['file_foto']['error'] === UPLOAD_ERR_NO_FILE) {
            $upload_ok = false;
            $upload_message = "Harap unggah file foto untuk galeri ini.";
        } else {
            $upload_ok = false;
            $upload_message = "Terjadi error saat upload file. Kode error: " . $_FILES['file_foto']['error'];
        }

        // 3. Simpan ke database jika upload berhasil
        if ($upload_ok) {
            try {
                $sql = "INSERT INTO galeri (nama_foto, deskripsi, file_foto, id_anggota, updated_by) 
                        VALUES (:nama, :deskripsi, :file, :id_anggota, :updated_by)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama' => $nama_foto,
                    ':deskripsi' => $deskripsi,
                    ':file' => $file_foto_path_for_db,
                    ':id_anggota' => $admin_user_id,
                    ':updated_by' => $admin_user_id
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Foto Galeri baru berhasil ditambahkan!</div>";
            } catch (Exception $e) {
                // Jika gagal simpan DB, hapus file yang sudah terupload (optional cleanup)
                if (file_exists($file_foto_path_for_db)) {
                    @unlink($file_foto_path_for_db);
                }
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan foto galeri (DB Error): " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan foto galeri (Upload Error): {$upload_message}</div>";
        }
    }

    // --- UPDATE (Edit Foto Galeri) ---
    if ($action === 'edit_galeri' && $pdo) {
        $id_foto = (int)$_POST['id_foto'];
        $nama_foto = trim($_POST['nama_foto']);
        $deskripsi = trim($_POST['deskripsi']);

        $new_file_name = $_FILES['file_foto']['name'] ?? '';
        $current_file_path = $_POST['current_file_foto']; // Path file foto lama
        $file_foto_path_for_db = $current_file_path;      // Default: gunakan foto lama
        $upload_ok = true;

        // Cek apakah ada file baru yang diupload
        if (isset($_FILES['file_foto']) && $_FILES['file_foto']['error'] == UPLOAD_ERR_OK && !empty($new_file_name)) {
            
            $file_name = basename($_FILES['file_foto']['name']);
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $unique_name = 'galeri_' . time() . '_' . $safe_file_name;
            $target_file = $target_dir . $unique_name;

            // Lakukan proses upload file baru
            if (move_uploaded_file($_FILES['file_foto']['tmp_name'], $target_file)) {
                $file_foto_path_for_db = $target_file;

                // Opsional: Hapus file lama di server
                if ($current_file_path && file_exists($current_file_path)) {
                    @unlink($current_file_path);
                }
            } else {
                $upload_ok = false;
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupload file baru. Perubahan DB dibatalkan.</div>";
            }
        }

        // Lakukan update DB hanya jika tidak ada error upload fatal
        if ($upload_ok) {
            try {
                $sql = "UPDATE galeri SET 
                            nama_foto = :nama, 
                            deskripsi = :deskripsi, 
                            file_foto = :file,
                            updated_by = :updated_by
                        WHERE id_foto = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama' => $nama_foto,
                    ':deskripsi' => $deskripsi,
                    ':file' => $file_foto_path_for_db, // Path baru atau lama
                    ':updated_by' => $admin_user_id,
                    ':id' => $id_foto
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Foto Galeri ID {$id_foto} berhasil diupdate!</div>";
            } catch (Exception $e) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupdate foto galeri: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}

// --- DELETE (Hapus Galeri - Menggunakan GET request) ---
if ($pdo && $active_page === 'galeri' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_foto = (int)$_GET['id'];

    // 1. Ambil path foto untuk dihapus dari server
    $file_foto_to_delete = '';
    try {
        $sql_select = "SELECT file_foto FROM galeri WHERE id_foto = :id";
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->execute([':id' => $id_foto]);
        $result = $stmt_select->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $file_foto_to_delete = $result['file_foto'];
        }

        // 2. Hapus dari database
        $sql = "DELETE FROM galeri WHERE id_foto = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_foto]);

        // 3. Hapus foto dari server (jika ada)
        if ($file_foto_to_delete && file_exists($file_foto_to_delete)) {
            @unlink($file_foto_to_delete);
        }

        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Foto Galeri ID {$id_foto} berhasil dihapus!</div>";
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menghapus foto galeri: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    // Redirect untuk menghilangkan parameter GET dari URL
    header("Location: admin-dashboard.php?page=galeri");
    exit;
}
// --- END: Penanganan Operasi CRUD Galeri (DELETE) ---

// --- START: Penanganan Operasi CRUD Publikasi (Hanya jika koneksi berhasil) ---
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $active_page === 'publikasi') {
    $action = $_POST['action'] ?? '';
    $target_dir = '../assets/files/publikasi/';

    // Pastikan direktori ada
    if (!is_dir($target_dir)) {
        // Coba buat folder jika belum ada
        if (!mkdir($target_dir, 0777, true)) {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal membuat folder upload: {$target_dir}. Pastikan izin tulis sudah diatur.</div>";
            $pdo = null; // Hentikan operasi DB jika gagal buat folder
        }
    }

    // --- CREATE (Tambah Publikasi Baru) ---
    if ($action === 'add_publikasi' && $pdo) {
        $judul = trim($_POST['judul']);
        $penulis = trim($_POST['penulis']);
        $tanggal_terbit = trim($_POST['tanggal_terbit']);
        $deskripsi = trim($_POST['deskripsi']);

        $upload_ok = true;
        $file_path_for_db = '';
        $upload_message = '';

        if (isset($_FILES['file_publikasi']) && $_FILES['file_publikasi']['error'] == UPLOAD_ERR_OK) {

            // 1. Tentukan nama file
            $file_name = basename($_FILES['file_publikasi']['name']);
            // Untuk keamanan dan unik, tambahkan prefix/timestamp
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $unique_name = 'publikasi_' . time() . '_' . $safe_file_name;
            $target_file = $target_dir . $unique_name;
            $file_path_for_db = $target_file;

            // 2. Lakukan proses upload
            if (!move_uploaded_file($_FILES['file_publikasi']['tmp_name'], $target_file)) {
                $upload_ok = false;
                $upload_message = "Gagal mengupload file. Pastikan folder '{$target_dir}' memiliki izin tulis (0777).";
            }
        } else if ($_FILES['file_publikasi']['error'] === UPLOAD_ERR_NO_FILE) {
            $upload_ok = false;
            $upload_message = "Harap unggah file publikasi (PDF, dll).";
        } else {
            $upload_ok = false;
            $upload_message = "Terjadi error saat upload file. Kode error: " . $_FILES['file_publikasi']['error'];
        }

        // 3. Simpan ke database jika upload berhasil
        if ($upload_ok) {
            try {
                // Menggunakan INSERT INTO eksplisit (sesuai dengan skema tabel publikasi)
                $sql = "INSERT INTO publikasi (id_anggota, judul, penulis, tanggal_terbit, file_publikasi, deskripsi) 
                        VALUES (:id_anggota, :judul, :penulis, :tanggal_terbit, :file, :deskripsi)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':id_anggota' => $admin_user_id, // Gunakan ID user admin yang login
                    ':judul' => $judul,
                    ':penulis' => $penulis,
                    ':tanggal_terbit' => $tanggal_terbit,
                    ':file' => $file_path_for_db,
                    ':deskripsi' => $deskripsi
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Publikasi baru berhasil ditambahkan!</div>";
            } catch (Exception $e) {
                // Jika gagal simpan DB, hapus file yang sudah terupload (optional cleanup)
                if (file_exists($file_path_for_db)) {
                    @unlink($file_path_for_db);
                }
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan publikasi (DB Error): " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan publikasi (Upload Error): {$upload_message}</div>";
        }
    }

    // --- UPDATE (Edit Publikasi) ---
    if ($action === 'edit_publikasi' && $pdo) {
        $id_publikasi = (int)$_POST['id_publikasi'];
        $judul = trim($_POST['judul']);
        $penulis = trim($_POST['penulis']);
        $tanggal_terbit = trim($_POST['tanggal_terbit']);
        $deskripsi = trim($_POST['deskripsi']);

        $new_file_name = $_FILES['file_publikasi']['name'] ?? '';
        $current_file_path = $_POST['current_file_publikasi']; // Path file lama
        $file_path_for_db = $current_file_path;              // Default: gunakan file lama
        $upload_ok = true;

        // Cek apakah ada file baru yang diupload
        if (isset($_FILES['file_publikasi']) && $_FILES['file_publikasi']['error'] == UPLOAD_ERR_OK && !empty($new_file_name)) {
            
            $file_name = basename($_FILES['file_publikasi']['name']);
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $unique_name = 'publikasi_' . time() . '_' . $safe_file_name;
            $target_file = $target_dir . $unique_name;

            // Lakukan proses upload file baru
            if (move_uploaded_file($_FILES['file_publikasi']['tmp_name'], $target_file)) {
                $file_path_for_db = $target_file;

                // Opsional: Hapus file lama di server
                if ($current_file_path && file_exists($current_file_path)) {
                    @unlink($current_file_path);
                }
            } else {
                $upload_ok = false;
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupload file baru. Perubahan DB dibatalkan.</div>";
            }
        }

        // Lakukan update DB hanya jika tidak ada error upload fatal
        if ($upload_ok) {
            try {
                $sql = "UPDATE publikasi SET 
                            judul = :judul, 
                            penulis = :penulis, 
                            tanggal_terbit = :tanggal_terbit, 
                            file_publikasi = :file,
                            deskripsi = :deskripsi,
                            updated_at = NOW()
                        WHERE id_publikasi = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':judul' => $judul,
                    ':penulis' => $penulis,
                    ':tanggal_terbit' => $tanggal_terbit,
                    ':file' => $file_path_for_db, // Path baru atau lama
                    ':deskripsi' => $deskripsi,
                    ':id' => $id_publikasi
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Publikasi ID {$id_publikasi} berhasil diupdate!</div>";
            } catch (Exception $e) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupdate publikasi: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}

// --- DELETE (Hapus Publikasi - Menggunakan GET request) ---
if ($pdo && $active_page === 'publikasi' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_publikasi = (int)$_GET['id'];

    // 1. Ambil path file untuk dihapus dari server
    $file_to_delete = '';
    try {
        $sql_select = "SELECT file_publikasi FROM publikasi WHERE id_publikasi = :id";
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->execute([':id' => $id_publikasi]);
        $result = $stmt_select->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $file_to_delete = $result['file_publikasi'];
        }

        // 2. Hapus dari database
        $sql = "DELETE FROM publikasi WHERE id_publikasi = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_publikasi]);

        // 3. Hapus file dari server (jika ada)
        if ($file_to_delete && file_exists($file_to_delete)) {
            @unlink($file_to_delete);
        }

        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Publikasi ID {$id_publikasi} berhasil dihapus!</div>";
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menghapus publikasi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    // Redirect untuk menghilangkan parameter GET dari URL
    header("Location: admin-dashboard.php?page=publikasi");
    exit;
}
// --- END: Penanganan Operasi CRUD Publikasi ---


// --- START: Data Fetching (READ) ---
// 4. Data Dashboard (Ringkasan)
$total_news = 0;
$total_facilities = 0;
$total_galleries = 0;
$total_publikasi = 0; // Tambahkan hitungan untuk publikasi
if ($pdo) {
    try {
        $total_news = $pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
        $total_facilities = $pdo->query("SELECT COUNT(*) FROM fasilitas")->fetchColumn();
        $total_galleries = $pdo->query("SELECT COUNT(*) FROM galeri")->fetchColumn();
        $total_publikasi = $pdo->query("SELECT COUNT(*) FROM publikasi")->fetchColumn(); // Hitung total publikasi
    } catch (Exception $e) {
        // Jika gagal koneksi/query, total tetap 0.
        // Pesan error akan ditampilkan di atas jika $db_error=true atau $message sudah terisi.
    }
}


// --- START: Data Berita ---
$news_data = [];
if ($active_page === 'berita' && $pdo) {
    try {
        // READ: Mengambil semua data berita, join ke tabel anggota/user
        $sql = "SELECT b.id_berita, b.judul, b.gambar, b.informasi, b.tanggal, b.status, a.nama_gelar AS author_name 
                FROM berita b 
                LEFT JOIN anggota a ON b.author = a.id_anggota 
                ORDER BY b.tanggal DESC";
        $stmt = $pdo->query($sql);
        $news_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data berita: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
// --- END: Data Berita ---

// --- START: Data Fasilitas ---
$fasilitas_data = [];
if ($active_page === 'fasilitas' && $pdo) {
    try {
        // READ: Mengambil semua data fasilitas, join ke tabel anggota/user
        $sql = "SELECT f.id_fasilitas, f.nama_fasilitas, f.deskripsi, f.foto, a.nama_gelar AS created_by_name
                FROM fasilitas f
                LEFT JOIN anggota a ON f.created_by = a.id_anggota
                ORDER BY f.id_fasilitas DESC";
        $stmt = $pdo->query($sql);
        $fasilitas_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data fasilitas: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
// --- END: Data Fasilitas ---

// --- START: Data Galeri (READ - Diambil dari DB jika koneksi berhasil) ---
$galeri_data = [];
if ($active_page === 'galeri' && $pdo) {
    try {
        // READ: Mengambil semua data galeri dari database
        // Join ke tabel anggota/user untuk mendapatkan nama uploader (id_anggota)
        $sql = "SELECT g.id_foto, g.nama_foto, g.deskripsi, g.file_foto, g.id_anggota, a.nama_gelar AS anggota_name 
                FROM galeri g 
                LEFT JOIN anggota a ON g.id_anggota = a.id_anggota 
                ORDER BY g.id_foto DESC";
        $stmt = $pdo->query($sql);
        $galeri_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data galeri: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
// --- END: Data Galeri ---

// --- START: Data Publikasi (READ - Diambil dari DB jika koneksi berhasil) ---
$publikasi_data = [];
if ($active_page === 'publikasi' && $pdo) {
    try {
        // READ: Mengambil semua data publikasi dari database
        // Join ke tabel member untuk mendapatkan nama uploader (id_anggota/id_member)
        $sql = "SELECT p.id_publikasi, p.judul, p.penulis, p.tanggal_terbit, p.file_publikasi, p.deskripsi, p.created_at, p.updated_at, m.nama AS nama_member
                FROM publikasi p 
                LEFT JOIN member m ON p.id_anggota = m.id_member 
                ORDER BY p.id_publikasi DESC";
        $stmt = $pdo->query($sql);
        $publikasi_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data publikasi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
// --- END: Data Publikasi ---

// --- Bagian HTML/Presentation ---
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Custom color variables (sesuaikan dengan tema yang sudah ada) */
        :root {
            --color-primary: #4f46e5; /* Indigo-600 */
            --color-primary-dark: #4338ca; /* Indigo-700 */
            --color-secondary: #059669; /* Emerald-600 */
        }

        .bg-primary { background-color: var(--color-primary); }
        .hover\:bg-primary-dark:hover { background-color: var(--color-primary-dark); }
        .border-primary { border-color: var(--color-primary); }
        .focus\:ring-primary:focus { --tw-ring-color: var(--color-primary); }
        .text-primary { color: var(--color-primary); }
        .border-secondary { border-color: var(--color-secondary); }
        .text-secondary { color: var(--color-secondary); }
        
        /* Utility untuk truncating text di table */
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Z-index untuk Modal (agar modal selalu di atas) */
        .z-\[100\] {
            z-index: 100;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="fixed top-0 left-0 h-full w-64 bg-white shadow-xl p-4 flex flex-col transition-transform duration-300 ease-in-out">
        
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-gray-800">Admin Panel</h2>
            <p class="text-sm text-gray-500">Selamat datang, <?php echo htmlspecialchars($username); ?></p>
        </div>

        <nav class="flex-grow">
            <ul>
                <a href="admin-dashboard.php?page=dashboard" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'dashboard' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                    <i class="fas fa-home w-5 h-5 mr-3 <?php echo ($active_page === 'dashboard' ? 'text-primary' : ''); ?>"></i> Dashboard
                </a>
                
                <h3 class="text-xs font-semibold uppercase text-gray-400 mt-4 mb-2 px-3">Konten Website</h3>

                <a href="admin-dashboard.php?page=berita" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'berita' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                    <i class="fas fa-newspaper w-5 h-5 mr-3 <?php echo ($active_page === 'berita' ? 'text-primary' : ''); ?>"></i> Kelola Berita
                </a>

                <a href="admin-dashboard.php?page=fasilitas" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'fasilitas' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                    <i class="fas fa-tools w-5 h-5 mr-3 <?php echo ($active_page === 'fasilitas' ? 'text-primary' : ''); ?>"></i> Kelola Fasilitas
                </a>

                <a href="admin-dashboard.php?page=galeri" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'galeri' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                    <i class="fas fa-image w-5 h-5 mr-3 <?php echo ($active_page === 'galeri' ? 'text-primary' : ''); ?>"></i> Kelola Galeri
                </a>
                
                <a href="admin-dashboard.php?page=publikasi" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'publikasi' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                    <i class="fas fa-book-open w-5 h-5 mr-3 <?php echo ($active_page === 'publikasi' ? 'text-primary' : ''); ?>"></i> Kelola Publikasi
                </a>
                
                <h3 class="text-xs font-semibold uppercase text-gray-400 mt-4 mb-2 px-3">Pengaturan</h3>
                
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors duration-200">
                    <i class="fas fa-cog w-5 h-5 mr-3"></i> Pengaturan Umum
                </a>
            </ul>
        </nav>

        <div class="mt-auto pt-4 border-t border-gray-200">
            <a href="logout.php" class="flex items-center p-3 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors duration-200">
                <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i> Keluar
            </a>
        </div>
    </div>

    <div class="ml-64 p-8 transition-all duration-300 ease-in-out">

        <?php if ($active_page === 'dashboard'): ?>
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Ringkasan</h1>
        <?php echo $message; // Tampilkan notifikasi koneksi DB ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            
            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-primary">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase">Total Berita</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_news; ?></p>
                    </div>
                    <i class="fas fa-newspaper text-4xl text-primary opacity-30"></i>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-secondary">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase">Total Fasilitas</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_facilities; ?></p>
                    </div>
                    <i class="fas fa-tools text-4xl text-secondary opacity-30"></i>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase">Total Galeri Foto</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_galleries; ?></p>
                    </div>
                    <i class="fas fa-image text-4xl text-purple-500 opacity-30"></i>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-teal-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase">Total Publikasi</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_publikasi; ?></p>
                    </div>
                    <i class="fas fa-book-open text-4xl text-teal-500 opacity-30"></i>
                </div>
            </div>
            
        </div>
        
        <?php elseif ($active_page === 'berita'): ?>
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Berita</h1>
        <?php echo $message; ?>

        <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
            <button onclick="openAddNewsModal()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition duration-150 flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Berita Baru
            </button>
        </div>
        
        <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Isi (Snippet)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($news_data)): ?>
                    <?php foreach ($news_data as $news): ?>
                    <tr data-id="<?php echo $news['id_berita']; ?>" 
                        data-judul="<?php echo htmlspecialchars($news['judul']); ?>" 
                        data-informasi="<?php echo htmlspecialchars($news['informasi']); ?>"
                        data-tanggal="<?php echo htmlspecialchars($news['tanggal']); ?>"
                        data-gambar="<?php echo htmlspecialchars($news['gambar']); ?>">
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $news['id_berita']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded object-cover" src="<?php echo htmlspecialchars($news['gambar']); ?>" alt="<?php echo htmlspecialchars($news['judul']); ?>">
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-xs"><?php echo htmlspecialchars($news['judul']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs overflow-hidden text-ellipsis line-clamp-2" style="max-width: 300px;">
                            <?php echo htmlspecialchars(substr($news['informasi'], 0, 100)) . (strlen($news['informasi']) > 100 ? '...' : ''); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(date('d M Y', strtotime($news['tanggal']))); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($news['author_name'] ?? 'Admin'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php if ($news['status'] === 'approved'): ?>bg-green-100 text-green-800
                                <?php elseif ($news['status'] === 'rejected'): ?>bg-red-100 text-red-800
                                <?php else: ?>bg-yellow-100 text-yellow-800
                                <?php endif; ?>">
                                <?php echo ucfirst($news['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                            <button onclick="openEditNewsModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <?php if ($news['status'] === 'pending'): ?>
                            <form action="admin-dashboard.php?page=berita" method="POST" class="inline-block" onsubmit="return confirm('Setujui berita ini?');">
                                <input type="hidden" name="action" value="verify_news">
                                <input type="hidden" name="id_berita" value="<?php echo $news['id_berita']; ?>">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="text-green-600 hover:text-green-900 p-2 rounded-md hover:bg-gray-100">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                            </form>
                            <form action="admin-dashboard.php?page=berita" method="POST" class="inline-block" onsubmit="return confirm('Tolak berita ini?');">
                                <input type="hidden" name="action" value="verify_news">
                                <input type="hidden" name="id_berita" value="<?php echo $news['id_berita']; ?>">
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 p-2 rounded-md hover:bg-gray-100">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </form>
                            <?php endif; ?>

                            <a href="admin-dashboard.php?page=berita&action=delete&id=<?php echo $news['id_berita']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini? File juga akan terhapus dari server.')" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            Belum ada data berita.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php elseif ($active_page === 'fasilitas'): ?>
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Fasilitas</h1>
        <?php echo $message; ?>

        <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
            <button onclick="openAddFasilitasModal()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition duration-150 flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Fasilitas Baru
            </button>
        </div>

        <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Fasilitas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diunggah Oleh</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($fasilitas_data)): ?>
                    <?php foreach ($fasilitas_data as $fasilitas): ?>
                    <tr data-id="<?php echo $fasilitas['id_fasilitas']; ?>" 
                        data-nama="<?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?>" 
                        data-deskripsi="<?php echo htmlspecialchars($fasilitas['deskripsi']); ?>"
                        data-foto="<?php echo htmlspecialchars($fasilitas['foto']); ?>">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $fasilitas['id_fasilitas']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img src="<?php echo htmlspecialchars($fasilitas['foto']); ?>" alt="<?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?>" class="h-10 w-10 rounded object-cover">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars(substr($fasilitas['deskripsi'], 0, 50)) . (strlen($fasilitas['deskripsi']) > 50 ? '...' : ''); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($fasilitas['created_by_name'] ?? 'Admin'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                            <button onclick="openEditFasilitasModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="admin-dashboard.php?page=fasilitas&action=delete&id=<?php echo $fasilitas['id_fasilitas']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini? Foto juga akan terhapus dari server.')" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            Belum ada data fasilitas.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php elseif ($active_page === 'galeri'): ?>
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Galeri Foto</h1>
        <?php echo $message; ?>

        <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
            <button onclick="openAddGaleriModal()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition duration-150 flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Foto Baru
            </button>
        </div>

        <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Foto/Judul</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploader</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($galeri_data)): ?>
                    <?php foreach ($galeri_data as $galeri): ?>
                    <tr data-id="<?php echo $galeri['id_foto']; ?>" 
                        data-nama="<?php echo htmlspecialchars($galeri['nama_foto']); ?>" 
                        data-deskripsi="<?php echo htmlspecialchars($galeri['deskripsi']); ?>"
                        data-file_foto="<?php echo htmlspecialchars($galeri['file_foto']); ?>">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $galeri['id_foto']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img src="<?php echo htmlspecialchars($galeri['file_foto']); ?>" alt="<?php echo htmlspecialchars($galeri['nama_foto']); ?>" class="h-10 w-10 rounded object-cover">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($galeri['nama_foto']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars(substr($galeri['deskripsi'], 0, 50)) . (strlen($galeri['deskripsi']) > 50 ? '...' : ''); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($galeri['anggota_name'] ?? 'Admin'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                            <button onclick="openEditGaleriModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="admin-dashboard.php?page=galeri&action=delete&id=<?php echo $galeri['id_foto']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus foto galeri ini? File juga akan terhapus dari server.')" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            Belum ada data galeri.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php elseif ($active_page === 'publikasi'): ?>
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Publikasi</h1>
        <?php echo $message; ?>

        <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
            <button onclick="openAddPublikasiModal()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition duration-150 flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Publikasi Baru
            </button>
        </div>

        <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Terbit</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploader</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($publikasi_data)): ?>
                    <?php foreach ($publikasi_data as $publikasi): ?>
                    <tr data-id="<?php echo $publikasi['id_publikasi']; ?>" 
                        data-judul="<?php echo htmlspecialchars($publikasi['judul']); ?>"
                        data-penulis="<?php echo htmlspecialchars($publikasi['penulis']); ?>"
                        data-tanggal_terbit="<?php echo htmlspecialchars($publikasi['tanggal_terbit']); ?>"
                        data-deskripsi="<?php echo htmlspecialchars($publikasi['deskripsi']); ?>"
                        data-file_publikasi="<?php echo htmlspecialchars($publikasi['file_publikasi']); ?>">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $publikasi['id_publikasi']; ?></td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-xs"><?php echo htmlspecialchars($publikasi['judul']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($publikasi['penulis']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(date('d M Y', strtotime($publikasi['tanggal_terbit']))); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php if (!empty($publikasi['file_publikasi'])): ?>
                                <a href="<?php echo htmlspecialchars($publikasi['file_publikasi']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center">
                                    <i class="fas fa-file-pdf mr-1"></i> Lihat File
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($publikasi['nama_member'] ?? 'Admin'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                            <button onclick="openEditPublikasiModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="admin-dashboard.php?page=publikasi&action=delete&id=<?php echo $publikasi['id_publikasi']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus publikasi ini? File juga akan terhapus dari server.')" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            Belum ada data publikasi.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php endif; ?>

    </div>

    <div id="add-news-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Berita Baru</h3>
                <form id="add-news-form" action="admin-dashboard.php?page=berita" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                    <input type="hidden" name="action" value="add_news">
                    
                    <div class="mb-4">
                        <label for="judul" class="block text-sm font-medium text-gray-700">Judul Berita</label>
                        <input type="text" id="judul" name="judul" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal Publikasi</label>
                        <input type="date" id="tanggal" name="tanggal" required value="<?php echo date('Y-m-d'); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="informasi" class="block text-sm font-medium text-gray-700">Isi Berita Lengkap</label>
                        <textarea id="informasi" name="informasi" rows="6" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="gambar" class="block text-sm font-medium text-gray-700">Upload Gambar Utama</label>
                        <input type="file" id="gambar" name="gambar" accept="image/*" required class="mt-1 block w-full">
                        <p class="mt-1 text-xs text-gray-500">Max size 2MB. Format: JPG, PNG, GIF.</p>
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" onclick="closeAddNewsModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300"> Batal </button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-base font-medium rounded-md shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"> Tambah Berita </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="edit-news-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Berita</h3>
                <form id="edit-news-form" action="admin-dashboard.php?page=berita" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                    <input type="hidden" name="action" value="edit_news">
                    <input type="hidden" name="id_berita" id="edit_id_berita">
                    <input type="hidden" name="current_gambar" id="edit_current_gambar">
                    
                    <div class="mb-4">
                        <label for="edit_judul" class="block text-sm font-medium text-gray-700">Judul Berita</label>
                        <input type="text" id="edit_judul" name="judul" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="edit_tanggal" class="block text-sm font-medium text-gray-700">Tanggal Publikasi</label>
                        <input type="date" id="edit_tanggal" name="tanggal" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="edit_informasi" class="block text-sm font-medium text-gray-700">Isi Berita Lengkap</label>
                        <textarea id="edit_informasi" name="informasi" rows="6" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="edit_gambar" class="block text-sm font-medium text-gray-700">Ganti Gambar Utama</label>
                        <p class="text-sm text-gray-500 mb-1">Gambar saat ini: <span id="current-gambar-name" class="font-medium text-primary"></span></p>
                        <input type="file" id="edit_gambar" name="gambar" accept="image/*" class="mt-1 block w-full">
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengganti gambar.</p>
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" onclick="closeEditNewsModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300"> Batal </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"> Simpan Perubahan </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="add-fasilitas-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Fasilitas Baru</h3>
                <form id="add-fasilitas-form" action="admin-dashboard.php?page=fasilitas" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                    <input type="hidden" name="action" value="add_fasilitas">
                    <div class="mb-4">
                        <label for="nama_fasilitas" class="block text-sm font-medium text-gray-700">Nama Fasilitas</label>
                        <input type="text" id="nama_fasilitas" name="nama_fasilitas" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="deskripsi_fasilitas" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea id="deskripsi_fasilitas" name="deskripsi" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="foto" class="block text-sm font-medium text-gray-700">Upload Foto</label>
                        <input type="file" id="foto" name="foto" accept="image/*" required class="mt-1 block w-full">
                        <p class="mt-1 text-xs text-gray-500">Max size 2MB. Format: JPG, PNG, GIF.</p>
                    </div>
                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" onclick="closeAddFasilitasModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300"> Batal </button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-base font-medium rounded-md shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"> Tambah Fasilitas </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="edit-fasilitas-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Fasilitas</h3>
                <form id="edit-fasilitas-form" action="admin-dashboard.php?page=fasilitas" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                    <input type="hidden" name="action" value="edit_fasilitas">
                    <input type="hidden" name="id_fasilitas" id="edit_id_fasilitas">
                    <input type="hidden" name="current_foto" id="edit_current_foto">

                    <div class="mb-4">
                        <label for="edit_nama_fasilitas" class="block text-sm font-medium text-gray-700">Nama Fasilitas</label>
                        <input type="text" id="edit_nama_fasilitas" name="nama_fasilitas" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="edit_deskripsi_fasilitas" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea id="edit_deskripsi_fasilitas" name="deskripsi" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="edit_foto" class="block text-sm font-medium text-gray-700">Ganti Foto</label>
                        <p class="text-sm text-gray-500 mb-1">Foto saat ini: <span id="current-foto-name" class="font-medium text-primary"></span></p>
                        <input type="file" id="edit_foto" name="foto" accept="image/*" class="mt-1 block w-full">
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengganti foto.</p>
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" onclick="closeEditFasilitasModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300"> Batal </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"> Simpan Perubahan </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="add-galeri-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Foto Galeri Baru</h3>
                <form id="add-galeri-form" action="admin-dashboard.php?page=galeri" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                    <input type="hidden" name="action" value="add_galeri">
                    <div class="mb-4">
                        <label for="nama_foto" class="block text-sm font-medium text-gray-700">Nama Foto / Judul</label>
                        <input type="text" id="nama_foto" name="nama_foto" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="deskripsi_galeri" class="block text-sm font-medium text-gray-700">Deskripsi Singkat</label>
                        <textarea id="deskripsi_galeri" name="deskripsi" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="file_foto" class="block text-sm font-medium text-gray-700">Upload File Foto</label>
                        <input type="file" id="file_foto" name="file_foto" accept="image/*" required class="mt-1 block w-full">
                        <p class="mt-1 text-xs text-gray-500">Max size 2MB. Format: JPG, PNG, GIF.</p>
                    </div>
                    
                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" onclick="closeAddGaleriModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300"> Batal </button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-base font-medium rounded-md shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"> Tambah Foto </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="edit-galeri-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Foto Galeri</h3>
                <form id="edit-galeri-form" action="admin-dashboard.php?page=galeri" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                    <input type="hidden" name="action" value="edit_galeri">
                    <input type="hidden" name="id_foto" id="edit_id_foto">
                    <input type="hidden" name="current_file_foto" id="edit_current_file_foto">

                    <div class="mb-4">
                        <label for="edit_nama_foto" class="block text-sm font-medium text-gray-700">Nama Foto / Judul</label>
                        <input type="text" id="edit_nama_foto" name="nama_foto" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="edit_deskripsi_galeri" class="block text-sm font-medium text-gray-700">Deskripsi Singkat</label>
                        <textarea id="edit_deskripsi_galeri" name="deskripsi" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="edit_file_foto" class="block text-sm font-medium text-gray-700">Ganti File Foto</label>
                        <p class="text-sm text-gray-500 mb-1">File saat ini: <span id="current-file-foto-name" class="font-medium text-primary"></span></p>
                        <input type="file" id="edit_file_foto" name="file_foto" accept="image/*" class="mt-1 block w-full">
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengganti file.</p>
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" onclick="closeEditGaleriModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300"> Batal </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"> Simpan Perubahan </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="add-publikasi-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Publikasi Baru</h3>
                <form id="add-publikasi-form" action="admin-dashboard.php?page=publikasi" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                    <input type="hidden" name="action" value="add_publikasi">
                    
                    <div class="mb-4">
                        <label for="judul_publikasi" class="block text-sm font-medium text-gray-700">Judul Publikasi</label>
                        <input type="text" id="judul_publikasi" name="judul" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="penulis" class="block text-sm font-medium text-gray-700">Penulis (Opsional)</label>
                        <input type="text" id="penulis" name="penulis" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="tanggal_terbit" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                        <input type="date" id="tanggal_terbit" name="tanggal_terbit" required value="<?php echo date('Y-m-d'); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="deskripsi_publikasi" class="block text-sm font-medium text-gray-700">Deskripsi Singkat</label>
                        <textarea id="deskripsi_publikasi" name="deskripsi" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="file_publikasi" class="block text-sm font-medium text-gray-700">Upload File Publikasi (PDF/Dokumen)</label>
                        <input type="file" id="file_publikasi" name="file_publikasi" accept=".pdf,.doc,.docx" required class="mt-1 block w-full">
                        <p class="mt-1 text-xs text-gray-500">Max size 5MB. Format: PDF, DOCX.</p>
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" onclick="closeAddPublikasiModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300"> Batal </button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-base font-medium rounded-md shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"> Tambah Publikasi </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="edit-publikasi-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Publikasi</h3>
                <form id="edit-publikasi-form" action="admin-dashboard.php?page=publikasi" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                    <input type="hidden" name="action" value="edit_publikasi">
                    <input type="hidden" name="id_publikasi" id="edit_id_publikasi">
                    <input type="hidden" name="current_file_publikasi" id="edit_current_file_publikasi">

                    <div class="mb-4">
                        <label for="edit_judul_publikasi" class="block text-sm font-medium text-gray-700">Judul Publikasi</label>
                        <input type="text" id="edit_judul_publikasi" name="judul" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="edit_penulis" class="block text-sm font-medium text-gray-700">Penulis (Opsional)</label>
                        <input type="text" id="edit_penulis" name="penulis" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="edit_tanggal_terbit" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                        <input type="date" id="edit_tanggal_terbit" name="tanggal_terbit" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="edit_deskripsi_publikasi" class="block text-sm font-medium text-gray-700">Deskripsi Singkat</label>
                        <textarea id="edit_deskripsi_publikasi" name="deskripsi" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="edit_file_publikasi" class="block text-sm font-medium text-gray-700">Ganti File Publikasi</label>
                        <p class="text-sm text-gray-500 mb-1">File saat ini: <span id="current-file-publikasi-name" class="font-medium text-primary"></span></p>
                        <input type="file" id="edit_file_publikasi" name="file_publikasi" accept=".pdf,.doc,.docx" class="mt-1 block w-full">
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengganti file.</p>
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" onclick="closeEditPublikasiModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300"> Batal </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"> Simpan Perubahan </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // --- START: Fungsi Modal Berita ---
        function openAddNewsModal() {
            document.getElementById('add-news-modal').classList.remove('hidden');
            document.getElementById('add-news-modal').querySelector('form').reset();
            // Set tanggal default ke hari ini
            document.getElementById('tanggal').valueAsDate = new Date();
        }

        function closeAddNewsModal() {
            document.getElementById('add-news-modal').classList.add('hidden');
            document.getElementById('add-news-modal').querySelector('form').reset();
        }

        function openEditNewsModal(button) {
            const row = button.closest('tr');
            const id = row.getAttribute('data-id');
            const judul = row.getAttribute('data-judul');
            const informasi = row.getAttribute('data-informasi');
            const tanggal = row.getAttribute('data-tanggal');
            const gambar = row.getAttribute('data-gambar');
            // Ambil nama file saja dari path lengkap
            const filename = gambar.substring(gambar.lastIndexOf('/') + 1);

            // Isi form modal
            document.getElementById('edit_id_berita').value = id;
            document.getElementById('edit_judul').value = judul;
            document.getElementById('edit_informasi').value = informasi;
            document.getElementById('edit_tanggal').value = tanggal;
            document.getElementById('edit_current_gambar').value = gambar; // Simpan path gambar saat ini
            document.getElementById('current-gambar-name').textContent = filename;

            // Hapus nilai input file saat edit modal dibuka
            document.getElementById('edit_gambar').value = '';

            // Tampilkan modal
            document.getElementById('edit-news-modal').classList.remove('hidden');
        }

        function closeEditNewsModal() {
            document.getElementById('edit-news-modal').classList.add('hidden');
            document.getElementById('edit-news-modal').querySelector('form').reset();
        }
        // --- END: Fungsi Modal Berita ---
        
        // --- START: Fungsi Modal Fasilitas ---
        function openAddFasilitasModal() {
            document.getElementById('add-fasilitas-modal').classList.remove('hidden');
            document.getElementById('add-fasilitas-modal').querySelector('form').reset();
        }
        
        function closeAddFasilitasModal() {
            document.getElementById('add-fasilitas-modal').classList.add('hidden');
            document.getElementById('add-fasilitas-modal').querySelector('form').reset();
        }

        function openEditFasilitasModal(button) {
            const row = button.closest('tr');
            const id = row.getAttribute('data-id');
            const nama = row.getAttribute('data-nama');
            const deskripsi = row.getAttribute('data-deskripsi');
            const foto = row.getAttribute('data-foto');
            // Ambil nama file saja dari path lengkap
            const filename = foto.substring(foto.lastIndexOf('/') + 1);

            // Isi form modal
            document.getElementById('edit_id_fasilitas').value = id;
            document.getElementById('edit_nama_fasilitas').value = nama;
            document.getElementById('edit_deskripsi_fasilitas').value = deskripsi;
            document.getElementById('edit_current_foto').value = foto; // Simpan path foto saat ini
            document.getElementById('current-foto-name').textContent = filename;

            // Hapus nilai input file saat edit modal dibuka
            document.getElementById('edit_foto').value = '';

            // Tampilkan modal
            document.getElementById('edit-fasilitas-modal').classList.remove('hidden');
        }
        
        function closeEditFasilitasModal() {
            document.getElementById('edit-fasilitas-modal').classList.add('hidden');
            document.getElementById('edit-fasilitas-modal').querySelector('form').reset();
        }
        // --- END: Fungsi Modal Fasilitas ---

        // --- START: Fungsi Modal Galeri ---
        function openAddGaleriModal() {
            document.getElementById('add-galeri-modal').classList.remove('hidden');
            document.getElementById('add-galeri-modal').querySelector('form').reset();
        }

        function closeAddGaleriModal() {
            document.getElementById('add-galeri-modal').classList.add('hidden');
            document.getElementById('add-galeri-modal').querySelector('form').reset();
        }

        function openEditGaleriModal(button) {
            const row = button.closest('tr');
            const id = row.getAttribute('data-id');
            const nama = row.getAttribute('data-nama');
            const deskripsi = row.getAttribute('data-deskripsi');
            const file_foto = row.getAttribute('data-file_foto');
            // Ambil nama file saja dari path lengkap
            const filename = file_foto.substring(file_foto.lastIndexOf('/') + 1);

            // Isi form modal
            document.getElementById('edit_id_foto').value = id;
            document.getElementById('edit_nama_foto').value = nama;
            document.getElementById('edit_deskripsi_galeri').value = deskripsi;
            document.getElementById('edit_current_file_foto').value = file_foto; // Simpan path foto saat ini
            document.getElementById('current-file-foto-name').textContent = filename;

            // Hapus nilai input file saat edit modal dibuka
            document.getElementById('edit_file_foto').value = '';

            // Tampilkan modal
            document.getElementById('edit-galeri-modal').classList.remove('hidden');
        }

        function closeEditGaleriModal() {
            document.getElementById('edit-galeri-modal').classList.add('hidden');
            document.getElementById('edit-galeri-modal').querySelector('form').reset();
        }
        // --- END: Fungsi Modal Galeri ---
        
        // --- START: Fungsi Modal Publikasi (BARU DITAMBAHKAN) ---
        function openAddPublikasiModal() {
            document.getElementById('add-publikasi-modal').classList.remove('hidden');
            document.getElementById('add-publikasi-modal').querySelector('form').reset();
            // Set tanggal default ke hari ini
            document.getElementById('tanggal_terbit').valueAsDate = new Date();
        }

        function closeAddPublikasiModal() {
            document.getElementById('add-publikasi-modal').classList.add('hidden');
            document.getElementById('add-publikasi-modal').querySelector('form').reset();
        }

        function openEditPublikasiModal(button) {
            const row = button.closest('tr');
            const id = row.getAttribute('data-id');
            const judul = row.getAttribute('data-judul');
            const penulis = row.getAttribute('data-penulis');
            const tanggal_terbit = row.getAttribute('data-tanggal_terbit');
            const deskripsi = row.getAttribute('data-deskripsi');
            const file_publikasi = row.getAttribute('data-file_publikasi');
            
            // Ambil nama file saja dari path lengkap
            const filename = file_publikasi.substring(file_publikasi.lastIndexOf('/') + 1);

            // Isi form modal
            document.getElementById('edit_id_publikasi').value = id;
            document.getElementById('edit_judul_publikasi').value = judul;
            document.getElementById('edit_penulis').value = penulis;
            document.getElementById('edit_tanggal_terbit').value = tanggal_terbit;
            document.getElementById('edit_deskripsi_publikasi').value = deskripsi;
            document.getElementById('edit_current_file_publikasi').value = file_publikasi; // Simpan path file saat ini
            document.getElementById('current-file-publikasi-name').textContent = filename;

            // Hapus nilai input file saat edit modal dibuka
            document.getElementById('edit_file_publikasi').value = '';

            // Tampilkan modal
            document.getElementById('edit-publikasi-modal').classList.remove('hidden');
        }

        function closeEditPublikasiModal() {
            document.getElementById('edit-publikasi-modal').classList.add('hidden');
            document.getElementById('edit-publikasi-modal').querySelector('form').reset();
        }
        // --- END: Fungsi Modal Publikasi ---
    </script>
</body>

</html>