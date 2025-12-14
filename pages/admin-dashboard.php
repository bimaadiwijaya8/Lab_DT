<?php
// admin-dashboard.php (All-in-One: Dashboard Ringkasan + Kelola Berita + Kelola Galeri + Kelola Fasilitas + Kelola Publikasi + Kelola Agenda + Kelola Anggota + Kelola Pengumuman)

// --- Bagian Logika PHP Awal ---
session_start();
date_default_timezone_set('Asia/Jakarta');
$is_authenticated = true; // Ganti dengan logika otentikasi sesungguhnya (misal: isset($_SESSION['user_id']))

if (!$is_authenticated) {
    header('Location: login.php');
    exit;
}

// 1. Variabel Konfigurasi Dasar
$current_year = date('Y');
$username = "AdminLDT"; // Ganti dengan nama user yang login
$active_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$admin_user_id = 1; // HARDCODED: Ganti dengan ID user yang login (untuk kolom 'author' pada tabel berita/pengumuman / 'created_by' pada fasilitas / 'id_anggota'/'updated_by' pada galeri / 'id_anggota' pada publikasi / 'id_anggota' pada agenda)
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
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>" .
                    "Kesalahan Koneksi Database: <b>PDOException</b>. Pastikan host, port, dbname, user, dan password di <b>db_connect.php</b> sudah benar. " .
                    "Detail Error: " . htmlspecialchars($e->getMessage()) . "
                    </div>";
        $pdo = null;
    } catch (Exception $e) {
        $db_error = true;
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>" .
                    "Kesalahan Sistem: " . htmlspecialchars($e->getMessage()) . "
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
        $author = trim($_POST['author']); // Get selected author from dropdown

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
                    ':author' => $author
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
        // 1. Ambil path gambar sebelum dihapus untuk dihapus dari server
        $gambar_to_delete = '';
        $sql_select = "SELECT gambar FROM berita WHERE id_berita = :id";
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->execute([':id' => $id_berita]);
        $result = $stmt_select->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $gambar_to_delete = $result['gambar'];
        }

        // 2. Hapus dari database
        $sql = "DELETE FROM berita WHERE id_berita = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_berita]);

        // 3. Hapus gambar dari server (jika ada)
        if ($gambar_to_delete && file_exists($gambar_to_delete)) {
            @unlink($gambar_to_delete);
        }

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
                    ':created_by' => 1 // Hardcoded user_id since we don't have login session yet
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
        $id_anggota = (int)$_POST['id_anggota']; // Get selected author from dropdown

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
                        VALUES (:nama_foto, :deskripsi, :file_foto, :id_anggota, :updated_by)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama_foto' => $nama_foto,
                    ':deskripsi' => $deskripsi,
                    ':file_foto' => $file_foto_path_for_db,
                    ':id_anggota' => $id_anggota, // Use selected author from dropdown
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
        $id_anggota = (int)$_POST['id_anggota']; // Get selected author from dropdown

        $new_file_name = $_FILES['file_foto']['name'] ?? '';
        $current_file_path = $_POST['current_file_foto']; // Path file lama
        $file_foto_path_for_db = $current_file_path;      // Default: gunakan file lama
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
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupload foto baru. Perubahan DB dibatalkan.</div>";
            }
        }

        // Lakukan update DB hanya jika tidak ada error upload fatal
        if ($upload_ok) {
            try {
                $sql = "UPDATE galeri SET 
                            nama_foto = :nama_foto, 
                            deskripsi = :deskripsi, 
                            file_foto = :file_foto,
                            id_anggota = :id_anggota,
                            updated_by = :updated_by 
                        WHERE id_foto = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama_foto' => $nama_foto,
                    ':deskripsi' => $deskripsi,
                    ':file_foto' => $file_foto_path_for_db, // Path baru atau lama
                    ':id_anggota' => $id_anggota, // Use selected author from dropdown
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

// --- DELETE (Hapus Foto Galeri - Menggunakan GET request) ---
if ($pdo && $active_page === 'galeri' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_foto = (int)$_GET['id'];

    // 1. Ambil path file untuk dihapus dari server
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
// --- END: Penanganan Operasi CRUD Galeri ---

// --- START: Penanganan Operasi CRUD Publikasi (Hanya jika koneksi berhasil) ---
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $active_page === 'publikasi') {
    $action = $_POST['action'] ?? '';
    $target_dir = '../assets/files/publikasi/'; // Direktori Publikasi (PDF/File)

    // Pastikan direktori ada
    if (!is_dir($target_dir)) {
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
                // Menggunakan INSERT eksplisit
                $sql = "INSERT INTO publikasi (judul, penulis, tanggal_terbit, file_publikasi, deskripsi, id_anggota) 
                        VALUES (:judul, :penulis, :tanggal_terbit, :file, :deskripsi, :id_anggota)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':id_anggota' => $admin_user_id, // ID Anggota/User yang login
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
        $file_path_for_db = $current_file_path;      // Default: gunakan file lama
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
                            deskripsi = :deskripsi,
                            file_publikasi = :file_publikasi 
                        WHERE id_publikasi = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':judul' => $judul,
                    ':penulis' => $penulis,
                    ':tanggal_terbit' => $tanggal_terbit,
                    ':deskripsi' => $deskripsi,
                    ':file_publikasi' => $file_path_for_db, // Path baru atau lama
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

// --- START: Penanganan Operasi CRUD Agenda (Hanya jika koneksi berhasil) ---
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $active_page === 'agenda') {
    $action = $_POST['action'] ?? '';

    // --- CREATE (Tambah Agenda Baru) ---
    if ($action === 'add_agenda') {
        $nama_agenda = trim($_POST['nama_agenda']);
        $tgl_agenda = trim($_POST['tgl_agenda']);
        $link_agenda = trim($_POST['link_agenda']);
        $id_anggota = (int)$_POST['id_anggota']; // ID anggota/user yang posting

        if (empty($nama_agenda) || empty($tgl_agenda)) {
             $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Nama agenda dan Tanggal agenda wajib diisi.</div>";
        } else {
            try {
                $sql = "INSERT INTO agenda (nama_agenda, tgl_agenda, link_agenda, id_anggota) 
                        VALUES (:nama_agenda, :tgl_agenda, :link_agenda, :id_anggota)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama_agenda' => $nama_agenda,
                    ':tgl_agenda' => $tgl_agenda,
                    ':link_agenda' => $link_agenda,
                    ':id_anggota' => $id_anggota
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Agenda baru berhasil ditambahkan!</div>";
            } catch (Exception $e) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan agenda: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }

    // --- UPDATE (Edit Agenda) ---
    if ($action === 'edit_agenda') {
        $id_agenda = (int)$_POST['id_agenda'];
        $nama_agenda = trim($_POST['nama_agenda']);
        $tgl_agenda = trim($_POST['tgl_agenda']);
        $link_agenda = trim($_POST['link_agenda']);

        if (empty($nama_agenda) || empty($tgl_agenda)) {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Nama agenda dan Tanggal agenda wajib diisi.</div>";
        } else {
            try {
                $sql = "UPDATE agenda SET 
                            nama_agenda = :nama_agenda, 
                            tgl_agenda = :tgl_agenda, 
                            link_agenda = :link_agenda 
                        WHERE id_agenda = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama_agenda' => $nama_agenda,
                    ':tgl_agenda' => $tgl_agenda,
                    ':link_agenda' => $link_agenda,
                    ':id' => $id_agenda
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Agenda ID {$id_agenda} berhasil diupdate!</div>";
            } catch (Exception $e) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupdate agenda: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}

// --- DELETE (Hapus Agenda - Menggunakan GET request) ---
if ($pdo && $active_page === 'agenda' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_agenda = (int)$_GET['id'];
    try {
        $sql = "DELETE FROM agenda WHERE id_agenda = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_agenda]);
        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Agenda ID {$id_agenda} berhasil dihapus!</div>";
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menghapus agenda: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    // Redirect untuk menghilangkan parameter GET dari URL
    header("Location: admin-dashboard.php?page=agenda");
    exit;
}
// --- END: Penanganan Operasi CRUD Agenda ---

// --- START: Penanganan Operasi CRUD Anggota (Hanya jika koneksi berhasil) ---
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $active_page === 'anggota') {
    $action = $_POST['action'] ?? '';
    $target_dir = '../assets/img/anggota/'; // Direktori Foto Anggota

    // Pastikan direktori ada
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal membuat folder upload: {$target_dir}. Pastikan izin tulis sudah diatur.</div>";
            $pdo = null; // Hentikan operasi DB jika gagal buat folder
        }
    }

    // --- CREATE (Tambah Anggota Baru) ---
    if ($action === 'add_anggota' && $pdo) {
        $nama_gelar = trim($_POST['nama_gelar']);
        $jabatan = trim($_POST['jabatan']);
        $email = trim($_POST['email']);
        $no_telp = trim($_POST['no_telp']);
        $bidang_keahlian = trim($_POST['bidang_keahlian']);

        $upload_ok = true;
        $foto_path_for_db = '';
        $upload_message = '';

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {

            // 1. Tentukan nama file
            $file_name = basename($_FILES['foto']['name']);
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $unique_name = 'anggota_' . time() . '_' . $safe_file_name;
            $target_file = $target_dir . $unique_name;
            $foto_path_for_db = $target_file;

            // 2. Lakukan proses upload
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $upload_ok = false;
                $upload_message = "Gagal mengupload foto. Pastikan folder '{$target_dir}' memiliki izin tulis (0777).";
            }
        } else if ($_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            $upload_ok = false;
            $upload_message = "Harap unggah foto anggota.";
        } else {
            $upload_ok = false;
            $upload_message = "Terjadi error saat upload file. Kode error: " . $_FILES['foto']['error'];
        }

        // 3. Simpan ke database jika upload berhasil
        if ($upload_ok) {
            try {
                $sql = "INSERT INTO anggota (nama_gelar, foto, jabatan, email, no_telp, bidang_keahlian) 
                        VALUES (:nama_gelar, :foto, :jabatan, :email, :no_telp, :bidang_keahlian)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama_gelar' => $nama_gelar,
                    ':foto' => $foto_path_for_db,
                    ':jabatan' => $jabatan,
                    ':email' => $email,
                    ':no_telp' => $no_telp,
                    ':bidang_keahlian' => $bidang_keahlian
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Anggota baru berhasil ditambahkan!</div>";
            } catch (Exception $e) {
                // Jika gagal simpan DB, hapus file yang sudah terupload (optional cleanup)
                if (file_exists($foto_path_for_db)) {
                    @unlink($foto_path_for_db);
                }
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan anggota (DB Error): " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan anggota (Upload Error): {$upload_message}</div>";
        }
    }

    // --- UPDATE (Edit Anggota) ---
    if ($action === 'edit_anggota' && $pdo) {
        $id_anggota = (int)$_POST['id_anggota'];
        $nama_gelar = trim($_POST['nama_gelar']);
        $jabatan = trim($_POST['jabatan']);
        $email = trim($_POST['email']);
        $no_telp = trim($_POST['no_telp']);
        $bidang_keahlian = trim($_POST['bidang_keahlian']);

        $new_file_name = $_FILES['foto']['name'] ?? '';
        $current_file_path = $_POST['current_foto']; // Path foto lama
        $foto_path_for_db = $current_file_path;      // Default: gunakan foto lama
        $upload_ok = true;

        // Cek apakah ada file baru yang diupload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK && !empty($new_file_name)) {

            $file_name = basename($_FILES['foto']['name']);
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $unique_name = 'anggota_' . time() . '_' . $safe_file_name;
            $target_file = $target_dir . $unique_name;

            // Lakukan proses upload file baru
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $foto_path_for_db = $target_file;

                // Opsional: Hapus file lama di server
                if ($current_file_path && file_exists($current_file_path)) {
                    @unlink($current_file_path);
                }
            } else {
                $upload_ok = false;
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupload foto baru. Perubahan DB dibatalkan.</div>";
            }
        }

        // Lakukan update DB hanya jika tidak ada error upload fatal
        if ($upload_ok) {
            try {
                $sql = "UPDATE anggota SET 
                            nama_gelar = :nama_gelar, 
                            foto = :foto, 
                            jabatan = :jabatan, 
                            email = :email, 
                            no_telp = :no_telp, 
                            bidang_keahlian = :bidang_keahlian
                        WHERE id_anggota = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama_gelar' => $nama_gelar,
                    ':foto' => $foto_path_for_db, // Path baru atau lama
                    ':jabatan' => $jabatan,
                    ':email' => $email,
                    ':no_telp' => $no_telp,
                    ':bidang_keahlian' => $bidang_keahlian,
                    ':id' => $id_anggota
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Anggota ID {$id_anggota} berhasil diupdate!</div>";
            } catch (Exception $e) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupdate anggota: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}

// --- DELETE (Hapus Anggota - Menggunakan GET request) ---
if ($pdo && $active_page === 'anggota' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_anggota = (int)$_GET['id'];
    // 1. Ambil path foto untuk dihapus dari server
    $foto_to_delete = '';
    try {
        $sql_select = "SELECT foto FROM anggota WHERE id_anggota = :id";
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->execute([':id' => $id_anggota]);
        $result = $stmt_select->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $foto_to_delete = $result['foto'];
        }

        // 2. Hapus dari database
        $sql = "DELETE FROM anggota WHERE id_anggota = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_anggota]);

        // 3. Hapus foto dari server (jika ada)
        if ($foto_to_delete && file_exists($foto_to_delete)) {
            @unlink($foto_to_delete);
        }

        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Anggota ID {$id_anggota} berhasil dihapus!</div>";
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menghapus anggota: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    // Redirect untuk menghilangkan parameter GET dari URL
    header("Location: admin-dashboard.php?page=anggota");
    exit;
}
// --- END: Penanganan Operasi CRUD Anggota ---

// --- START: Penanganan Operasi CRUD Pengumuman (Hanya jika koneksi berhasil) ---
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $active_page === 'pengumuman') {
    $action = $_POST['action'] ?? '';

    // --- CREATE (Tambah Pengumuman Baru) ---
    if ($action === 'add_pengumuman') {
        $judul = trim($_POST['judul']);
        $informasi = trim($_POST['informasi']);
        $tanggal = trim($_POST['tanggal']);
        $author = (int)$_POST['author']; // ID anggota/user yang posting

        if (empty($judul) || empty($informasi) || empty($tanggal)) {
             $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Judul, Isi Pengumuman, dan Tanggal Posting wajib diisi.</div>";
        } else {
            try {
                $sql = "INSERT INTO pengumuman (judul, informasi, tanggal, id_anggota) 
                        VALUES (:judul, :isi, :tanggal, :author)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':judul' => $judul,
                    ':isi' => $informasi,
                    ':tanggal' => $tanggal,
                    ':author' => $author
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Pengumuman baru berhasil ditambahkan!</div>";
            } catch (Exception $e) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan pengumuman (DB Error): " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }

    // --- UPDATE (Edit Pengumuman) ---
    if ($action === 'edit_pengumuman') {
        $id_pengumuman = (int)$_POST['id_pengumuman'];
        $judul = trim($_POST['judul']);
        $informasi = trim($_POST['informasi']);
        $tanggal = trim($_POST['tanggal']);

        if (empty($judul) || empty($informasi) || empty($tanggal)) {
             $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Judul, Isi Pengumuman, dan Tanggal Posting wajib diisi.</div>";
        } else {
            try {
                $sql = "UPDATE pengumuman SET 
                            judul = :judul, 
                            informasi = :isi, 
                            tanggal = :tanggal 
                        WHERE id_pengumuman = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':judul' => $judul,
                    ':isi' => $informasi,
                    ':tanggal' => $tanggal,
                    ':id' => $id_pengumuman
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Pengumuman ID {$id_pengumuman} berhasil diupdate!</div>";
            } catch (Exception $e) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupdate pengumuman: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}

// --- DELETE (Hapus Pengumuman - Menggunakan GET request) ---
if ($pdo && $active_page === 'pengumuman' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_pengumuman = (int)$_GET['id'];
    try {
        $sql = "DELETE FROM pengumuman WHERE id_pengumuman = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_pengumuman]);
        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Pengumuman ID {$id_pengumuman} berhasil dihapus!</div>";
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menghapus pengumuman: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    // Redirect untuk menghilangkan parameter GET dari URL
    header("Location: admin-dashboard.php?page=pengumuman");
    exit;
}
// --- END: Penanganan Operasi CRUD Pengumuman ---


// --- START: Data Dashboard & Data List ---
$total_news = 0;
$total_pending_news = 0;
$total_fasilitas = 0;
$total_galeri = 0;
$total_publikasi = 0;
$total_agenda = 0;
$total_anggota = 0;
$total_pengumuman = 0; // Tambah: Total Pengumuman

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM berita");
        $total_news = $stmt->fetchColumn();
        $stmt = $pdo->query("SELECT COUNT(*) FROM berita WHERE status = 'pending'");
        $total_pending_news = $stmt->fetchColumn();
        $stmt = $pdo->query("SELECT COUNT(*) FROM fasilitas");
        $total_fasilitas = $stmt->fetchColumn();
        $stmt = $pdo->query("SELECT COUNT(*) FROM galeri");
        $total_galeri = $stmt->fetchColumn();
        $stmt = $pdo->query("SELECT COUNT(*) FROM publikasi");
        $total_publikasi = $stmt->fetchColumn();
        $stmt = $pdo->query("SELECT COUNT(*) FROM agenda");
        $total_agenda = $stmt->fetchColumn();
        $stmt = $pdo->query("SELECT COUNT(*) FROM anggota");
        $total_anggota = $stmt->fetchColumn();
        // Tambah: Total Pengumuman
        $stmt = $pdo->query("SELECT COUNT(*) FROM pengumuman");
        $total_pengumuman = $stmt->fetchColumn();
        // Akhir Tambahan Pengumuman
    } catch (Exception $e) {
        // Biarkan count 0 jika ada error
    }
}

// --- START: Data Berita ---
$news_data = [];
$anggota_list = []; // List Anggota/User untuk dropdown Author
if ($pdo) {
    try {
        // Ambil semua anggota untuk dropdown Author (Berita, Agenda, Pengumuman)
        $sql_anggota = "SELECT id_anggota, nama_gelar FROM anggota ORDER BY nama_gelar ASC";
        $stmt_anggota = $pdo->query($sql_anggota);
        $anggota_list = $stmt_anggota->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Handle error jika tabel anggota tidak ditemukan/error
    }
}

if ($active_page === 'berita' && $pdo) {
    try {
        // READ: Mengambil semua data berita, join ke tabel anggota/user
        $sql = "SELECT b.*, a.nama_gelar AS author_name FROM berita b LEFT JOIN anggota a ON b.author = a.id_anggota ORDER BY b.id_berita DESC";
        $stmt = $pdo->query($sql);
        $news_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data berita: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
// --- END: Data Berita ---

// --- START: Data Fasilitas (READ - Diambil dari DB jika koneksi berhasil) ---
$fasilitas_data = [];
if ($active_page === 'fasilitas' && $pdo) {
    try {
        // READ: Mengambil semua data fasilitas
        $sql = "SELECT * FROM fasilitas ORDER BY id_fasilitas DESC";
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
        // READ: Mengambil semua data galeri
        $sql = "SELECT g.*, u.username AS uploader_name, a.nama_gelar AS author_name FROM galeri g LEFT JOIN users u ON g.updated_by = u.id LEFT JOIN anggota a ON g.id_anggota = a.id_anggota ORDER BY g.id_foto DESC";
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
        // READ: Mengambil semua data publikasi
        $sql = "SELECT p.*, a.nama_gelar AS nama_member FROM publikasi p LEFT JOIN anggota a ON p.id_anggota = a.id_anggota ORDER BY p.id_publikasi DESC";
        $stmt = $pdo->query($sql);
        $publikasi_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data publikasi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
// --- END: Data Publikasi ---

// --- START: Data Agenda (READ - Diambil dari DB jika koneksi berhasil) ---
$agenda_data = [];
if ($active_page === 'agenda' && $pdo) {
    try {
        // READ: Mengambil semua data agenda
        $sql = "SELECT a.*, b.nama_gelar AS author_name FROM agenda a LEFT JOIN anggota b ON a.id_anggota = b.id_anggota ORDER BY a.tgl_agenda ASC, a.id_agenda DESC";
        $stmt = $pdo->query($sql);
        $agenda_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data agenda: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
// --- END: Data Agenda ---

// --- START: Data Anggota (READ - Diambil dari DB jika koneksi berhasil) ---
$anggota_data = [];
if ($active_page === 'anggota' && $pdo) {
    try {
        // READ: Mengambil semua data anggota
        $sql = "SELECT * FROM anggota ORDER BY nama_gelar ASC";
        $stmt = $pdo->query($sql);
        $anggota_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data anggota: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
// --- END: Data Anggota ---

// --- START: Data Pengumuman (READ - Diambil dari DB jika koneksi berhasil) ---
$pengumuman_data = [];
if ($active_page === 'pengumuman' && $pdo) {
    try {
        // READ: Mengambil semua data pengumuman, join ke tabel anggota/user
        $sql = "SELECT p.*, a.nama_gelar AS author_name 
                FROM pengumuman p 
                LEFT JOIN anggota a ON p.id_anggota = a.id_anggota
                ORDER BY p.tanggal DESC, p.id_pengumuman DESC";
        $stmt = $pdo->query($sql);
        $pengumuman_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data pengumuman: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
// --- END: Data Pengumuman ---

// --- Bagian HTML/Design Dashboard Dimulai ---
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
        /* Custom colors */
        .bg-primary { background-color: #3b82f6; /* Blue 500 */ }
        .hover:bg-primary-dark:hover { background-color: #2563eb; /* Blue 600 */ }
        .text-primary { color: #3b82f6; }
        .border-primary { border-color: #3b82f6; }
        .focus\:ring-primary:focus { --tw-ring-color: #3b82f6; }
        .bg-secondary { background-color: #10b981; /* Emerald 500 */ }
        .text-secondary { color: #10b981; }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .z-\[100\] { z-index: 100; }
        
        /* Sidebar toggle styles */
        .sidebar-closed {
            transform: translateX(-100%);
        }
        .main-content-shifted {
            margin-left: 0 !important;
        }
        .toggle-btn {
            position: fixed;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            z-index: 1000;
            background: white;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .toggle-btn:hover {
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
        .sidebar-open .toggle-btn {
            left: 268px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-100 via-slate-50 to-slate-200 min-h-screen text-gray-900 antialiased sidebar-open">
    
    <!-- Toggle Button -->
    <button class="toggle-btn" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
        <i class="fas fa-chevron-left text-gray-700"></i>
    </button>
    
    <div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100 shadow-xl border-r border-slate-800/60 px-5 py-6 flex flex-col transition-transform duration-300 ease-in-out">
        <div class="mb-8 flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-primary/20 flex items-center justify-center text-primary">
                <i class="fas fa-flask text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-white leading-snug">Admin Panel</h2>
                <p class="text-xs text-slate-400">LDT • <?php echo $current_year; ?></p>
            </div>
        </div>
        
        <div class="flex-grow">
            <p class="text-[11px] font-semibold tracking-[0.16em] uppercase text-slate-500 mb-3">Navigasi Utama</p>
            <nav class="space-y-1.5">
                <ul>
                    <li><a href="admin-dashboard.php?page=dashboard" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 <?php echo $active_page === 'dashboard' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white'; ?>"><i class="fas fa-home w-5 h-5 mr-3 flex items-center justify-center"></i> Dashboard</a></li>
                    <li><a href="admin-dashboard.php?page=berita" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 <?php echo $active_page === 'berita' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white'; ?>"><i class="fas fa-newspaper w-5 h-5 mr-3 flex items-center justify-center"></i> Kelola Berita</a></li>
                    <li><a href="admin-dashboard.php?page=fasilitas" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 <?php echo $active_page === 'fasilitas' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white'; ?>"><i class="fas fa-building w-5 h-5 mr-3 flex items-center justify-center"></i> Kelola Fasilitas</a></li>
                    <li><a href="admin-dashboard.php?page=galeri" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 <?php echo $active_page === 'galeri' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white'; ?>"><i class="fas fa-image w-5 h-5 mr-3 flex items-center justify-center"></i> Kelola Galeri</a></li>
                    <li><a href="admin-dashboard.php?page=publikasi" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 <?php echo $active_page === 'publikasi' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white'; ?>"><i class="fas fa-book-open w-5 h-5 mr-3 flex items-center justify-center"></i> Kelola Publikasi</a></li>
                    <li><a href="admin-dashboard.php?page=agenda" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 <?php echo $active_page === 'agenda' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white'; ?>"><i class="fas fa-calendar-alt w-5 h-5 mr-3 flex items-center justify-center"></i> Kelola Agenda</a></li>
                    <li><a href="admin-dashboard.php?page=pengumuman" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 <?php echo $active_page === 'pengumuman' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white'; ?>"><i class="fas fa-bullhorn w-5 h-5 mr-3 flex items-center justify-center"></i> Kelola Pengumuman</a></li>
                    <li><a href="admin-dashboard.php?page=anggota" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 <?php echo $active_page === 'anggota' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white'; ?>"><i class="fas fa-users w-5 h-5 mr-3 flex items-center justify-center"></i> Kelola Anggota</a></li>
                </ul>
            </nav>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-800/60">
            <a href="logout.php" class="flex items-center px-3 py-2 rounded-lg text-red-300 hover:bg-red-500/10 hover:text-red-100 text-sm font-medium transition-colors duration-150">
                <i class="fas fa-sign-out-alt w-5 h-5 mr-3 flex items-center justify-center"></i> Logout (<?php echo htmlspecialchars($username); ?>)
            </a>
        </div>
    </div>

    <div id="mainContent" class="ml-64 p-6 md:p-10 transition-all duration-300 ease-in-out">
        <header class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <p class="flex items-center gap-2 text-sm text-gray-500">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <i class="fas fa-shield-alt text-[11px]"></i>
                    </span>
                    <span class="font-medium">Panel Admin LDT</span>
                </p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900">
                    Selamat Datang, <?php echo htmlspecialchars($username); ?>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Halaman aktif: 
                    <span class="font-semibold text-gray-700"><?php echo ucfirst($active_page); ?></span>
                </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Tanggal</p>
                    <p class="text-sm font-medium text-gray-700"><?php echo date('d F Y H:i'); ?></p>
                </div>
                <div class="flex items-center gap-3 rounded-full bg-white/80 backdrop-blur px-3 py-2 shadow-sm border border-gray-100">
                    <div class="h-9 w-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-semibold">
                        <?php echo strtoupper(substr($username, 0, 1)); ?>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold text-gray-800"><?php echo htmlspecialchars($username); ?></p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                </div>
            </div>
        </header>

        <?php if ($db_error): ?>
            <div class="mt-4">
                <?php echo $message; ?>
            </div>
        <?php elseif ($active_page === 'dashboard'): ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Ringkasan Data</h1>
            <?php echo $message; // Menampilkan pesan dari operasi CRUD sebelumnya jika ada redirect ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-primary">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Berita</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_news; ?></p>
                        </div>
                        <i class="fas fa-newspaper text-4xl text-primary opacity-30"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-yellow-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Berita Pending</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_pending_news; ?></p>
                        </div>
                        <i class="fas fa-hourglass-half text-4xl text-yellow-500 opacity-30"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-secondary">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Fasilitas</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_fasilitas; ?></p>
                        </div>
                        <i class="fas fa-building text-4xl text-secondary opacity-30"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Galeri</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_galeri; ?></p>
                        </div>
                        <i class="fas fa-image text-4xl text-purple-500 opacity-30"></i>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-pink-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Publikasi</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_publikasi; ?></p>
                        </div>
                        <i class="fas fa-book-open text-4xl text-pink-500 opacity-30"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-teal-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Agenda</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_agenda; ?></p>
                        </div>
                        <i class="fas fa-calendar-alt text-4xl text-teal-500 opacity-30"></i>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-red-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Anggota</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_anggota; ?></p>
                        </div>
                        <i class="fas fa-users text-4xl text-red-500 opacity-30"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Pengumuman</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_pengumuman; ?></p>
                        </div>
                        <i class="fas fa-bullhorn text-4xl text-orange-500 opacity-30"></i>
                    </div>
                </div>
                </div>

            <?php elseif ($active_page === 'berita'): ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Berita</h1>
            <?php echo $message; ?>

            <div class="flex justify-end mb-6">
                <button onclick="openAddNewsModal()" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300">
                    <i class="fas fa-plus mr-2"></i> Tambah Berita Baru
                </button>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($news_data)): ?>
                            <?php foreach ($news_data as $news): ?>
                                <tr data-id="<?php echo $news['id_berita']; ?>" 
                                    data-judul="<?php echo htmlspecialchars($news['judul']); ?>" 
                                    data-informasi="<?php echo htmlspecialchars($news['informasi']); ?>" 
                                    data-tanggal="<?php echo htmlspecialchars($news['tanggal']); ?>" 
                                    data-author="<?php echo htmlspecialchars($news['author']); ?>"
                                    data-gambar="<?php echo htmlspecialchars($news['gambar']); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <img src="<?php echo htmlspecialchars($news['gambar']); ?>" alt="Gambar Berita" class="h-10 w-10 rounded object-cover">
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 line-clamp-2" style="max-width: 300px;"><?php echo htmlspecialchars($news['judul']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d F Y', strtotime($news['tanggal'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($news['author_name'] ?? 'Admin'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                            $status_class = [
                                                'approved' => 'bg-green-100 text-green-800', 
                                                'pending' => 'bg-yellow-100 text-yellow-800', 
                                                'rejected' => 'bg-red-100 text-red-800'
                                            ][$news['status']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                            <?php echo ucfirst($news['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                        <button onclick="openEditNewsModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="admin-dashboard.php?page=berita&action=delete&id=<?php echo $news['id_berita']; ?>" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini? Gambar juga akan terhapus dari server.')" 
                                           class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <button onclick="openVerifyModal(<?php echo $news['id_berita']; ?>, '<?php echo $news['status']; ?>')" class="text-gray-500 hover:text-gray-900 p-2 rounded-md hover:bg-gray-100" title="Verifikasi Berita">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="quickReject(<?php echo $news['id_berita']; ?>)" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100" title="Tolak Berita">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data berita.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($active_page === 'fasilitas'): ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Fasilitas</h1>
            <?php echo $message; ?>

            <div class="flex justify-end mb-6">
                <button onclick="openAddFasilitasModal()" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300">
                    <i class="fas fa-plus mr-2"></i> Tambah Fasilitas Baru
                </button>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Fasilitas</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi (Snippet)</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($fasilitas_data)): ?>
                            <?php foreach ($fasilitas_data as $fasilitas): ?>
                                <tr data-id="<?php echo $fasilitas['id_fasilitas']; ?>" 
                                    data-nama_fasilitas="<?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?>" 
                                    data-deskripsi="<?php echo htmlspecialchars($fasilitas['deskripsi']); ?>" 
                                    data-foto="<?php echo htmlspecialchars($fasilitas['foto']); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <img src="<?php echo htmlspecialchars($fasilitas['foto']); ?>" alt="Foto Fasilitas" class="h-10 w-10 rounded object-cover">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" style="max-width: 400px;">
                                        <?php echo htmlspecialchars(substr($fasilitas['deskripsi'], 0, 100)) . (strlen($fasilitas['deskripsi']) > 100 ? '...' : ''); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                        <button onclick="openEditFasilitasModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="admin-dashboard.php?page=fasilitas&action=delete&id=<?php echo $fasilitas['id_fasilitas']; ?>" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini? Foto juga akan terhapus dari server.')" 
                                           class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada data fasilitas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($active_page === 'galeri'): ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Galeri</h1>
            <?php echo $message; ?>

            <div class="flex justify-end mb-6">
                <button onclick="openAddGaleriModal()" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300">
                    <i class="fas fa-plus mr-2"></i> Tambah Foto Galeri Baru
                </button>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Foto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi (Snippet)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diupload Oleh</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($galeri_data)): ?>
                            <?php foreach ($galeri_data as $galeri): ?>
                                <tr data-id="<?php echo $galeri['id_foto']; ?>" 
                                    data-nama_foto="<?php echo htmlspecialchars($galeri['nama_foto']); ?>" 
                                    data-deskripsi="<?php echo htmlspecialchars($galeri['deskripsi']); ?>" 
                                    data-file_foto="<?php echo htmlspecialchars($galeri['file_foto']); ?>"
                                    data-author="<?php echo htmlspecialchars($galeri['id_anggota']); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <img src="<?php echo htmlspecialchars($galeri['file_foto']); ?>" alt="Foto Galeri" class="h-10 w-10 rounded object-cover">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($galeri['nama_foto']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" style="max-width: 400px;"><?php echo htmlspecialchars(substr($galeri['deskripsi'], 0, 100)) . (strlen($galeri['deskripsi']) > 100 ? '...' : ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($galeri['author_name'] ?? '-'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($galeri['uploader_name'] ?? 'Admin'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                        <button onclick="openEditGaleriModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="admin-dashboard.php?page=galeri&action=delete&id=<?php echo $galeri['id_foto']; ?>" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus foto galeri ini? File juga akan terhapus dari server.')" 
                                           class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data galeri.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        
        <?php elseif ($active_page === 'publikasi'): ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Publikasi</h1>
            <?php echo $message; ?>

            <div class="flex justify-end mb-6">
                <button onclick="openAddPublikasiModal()" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300">
                    <i class="fas fa-plus mr-2"></i> Tambah Publikasi Baru
                </button>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Terbit</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi (Snippet)</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Aksi</span>
                            </th>
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
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 line-clamp-2" style="max-width: 200px;"><?php echo htmlspecialchars($publikasi['judul']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($publikasi['penulis']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($publikasi['tanggal_terbit']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="<?php echo htmlspecialchars($publikasi['file_publikasi']); ?>" target="_blank" class="text-primary hover:text-primary-dark">
                                            <i class="fas fa-file-pdf mr-1"></i> Lihat File
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" style="max-width: 300px;"><?php echo htmlspecialchars(substr($publikasi['deskripsi'], 0, 100)) . (strlen($publikasi['deskripsi']) > 100 ? '...' : ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                        <button onclick="openEditPublikasiModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="admin-dashboard.php?page=publikasi&action=delete&id=<?php echo $publikasi['id_publikasi']; ?>" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus publikasi ini? File juga akan terhapus dari server.')" 
                                           class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada data publikasi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($active_page === 'agenda'): ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Agenda</h1>
            <?php echo $message; ?>

            <div class="flex justify-end mb-6">
                <button onclick="openAddAgendaModal()" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300">
                    <i class="fas fa-plus mr-2"></i> Tambah Agenda Baru
                </button>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Agenda</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($agenda_data)): ?>
                            <?php foreach ($agenda_data as $agenda): ?>
                                <tr data-id="<?php echo $agenda['id_agenda']; ?>" 
                                    data-nama_agenda="<?php echo htmlspecialchars($agenda['nama_agenda']); ?>" 
                                    data-tgl_agenda="<?php echo htmlspecialchars($agenda['tgl_agenda']); ?>" 
                                    data-link_agenda="<?php echo htmlspecialchars($agenda['link_agenda']); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $agenda['id_agenda']; ?></td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 line-clamp-2" style="max-width: 300px;"><?php echo htmlspecialchars($agenda['nama_agenda']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($agenda['tgl_agenda']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <?php if (!empty($agenda['link_agenda'])): ?>
                                            <a href="<?php echo htmlspecialchars($agenda['link_agenda']); ?>" target="_blank" class="text-primary hover:text-primary-dark">
                                                <i class="fas fa-link mr-1"></i> Link
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($agenda['author_name'] ?? 'Admin'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                        <button onclick="openEditAgendaModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="admin-dashboard.php?page=agenda&action=delete&id=<?php echo $agenda['id_agenda']; ?>" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus agenda ini?')" 
                                           class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data agenda.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($active_page === 'pengumuman'): ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Pengumuman</h1>
            <?php echo $message; ?>

            <div class="flex justify-end mb-6">
                <button onclick="openAddPengumumanModal()" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300">
                    <i class="fas fa-plus mr-2"></i> Tambah Pengumuman Baru
                </button>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Isi (Snippet)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Posting</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($pengumuman_data)): ?>
                            <?php foreach ($pengumuman_data as $pengumuman): ?>
                                <tr data-id="<?php echo $pengumuman['id_pengumuman']; ?>" 
                                    data-judul="<?php echo htmlspecialchars($pengumuman['judul']); ?>" 
                                    data-informasi="<?php echo htmlspecialchars($pengumuman['informasi']); ?>" 
                                    data-tanggal="<?php echo htmlspecialchars($pengumuman['tanggal']); ?>"
                                    data-author="<?php echo htmlspecialchars($pengumuman['id_anggota']); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $pengumuman['id_pengumuman']; ?></td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 line-clamp-2" style="max-width: 250px;"><?php echo htmlspecialchars($pengumuman['judul']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500 text-ellipsis line-clamp-2" style="max-width: 350px;">
                                        <?php echo htmlspecialchars(substr($pengumuman['informasi'], 0, 100)) . (strlen($pengumuman['informasi']) > 100 ? '...' : ''); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($pengumuman['tanggal']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($pengumuman['id_anggota'] ?? 'Admin'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                        <button onclick="openEditPengumumanModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="admin-dashboard.php?page=pengumuman&action=delete&id=<?php echo $pengumuman['id_pengumuman']; ?>" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')" 
                                           class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data pengumuman.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php elseif ($active_page === 'anggota'): ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Anggota</h1>
            <?php echo $message; ?>

            <div class="flex justify-end mb-6">
                <button onclick="openAddAnggotaModal()" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300">
                    <i class="fas fa-user-plus mr-2"></i> Tambah Anggota Baru
                </button>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama & Gelar</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bidang Keahlian (Snippet)</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($anggota_data)): ?>
                            <?php foreach ($anggota_data as $anggota): ?>
                                <tr data-id="<?php echo $anggota['id_anggota']; ?>" 
                                    data-nama_gelar="<?php echo htmlspecialchars($anggota['nama_gelar']); ?>" 
                                    data-jabatan="<?php echo htmlspecialchars($anggota['jabatan']); ?>"
                                    data-email="<?php echo htmlspecialchars($anggota['email']); ?>"
                                    data-no_telp="<?php echo htmlspecialchars($anggota['no_telp']); ?>"
                                    data-bidang_keahlian="<?php echo htmlspecialchars($anggota['bidang_keahlian']); ?>"
                                    data-foto="<?php echo htmlspecialchars($anggota['foto']); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $anggota['id_anggota']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <img src="<?php echo htmlspecialchars($anggota['foto']); ?>" alt="Foto Anggota" class="h-10 w-10 rounded-full object-cover">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($anggota['nama_gelar']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($anggota['jabatan']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <p class="truncate"><?php echo htmlspecialchars($anggota['email']); ?></p>
                                        <p class="truncate text-xs text-gray-400"><?php echo htmlspecialchars($anggota['no_telp']); ?></p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" style="max-width: 300px;"><?php echo htmlspecialchars(substr($anggota['bidang_keahlian'], 0, 100)) . (strlen($anggota['bidang_keahlian']) > 100 ? '...' : ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                        <button onclick="openEditAnggotaModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="admin-dashboard.php?page=anggota&action=delete&id=<?php echo $anggota['id_anggota']; ?>" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini? Foto juga akan terhapus dari server.')" 
                                           class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada data anggota.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
        <?php elseif ($active_page === 'settings'): ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Pengaturan Sistem</h1>
            <?php echo $message; ?>
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <p class="text-gray-500">Halaman ini digunakan untuk mengelola pengaturan umum seperti nama situs, footer, dll.</p>
            </div>
        <?php else: ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Halaman Tidak Ditemukan</h1>
            <p class="text-gray-500">Halaman `<?php echo htmlspecialchars($active_page); ?>` tidak tersedia.</p>
        <?php endif; ?>
    </div>


    <div id="addNewsModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=berita" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_news">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modal-title">Tambah Berita Baru</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="judul" class="block text-sm font-medium text-gray-700">Judul Berita</label>
                                <input type="text" name="judul" id="judul" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar Utama (Wajib)</label>
                                <input type="file" name="gambar" id="gambar" required accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                            </div>
                            <div>
                                <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal Berita</label>
                                <input type="date" name="tanggal" id="tanggal" value="<?php echo date('Y-m-d'); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="author" class="block text-sm font-medium text-gray-700">Author</label>
                                <select name="author" id="author" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                                    <?php foreach ($anggota_list as $anggota): ?>
                                        <option value="<?php echo $anggota['id_anggota']; ?>" <?php echo $anggota['id_anggota'] == $admin_user_id ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($anggota['nama_gelar']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="informasi" class="block text-sm font-medium text-gray-700">Isi Berita</label>
                                <textarea name="informasi" id="informasi" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" onclick="closeAddNewsModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editNewsModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=berita" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_news">
                    <input type="hidden" name="id_berita" id="edit_id_berita">
                    <input type="hidden" name="current_gambar" id="edit_current_gambar">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Berita</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="edit_judul" class="block text-sm font-medium text-gray-700">Judul Berita</label>
                                <input type="text" name="judul" id="edit_judul" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_gambar" class="block text-sm font-medium text-gray-700">Ganti Gambar Utama (Opsional)</label>
                                <img id="edit_current_gambar_preview" src="" alt="Gambar Lama" class="h-16 w-16 object-cover rounded mb-2">
                                <input type="file" name="gambar" id="edit_gambar" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-500 file:text-white hover:file:bg-gray-600">
                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengganti gambar.</p>
                            </div>
                            <div>
                                <label for="edit_tanggal" class="block text-sm font-medium text-gray-700">Tanggal Berita</label>
                                <input type="date" name="tanggal" id="edit_tanggal" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_informasi" class="block text-sm font-medium text-gray-700">Isi Berita</label>
                                <textarea name="informasi" id="edit_informasi" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditNewsModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="verifyModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-sm sm:w-full">
                <form action="admin-dashboard.php?page=berita" method="POST">
                    <input type="hidden" name="action" value="verify_news">
                    <input type="hidden" name="id_berita" id="verify_id_berita">
                    <input type="hidden" name="status" id="verify_status_input">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Verifikasi Berita</h3>
                        <p class="text-sm text-gray-500" id="verify_current_status">Status saat ini: </p>
                        <p class="mt-2 text-sm text-gray-700">Pilih aksi untuk berita ini:</p>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse space-y-2 sm:space-y-0 sm:space-x-2">
                        <button type="submit" onclick="document.getElementById('verify_status_input').value='approved'" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fas fa-check-circle mr-1"></i> Setujui (Approve)
                        </button>
                        <button type="submit" onclick="document.getElementById('verify_status_input').value='rejected'" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fas fa-times-circle mr-1"></i> Tolak (Reject)
                        </button>
                        <button type="button" onclick="closeVerifyModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="addFasilitasModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=fasilitas" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_fasilitas">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Tambah Fasilitas Baru</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="nama_fasilitas" class="block text-sm font-medium text-gray-700">Nama Fasilitas</label>
                                <input type="text" name="nama_fasilitas" id="nama_fasilitas" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="foto" class="block text-sm font-medium text-gray-700">Foto Fasilitas (Wajib)</label>
                                <input type="file" name="foto" id="foto" required accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                            </div>
                            <div>
                                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea name="deskripsi" id="deskripsi" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" onclick="closeAddFasilitasModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editFasilitasModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=fasilitas" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_fasilitas">
                    <input type="hidden" name="id_fasilitas" id="edit_id_fasilitas">
                    <input type="hidden" name="current_foto" id="edit_current_foto">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Fasilitas</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="edit_nama_fasilitas" class="block text-sm font-medium text-gray-700">Nama Fasilitas</label>
                                <input type="text" name="nama_fasilitas" id="edit_nama_fasilitas" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_foto" class="block text-sm font-medium text-gray-700">Ganti Foto (Opsional)</label>
                                <img id="edit_current_foto_preview" src="" alt="Foto Lama" class="h-16 w-16 object-cover rounded mb-2">
                                <input type="file" name="foto" id="edit_foto" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-500 file:text-white hover:file:bg-gray-600">
                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengganti foto.</p>
                            </div>
                            <div>
                                <label for="edit_deskripsi_fasilitas" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea name="deskripsi" id="edit_deskripsi_fasilitas" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditFasilitasModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="addGaleriModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=galeri" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_galeri">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Tambah Foto Galeri Baru</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="nama_foto" class="block text-sm font-medium text-gray-700">Nama Foto/Kegiatan</label>
                                <input type="text" name="nama_foto" id="nama_foto" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="file_foto" class="block text-sm font-medium text-gray-700">File Foto (Wajib)</label>
                                <input type="file" name="file_foto" id="file_foto" required accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                            </div>
                            <div>
                                <label for="deskripsi_galeri" class="block text-sm font-medium text-gray-700">Deskripsi/Keterangan</label>
                                <textarea name="deskripsi" id="deskripsi_galeri" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
                            </div>
                            <div>
                                <label for="author_galeri" class="block text-sm font-medium text-gray-700">Author</label>
                                <select name="id_anggota" id="author_galeri" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                                    <?php foreach ($anggota_list as $anggota): ?>
                                        <option value="<?php echo $anggota['id_anggota']; ?>" <?php echo $anggota['id_anggota'] == $admin_user_id ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($anggota['nama_gelar']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" onclick="closeAddGaleriModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editGaleriModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=galeri" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_galeri">
                    <input type="hidden" name="id_foto" id="edit_id_foto">
                    <input type="hidden" name="current_file_foto" id="edit_current_file_foto">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Foto Galeri</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="edit_nama_foto" class="block text-sm font-medium text-gray-700">Nama Foto/Kegiatan</label>
                                <input type="text" name="nama_foto" id="edit_nama_foto" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_file_foto" class="block text-sm font-medium text-gray-700">Ganti File Foto (Opsional)</label>
                                <img id="edit_current_file_foto_preview" src="" alt="Foto Lama" class="h-16 w-16 object-cover rounded mb-2">
                                <input type="file" name="file_foto" id="edit_file_foto" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-500 file:text-white hover:file:bg-gray-600">
                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengganti file.</p>
                            </div>
                            <div>
                                <label for="edit_deskripsi_galeri" class="block text-sm font-medium text-gray-700">Deskripsi/Keterangan</label>
                                <textarea name="deskripsi" id="edit_deskripsi_galeri" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
                            </div>
                            <div>
                                <label for="edit_author_galeri" class="block text-sm font-medium text-gray-700">Author</label>
                                <select name="id_anggota" id="edit_author_galeri" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                                    <?php foreach ($anggota_list as $anggota): ?>
                                        <option value="<?php echo $anggota['id_anggota']; ?>">
                                            <?php echo htmlspecialchars($anggota['nama_gelar']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditGaleriModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="addPublikasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=publikasi" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_publikasi">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Tambah Publikasi Baru</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="judul_publikasi" class="block text-sm font-medium text-gray-700">Judul Publikasi</label>
                                <input type="text" name="judul" id="judul_publikasi" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="penulis" class="block text-sm font-medium text-gray-700">Penulis (Opsional)</label>
                                <input type="text" name="penulis" id="penulis" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="tanggal_terbit" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                                <input type="date" name="tanggal_terbit" id="tanggal_terbit" value="<?php echo date('Y-m-d'); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="file_publikasi" class="block text-sm font-medium text-gray-700">File Publikasi (Wajib, PDF/Doc)</label>
                                <input type="file" name="file_publikasi" id="file_publikasi" required accept=".pdf,.doc,.docx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                            </div>
                            <div>
                                <label for="deskripsi_publikasi" class="block text-sm font-medium text-gray-700">Deskripsi/Abstrak</label>
                                <textarea name="deskripsi" id="deskripsi_publikasi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" onclick="closeAddPublikasiModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editPublikasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=publikasi" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_publikasi">
                    <input type="hidden" name="id_publikasi" id="edit_id_publikasi">
                    <input type="hidden" name="current_file_publikasi" id="edit_current_file_publikasi">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Publikasi</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="edit_judul_publikasi" class="block text-sm font-medium text-gray-700">Judul Publikasi</label>
                                <input type="text" name="judul" id="edit_judul_publikasi" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_penulis" class="block text-sm font-medium text-gray-700">Penulis (Opsional)</label>
                                <input type="text" name="penulis" id="edit_penulis" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_tanggal_terbit" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                                <input type="date" name="tanggal_terbit" id="edit_tanggal_terbit" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_file_publikasi" class="block text-sm font-medium text-gray-700">Ganti File Publikasi (Opsional)</label>
                                <p id="edit_current_file_publikasi_info" class="text-sm text-gray-500 mb-2">File saat ini: -</p>
                                <input type="file" name="file_publikasi" id="edit_file_publikasi" accept=".pdf,.doc,.docx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-500 file:text-white hover:file:bg-gray-600">
                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengganti file.</p>
                            </div>
                            <div>
                                <label for="edit_deskripsi_publikasi" class="block text-sm font-medium text-gray-700">Deskripsi/Abstrak</label>
                                <textarea name="deskripsi" id="edit_deskripsi_publikasi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditPublikasiModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="addAgendaModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=agenda" method="POST">
                    <input type="hidden" name="action" value="add_agenda">
                    <input type="hidden" name="id_anggota" value="<?php echo $admin_user_id; ?>"> <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Tambah Agenda Baru</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="nama_agenda" class="block text-sm font-medium text-gray-700">Nama/Judul Agenda</label>
                                <input type="text" name="nama_agenda" id="nama_agenda" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="tgl_agenda" class="block text-sm font-medium text-gray-700">Tanggal Agenda</label>
                                <input type="date" name="tgl_agenda" id="tgl_agenda" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="link_agenda" class="block text-sm font-medium text-gray-700">Link Zoom/Google Meet/Website (Opsional)</label>
                                <input type="url" name="link_agenda" id="link_agenda" placeholder="https://..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" onclick="closeAddAgendaModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editAgendaModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=agenda" method="POST">
                    <input type="hidden" name="action" value="edit_agenda">
                    <input type="hidden" name="id_agenda" id="edit_id_agenda">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Agenda</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="edit_nama_agenda" class="block text-sm font-medium text-gray-700">Nama/Judul Agenda</label>
                                <input type="text" name="nama_agenda" id="edit_nama_agenda" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_tgl_agenda" class="block text-sm font-medium text-gray-700">Tanggal Agenda</label>
                                <input type="date" name="tgl_agenda" id="edit_tgl_agenda" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_link_agenda" class="block text-sm font-medium text-gray-700">Link Zoom/Google Meet/Website (Opsional)</label>
                                <input type="url" name="link_agenda" id="edit_link_agenda" placeholder="https://..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditAgendaModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="addAnggotaModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=anggota" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_anggota">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Tambah Anggota Baru</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="nama_gelar" class="block text-sm font-medium text-gray-700">Nama & Gelar</label>
                                <input type="text" name="nama_gelar" id="nama_gelar" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
                                <input type="text" name="jabatan" id="jabatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="no_telp" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                <input type="text" name="no_telp" id="no_telp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="bidang_keahlian" class="block text-sm font-medium text-gray-700">Bidang Keahlian</label>
                                <textarea name="bidang_keahlian" id="bidang_keahlian" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
                            </div>
                            <div>
                                <label for="foto_anggota" class="block text-sm font-medium text-gray-700">Foto Anggota (Wajib)</label>
                                <input type="file" name="foto" id="foto_anggota" required accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" onclick="closeAddAnggotaModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editAnggotaModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=anggota" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_anggota">
                    <input type="hidden" name="id_anggota" id="edit_id_anggota">
                    <input type="hidden" name="current_foto" id="edit_current_foto">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Anggota</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="edit_nama_gelar" class="block text-sm font-medium text-gray-700">Nama & Gelar</label>
                                <input type="text" name="nama_gelar" id="edit_nama_gelar" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
                                <input type="text" name="jabatan" id="edit_jabatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="edit_email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_no_telp" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                <input type="text" name="no_telp" id="edit_no_telp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_bidang_keahlian" class="block text-sm font-medium text-gray-700">Bidang Keahlian</label>
                                <textarea name="bidang_keahlian" id="edit_bidang_keahlian" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
                            </div>
                            <div>
                                <label for="edit_foto" class="block text-sm font-medium text-gray-700">Ganti Foto (Opsional)</label>
                                <img id="edit_current_foto_preview" src="" alt="Foto Lama" class="h-16 w-16 object-cover rounded-full mb-2">
                                <input type="file" name="foto" id="edit_foto" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-500 file:text-white hover:file:bg-gray-600">
                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengganti foto.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditAnggotaModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="addPengumumanModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=pengumuman" method="POST">
                    <input type="hidden" name="action" value="add_pengumuman">
                    <input type="hidden" name="author" value="<?php echo $admin_user_id; ?>"> <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modal-title">Tambah Pengumuman Baru</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="judul_pengumuman" class="block text-sm font-medium text-gray-700">Judul Pengumuman</label>
                                <input type="text" name="judul" id="judul_pengumuman" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal Posting</label>
                                <input type="date" name="tanggal" id="tanggal" value="<?php echo date('Y-m-d'); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="informasi" class="block text-sm font-medium text-gray-700">Isi Pengumuman</label>
                                <textarea name="informasi" id="informasi" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" onclick="closeAddPengumumanModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editPengumumanModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="admin-dashboard.php?page=pengumuman" method="POST">
                    <input type="hidden" name="action" value="edit_pengumuman">
                    <input type="hidden" name="id_pengumuman" id="edit_id_pengumuman">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Pengumuman</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="edit_judul_pengumuman" class="block text-sm font-medium text-gray-700">Judul Pengumuman</label>
                                <input type="text" name="judul" id="edit_judul_pengumuman" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_tanggal_posting" class="block text-sm font-medium text-gray-700">Tanggal Posting</label>
                                <input type="date" name="tanggal" id="edit_tanggal_posting" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_isi_pengumuman" class="block text-sm font-medium text-gray-700">Isi Pengumuman</label>
                                <textarea name="informasi" id="edit_isi_pengumuman" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditPengumumanModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Set Primary color for Tailwind (Jika diperlukan untuk konsistensi)
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        'primary-dark': '#2563eb',
                    }
                }
            }
        }
        
        // --- Berita Modals ---
        function openAddNewsModal() {
            document.getElementById('addNewsModal').classList.remove('hidden');
        }

        function closeAddNewsModal() {
            document.getElementById('addNewsModal').classList.add('hidden');
        }
        
        function openEditNewsModal(button) {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const judul = row.dataset.judul;
            const informasi = row.dataset.informasi;
            const tanggal = row.dataset.tanggal;
            const gambar = row.dataset.gambar; // Path gambar

            document.getElementById('edit_id_berita').value = id;
            document.getElementById('edit_judul').value = judul;
            document.getElementById('edit_informasi').value = informasi;
            document.getElementById('edit_tanggal').value = tanggal;
            document.getElementById('edit_current_gambar').value = gambar; // Path gambar lama
            document.getElementById('edit_current_gambar_preview').src = gambar; // Preview gambar lama

            // Reset input file agar tidak terisi otomatis
            document.getElementById('edit_gambar').value = '';

            document.getElementById('editNewsModal').classList.remove('hidden');
        }

        function closeEditNewsModal() {
            document.getElementById('editNewsModal').classList.add('hidden');
        }

        function openVerifyModal(id, status) {
            document.getElementById('verify_id_berita').value = id;
            document.getElementById('verify_current_status').innerHTML = `Status saat ini: <b>${status.toUpperCase()}</b>`;
            document.getElementById('verifyModal').classList.remove('hidden');
        }

        function closeVerifyModal() {
            document.getElementById('verifyModal').classList.add('hidden');
        }
        
        function quickReject(id) {
            if (confirm('Apakah Anda yakin ingin menolak berita ini?')) {
                // Create form for quick reject
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin-dashboard.php?page=berita';
                
                // Add hidden inputs
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'verify_news';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id_berita';
                idInput.value = id;
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = 'rejected';
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                form.appendChild(statusInput);
                
                // Submit form
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // --- Fasilitas Modals ---
        function openAddFasilitasModal() {
            document.getElementById('addFasilitasModal').classList.remove('hidden');
        }

        function closeAddFasilitasModal() {
            document.getElementById('addFasilitasModal').classList.add('hidden');
        }

        function openEditFasilitasModal(button) {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const nama = row.dataset.nama_fasilitas;
            const deskripsi = row.dataset.deskripsi;
            const foto = row.dataset.foto;

            document.getElementById('edit_id_fasilitas').value = id;
            document.getElementById('edit_nama_fasilitas').value = nama;
            document.getElementById('edit_deskripsi_fasilitas').value = deskripsi;
            document.getElementById('edit_current_foto').value = foto; // Path foto lama
            document.getElementById('edit_current_foto_preview').src = foto; // Preview foto lama

            // Reset input file agar tidak terisi otomatis
            document.getElementById('edit_foto').value = '';

            document.getElementById('editFasilitasModal').classList.remove('hidden');
        }

        function closeEditFasilitasModal() {
            document.getElementById('editFasilitasModal').classList.add('hidden');
        }

        // --- Galeri Modals ---
        function openAddGaleriModal() {
            document.getElementById('addGaleriModal').classList.remove('hidden');
        }

        function closeAddGaleriModal() {
            document.getElementById('addGaleriModal').classList.add('hidden');
        }

        function openEditGaleriModal(button) {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const nama_foto = row.dataset.nama_foto;
            const deskripsi = row.dataset.deskripsi;
            const file_foto = row.dataset.file_foto;
            const author = row.dataset.author;

            document.getElementById('edit_id_foto').value = id;
            document.getElementById('edit_nama_foto').value = nama_foto;
            document.getElementById('edit_deskripsi_galeri').value = deskripsi;
            document.getElementById('edit_current_file_foto').value = file_foto; // Path file lama
            document.getElementById('edit_current_file_foto_preview').src = file_foto; // Preview file lama
            
            // Set author dropdown value
            document.getElementById('edit_author_galeri').value = author;

            // Reset input file
            document.getElementById('edit_file_foto').value = '';

            document.getElementById('editGaleriModal').classList.remove('hidden');
        }

        function closeEditGaleriModal() {
            document.getElementById('editGaleriModal').classList.add('hidden');
        }
        
        // --- Publikasi Modals ---
        function openAddPublikasiModal() {
            document.getElementById('addPublikasiModal').classList.remove('hidden');
        }

        function closeAddPublikasiModal() {
            document.getElementById('addPublikasiModal').classList.add('hidden');
        }
        
        function openEditPublikasiModal(button) {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const judul = row.dataset.judul;
            const penulis = row.dataset.penulis;
            const tanggal_terbit = row.dataset.tanggal_terbit;
            const deskripsi = row.dataset.deskripsi;
            const file_publikasi = row.dataset.file_publikasi;

            document.getElementById('edit_id_publikasi').value = id;
            document.getElementById('edit_judul_publikasi').value = judul;
            document.getElementById('edit_penulis').value = penulis;
            document.getElementById('edit_tanggal_terbit').value = tanggal_terbit;
            document.getElementById('edit_deskripsi_publikasi').value = deskripsi;
            document.getElementById('edit_current_file_publikasi').value = file_publikasi;
            
            // Tampilkan nama file saat ini
            const filename = file_publikasi.substring(file_publikasi.lastIndexOf('/') + 1);
            document.getElementById('edit_current_file_publikasi_info').innerHTML = `File saat ini: <a href="${file_publikasi}" target="_blank" class="text-primary hover:text-primary-dark font-medium">${filename}</a>`;

            // Reset input file
            document.getElementById('edit_file_publikasi').value = '';

            document.getElementById('editPublikasiModal').classList.remove('hidden');
        }

        function closeEditPublikasiModal() {
            document.getElementById('editPublikasiModal').classList.add('hidden');
        }

        // --- Agenda Modals ---
        function openAddAgendaModal() {
            document.getElementById('addAgendaModal').classList.remove('hidden');
        }

        function closeAddAgendaModal() {
            document.getElementById('addAgendaModal').classList.add('hidden');
        }

        function openEditAgendaModal(button) {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const nama_agenda = row.dataset.nama_agenda;
            const tgl_agenda = row.dataset.tgl_agenda;
            const link_agenda = row.dataset.link_agenda;

            document.getElementById('edit_id_agenda').value = id;
            document.getElementById('edit_nama_agenda').value = nama_agenda;
            document.getElementById('edit_tgl_agenda').value = tgl_agenda;
            document.getElementById('edit_link_agenda').value = link_agenda;

            document.getElementById('editAgendaModal').classList.remove('hidden');
        }

        function closeEditAgendaModal() {
            document.getElementById('editAgendaModal').classList.add('hidden');
        }

        // --- Anggota Modals ---
        function openAddAnggotaModal() {
            document.getElementById('addAnggotaModal').classList.remove('hidden');
        }

        function closeAddAnggotaModal() {
            document.getElementById('addAnggotaModal').classList.add('hidden');
        }

        function openEditAnggotaModal(button) {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const nama_gelar = row.dataset.nama_gelar;
            const jabatan = row.dataset.jabatan;
            const email = row.dataset.email;
            const no_telp = row.dataset.no_telp;
            const bidang_keahlian = row.dataset.bidang_keahlian;
            const foto = row.dataset.foto;

            document.getElementById('edit_id_anggota').value = id;
            document.getElementById('edit_nama_gelar').value = nama_gelar;
            document.getElementById('edit_jabatan').value = jabatan;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_no_telp').value = no_telp;
            document.getElementById('edit_bidang_keahlian').value = bidang_keahlian;
            document.getElementById('edit_current_foto').value = foto; // Path foto lama
            document.getElementById('edit_current_foto_preview').src = foto; // Preview foto lama

            // Reset input file agar tidak terisi otomatis
            document.getElementById('edit_foto').value = '';

            document.getElementById('editAnggotaModal').classList.remove('hidden');
        }

        function closeEditAnggotaModal() {
            document.getElementById('editAnggotaModal').classList.add('hidden');
        }

        // --- Pengumuman Modals ---
        function openAddPengumumanModal() {
            document.getElementById('addPengumumanModal').classList.remove('hidden');
        }

        function closeAddPengumumanModal() {
            document.getElementById('addPengumumanModal').classList.add('hidden');
        }

        function openEditPengumumanModal(button) {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const judul = row.dataset.judul;
            const informasi = row.dataset.informasi;
            const tanggal = row.dataset.tanggal;

            document.getElementById('edit_id_pengumuman').value = id;
            document.getElementById('edit_judul_pengumuman').value = judul;
            document.getElementById('edit_isi_pengumuman').value = informasi;
            document.getElementById('edit_tanggal_posting').value = tanggal;

            document.getElementById('editPengumumanModal').classList.remove('hidden');
        }

        function closeEditPengumumanModal() {
            document.getElementById('editPengumumanModal').classList.add('hidden');
        }

        // --- Sidebar Toggle Function ---
        function toggleSidebar() {
            const body = document.body;
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.querySelector('.toggle-btn i');
            
            if (body.classList.contains('sidebar-open')) {
                // Close sidebar
                body.classList.remove('sidebar-open');
                sidebar.classList.add('sidebar-closed');
                mainContent.classList.add('main-content-shifted');
                toggleBtn.classList.remove('fa-chevron-left');
                toggleBtn.classList.add('fa-chevron-right');
            } else {
                // Open sidebar
                body.classList.add('sidebar-open');
                sidebar.classList.remove('sidebar-closed');
                mainContent.classList.remove('main-content-shifted');
                toggleBtn.classList.remove('fa-chevron-right');
                toggleBtn.classList.add('fa-chevron-left');
            }
        }
    </script>
</body>
</html>