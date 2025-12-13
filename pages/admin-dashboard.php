<?php
// admin-dashboard.php (All-in-One: Dashboard Ringkasan + Kelola Berita + Kelola Galeri + Kelola Fasilitas + Kelola Publikasi + Kelola Agenda + Kelola Anggota)

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
                    ':id_anggota' => $admin_user_id, // Gunakan admin_user_id sebagai id_anggota (sesuai template)
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

        $new_file_foto_name = $_FILES['file_foto']['name'] ?? '';
        $current_file_foto_path = $_POST['current_file_foto'];
        $file_foto_path_for_db = $current_file_foto_path;
        $upload_ok = true;

        // Cek apakah ada file baru yang diupload
        if (isset($_FILES['file_foto']) && $_FILES['file_foto']['error'] == UPLOAD_ERR_OK && !empty($new_file_foto_name)) {

            $file_name = basename($_FILES['file_foto']['name']);
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $unique_name = 'galeri_' . time() . '_' . $safe_file_name;
            $target_file = $target_dir . $unique_name;

            // Lakukan proses upload file baru
            if (move_uploaded_file($_FILES['file_foto']['tmp_name'], $target_file)) {
                $file_foto_path_for_db = $target_file;

                // Opsional: Hapus file lama di server
                if ($current_file_foto_path && file_exists($current_file_foto_path)) {
                    @unlink($current_file_foto_path);
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
    $target_dir = '../assets/files/publikasi/';

    // Pastikan direktori ada
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal membuat folder upload: {$target_dir}. Pastikan izin tulis sudah diatur.</div>";
            $pdo = null;
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
                $upload_message = "Gagal mengupload file publikasi. Pastikan folder '{$target_dir}' memiliki izin tulis (0777).";
            }
        } else if ($_FILES['file_publikasi']['error'] === UPLOAD_ERR_NO_FILE) {
            $upload_ok = false;
            $upload_message = "Harap unggah file publikasi (PDF/Dokumen) untuk publikasi ini.";
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
        $current_file_path = $_POST['current_file_publikasi'];
        $file_path_for_db = $current_file_path;
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
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupload file publikasi baru. Perubahan DB dibatalkan.</div>";
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


// --- START: Penanganan Operasi CRUD Agenda (Hanya jika koneksi berhasil) ---
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $active_page === 'agenda') {
    $action = $_POST['action'] ?? '';

    // --- CREATE (Tambah Agenda Baru) ---
    if ($action === 'add_agenda') {
        $nama_agenda = trim($_POST['nama_agenda']);
        $tgl_agenda = trim($_POST['tgl_agenda']);
        $link_agenda = trim($_POST['link_agenda']);

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
                $sql = "UPDATE agenda SET nama_agenda = :nama_agenda, tgl_agenda = :tgl_agenda, link_agenda = :link_agenda WHERE id_agenda = :id";
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
    $target_dir = '../assets/img/anggota/'; // Direktori Anggota

    // Pastikan direktori ada
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal membuat folder upload: {$target_dir}. Pastikan izin tulis sudah diatur.</div>";
            $pdo = null;
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

        // Handle Foto Upload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {

            $file_name = basename($_FILES['foto']['name']);
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $unique_name = 'anggota_' . time() . '_' . $safe_file_name;
            $target_file = $target_dir . $unique_name;
            $foto_path_for_db = $target_file;

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $upload_ok = false;
                $upload_message = "Gagal mengupload foto. Pastikan folder '{$target_dir}' memiliki izin tulis (0777).";
            }
        } else if ($_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            $upload_ok = false;
            $upload_message = "Harap unggah foto untuk anggota ini.";
        } else {
            $upload_ok = false;
            $upload_message = "Terjadi error saat upload file. Kode error: " . $_FILES['foto']['error'];
        }

        // Simpan ke database jika upload berhasil
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
                    ':bidang_keahlian' => $bidang_keahlian,
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

        $new_foto_name = $_FILES['foto']['name'] ?? '';
        $current_foto_path = $_POST['current_foto'];
        $foto_path_for_db = $current_foto_path;
        $upload_ok = true;

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK && !empty($new_foto_name)) {

            $file_name = basename($_FILES['foto']['name']);
            $safe_file_name = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $file_name);
            $unique_name = 'anggota_' . time() . '_' . $safe_file_name;
            $target_file = $target_dir . $unique_name;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $foto_path_for_db = $target_file;
                // Hapus foto lama
                if ($current_foto_path && file_exists($current_foto_path)) {
                    @unlink($current_foto_path);
                }
            } else {
                $upload_ok = false;
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupload foto baru. Perubahan DB dibatalkan.</div>";
            }
        }

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
                    ':foto' => $foto_path_for_db,
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


