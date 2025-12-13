<?php
// admin-dashboard.php (All-in-One: Dashboard Ringkasan + Kelola Berita + Kelola Galeri + Kelola Fasilitas + Kelola Publikasi + Kelola Agenda)

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
$admin_user_id = 1; // HARDCODED: Ganti dengan ID user yang login (untuk kolom 'author' pada tabel berita / 'created_by' pada fasilitas / 'id_anggota'/'updated_by' pada galeri / 'id_anggota' pada publikasi / 'id_anggota' pada agenda)
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
                        VALUES (:nama_foto, :deskripsi, :file_foto, :id_anggota, :updated_by)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama_foto' => $nama_foto,
                    ':deskripsi' => $deskripsi,
                    ':file_foto' => $file_foto_path_for_db,
                    ':id_anggota' => $admin_user_id, // Asumsi id_anggota = creator
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
            
            // Re-upload logic sama dengan CREATE
            $file_name = basename($_FILES['file_foto']['name']);
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $unique_name = 'galeri_' . time() . '_' . $safe_file_name;
            $target_file = $target_dir . $unique_name;

            // Lakukan proses upload file baru
            if (move_uploaded_file($_FILES['file_foto']['tmp_name'], $target_file)) {
                $file_foto_path_for_db = $target_file;

                // Opsional: Hapus foto lama di server
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
                            updated_by = :updated_by
                        WHERE id_foto = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama_foto' => $nama_foto,
                    ':deskripsi' => $deskripsi,
                    ':file_foto' => $file_foto_path_for_db,
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
// --- END: Penanganan Operasi CRUD Galeri (DELETE) ---

// --- START: Penanganan Operasi CRUD Publikasi (Hanya jika koneksi berhasil) ---
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $active_page === 'publikasi') {
    $action = $_POST['action'] ?? '';
    $target_dir = '../assets/files/publikasi/'; // Direktori Publikasi

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
            $upload_message = "Harap unggah file publikasi (PDF, dll.).";
        } else {
            $upload_ok = false;
            $upload_message = "Terjadi error saat upload file. Kode error: " . $_FILES['file_publikasi']['error'];
        }

        // 3. Simpan ke database jika upload berhasil
        if ($upload_ok) {
            try {
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
            
            // Re-upload logic sama dengan CREATE
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
                            deskripsi = :deskripsi
                        WHERE id_publikasi = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':judul' => $judul,
                    ':penulis' => $penulis,
                    ':tanggal_terbit' => $tanggal_terbit,
                    ':file' => $file_path_for_db,
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


// --- START: Penanganan Operasi CRUD Agenda (Hanya jika koneksi berhasil) ---
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $active_page === 'agenda') {
    $action = $_POST['action'] ?? '';

    // --- CREATE (Tambah Agenda Baru) ---
    if ($action === 'add_agenda') {
        $nama_agenda = trim($_POST['nama_agenda']);
        $tgl_agenda = trim($_POST['tgl_agenda']);
        $link_agenda = trim($_POST['link_agenda']); // link_agenda tidak wajib diisi

        if (empty($nama_agenda) || empty($tgl_agenda)) {
             $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Nama agenda dan Tanggal agenda wajib diisi.</div>";
        } else {
            try {
                // Skema tabel: id_agenda, nama_agenda, tgl_agenda, link_agenda, id_anggota
                $sql = "INSERT INTO agenda (nama_agenda, tgl_agenda, link_agenda, id_anggota) 
                        VALUES (:nama_agenda, :tgl_agenda, :link_agenda, :id_anggota)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama_agenda' => $nama_agenda,
                    ':tgl_agenda' => $tgl_agenda,
                    ':link_agenda' => $link_agenda,
                    ':id_anggota' => $admin_user_id
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Agenda baru berhasil ditambahkan!</div>";
            } catch (Exception $e) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal menambahkan agenda (DB Error): " . htmlspecialchars($e->getMessage()) . "</div>";
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


// --- START: Data Retrieval (READ - Diambil dari DB jika koneksi berhasil) ---
$total_news = 0;
$total_pending_news = 0;
$total_fasilitas = 0;
$total_galeri = 0;
$total_publikasi = 0;
$total_agenda = 0; // Tambahkan total agenda

if ($pdo) {
    try {
        // Query untuk dashboard ringkasan
        $sql_counts = "
            SELECT 
                (SELECT COUNT(*) FROM berita) AS total_news,
                (SELECT COUNT(*) FROM berita WHERE status = 'pending') AS total_pending_news,
                (SELECT COUNT(*) FROM fasilitas) AS total_fasilitas,
                (SELECT COUNT(*) FROM galeri) AS total_galeri,
                (SELECT COUNT(*) FROM publikasi) AS total_publikasi,
                (SELECT COUNT(*) FROM agenda) AS total_agenda -- Hitung total agenda
        ";
        $counts_result = $pdo->query($sql_counts)->fetch(PDO::FETCH_ASSOC);

        $total_news = $counts_result['total_news'];
        $total_pending_news = $counts_result['total_pending_news'];
        $total_fasilitas = $counts_result['total_fasilitas'];
        $total_galeri = $counts_result['total_galeri'];
        $total_publikasi = $counts_result['total_publikasi'];
        $total_agenda = $counts_result['total_agenda']; // Ambil nilai total agenda
    } catch (Exception $e) {
        // Biarkan count 0 jika ada error
    }
}


// --- START: Data Berita ---
$news_data = [];
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

// --- START: Data Fasilitas ---
$fasilitas_data = [];
if ($active_page === 'fasilitas' && $pdo) {
    try {
        // READ: Mengambil semua data fasilitas, join ke tabel anggota/user
        $sql = "SELECT f.id_fasilitas, f.nama_fasilitas, f.deskripsi, f.foto, a.nama_gelar AS created_by_name FROM fasilitas f LEFT JOIN anggota a ON f.created_by = a.id_anggota ORDER BY f.id_fasilitas DESC";
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
        $sql = "SELECT g.id_foto, g.nama_foto, g.deskripsi, g.file_foto, a.nama_gelar AS anggota_name 
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
        // READ: Mengambil semua data publikasi
        // Menggunakan VIEW vw_publikasi_member (join ke tabel member) jika ada, atau join ke anggota jika tidak
        $sql = "SELECT p.*, a.nama_gelar AS nama_member 
                FROM publikasi p 
                LEFT JOIN anggota a ON p.id_anggota = a.id_anggota 
                ORDER BY p.id_publikasi DESC";
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
        // READ: Mengambil semua data agenda dari database
        // Join ke tabel anggota/user untuk mendapatkan nama uploader (id_anggota)
        $sql = "SELECT 
                    a.id_agenda, 
                    a.nama_agenda, 
                    a.tgl_agenda, 
                    a.link_agenda, 
                    ag.nama_gelar AS created_by_name 
                FROM 
                    agenda a 
                LEFT JOIN 
                    anggota ag ON a.id_anggota = ag.id_anggota 
                ORDER BY 
                    a.tgl_agenda DESC";
        $stmt = $pdo->query($sql);
        $agenda_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data agenda: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
// --- END: Data Agenda ---
// --- END: Data Retrieval (READ) ---


// --- START: Render Konten Dashboard ---
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo ucwords($active_page); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --color-primary: #10B981; /* Emerald-500 */
            --color-primary-dark: #059669; /* Emerald-600 */
            --color-secondary: #3B82F6; /* Blue-500 */
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
        .z-\[100\] { z-index: 100; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="fixed top-0 left-0 h-full w-64 bg-white shadow-xl p-4 flex flex-col transition-transform duration-300 ease-in-out">
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-gray-800">Admin Panel</h2>
            <p class="text-sm text-gray-500">LDT - <?php echo $current_year; ?></p>
        </div>

        <div class="flex-grow">
            <p class="text-xs uppercase text-gray-400 mb-2">Navigasi Utama</p>
            <nav class="space-y-2">
                <ul>
                    <li><a href="admin-dashboard.php?page=dashboard" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'dashboard' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-home w-5 h-5 mr-3"></i> Dashboard</a></li>
                    <li><a href="admin-dashboard.php?page=berita" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'berita' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-newspaper w-5 h-5 mr-3"></i> Kelola Berita</a></li>
                    <li><a href="admin-dashboard.php?page=publikasi" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'publikasi' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-book-open w-5 h-5 mr-3"></i> Kelola Publikasi</a></li>
                    <li><a href="admin-dashboard.php?page=galeri" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'galeri' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-image w-5 h-5 mr-3"></i> Kelola Galeri</a></li>
                    <li><a href="admin-dashboard.php?page=fasilitas" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'fasilitas' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-building w-5 h-5 mr-3"></i> Kelola Fasilitas</a></li>
                    <li><a href="admin-dashboard.php?page=agenda" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'agenda' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-calendar-alt w-5 h-5 mr-3"></i> Kelola Agenda</a></li>
                </ul>
            </nav>
        </div>

        <div class="mt-auto pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-700 mb-2">Halo, <b><?php echo htmlspecialchars($username); ?></b></p>
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

                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-red-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Publikasi</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_publikasi; ?></p>
                        </div>
                        <i class="fas fa-book-open text-4xl text-red-500 opacity-30"></i>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Agenda</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_agenda; ?></p>
                        </div>
                        <i class="fas fa-calendar-alt text-4xl text-orange-500 opacity-30"></i>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Informasi (Snippet)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="relative px-6 py-3"> <span class="sr-only">Aksi</span> </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($news_data)): ?>
                            <?php foreach ($news_data as $news): ?>
                            <tr data-id="<?php echo $news['id_berita']; ?>" data-judul="<?php echo htmlspecialchars($news['judul']); ?>" data-informasi="<?php echo htmlspecialchars($news['informasi']); ?>" data-tanggal="<?php echo htmlspecialchars($news['tanggal']); ?>" data-gambar="<?php echo htmlspecialchars($news['gambar']); ?>">
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
                                    <?php 
                                        $status_class = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                        ][$news['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                        <?php echo ucwords($news['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                    <button onclick="openEditNewsModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="admin-dashboard.php?page=berita&action=delete&id=<?php echo $news['id_berita']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini?')" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">Belum ada data berita.</td></tr>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Fasilitas</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diunggah Oleh</th>
                            <th scope="col" class="relative px-6 py-3"> <span class="sr-only">Aksi</span> </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($fasilitas_data)): ?>
                            <?php foreach ($fasilitas_data as $fasilitas): ?>
                            <tr data-id="<?php echo $fasilitas['id_fasilitas']; ?>" data-nama="<?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?>" data-deskripsi="<?php echo htmlspecialchars($fasilitas['deskripsi']); ?>" data-foto="<?php echo htmlspecialchars($fasilitas['foto']); ?>">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $fasilitas['id_fasilitas']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img src="<?php echo htmlspecialchars($fasilitas['foto']); ?>" alt="<?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?>" class="h-10 w-10 rounded object-cover">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" style="max-width: 400px;"><?php echo htmlspecialchars(substr($fasilitas['deskripsi'], 0, 100)) . (strlen($fasilitas['deskripsi']) > 100 ? '...' : ''); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($fasilitas['created_by_name'] ?? 'Admin'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                    <button onclick="openEditFasilitasModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="admin-dashboard.php?page=fasilitas&action=delete&id=<?php echo $fasilitas['id_fasilitas']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini? Foto juga akan terhapus dari server.')" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data fasilitas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($active_page === 'galeri'): ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Galeri Foto</h1>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Foto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi (Snippet)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diunggah Oleh</th>
                            <th scope="col" class="relative px-6 py-3"> <span class="sr-only">Aksi</span> </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($galeri_data)): ?>
                            <?php foreach ($galeri_data as $galeri): ?>
                            <tr data-id="<?php echo $galeri['id_foto']; ?>" data-nama="<?php echo htmlspecialchars($galeri['nama_foto']); ?>" data-deskripsi="<?php echo htmlspecialchars($galeri['deskripsi']); ?>" data-file_foto="<?php echo htmlspecialchars($galeri['file_foto']); ?>">
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Terbit</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diunggah Oleh</th>
                            <th scope="col" class="relative px-6 py-3"> <span class="sr-only">Aksi</span> </th>
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
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-xs line-clamp-2"><?php echo htmlspecialchars($publikasi['judul']); ?></td>
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
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($agenda_data)): ?>
                            <?php foreach ($agenda_data as $agenda): ?>
                            <tr data-id="<?php echo $agenda['id_agenda']; ?>" 
                                data-nama="<?php echo htmlspecialchars($agenda['nama_agenda']); ?>" 
                                data-tgl="<?php echo htmlspecialchars($agenda['tgl_agenda']); ?>"
                                data-link="<?php echo htmlspecialchars($agenda['link_agenda']); ?>">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $agenda['id_agenda']; ?></td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-xs line-clamp-2"><?php echo htmlspecialchars($agenda['nama_agenda']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(date('d M Y', strtotime($agenda['tgl_agenda']))); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if (!empty($agenda['link_agenda'])): ?>
                                        <a href="<?php echo htmlspecialchars($agenda['link_agenda']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center">
                                            <i class="fas fa-external-link-alt mr-1"></i> Buka Link
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($agenda['created_by_name'] ?? 'Admin'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                    <button onclick="openEditAgendaModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="admin-dashboard.php?page=agenda&action=delete&id=<?php echo $agenda['id_agenda']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus agenda ini?')" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data agenda.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>

    <div id="add-agenda-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Agenda Baru</h3>
                <form id="add-agenda-form" action="admin-dashboard.php?page=agenda" method="POST" class="mt-4 text-left">
                    <input type="hidden" name="action" value="add_agenda">
                    
                    <div class="mb-4">
                        <label for="add_nama_agenda" class="block text-sm font-medium text-gray-700">Nama/Judul Agenda *</label>
                        <input type="text" id="add_nama_agenda" name="nama_agenda" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    
                    <div class="mb-4">
                        <label for="add_tgl_agenda" class="block text-sm font-medium text-gray-700">Tanggal Agenda *</label>
                        <input type="date" id="add_tgl_agenda" name="tgl_agenda" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <div class="mb-4">
                        <label for="add_link_agenda" class="block text-sm font-medium text-gray-700">Link Agenda (Opsional)</label>
                        <input type="url" id="add_link_agenda" name="link_agenda" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary" placeholder="Contoh: http://zoom.us/j/12345">
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:col-start-2 sm:text-sm">
                            Simpan Agenda
                        </button>
                        <button type="button" onclick="closeAddAgendaModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="edit-agenda-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Agenda</h3>
                <form id="edit-agenda-form" action="admin-dashboard.php?page=agenda" method="POST" class="mt-4 text-left">
                    <input type="hidden" name="action" value="edit_agenda">
                    <input type="hidden" name="id_agenda" id="edit_id_agenda">
                    
                    <div class="mb-4">
                        <label for="edit_nama_agenda" class="block text-sm font-medium text-gray-700">Nama/Judul Agenda *</label>
                        <input type="text" id="edit_nama_agenda" name="nama_agenda" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    
                    <div class="mb-4">
                        <label for="edit_tgl_agenda" class="block text-sm font-medium text-gray-700">Tanggal Agenda *</label>
                        <input type="date" id="edit_tgl_agenda" name="tgl_agenda" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <div class="mb-4">
                        <label for="edit_link_agenda" class="block text-sm font-medium text-gray-700">Link Agenda (Opsional)</label>
                        <input type="url" id="edit_link_agenda" name="link_agenda" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:col-start-2 sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditAgendaModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="add-news-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Berita Baru</h3>
                <form id="add-news-form" action="admin-dashboard.php?page=berita" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                    <input type="hidden" name="action" value="add_news">
                    <div class="mb-4">
                        <label for="add_judul" class="block text-sm font-medium text-gray-700">Judul Berita</label>
                        <input type="text" id="add_judul" name="judul" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="add_tanggal" class="block text-sm font-medium text-gray-700">Tanggal Publikasi</label>
                        <input type="date" id="add_tanggal" name="tanggal" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="add_informasi" class="block text-sm font-medium text-gray-700">Informasi/Isi Berita</label>
                        <textarea id="add_informasi" name="informasi" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="add_gambar" class="block text-sm font-medium text-gray-700">Gambar Utama (Max 2MB)</label>
                        <input type="file" id="add_gambar" name="gambar" accept="image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:col-start-2 sm:text-sm">
                            Tambah Berita
                        </button>
                        <button type="button" onclick="closeAddNewsModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Batal
                        </button>
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
                        <label for="edit_informasi" class="block text-sm font-medium text-gray-700">Informasi/Isi Berita</label>
                        <textarea id="edit_informasi" name="informasi" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Gambar Saat Ini</label>
                        <div class="mt-1 mb-2">
                            <img id="edit-current-image" class="h-16 w-16 rounded object-cover" src="" alt="Gambar Berita Saat Ini">
                        </div>
                        <label for="edit_gambar" class="block text-sm font-medium text-gray-700">Ganti Gambar (Opsional)</label>
                        <input type="file" id="edit_gambar" name="gambar" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-500 file:text-white hover:file:bg-indigo-600">
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:col-start-2 sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditNewsModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Batal
                        </button>
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
                        <label for="add_nama_fasilitas" class="block text-sm font-medium text-gray-700">Nama Fasilitas</label>
                        <input type="text" id="add_nama_fasilitas" name="nama_fasilitas" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="add_deskripsi_fasilitas" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea id="add_deskripsi_fasilitas" name="deskripsi" rows="3" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="add_foto" class="block text-sm font-medium text-gray-700">Foto Fasilitas (Max 2MB)</label>
                        <input type="file" id="add_foto" name="foto" accept="image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:col-start-2 sm:text-sm">
                            Tambah Fasilitas
                        </button>
                        <button type="button" onclick="closeAddFasilitasModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Batal
                        </button>
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
                        <textarea id="edit_deskripsi_fasilitas" name="deskripsi" rows="3" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Foto Saat Ini</label>
                        <div class="mt-1 mb-2">
                            <img id="edit-current-foto" class="h-16 w-16 rounded object-cover" src="" alt="Foto Fasilitas Saat Ini">
                        </div>
                        <label for="edit_foto" class="block text-sm font-medium text-gray-700">Ganti Foto (Opsional)</label>
                        <input type="file" id="edit_foto" name="foto" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-500 file:text-white hover:file:bg-indigo-600">
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:col-start-2 sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditFasilitasModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Batal
                        </button>
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
                        <label for="add_nama_foto" class="block text-sm font-medium text-gray-700">Nama Foto/Judul</label>
                        <input type="text" id="add_nama_foto" name="nama_foto" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="add_deskripsi_galeri" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea id="add_deskripsi_galeri" name="deskripsi" rows="3" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="add_file_foto" class="block text-sm font-medium text-gray-700">File Foto (Max 2MB)</label>
                        <input type="file" id="add_file_foto" name="file_foto" accept="image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:col-start-2 sm:text-sm">
                            Tambah Foto
                        </button>
                        <button type="button" onclick="closeAddGaleriModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Batal
                        </button>
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
                        <label for="edit_nama_foto" class="block text-sm font-medium text-gray-700">Nama Foto/Judul</label>
                        <input type="text" id="edit_nama_foto" name="nama_foto" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="edit_deskripsi_galeri" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea id="edit_deskripsi_galeri" name="deskripsi" rows="3" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Foto Saat Ini</label>
                        <div class="mt-1 mb-2">
                            <img id="edit-current-galeri-image" class="h-16 w-16 rounded object-cover" src="" alt="Foto Galeri Saat Ini">
                        </div>
                        <label for="edit_file_foto" class="block text-sm font-medium text-gray-700">Ganti File Foto (Opsional)</label>
                        <input type="file" id="edit_file_foto" name="file_foto" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-500 file:text-white hover:file:bg-indigo-600">
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:col-start-2 sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditGaleriModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Batal
                        </button>
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
                        <label for="add_judul_publikasi" class="block text-sm font-medium text-gray-700">Judul Publikasi</label>
                        <input type="text" id="add_judul_publikasi" name="judul" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="add_penulis" class="block text-sm font-medium text-gray-700">Penulis</label>
                        <input type="text" id="add_penulis" name="penulis" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="add_tanggal_terbit" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                        <input type="date" id="add_tanggal_terbit" name="tanggal_terbit" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="add_deskripsi_publikasi" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea id="add_deskripsi_publikasi" name="deskripsi" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="add_file_publikasi" class="block text-sm font-medium text-gray-700">File Publikasi (PDF/Dokumen)</label>
                        <input type="file" id="add_file_publikasi" name="file_publikasi" accept=".pdf,.doc,.docx" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:col-start-2 sm:text-sm">
                            Tambah Publikasi
                        </button>
                        <button type="button" onclick="closeAddPublikasiModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Batal
                        </button>
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
                        <label for="edit_penulis" class="block text-sm font-medium text-gray-700">Penulis</label>
                        <input type="text" id="edit_penulis" name="penulis" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="edit_tanggal_terbit" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                        <input type="date" id="edit_tanggal_terbit" name="tanggal_terbit" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="edit_deskripsi_publikasi" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea id="edit_deskripsi_publikasi" name="deskripsi" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">File Saat Ini:</label>
                        <p id="current-file-publikasi-name" class="text-sm text-gray-500 mb-2"></p>
                        <label for="edit_file_publikasi" class="block text-sm font-medium text-gray-700">Ganti File (Opsional)</label>
                        <input type="file" id="edit_file_publikasi" name="file_publikasi" accept=".pdf,.doc,.docx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-500 file:text-white hover:file:bg-indigo-600">
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:col-start-2 sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditPublikasiModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="verify-news-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Verifikasi Berita</h3>
                <p class="text-sm text-gray-500 mt-2">Anda akan mengubah status berita dengan ID <span id="verify_id_berita_display" class="font-bold"></span>.</p>
                <form action="admin-dashboard.php?page=berita" method="POST" class="mt-4">
                    <input type="hidden" name="action" value="verify_news">
                    <input type="hidden" name="id_berita" id="verify_id_berita">
                    
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="submit" name="status" value="approved" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:col-start-2 sm:text-sm">
                            <i class="fas fa-check mr-2"></i> Setujui (Approve)
                        </button>
                        <button type="submit" name="status" value="rejected" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-1 sm:text-sm">
                            <i class="fas fa-times mr-2"></i> Tolak (Reject)
                        </button>
                    </div>
                    <button type="button" onclick="closeVerifyNewsModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                        Batal
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script>
        // --- START: Fungsi Modal Agenda ---
        function openAddAgendaModal() {
            document.getElementById('add-agenda-modal').classList.remove('hidden');
            // Reset form saat dibuka
            document.getElementById('add-agenda-form').reset();
        }

        function closeAddAgendaModal() {
            document.getElementById('add-agenda-modal').classList.add('hidden');
            document.getElementById('add-agenda-form').reset();
        }

        function openEditAgendaModal(button) {
            const row = button.closest('tr');
            const id = row.getAttribute('data-id');
            const nama = row.getAttribute('data-nama');
            const tgl = row.getAttribute('data-tgl');
            const link = row.getAttribute('data-link');

            // Isi form modal
            document.getElementById('edit_id_agenda').value = id;
            document.getElementById('edit_nama_agenda').value = nama;
            document.getElementById('edit_tgl_agenda').value = tgl;
            document.getElementById('edit_link_agenda').value = link;

            // Tampilkan modal
            document.getElementById('edit-agenda-modal').classList.remove('hidden');
        }

        function closeEditAgendaModal() {
            document.getElementById('edit-agenda-modal').classList.add('hidden');
            document.getElementById('edit-agenda-modal').querySelector('form').reset();
        }
        // --- END: Fungsi Modal Agenda ---


        // --- START: Fungsi Modal Berita ---
        function openAddNewsModal() {
            document.getElementById('add-news-modal').classList.remove('hidden');
            document.getElementById('add-news-form').reset();
        }

        function closeAddNewsModal() {
            document.getElementById('add-news-modal').classList.add('hidden');
            document.getElementById('add-news-form').reset();
        }

        function openEditNewsModal(button) {
            const row = button.closest('tr');
            const id = row.getAttribute('data-id');
            const judul = row.getAttribute('data-judul');
            const informasi = row.getAttribute('data-informasi');
            const tanggal = row.getAttribute('data-tanggal');
            const gambar = row.getAttribute('data-gambar');

            // Isi form modal
            document.getElementById('edit_id_berita').value = id;
            document.getElementById('edit_judul').value = judul;
            document.getElementById('edit_informasi').value = informasi;
            document.getElementById('edit_tanggal').value = tanggal;
            document.getElementById('edit_current_gambar').value = gambar; // Simpan path gambar saat ini
            document.getElementById('edit-current-image').src = gambar;

            // Hapus nilai input file saat edit modal dibuka
            document.getElementById('edit_gambar').value = '';

            // Tampilkan modal
            document.getElementById('edit-news-modal').classList.remove('hidden');
        }

        function closeEditNewsModal() {
            document.getElementById('edit-news-modal').classList.add('hidden');
            document.getElementById('edit-news-modal').querySelector('form').reset();
        }

        function openVerifyNewsModal(button) {
            const row = button.closest('tr');
            const id = row.getAttribute('data-id');

            document.getElementById('verify_id_berita').value = id;
            document.getElementById('verify_id_berita_display').textContent = id;
            document.getElementById('verify-news-modal').classList.remove('hidden');
        }

        function closeVerifyNewsModal() {
            document.getElementById('verify-news-modal').classList.add('hidden');
        }
        // --- END: Fungsi Modal Berita ---


        // --- START: Fungsi Modal Fasilitas ---
        function openAddFasilitasModal() {
            document.getElementById('add-fasilitas-modal').classList.remove('hidden');
            document.getElementById('add-fasilitas-form').reset();
        }

        function closeAddFasilitasModal() {
            document.getElementById('add-fasilitas-modal').classList.add('hidden');
            document.getElementById('add-fasilitas-form').reset();
        }

        function openEditFasilitasModal(button) {
            const row = button.closest('tr');
            const id = row.getAttribute('data-id');
            const nama = row.getAttribute('data-nama');
            const deskripsi = row.getAttribute('data-deskripsi');
            const foto = row.getAttribute('data-foto');

            // Isi form modal
            document.getElementById('edit_id_fasilitas').value = id;
            document.getElementById('edit_nama_fasilitas').value = nama;
            document.getElementById('edit_deskripsi_fasilitas').value = deskripsi;
            document.getElementById('edit_current_foto').value = foto; // Simpan path foto saat ini
            document.getElementById('edit-current-foto').src = foto;

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
            document.getElementById('add-galeri-form').reset();
        }

        function closeAddGaleriModal() {
            document.getElementById('add-galeri-modal').classList.add('hidden');
            document.getElementById('add-galeri-form').reset();
        }

        function openEditGaleriModal(button) {
            const row = button.closest('tr');
            const id = row.getAttribute('data-id');
            const nama = row.getAttribute('data-nama');
            const deskripsi = row.getAttribute('data-deskripsi');
            const file_foto = row.getAttribute('data-file_foto');

            // Isi form modal
            document.getElementById('edit_id_foto').value = id;
            document.getElementById('edit_nama_foto').value = nama;
            document.getElementById('edit_deskripsi_galeri').value = deskripsi;
            document.getElementById('edit_current_file_foto').value = file_foto; // Simpan path file saat ini
            document.getElementById('edit-current-galeri-image').src = file_foto;

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


        // --- START: Fungsi Modal Publikasi ---
        function openAddPublikasiModal() {
            document.getElementById('add-publikasi-modal').classList.remove('hidden');
            document.getElementById('add-publikasi-form').reset();
        }

        function closeAddPublikasiModal() {
            document.getElementById('add-publikasi-modal').classList.add('hidden');
            document.getElementById('add-publikasi-form').reset();
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