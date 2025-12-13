<?php
// admin-dashboard.php (All-in-One: Dashboard Ringkasan + Kelola Berita + Kelola Galeri + Kelola Fasilitas)

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
$admin_user_id = 1; // HARDCODED: Ganti dengan ID user yang login (untuk kolom 'author' pada tabel berita / 'created_by' pada fasilitas / 'id_anggota'/'updated_by' pada galeri)
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

    // --- CREATE (Tambah Foto Galeri Baru) ---
    if ($action === 'add_galeri') {
        $nama_foto = trim($_POST['nama_foto']);
        $deskripsi = trim($_POST['deskripsi']);

        $upload_ok = true;
        $file_foto_path_for_db = '';
        $upload_message = '';
        $target_dir = '../assets/img/galeri/'; // Direktori Galeri

        if (isset($_FILES['file_foto']) && $_FILES['file_foto']['error'] == UPLOAD_ERR_OK) {

            // 1. Tentukan direktori dan nama file
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

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
                // Gunakan INSERT INTO eksplisit (sesuai pola Berita/Fasilitas)
                // Tabel galeri: id_foto, nama_foto, deskripsi, file_foto, id_anggota, updated_by
                $sql = "INSERT INTO galeri (nama_foto, deskripsi, file_foto, id_anggota, updated_by) 
                        VALUES (:nama_foto, :deskripsi, :file_foto, :id_anggota, :updated_by)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama_foto' => $nama_foto,
                    ':deskripsi' => $deskripsi,
                    ':file_foto' => $file_foto_path_for_db,
                    ':id_anggota' => $admin_user_id, // Gunakan id_anggota sebagai uploader awal
                    ':updated_by' => $admin_user_id // Inisialisasi updated_by
                ]);
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Foto galeri baru berhasil ditambahkan!</div>";
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
    if ($action === 'edit_galeri') {
        $id_foto = (int)$_POST['id_foto'];
        $nama_foto = trim($_POST['nama_foto']);
        $deskripsi = trim($_POST['deskripsi']);

        $new_file_name = $_FILES['file_foto']['name'] ?? '';
        $current_file_path = $_POST['current_file_foto']; // Path file foto lama
        $file_foto_path_for_db = $current_file_path;      // Default: gunakan foto lama
        $upload_ok = true;
        $target_dir = '../assets/img/galeri/';

        // Cek apakah ada file baru yang diupload
        if (isset($_FILES['file_foto']) && $_FILES['file_foto']['error'] == UPLOAD_ERR_OK && !empty($new_file_name)) {

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

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
                // UPDATE Galeri
                $sql = "UPDATE galeri SET 
                            nama_foto = :nama, 
                            deskripsi = :deskripsi, 
                            file_foto = :file_foto,
                            updated_by = :updated_by
                        WHERE id_foto = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama' => $nama_foto,
                    ':deskripsi' => $deskripsi,
                    ':file_foto' => $file_foto_path_for_db, // Path baru atau lama
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
// --- END: Penanganan Operasi CRUD Galeri (POST) ---


// --- DELETE (Hapus Foto Galeri - Menggunakan GET request) ---
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

// --- START: Data Fetching (READ) ---

// 4. Data Dashboard (Ringkasan)
$total_news = 0;
$total_facilities = 0;
$total_gallery = 0;
$total_members = 0;

if ($pdo) {
    try {
        // Query untuk dashboard (Total Berita)
        $stmt = $pdo->query("SELECT COUNT(*) FROM berita");
        $total_news = $stmt->fetchColumn();

        // Query untuk dashboard (Total Fasilitas)
        $stmt = $pdo->query("SELECT COUNT(*) FROM fasilitas");
        $total_facilities = $stmt->fetchColumn();

        // Query untuk dashboard (Total Galeri)
        $stmt = $pdo->query("SELECT COUNT(*) FROM galeri");
        $total_gallery = $stmt->fetchColumn();

        // Query untuk dashboard (Total Anggota)
        $stmt = $pdo->query("SELECT COUNT(*) FROM member"); // Asumsi 'member' adalah tabel anggota
        $total_members = $stmt->fetchColumn();
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data ringkasan dashboard: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// 5. Data Berita (READ - Diambil dari DB jika koneksi berhasil)
$news_data = [];
if ($active_page === 'berita' && $pdo) {
    try {
        // READ: Mengambil semua data berita dari database
        $sql = "SELECT b.id_berita, b.judul, b.gambar, b.informasi, b.tanggal, b.author, b.status, a.nama_gelar AS author_name 
                FROM berita b 
                LEFT JOIN anggota a ON b.author = a.id_anggota 
                ORDER BY b.id_berita DESC";
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
        // READ: Mengambil semua data fasilitas dari database
        $sql = "SELECT f.id_fasilitas, f.nama_fasilitas, f.deskripsi, f.foto, f.created_by, a.nama_gelar AS creator_name 
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


// 6. Penentuan Judul Halaman
$page_title = "Admin Dashboard | ";
switch ($active_page) {
    case 'berita':
        $page_title .= "Kelola Berita";
        break;
    case 'fasilitas':
        $page_title .= "Kelola Fasilitas";
        break;
    case 'galeri':
        $page_title .= "Kelola Galeri";
        break;
    case 'agenda':
        $page_title .= "Kelola Agenda";
        break;
    case 'publikasi':
        $page_title .= "Kelola Publikasi";
        break;
    case 'pengumuman':
        $page_title .= "Kelola Pengumuman";
        break;
    case 'settings':
        $page_title .= "Pengaturan Sistem";
        break;
    default:
        $page_title .= "Ringkasan";
        break;
}

// --- Akhir Logika PHP ---
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .text-primary {
            color: #00A0D6;
        }

        .border-primary {
            border-color: #00A0D6;
        }

        .bg-primary {
            background-color: #00A0D6;
        }

        .focus\:ring-primary:focus {
            --tw-ring-color: #00A0D6;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-50 antialiased">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-white shadow-xl flex-shrink-0 p-4 border-r border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900 mb-6 border-b pb-4">Admin Panel</h1>
            <nav class="space-y-2">
                <a href="admin-dashboard.php?page=dashboard" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'dashboard' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                    <i class="fas fa-home w-5 h-5 mr-3 <?php echo ($active_page === 'dashboard' ? 'text-primary' : ''); ?>"></i> Dashboard
                </a>

                <h3 class="text-xs font-semibold uppercase text-gray-400 mt-4 mb-2 px-3">Konten Web</h3>

                <a href="admin-dashboard.php?page=berita" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'berita' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                    <i class="fas fa-newspaper w-5 h-5 mr-3 <?php echo ($active_page === 'berita' ? 'text-primary' : ''); ?>"></i> Kelola Berita
                </a>

                <a href="admin-dashboard.php?page=fasilitas" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'fasilitas' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                    <i class="fas fa-tools w-5 h-5 mr-3 <?php echo ($active_page === 'fasilitas' ? 'text-primary' : ''); ?>"></i> Kelola Fasilitas
                </a>

                <a href="admin-dashboard.php?page=galeri" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'galeri' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                    <i class="fas fa-images w-5 h-5 mr-3 <?php echo ($active_page === 'galeri' ? 'text-primary' : ''); ?>"></i> Kelola Galeri
                </a>

                <a href="admin-dashboard.php?page=agenda" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'agenda' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                    <i class="fas fa-calendar-alt w-5 h-5 mr-3 <?php echo ($active_page === 'agenda' ? 'text-primary' : ''); ?>"></i> Kelola Agenda
                </a>
                <a href="admin-dashboard.php?page=publikasi" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'publikasi' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                    <i class="fas fa-book-open w-5 h-5 mr-3 <?php echo ($active_page === 'publikasi' ? 'text-primary' : ''); ?>"></i> Kelola Publikasi
                </a>
                <a href="admin-dashboard.php?page=pengumuman" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'pengumuman' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                    <i class="fas fa-bullhorn w-5 h-5 mr-3 <?php echo ($active_page === 'pengumuman' ? 'text-primary' : ''); ?>"></i> Kelola Pengumuman
                </a>

                <h3 class="text-xs font-semibold uppercase text-gray-400 mt-4 mb-2 px-3">Sistem</h3>

                <a href="admin-dashboard.php?page=settings" class="flex items-center p-3 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors duration-200 <?php echo ($active_page === 'settings' ? 'bg-blue-50 text-gray-800 font-semibold' : 'text-gray-600'); ?>">
                    <i class="fas fa-cog w-5 h-5 mr-3 <?php echo ($active_page === 'settings' ? 'text-primary' : ''); ?>"></i> Pengaturan Sistem
                </a>

                <a href="logout.php" class="flex items-center p-3 rounded-lg hover:bg-red-100 text-red-600 hover:text-red-800 transition-colors duration-200 mt-4">
                    <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i> Logout
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow-sm p-4 sticky top-0 z-40 flex justify-between items-center">
                <h1 class="text-xl font-semibold text-gray-800">
                    <?php echo str_replace('Admin Dashboard | ', '', $page_title); ?>
                </h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Halo, <?php echo htmlspecialchars($username); ?></span>
                    <img class="h-10 w-10 rounded-full object-cover border-2 border-primary" src="https://via.placeholder.com/150/00A0D6/FFFFFF?text=AD" alt="Admin Avatar">
                </div>
            </header>

            <main class="flex-grow p-4 md:p-8">
                <?php if ($active_page === 'dashboard'): ?>
                    <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Ringkasan</h1>

                    <?php echo $message; // Tampilkan notifikasi koneksi DB 
                    ?>

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

                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase">Total Foto Galeri</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_gallery; ?></p>
                                </div>
                                <i class="fas fa-images text-4xl text-green-500 opacity-30"></i>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-red-500">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase">Total Anggota</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_members; ?></p>
                                </div>
                                <i class="fas fa-users text-4xl text-red-500 opacity-30"></i>
                            </div>
                        </div>
                    </div>


                <?php elseif ($active_page === 'berita'): ?>
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                        <?php echo $message; ?>
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-800">Daftar Berita</h2>
                            <button onclick="openAddNewsModal()" class="flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-plus mr-2"></i> Tambah Berita Baru
                            </button>
                        </div>

                        <div class="shadow overflow-x-auto border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider max-w-sm">Informasi</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Aksi</span>
                                        </th>
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
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php if ($news['status'] === 'approved') echo 'bg-green-100 text-green-800';
                                                                                                                                else if ($news['status'] === 'rejected') echo 'bg-red-100 text-red-800';
                                                                                                                                else echo 'bg-yellow-100 text-yellow-800'; ?>">
                                                        <?php echo ucfirst($news['status']); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                                    <button onclick="openEditNewsModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="admin-dashboard.php?page=berita&action=delete&id=<?php echo $news['id_berita']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini?')" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php if ($news['status'] === 'pending'): ?>
                                                        <form method="POST" action="admin-dashboard.php?page=berita" class="inline">
                                                            <input type="hidden" name="action" value="verify_news">
                                                            <input type="hidden" name="id_berita" value="<?php echo $news['id_berita']; ?>">
                                                            <button type="submit" name="status" value="approved" class="text-green-600 hover:text-green-900 p-2 rounded-md hover:bg-gray-100">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="submit" name="status" value="rejected" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data berita.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php elseif ($active_page === 'fasilitas'): ?>
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                        <?php echo $message; ?>
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-800">Daftar Fasilitas</h2>
                            <button onclick="openAddFasilitasModal()" class="flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-plus mr-2"></i> Tambah Fasilitas
                            </button>
                        </div>

                        <div class="shadow overflow-x-auto border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Fasilitas</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Aksi</span>
                                        </th>
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
                                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars(substr($fasilitas['deskripsi'], 0, 50)) . (strlen($fasilitas['deskripsi']) > 50 ? '...' : ''); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($fasilitas['creator_name'] ?? 'Admin'); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                                    <button onclick="openEditFasilitasModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="admin-dashboard.php?page=fasilitas&action=delete&id=<?php echo $fasilitas['id_fasilitas']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini?')" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data fasilitas.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php elseif ($active_page === 'galeri'): ?>
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                        <?php echo $message; ?>
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-800">Daftar Foto Galeri</h2>
                            <button onclick="openAddGaleriModal()" class="flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-plus mr-2"></i> Tambah Foto Galeri
                            </button>
                        </div>

                        <div class="shadow overflow-x-auto border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Foto</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
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
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data galeri.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php elseif ($active_page === 'agenda'): ?>
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Daftar Agenda</h2>
                        <p class="text-gray-600">Fitur Kelola Agenda akan ditambahkan kemudian.</p>
                    </div>

                <?php elseif ($active_page === 'publikasi'): ?>
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Daftar Publikasi</h2>
                        <p class="text-gray-600">Fitur Kelola Publikasi akan ditambahkan kemudian.</p>
                    </div>

                <?php elseif ($active_page === 'pengumuman'): ?>
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Daftar Pengumuman</h2>
                        <p class="text-gray-600">Fitur Kelola Pengumuman akan ditambahkan kemudian.</p>
                    </div>

                <?php elseif ($active_page === 'settings'): ?>
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Pengaturan Sistem</h2>
                        <form>
                            <div class="mb-4">
                                <label for="nama_lab" class="block text-sm font-medium text-gray-700">Nama Laboratorium/Institusi</label>
                                <input type="text" id="nama_lab" name="nama_lab" value="Laboratorium XYZ" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="email_admin" class="block text-sm font-medium text-gray-700">Email Kontak Admin</label>
                                <input type="email" id="email_admin" name="email_admin" value="admin@example.com" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-primary text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-600">Simpan Pengaturan</button>
                        </form>
                    </div>
                <?php endif; ?>

            </main>

            <footer class="p-4 text-center text-sm text-gray-500 border-t border-gray-200">
                &copy; <?php echo $current_year; ?> Admin Dashboard.
            </footer>
        </div>
    </div>

    <div id="add-news-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Berita Baru</h3>
                <form action="admin-dashboard.php?page=berita" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
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
                        <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp" required class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    </div>
                    <div class="items-center px-4 py-3">
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Tambah Berita
                        </button>
                        <button type="button" onclick="closeAddNewsModal()" class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300">
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
                        <label for="edit_informasi" class="block text-sm font-medium text-gray-700">Isi Berita Lengkap</label>
                        <textarea id="edit_informasi" name="informasi" rows="6" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Gambar Saat Ini:</label>
                        <p id="current-gambar-name" class="text-xs text-gray-500 mb-2"></p>
                    </div>
                    <div class="mb-4">
                        <label for="edit_gambar" class="block text-sm font-medium text-gray-700">Ganti Gambar Utama (Biarkan kosong jika tidak diganti)</label>
                        <input type="file" id="edit_gambar" name="gambar" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    </div>
                    <div class="items-center px-4 py-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditNewsModal()" class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300">
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
                        <label for="nama_fasilitas" class="block text-sm font-medium text-gray-700">Nama Fasilitas</label>
                        <input type="text" id="nama_fasilitas" name="nama_fasilitas" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="foto" class="block text-sm font-medium text-gray-700">Upload Foto Fasilitas</label>
                        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" required class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    </div>
                    <div class="items-center px-4 py-3">
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"> Tambah Fasilitas </button>
                        <button type="button" onclick="closeAddFasilitasModal()" class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300"> Batal </button>
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
                        <label for="edit_foto_fasilitas" class="block text-sm font-medium text-gray-700">Ganti Foto Fasilitas (Kosongkan jika tidak diubah)</label>
                        <p class="text-xs text-gray-500 mb-1">Foto saat ini: <span id="current-foto-name-fasilitas" class="font-semibold"></span></p>
                        <input type="file" id="edit_foto_fasilitas" name="foto" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    </div>

                    <div class="items-center px-4 py-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"> Simpan Perubahan </button>
                        <button type="button" onclick="closeEditFasilitasModal()" class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300"> Batal </button>
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
                        <label for="deskripsi_galeri" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea id="deskripsi_galeri" name="deskripsi" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="file_foto" class="block text-sm font-medium text-gray-700">Upload File Foto</label>
                        <input type="file" id="file_foto" name="file_foto" accept="image/jpeg,image/png,image/webp" required class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    </div>
                    <div class="items-center px-4 py-3">
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"> Tambah Foto </button>
                        <button type="button" onclick="closeAddGaleriModal()" class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300"> Batal </button>
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
                        <label for="edit_deskripsi_galeri" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea id="edit_deskripsi_galeri" name="deskripsi" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="edit_file_foto" class="block text-sm font-medium text-gray-700">Ganti File Foto (Kosongkan jika tidak diubah)</label>
                        <p class="text-xs text-gray-500 mb-1">File saat ini: <span id="current-file-foto-name" class="font-semibold"></span></p>
                        <input type="file" id="edit_file_foto" name="file_foto" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    </div>

                    <div class="items-center px-4 py-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"> Simpan Perubahan </button>
                        <button type="button" onclick="closeEditGaleriModal()" class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300"> Batal </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // --- START: Fungsi Modal Berita (Ditambahkan) ---
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
            const filename = gambar.substring(gambar.lastIndexOf('/') + 1); // Ambil nama file saja

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

        // ... (Kode JS Modal Berita yang sudah ada) ...

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
            document.getElementById('current-foto-name-fasilitas').textContent = filename;

            // Hapus nilai input file saat edit modal dibuka
            document.getElementById('edit_foto_fasilitas').value = '';

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
    </script>
</body>

</html>