// 4. Penghitungan Statistik Dashboard (Hanya jika koneksi berhasil)
$total_news = 0;
$total_pending_news = 0;
$total_fasilitas = 0;
$total_galeri = 0;
$total_publikasi = 0;
$total_agenda = 0;
$total_anggota = 0; // Tambahkan variabel Anggota

if ($pdo && $active_page === 'dashboard') {
    try {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM berita) AS total_news,
                    (SELECT COUNT(*) FROM berita WHERE status = 'pending') AS total_pending_news,
                    (SELECT COUNT(*) FROM fasilitas) AS total_fasilitas,
                    (SELECT COUNT(*) FROM galeri) AS total_galeri,
                    (SELECT COUNT(*) FROM publikasi) AS total_publikasi,
                    (SELECT COUNT(*) FROM agenda) AS total_agenda,
                    (SELECT COUNT(*) FROM anggota) AS total_anggota
                ";
        $stmt = $pdo->query($sql);
        $counts_result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Simpan hasil
        $total_news = $counts_result['total_news'];
        $total_pending_news = $counts_result['total_pending_news'];
        $total_fasilitas = $counts_result['total_fasilitas'];
        $total_galeri = $counts_result['total_galeri'];
        $total_publikasi = $counts_result['total_publikasi'];
        $total_agenda = $counts_result['total_agenda'];
        $total_anggota = $counts_result['total_anggota']; // Ambil nilai total anggota
    } catch (Exception $e) {
        // Biarkan count 0 jika ada error
    }
}

// --- START: Data Berita ---
$news_data = [];
$anggota_list = []; // List Anggota/User untuk dropdown Author
if ($pdo) {
    try {
        // Ambil semua anggota untuk dropdown Author (Berita)
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
        // READ: Mengambil semua data fasilitas, join ke tabel anggota/user
        $sql = "SELECT f.*, a.nama_gelar AS created_by_name FROM fasilitas f LEFT JOIN anggota a ON f.created_by = a.id_anggota ORDER BY f.id_fasilitas DESC";
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
        // READ: Mengambil semua data galeri, join ke tabel anggota/user
        $sql = "SELECT g.*, a.nama_gelar AS author_name FROM galeri g LEFT JOIN anggota a ON g.id_anggota = a.id_anggota ORDER BY g.id_foto DESC";
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
        $sql = "SELECT * FROM anggota ORDER BY id_anggota DESC";
        $stmt = $pdo->query($sql);
        $anggota_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data anggota: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
// --- END: Data Anggota ---


// --- Bagian HTML ---
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LDT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* Custom styles */
        .bg-primary { background-color: #3B82F6; } /* Tailwind blue-500 */
        .hover:bg-primary-dark:hover { background-color: #2563EB; } /* Tailwind blue-600 */
        .text-primary { color: #3B82F6; }

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
            <p class="text-sm text-gray-500">LDT - <?php echo $current_year; ?></p>
        </div>
        <div class="flex-grow">
            <p class="text-xs uppercase text-gray-400 mb-2">Navigasi Utama</p>
            <nav class="space-y-2">
                <ul>
                    <li><a href="admin-dashboard.php?page=dashboard" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'dashboard' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-home w-5 h-5 mr-3"></i> Dashboard</a></li>
                    <li><a href="admin-dashboard.php?page=berita" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'berita' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-newspaper w-5 h-5 mr-3"></i> Kelola Berita</a></li>
                    <li><a href="admin-dashboard.php?page=publikasi" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'publikasi' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-book w-5 h-5 mr-3"></i> Kelola Publikasi</a></li>
                    <li><a href="admin-dashboard.php?page=galeri" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'galeri' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-images w-5 h-5 mr-3"></i> Kelola Galeri</a></li>
                    <li><a href="admin-dashboard.php?page=fasilitas" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'fasilitas' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-building w-5 h-5 mr-3"></i> Kelola Fasilitas</a></li>
                    <li><a href="admin-dashboard.php?page=agenda" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'agenda' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-calendar-alt w-5 h-5 mr-3"></i> Kelola Agenda</a></li>
                    <li><a href="admin-dashboard.php?page=anggota" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'anggota' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-users w-5 h-5 mr-3"></i> Kelola Anggota</a></li>
                    <li><a href="admin-dashboard.php?page=settings" class="flex items-center p-3 rounded-lg <?php echo $active_page === 'settings' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>"><i class="fas fa-cog w-5 h-5 mr-3"></i> Pengaturan</a></li>
                </ul>
            </nav>
        </div>
        <div class="mt-4 border-t pt-4">
            <a href="logout.php" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-red-100 hover:text-red-600">
                <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i> Keluar
            </a>
        </div>
    </div>
    <div class="ml-64 p-8">
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-light text-gray-700">Selamat Datang, <?php echo htmlspecialchars($username); ?>!</h1>
            <div class="flex items-center space-x-4">
                <span class="text-gray-500"><?php echo date('l, d F Y'); ?></span>
                <img src="https://via.placeholder.com/40" alt="Avatar" class="h-10 w-10 rounded-full object-cover border-2 border-primary">
            </div>
        </header>

        <?php if ($active_page === 'dashboard'): ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Ringkasan Statistik</h1>
            
            <?php echo $message; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-6">
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
                        <i class="fas fa-book-reader text-4xl text-red-500 opacity-30"></i>
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

                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-teal-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Anggota</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_anggota; ?></p>
                        </div>
                        <i class="fas fa-users text-4xl text-teal-500 opacity-30"></i>
                    </div>
                </div>
                </div>

            <div class="mt-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Aktivitas Terbaru</h2>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
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
                                    data-author="<?php echo htmlspecialchars($news['author']); ?>"
                                    data-gambar="<?php echo htmlspecialchars($news['gambar']); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $news['id_berita']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <img src="<?php echo htmlspecialchars($news['gambar']); ?>" alt="Gambar Berita" class="h-10 w-10 rounded object-cover">
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 line-clamp-2" style="max-width: 250px;"><?php echo htmlspecialchars($news['judul']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500 text-ellipsis line-clamp-2" style="max-width: 300px;">
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
                                        <?php if ($news['status'] === 'pending'): ?>
                                            <button onclick="approveNews(<?php echo $news['id_berita']; ?>)" class="text-green-600 hover:text-green-900 p-2 rounded-md hover:bg-gray-100" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button onclick="rejectNews(<?php echo $news['id_berita']; ?>)" class="text-yellow-600 hover:text-yellow-900 p-2 rounded-md hover:bg-gray-100" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button onclick="openEditNewsModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="admin-dashboard.php?page=berita&action=delete&id=<?php echo $news['id_berita']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini? Gambar juga akan terhapus dari server.')" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100" title="Hapus">
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi (Snippet)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($fasilitas_data)): ?>
                            <?php foreach ($fasilitas_data as $fasilitas): ?>
                                <tr data-id="<?php echo $fasilitas['id_fasilitas']; ?>" 
                                    data-nama_fasilitas="<?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?>"
                                    data-deskripsi="<?php echo htmlspecialchars($fasilitas['deskripsi']); ?>"
                                    data-foto="<?php echo htmlspecialchars($fasilitas['foto']); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $fasilitas['id_fasilitas']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <img src="<?php echo htmlspecialchars($fasilitas['foto']); ?>" alt="Foto Fasilitas" class="h-10 w-10 rounded object-cover">
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
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Galeri</h1>
            
            <?php echo $message; ?>

            <div class="flex justify-end mb-6">
                <button onclick="openAddGaleriModal()" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300">
                    <i class="fas fa-plus mr-2"></i> Tambah Foto Galeri
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
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($galeri_data)): ?>
                            <?php foreach ($galeri_data as $galeri): ?>
                                <tr data-id="<?php echo $galeri['id_foto']; ?>" 
                                    data-nama_foto="<?php echo htmlspecialchars($galeri['nama_foto']); ?>"
                                    data-deskripsi="<?php echo htmlspecialchars($galeri['deskripsi']); ?>"
                                    data-file_foto="<?php echo htmlspecialchars($galeri['file_foto']); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $galeri['id_foto']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <img src="<?php echo htmlspecialchars($galeri['file_foto']); ?>" alt="Foto Galeri" class="h-10 w-10 rounded object-cover">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($galeri['nama_foto']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" style="max-width: 400px;"><?php echo htmlspecialchars(substr($galeri['deskripsi'], 0, 100)) . (strlen($galeri['deskripsi']) > 100 ? '...' : ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($galeri['author_name'] ?? 'Admin'); ?></td>
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
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 line-clamp-2" style="max-width: 250px;"><?php echo htmlspecialchars($publikasi['judul']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($publikasi['penulis']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(date('d M Y', strtotime($publikasi['tanggal_terbit']))); ?></td>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Agenda</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link/Tempat</th>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(date('d M Y', strtotime($agenda['tgl_agenda']))); ?></td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 line-clamp-2" style="max-width: 300px;"><?php echo htmlspecialchars($agenda['nama_agenda']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" style="max-width: 350px;">
                                        <?php if (!empty($agenda['link_agenda'])): ?>
                                            <a href="<?php echo htmlspecialchars($agenda['link_agenda']); ?>" target="_blank" class="text-primary hover:text-primary-dark">
                                                <i class="fas fa-link mr-1"></i> <?php echo htmlspecialchars($agenda['link_agenda']); ?>
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
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
                            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data agenda.</td></tr>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email & Telepon</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bidang Keahlian</th>
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
                                        <span class="block"><?php echo htmlspecialchars($anggota['email']); ?></span>
                                        <span class="block text-xs text-gray-400"><?php echo htmlspecialchars($anggota['no_telp']); ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" style="max-width: 300px;"><?php echo htmlspecialchars($anggota['bidang_keahlian']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                        <button onclick="openEditAnggotaModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="admin-dashboard.php?page=anggota&action=delete&id=<?php echo $anggota['id_anggota']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini? Foto juga akan terhapus dari server.')" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
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
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Tambah Berita Baru</h3>
                        <form id="add-news-form" action="admin-dashboard.php?page=berita" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                            <input type="hidden" name="action" value="add_news">
                            <div class="mb-4">
                                <label for="add_judul" class="block text-sm font-medium text-gray-700">Judul Berita</label>
                                <input type="text" id="add_judul" name="judul" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="add_tanggal" class="block text-sm font-medium text-gray-700">Tanggal Berita</label>
                                <input type="date" id="add_tanggal" name="tanggal" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="add_informasi" class="block text-sm font-medium text-gray-700">Isi Informasi/Berita</label>
                                <textarea id="add_informasi" name="informasi" rows="5" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="add_author" class="block text-sm font-medium text-gray-700">Penulis (Anggota)</label>
                                <select id="add_author" name="author" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    <option value="">-- Pilih Penulis --</option>
                                    <?php foreach ($anggota_list as $anggota): ?>
                                        <option value="<?php echo $anggota['id_anggota']; ?>"><?php echo htmlspecialchars($anggota['nama_gelar']); ?></option>
                                    <?php endforeach; ?>
                                    <option value="<?php echo $admin_user_id; ?>">Admin (Default)</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="add_gambar" class="block text-sm font-medium text-gray-700">Gambar Utama (Max 2MB)</label>
                                <input type="file" id="add_gambar" name="gambar" accept="image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan
                                </button>
                                <button type="button" onclick="closeModal('addNewsModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="editNewsModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title-edit" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-edit">Edit Berita</h3>
                        <form id="edit-news-form" action="admin-dashboard.php?page=berita" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                            <input type="hidden" name="action" value="edit_news">
                            <input type="hidden" name="id_berita" id="edit_id_berita">
                            <input type="hidden" name="current_gambar" id="edit_current_gambar">

                            <div class="mb-4">
                                <label for="edit_judul" class="block text-sm font-medium text-gray-700">Judul Berita</label>
                                <input type="text" id="edit_judul" name="judul" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="edit_tanggal" class="block text-sm font-medium text-gray-700">Tanggal Berita</label>
                                <input type="date" id="edit_tanggal" name="tanggal" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="edit_informasi" class="block text-sm font-medium text-gray-700">Isi Informasi/Berita</label>
                                <textarea id="edit_informasi" name="informasi" rows="5" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="edit_gambar" class="block text-sm font-medium text-gray-700">Ganti Gambar Utama (Opsional)</label>
                                <div class="flex items-center space-x-4 mt-2">
                                    <img id="edit_current_gambar_preview" class="h-16 w-16 rounded object-cover border" alt="Gambar Saat Ini">
                                    <input type="file" id="edit_gambar" name="gambar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengganti gambar.</p>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan Perubahan
                                </button>
                                <button type="button" onclick="closeModal('editNewsModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="addFasilitasModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
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
                                <label for="add_foto_fasilitas" class="block text-sm font-medium text-gray-700">Foto Fasilitas (Max 2MB)</label>
                                <input type="file" id="add_foto_fasilitas" name="foto" accept="image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan
                                </button>
                                <button type="button" onclick="closeModal('addFasilitasModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="editFasilitasModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title-edit" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Fasilitas</h3>
                        <form id="edit-fasilitas-form" action="admin-dashboard.php?page=fasilitas" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                            <input type="hidden" name="action" value="edit_fasilitas">
                            <input type="hidden" name="id_fasilitas" id="edit_id_fasilitas">
                            <input type="hidden" name="current_foto" id="edit_current_foto_fasilitas">

                            <div class="mb-4">
                                <label for="edit_nama_fasilitas" class="block text-sm font-medium text-gray-700">Nama Fasilitas</label>
                                <input type="text" id="edit_nama_fasilitas" name="nama_fasilitas" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="edit_deskripsi_fasilitas" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea id="edit_deskripsi_fasilitas" name="deskripsi" rows="3" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="edit_foto_fasilitas" class="block text-sm font-medium text-gray-700">Ganti Foto Fasilitas (Opsional)</label>
                                <div class="flex items-center space-x-4 mt-2">
                                    <img id="edit_current_foto_fasilitas_preview" class="h-16 w-16 rounded object-cover border" alt="Foto Saat Ini">
                                    <input type="file" id="edit_foto_fasilitas" name="foto" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengganti foto.</p>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan Perubahan
                                </button>
                                <button type="button" onclick="closeModal('editFasilitasModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="addGaleriModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Foto Galeri</h3>
                        <form id="add-galeri-form" action="admin-dashboard.php?page=galeri" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                            <input type="hidden" name="action" value="add_galeri">
                            <div class="mb-4">
                                <label for="add_nama_foto" class="block text-sm font-medium text-gray-700">Nama Foto</label>
                                <input type="text" id="add_nama_foto" name="nama_foto" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="add_deskripsi_galeri" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea id="add_deskripsi_galeri" name="deskripsi" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="add_file_foto" class="block text-sm font-medium text-gray-700">File Foto (Max 5MB)</label>
                                <input type="file" id="add_file_foto" name="file_foto" accept="image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan
                                </button>
                                <button type="button" onclick="closeModal('addGaleriModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="editGaleriModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title-edit" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Foto Galeri</h3>
                        <form id="edit-galeri-form" action="admin-dashboard.php?page=galeri" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                            <input type="hidden" name="action" value="edit_galeri">
                            <input type="hidden" name="id_foto" id="edit_id_foto">
                            <input type="hidden" name="current_file_foto" id="edit_current_file_foto">

                            <div class="mb-4">
                                <label for="edit_nama_foto" class="block text-sm font-medium text-gray-700">Nama Foto</label>
                                <input type="text" id="edit_nama_foto" name="nama_foto" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="edit_deskripsi_galeri" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea id="edit_deskripsi_galeri" name="deskripsi" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="edit_file_foto" class="block text-sm font-medium text-gray-700">Ganti File Foto (Opsional)</label>
                                <div class="flex items-center space-x-4 mt-2">
                                    <img id="edit_current_file_foto_preview" class="h-16 w-16 rounded object-cover border" alt="Foto Saat Ini">
                                    <input type="file" id="edit_file_foto" name="file_foto" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengganti file.</p>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan Perubahan
                                </button>
                                <button type="button" onclick="closeModal('editGaleriModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="addPublikasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Publikasi Baru</h3>
                        <form id="add-publikasi-form" action="admin-dashboard.php?page=publikasi" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                            <input type="hidden" name="action" value="add_publikasi">
                            <div class="mb-4">
                                <label for="add_judul_publikasi" class="block text-sm font-medium text-gray-700">Judul Publikasi</label>
                                <input type="text" id="add_judul_publikasi" name="judul" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="add_penulis_publikasi" class="block text-sm font-medium text-gray-700">Penulis</label>
                                <input type="text" id="add_penulis_publikasi" name="penulis" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="add_tanggal_terbit" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                                <input type="date" id="add_tanggal_terbit" name="tanggal_terbit" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="add_deskripsi_publikasi" class="block text-sm font-medium text-gray-700">Deskripsi/Ringkasan</label>
                                <textarea id="add_deskripsi_publikasi" name="deskripsi" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="add_file_publikasi" class="block text-sm font-medium text-gray-700">File Publikasi (PDF/Dokumen)</label>
                                <input type="file" id="add_file_publikasi" name="file_publikasi" accept=".pdf, .doc, .docx" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                                <p class="text-xs text-gray-500 mt-1">Hanya format PDF, DOC, atau DOCX yang diizinkan.</p>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan
                                </button>
                                <button type="button" onclick="closeModal('addPublikasiModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="editPublikasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title-edit" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
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
                                <label for="edit_penulis_publikasi" class="block text-sm font-medium text-gray-700">Penulis</label>
                                <input type="text" id="edit_penulis_publikasi" name="penulis" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="edit_tanggal_terbit" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                                <input type="date" id="edit_tanggal_terbit" name="tanggal_terbit" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="edit_deskripsi_publikasi" class="block text-sm font-medium text-gray-700">Deskripsi/Ringkasan</label>
                                <textarea id="edit_deskripsi_publikasi" name="deskripsi" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="edit_file_publikasi" class="block text-sm font-medium text-gray-700">Ganti File Publikasi (Opsional)</label>
                                <p id="current_file_display" class="text-sm text-gray-500 mb-2"></p>
                                <input type="file" id="edit_file_publikasi" name="file_publikasi" accept=".pdf, .doc, .docx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengganti file.</p>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan Perubahan
                                </button>
                                <button type="button" onclick="closeModal('editPublikasiModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="addAgendaModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Agenda Baru</h3>
                        <form id="add-agenda-form" action="admin-dashboard.php?page=agenda" method="POST" class="mt-4 text-left">
                            <input type="hidden" name="action" value="add_agenda">
                            <div class="mb-4">
                                <label for="add_nama_agenda" class="block text-sm font-medium text-gray-700">Nama Agenda</label>
                                <input type="text" id="add_nama_agenda" name="nama_agenda" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="add_tgl_agenda" class="block text-sm font-medium text-gray-700">Tanggal Agenda</label>
                                <input type="date" id="add_tgl_agenda" name="tgl_agenda" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="add_link_agenda" class="block text-sm font-medium text-gray-700">Link/Tempat (Opsional)</label>
                                <input type="text" id="add_link_agenda" name="link_agenda" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary" placeholder="Masukkan link zoom/lokasi fisik">
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan
                                </button>
                                <button type="button" onclick="closeModal('addAgendaModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="editAgendaModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title-edit" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Agenda</h3>
                        <form id="edit-agenda-form" action="admin-dashboard.php?page=agenda" method="POST" class="mt-4 text-left">
                            <input type="hidden" name="action" value="edit_agenda">
                            <input type="hidden" name="id_agenda" id="edit_id_agenda">

                            <div class="mb-4">
                                <label for="edit_nama_agenda" class="block text-sm font-medium text-gray-700">Nama Agenda</label>
                                <input type="text" id="edit_nama_agenda" name="nama_agenda" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="edit_tgl_agenda" class="block text-sm font-medium text-gray-700">Tanggal Agenda</label>
                                <input type="date" id="edit_tgl_agenda" name="tgl_agenda" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="edit_link_agenda" class="block text-sm font-medium text-gray-700">Link/Tempat (Opsional)</label>
                                <input type="text" id="edit_link_agenda" name="link_agenda" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary" placeholder="Masukkan link zoom/lokasi fisik">
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan Perubahan
                                </button>
                                <button type="button" onclick="closeModal('editAgendaModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="addAnggotaModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Tambah Anggota Baru</h3>
                        <form id="add-anggota-form" action="admin-dashboard.php?page=anggota" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                            <input type="hidden" name="action" value="add_anggota">
                            <div class="mb-4">
                                <label for="add_nama_gelar" class="block text-sm font-medium text-gray-700">Nama & Gelar</label>
                                <input type="text" id="add_nama_gelar" name="nama_gelar" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="add_jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
                                <input type="text" id="add_jabatan" name="jabatan" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="add_email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="add_email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="add_no_telp" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                <input type="text" id="add_no_telp" name="no_telp" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="add_bidang_keahlian" class="block text-sm font-medium text-gray-700">Bidang Keahlian</label>
                                <textarea id="add_bidang_keahlian" name="bidang_keahlian" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="add_foto" class="block text-sm font-medium text-gray-700">Foto Anggota (Max 2MB)</label>
                                <input type="file" id="add_foto" name="foto" accept="image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan
                                </button>
                                <button type="button" onclick="closeModal('addAnggotaModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="editAnggotaModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title-edit" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-edit">Edit Anggota</h3>
                        <form id="edit-anggota-form" action="admin-dashboard.php?page=anggota" method="POST" enctype="multipart/form-data" class="mt-4 text-left">
                            <input type="hidden" name="action" value="edit_anggota">
                            <input type="hidden" name="id_anggota" id="edit_id_anggota">
                            <input type="hidden" name="current_foto" id="edit_current_foto">

                            <div class="mb-4">
                                <label for="edit_nama_gelar" class="block text-sm font-medium text-gray-700">Nama & Gelar</label>
                                <input type="text" id="edit_nama_gelar" name="nama_gelar" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="edit_jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
                                <input type="text" id="edit_jabatan" name="jabatan" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="edit_email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="edit_email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="edit_no_telp" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                <input type="text" id="edit_no_telp" name="no_telp" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label for="edit_bidang_keahlian" class="block text-sm font-medium text-gray-700">Bidang Keahlian</label>
                                <textarea id="edit_bidang_keahlian" name="bidang_keahlian" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="edit_foto" class="block text-sm font-medium text-gray-700">Ganti Foto Anggota (Opsional)</label>
                                <div class="flex items-center space-x-4 mt-2">
                                    <img id="edit_current_foto_preview" class="h-16 w-16 rounded-full object-cover border" alt="Foto Saat Ini">
                                    <input type="file" id="edit_foto" name="foto" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengganti foto.</p>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan Perubahan
                                </button>
                                <button type="button" onclick="closeModal('editAnggotaModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="settingsModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        </div>
    <script>
        // Fungsi umum untuk menutup modal
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        // --- START: Fungsi Modal Berita ---
        function openAddNewsModal() {
            document.getElementById('addNewsModal').classList.remove('hidden');
        }

        function openEditNewsModal(button) {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const judul = row.dataset.judul;
            const informasi = row.dataset.informasi;
            const tanggal = row.dataset.tanggal;
            const gambar = row.dataset.gambar;
            // const author = row.dataset.author; // Tidak perlu diubah di edit form, hanya untuk ditampilkan di tabel

            document.getElementById('edit_id_berita').value = id;
            document.getElementById('edit_judul').value = judul;
            document.getElementById('edit_informasi').value = informasi;
            document.getElementById('edit_tanggal').value = tanggal;
            document.getElementById('edit_current_gambar').value = gambar;
            document.getElementById('edit_current_gambar_preview').src = gambar;
            // Reset input file agar tidak terisi otomatis
            document.getElementById('edit_gambar').value = '';

            document.getElementById('editNewsModal').classList.remove('hidden');
        }

        function approveNews(id_berita) {
            if (confirm('Apakah Anda yakin ingin menyetujui berita ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin-dashboard.php?page=berita';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'verify_news';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id_berita';
                idInput.value = id_berita;
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = 'approved';
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                form.appendChild(statusInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        function rejectNews(id_berita) {
            if (confirm('Apakah Anda yakin ingin menolak berita ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin-dashboard.php?page=berita';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'verify_news';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id_berita';
                idInput.value = id_berita;
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = 'rejected';
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                form.appendChild(statusInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        // --- END: Fungsi Modal Berita ---

        // --- START: Fungsi Modal Fasilitas ---
        function openAddFasilitasModal() {
            document.getElementById('addFasilitasModal').classList.remove('hidden');
        }

        function openEditFasilitasModal(button) {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const nama_fasilitas = row.dataset.nama_fasilitas;
            const deskripsi = row.dataset.deskripsi;
            const foto = row.dataset.foto;

            document.getElementById('edit_id_fasilitas').value = id;
            document.getElementById('edit_nama_fasilitas').value = nama_fasilitas;
            document.getElementById('edit_deskripsi_fasilitas').value = deskripsi;
            document.getElementById('edit_current_foto_fasilitas').value = foto;
            document.getElementById('edit_current_foto_fasilitas_preview').src = foto;
            document.getElementById('edit_foto_fasilitas').value = '';

            document.getElementById('editFasilitasModal').classList.remove('hidden');
        }
        // --- END: Fungsi Modal Fasilitas ---

        // --- START: Fungsi Modal Galeri ---
        function openAddGaleriModal() {
            document.getElementById('addGaleriModal').classList.remove('hidden');
        }

        function openEditGaleriModal(button) {
            const row = button.closest('tr');
            const id = row.dataset.id;
            const nama_foto = row.dataset.nama_foto;
            const deskripsi = row.dataset.deskripsi;
            const file_foto = row.dataset.file_foto;

            document.getElementById('edit_id_foto').value = id;
            document.getElementById('edit_nama_foto').value = nama_foto;
            document.getElementById('edit_deskripsi_galeri').value = deskripsi;
            document.getElementById('edit_current_file_foto').value = file_foto;
            document.getElementById('edit_current_file_foto_preview').src = file_foto;
            document.getElementById('edit_file_foto').value = '';

            document.getElementById('editGaleriModal').classList.remove('hidden');
        }
        // --- END: Fungsi Modal Galeri ---

        // --- START: Fungsi Modal Publikasi ---
        function openAddPublikasiModal() {
            document.getElementById('addPublikasiModal').classList.remove('hidden');
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
            document.getElementById('edit_penulis_publikasi').value = penulis;
            document.getElementById('edit_tanggal_terbit').value = tanggal_terbit;
            document.getElementById('edit_deskripsi_publikasi').value = deskripsi;
            document.getElementById('edit_current_file_publikasi').value = file_publikasi;

            // Tampilkan nama file yang sedang aktif
            const filename = file_publikasi.substring(file_publikasi.lastIndexOf('/') + 1);
            document.getElementById('current_file_display').innerHTML = `File saat ini: <a href="${file_publikasi}" target="_blank" class="text-primary hover:text-primary-dark font-medium">${filename}</a>`;
            
            // Reset input file agar tidak terisi otomatis
            document.getElementById('edit_file_publikasi').value = '';

            document.getElementById('editPublikasiModal').classList.remove('hidden');
        }
        // --- END: Fungsi Modal Publikasi ---

        // --- START: Fungsi Modal Agenda ---
        function openAddAgendaModal() {
            document.getElementById('addAgendaModal').classList.remove('hidden');
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
        // --- END: Fungsi Modal Agenda ---

        // --- START: Fungsi Modal Anggota ---
        function openAddAnggotaModal() {
            document.getElementById('addAnggotaModal').classList.remove('hidden');
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
        // --- END: Fungsi Modal Anggota ---
    </script>
</body>

</html>