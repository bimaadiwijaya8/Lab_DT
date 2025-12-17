<?php
// member-dashboard.php (Member Dashboard Penuh)

// --- Bagian Logika PHP Awal ---
session_start();
date_default_timezone_set('Asia/Jakarta');

// Logika Proteksi Sesi dan Role
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'member') {
    header('Location: login.php');
    exit;
}

// 1. Variabel Konfigurasi Dasar
// member-dashboard.php (sekitar baris 15-16)

// 1. Variabel Konfigurasi Dasar
$current_year = date('Y');
$username = $_SESSION['username']; 
$hardcoded_member_id = $_SESSION['user_id'];
$active_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard'; 
$message = '';

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

// --- START: Penanganan Operasi CRUD Publikasi (Hanya jika koneksi berhasil dan di halaman publikasi) ---
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
            $upload_message = "Harap unggah file publikasi (PDF/DOCX) untuk publikasi ini.";
        } else {
            $upload_ok = false;
            $upload_message = "Terjadi error saat upload file. Kode error: " . $_FILES['file_publikasi']['error'];
        }

        // 3. Simpan ke database jika upload berhasil
        if ($upload_ok) {
            try {
                $sql = "INSERT INTO publikasi (judul, penulis, tanggal_terbit, file_publikasi, deskripsi, id_member, status) 
                        VALUES (:judul, :penulis, :tanggal_terbit, :file_publikasi, :deskripsi, :id_member, :status)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':judul' => $judul,
                    ':penulis' => $penulis,
                    ':tanggal_terbit' => $tanggal_terbit,
                    ':file_publikasi' => $file_path_for_db,
                    ':deskripsi' => $deskripsi,
                    ':id_member' => $hardcoded_member_id,
                    ':status' => 'pending' // Default status for new publications
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
        $target_dir = '../assets/files/publikasi/';

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
                            file_publikasi = :file_publikasi, 
                            deskripsi = :deskripsi,
                            id_member = :id_member,
                            status = 'pending'
                        WHERE id_publikasi = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':judul' => $judul,
                    ':penulis' => $penulis,
                    ':tanggal_terbit' => $tanggal_terbit,
                    ':file_publikasi' => $file_path_for_db, // Path baru atau lama
                    ':deskripsi' => $deskripsi,
                    ':id_member' => $hardcoded_member_id,
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
    header("Location: member-dashboard.php?page=publikasi");
    exit;
}
// --- END: Penanganan Operasi CRUD Publikasi ---


// --- START: Data Member & Publikasi (READ) ---
$member_list = [];
$anggota_list = []; // Dibuat ulang dari member_list untuk kompatibilitas form lama
$publikasi_data = [];
$member_data = [];
$publikasi_count = 0;

if ($pdo) {
    // 1. Ambil Data Member yang Sedang Login (dari tabel 'member')
    try {
        // PERBAIKAN: Mengganti tabel 'anggota' menjadi 'member' dan kolom 'nama_gelar' menjadi 'nama', 'foto_profil' menjadi 'foto', dan 'id_anggota' menjadi 'id_member'.
        // DITAMBAHKAN: kolom jurusan, prodi, kelas, tahun_angkatan, no_telp, status
        $sql_member = "SELECT id_member, nama, nim, foto, jurusan, prodi, kelas, tahun_angkatan, no_telp, status, email FROM member WHERE id_member = :id";
        $stmt_member = $pdo->prepare($sql_member);
        $stmt_member->execute([':id' => $hardcoded_member_id]);
        $fetched_member = $stmt_member->fetch(PDO::FETCH_ASSOC);

        if ($fetched_member) {
            // Mapping hasil query ke kunci yang digunakan di template HTML
            $member_data = [
                'id_member' => $fetched_member['id_member'],
                'nama_gelar' => $fetched_member['nama'], // Menggunakan nama_gelar untuk konsistensi tampilan
                'nim' => $fetched_member['nim'],
                'foto_profil' => $fetched_member['foto'], // Menggunakan foto_profil untuk konsistensi tampilan
                'jurusan' => $fetched_member['jurusan'], // DATA BARU
                'prodi' => $fetched_member['prodi'], // DATA BARU
                'kelas' => $fetched_member['kelas'], // DATA BARU
                'tahun_angkatan' => $fetched_member['tahun_angkatan'], // DATA BARU
                'no_telp' => $fetched_member['no_telp'], // DATA BARU
                'status' => $fetched_member['status'], // DATA BARU
                'email' => $fetched_member['email'] // DATA BARU
            ];
            $username = $member_data['nama_gelar']; // Update username di header
        }
    } catch (Exception $e) {
        // Tampilkan pesan error spesifik jika terjadi kesalahan pada kueri member
        $message .= "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data member: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

    // 2. Ambil List Member (untuk dropdown Publikasi)
    try {
        // PERBAIKAN: Mengganti tabel 'anggota' menjadi 'member' dan kolom 'nama_gelar' menjadi 'nama'.
        $sql = "SELECT id_member, nama FROM member ORDER BY nama ASC";
        $stmt = $pdo->query($sql);
        $member_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map member_list ke format lama (id_anggota, nama_gelar) untuk kompatibilitas form/logic
        $anggota_list = array_map(function ($m) {
            return ['id_anggota' => $m['id_member'], 'nama_gelar' => $m['nama']];
        }, $member_list);
    } catch (Exception $e) {
        $message .= "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data list member: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

    // 3. Ambil Data Publikasi (Hanya untuk halaman publikasi/dashboard)
    if ($active_page === 'publikasi' || $active_page === 'dashboard') {
        try {
            // PERBAIKAN: Join ke tabel 'member' bukan 'anggota'. Asumsi: id_anggota di publikasi merujuk ke id_member.
            // Kolom: 'nama' sebagai author_name, 'jurusan' sebagai author_bidang
            $sql = "SELECT p.*, m.nama AS author_name, m.jurusan AS author_bidang FROM publikasi p LEFT JOIN member m ON p.id_member = m.id_member WHERE p.id_member = :member_id ORDER BY p.tanggal_terbit DESC, p.id_publikasi DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':member_id' => $hardcoded_member_id]);
            $publikasi_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $publikasi_count = count($publikasi_data);
        } catch (Exception $e) {
            $message .= "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengambil data publikasi: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
// --- END: Data Member & Publikasi ---

// --- Penanganan Perubahan Data Diri (Settings) ---
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $active_page === 'settings') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $new_nama = trim($_POST['nama_gelar']);
        $new_nim = trim($_POST['nim']);
        $new_kelas = trim($_POST['kelas']); // KOLOM BARU
        $new_no_telp = trim($_POST['no_telp']); // KOLOM BARU
        $new_status = trim($_POST['status']); // KOLOM BARU
        $current_photo = $_POST['current_foto_profil'];

        // --- VALIDASI TAMBAHAN ---
        if (empty($new_nama) || empty($new_nim)) {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Nama Lengkap dan NIM tidak boleh kosong. Harap periksa kembali isian Anda.</div>";
            $upload_ok = false;
        } else {
            $upload_ok = true; // Set default success
        }
        // -------------------------

        $file_path_for_db = $current_photo;
        $target_dir = '../assets/img/profile/';

        // Pastikan direktori ada
        if ($upload_ok && !is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal membuat folder profil: {$target_dir}. Pastikan izin tulis sudah diatur.</div>";
                $upload_ok = false;
            }
        }

        // --- Logika Handle Photo Upload ---
        if ($upload_ok && isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == UPLOAD_ERR_OK) {
            $file_info = $_FILES['foto_profil'];
            $max_size = 2 * 1024 * 1024; // 2MB
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

            if ($file_info['size'] > $max_size) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal upload foto: Ukuran file melebihi batas 2MB.</div>";
                $upload_ok = false;
            } elseif (!in_array(mime_content_type($file_info['tmp_name']), $allowed_types)) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal upload foto: Hanya format JPG, PNG yang diizinkan.</div>";
                $upload_ok = false;
            } else {
                // Buat nama file unik
                $ext = pathinfo($file_info['name'], PATHINFO_EXTENSION);
                $unique_name = 'profile_' . $hardcoded_member_id . '_' . time() . '.' . $ext;
                $target_file = $target_dir . $unique_name;

                // Pindahkan file ke direktori target
                if (move_uploaded_file($file_info['tmp_name'], $target_file)) {
                    $file_path_for_db = $target_file;

                    // Opsional: Hapus file lama jika bukan default
                    $default_photo = '../assets/img/user-default.png';
                    if ($current_photo && $current_photo !== $default_photo && file_exists($current_photo)) {
                        @unlink($current_photo);
                    }
                } else {
                    $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupload foto. Pastikan folder '{$target_dir}' memiliki izin tulis (0777).</div>";
                    $upload_ok = false;
                }
            }
        }
        // --- END: Logika Handle Photo Upload ---

        if ($upload_ok) {
            try {
                // PERBAIKAN: UPDATE ke tabel 'member' dan kolom 'nama', 'foto', 'nim', kelas, no_telp, status.
                // DITAMBAHKAN: kolom kelas, no_telp, status
                $sql = "UPDATE member SET 
                            nama = :nama, 
                            nim = :nim, 
                            foto = :foto,
                            kelas = :kelas,
                            no_telp = :no_telp,
                            status = :status       /* <-- KINI MENGGUNAKAN NILAI DARI FORM */
                        WHERE id_member = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama' => $new_nama,
                    ':nim' => $new_nim,
                    ':foto' => $file_path_for_db, // Path baru atau lama
                    ':kelas' => $new_kelas, // DATA BARU
                    ':no_telp' => $new_no_telp, // DATA BARU
                    ':status' => $new_status, // DATA BARU (SUDAH DIUBAH DI FORM AGAR SESUAI DB)
                    ':id' => $hardcoded_member_id // Menggunakan ID member
                ]);

                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Data diri berhasil diupdate!</div>";

                // Refresh data member setelah update
                $member_data['nama_gelar'] = $new_nama;
                $member_data['nim'] = $new_nim;
                $member_data['foto_profil'] = $file_path_for_db; // Update foto profil
                $member_data['kelas'] = $new_kelas; // DATA BARU
                $member_data['no_telp'] = $new_no_telp; // DATA BARU
                $member_data['status'] = $new_status; // DATA BARU
                $username = $new_nama;
            } catch (Exception $e) {
                // --- PERBAIKAN: DEBUGGING LEBIH DETAIL ---
                $pdo_error_info = $stmt->errorInfo();
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                            Gagal mengupdate data diri (DB Error): " . htmlspecialchars($e->getMessage()) .
                    "<br><b>Detail DB:</b> State " . htmlspecialchars($pdo_error_info[0]) .
                    ", Code " . htmlspecialchars($pdo_error_info[1]) .
                    ", Message: " . htmlspecialchars($pdo_error_info[2]) .
                    "</div>";
                // ------------------------------------------
            }
        }
    }

    // --- Penanganan Perubahan Password ---
    if ($action === 'update_password') {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // 1. Validasi Input
        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Semua field password harus diisi.</div>";
        } elseif ($new_password !== $confirm_password) {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Password baru dan konfirmasi password tidak cocok.</div>";
        } elseif (strlen($new_password) < 6) { // Contoh validasi minimal 6 karakter
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Password baru minimal 6 karakter.</div>";
        } else {
            try {
                // 2. Ambil password lama (hash) dari DB
                // ASUMSI: Ada kolom 'password' di tabel 'member'
                $sql_check = "SELECT password FROM member WHERE id_member = :id";
                $stmt_check = $pdo->prepare($sql_check);
                $stmt_check->execute([':id' => $hardcoded_member_id]);
                $user_data = $stmt_check->fetch(PDO::FETCH_ASSOC);

                if ($user_data) {
                    // 3. Verifikasi Password Lama
                    // KARENA NILAI DB ADALAH 'pass_hash_rani', KITA ASUMSIKAN ITU ADALAH PLAINTEXT/UNHASHED
                    // KITA HARUS MEMBANDINGKAN INPUT PLAINTEXT DENGAN NILAI DB PLAINTEXT.

                    $is_password_valid = (md5($old_password) === $user_data['password']); // BANDINGKAN HASH DARI INPUT DENGAN HASH DI DB

                    // Contoh menggunakan password_verify (DIANJURKAN JIKA menggunakan password_hash saat registrasi/insert)
                    // $is_password_valid = password_verify($old_password, $user_data['password']); 

                    if ($is_password_valid) {
                        // 4. Hash Password Baru (Menggunakan MD5 agar konsisten dengan implementasi awal)
                        // Pastikan password baru di-hash sebelum disimpan.
                        $hashed_new_password = md5($new_password); 

                        // 5. Update Password di DB
                        $sql_update = "UPDATE member SET password = :password WHERE id_member = :id";
                        $stmt_update = $pdo->prepare($sql_update);
                        $stmt_update->execute([
                            ':password' => $hashed_new_password,
                            ':id' => $hardcoded_member_id
                        ]);

                        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>Password berhasil diubah!</div>";
                    } else {
                        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Password lama salah.</div>";
                    }
                } else {
                    $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Error: Data pengguna tidak ditemukan.</div>";
                }
            } catch (Exception $e) {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>Gagal mengupdate password (DB Error): " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}


// --- Bagian HTML/Design Dashboard Dimulai --- 
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard - <?php echo ucfirst($active_page); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom primary color */
        .bg-primary {
            background-color: #3b82f6;
        }

        .hover\:bg-primary-dark:hover {
            background-color: #2563eb;
        }

        .text-primary {
            color: #3b82f6;
        }

        .focus\:border-primary:focus {
            border-color: #3b82f6;
        }

        .focus\:ring-primary:focus {
            --tw-ring-color: #3b82f6;
        }

        /* Custom styles for sidebar */
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

        body.modal-open {
            overflow: hidden;
        }

        /* Modal backdrop styles to prevent interaction */
        .modal-open .toggle-btn {
            pointer-events: none;
            opacity: 0.5;
        }

        /* Increase modal z-index to be higher than toggle button */
        .modal {
            z-index: 1001;
        }

        /* Text Ellipsis Helper (used for table cells) */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-gray-50 flex sidebar-open">

    <button class="toggle-btn" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
        <i class="fas fa-chevron-left text-gray-700"></i>
    </button>

    <div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100 shadow-xl border-r border-slate-800/60 px-5 py-6 flex flex-col transition-transform duration-300 ease-in-out">
        <div class="mb-8 flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-primary/20 flex items-center justify-center text-primary">
                <i class="fas fa-flask text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-white leading-snug">Member Panel</h2>
                <p class="text-xs text-slate-400">Lab Data Technologies • <?php echo $current_year; ?></p>
            </div>
        </div>

        <div class="flex-grow">
            <p class="text-[11px] font-semibold tracking-[0.16em] uppercase text-slate-500 mb-3">Navigasi Utama</p>
            <nav class="space-y-1.5">
                <ul>
                    <li><a href="member-dashboard.php?page=dashboard" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 <?php echo $active_page === 'dashboard' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white'; ?>"><i class="fas fa-chart-line w-5 h-5 mr-3 flex items-center justify-center"></i> Dashboard</a></li>

                    <li><a href="member-dashboard.php?page=publikasi" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 <?php echo $active_page === 'publikasi' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white'; ?>"><i class="fas fa-book-open w-5 h-5 mr-3 flex items-center justify-center"></i> Kelola Publikasi</a></li>

                    <li><a href="member-dashboard.php?page=settings" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 <?php echo $active_page === 'settings' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white'; ?>"><i class="fas fa-cog w-5 h-5 mr-3 flex items-center justify-center"></i> Settings</a></li>

                    <li><a href="member-dashboard.php?page=panduan" class="mt-4 flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 <?php echo $active_page === 'panduan' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white'; ?>"><i class="fas fa-question-circle w-5 h-5 mr-3 flex items-center justify-center"></i> Panduan Member</a></li>
                </ul>
            </nav>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-800/60">
            <a href="../assets/php/logout.php" class="flex items-center px-3 py-2 rounded-lg text-red-300 hover:bg-red-500/10 hover:text-red-100 text-sm font-medium transition-colors duration-150">
                <i class="fas fa-sign-out-alt w-5 h-5 mr-3 flex items-center justify-center"></i> Logout (<?php echo htmlspecialchars($username); ?>)
            </a>
        </div>
    </div>

    <div id="mainContent" class="ml-64 p-6 md:p-10 transition-all duration-300 ease-in-out w-full">
        <header class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <p class="flex items-center gap-2 text-sm text-gray-500">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <i class="fas fa-shield-alt text-[11px]"></i>
                    </span>
                    <span class="font-medium">Panel Member LDT</span>
                </p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900"> Selamat Datang, <?php echo htmlspecialchars($username); ?> </h1>
                <p class="mt-1 text-sm text-gray-500"> Halaman aktif: <span class="font-semibold text-gray-700"><?php echo ucfirst($active_page); ?></span> </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Tanggal</p>
                    <p class="text-sm font-medium text-gray-700"><?php echo date('d F Y H:i'); ?></p>
                </div>
                <div class="flex items-center gap-3 rounded-full bg-white/80 backdrop-blur px-3 py-2 shadow-sm border border-gray-100">
                    <img src="<?php echo htmlspecialchars($member_data['foto_profil'] ?? '../assets/img/user-default.png'); ?>" alt="Profil" class="h-8 w-8 rounded-full object-cover">
                    <span class="text-sm font-semibold text-gray-800"><?php echo htmlspecialchars($username); ?></span>
                </div>
            </div>
        </header>

        <main>
            <?php echo $message; // Tampilkan notifikasi di sini 
            ?>

            <?php
            // --- START: TAMPILAN KONTEN PER HALAMAN ---

            if ($active_page === 'dashboard'):
                // ----------------------------------------------------
                // KONTEN DASHBOARD (Data Diri, Publikasi Count, Activity)
                // ----------------------------------------------------
            ?>
                <h1 class="text-3xl font-bold text-gray-800 mb-6">Ringkasan Dashboard</h1>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-primary">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-700">Data Diri</h2>
                            <i class="fas fa-user-circle text-2xl text-primary/70"></i>
                        </div>
                        <div class="mt-4 text-center">
                            <img src="<?php echo htmlspecialchars($member_data['foto_profil'] ?? '../assets/img/user-default.png'); ?>" alt="Foto Profil" class="h-20 w-20 rounded-full object-cover mx-auto mb-3 border-2 border-primary p-0.5">
                            <p class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($member_data['nama_gelar'] ?? 'N/A'); ?></p>
                            <p class="text-sm text-gray-500">NIM: <?php echo htmlspecialchars($member_data['nim'] ?? 'N/A'); ?></p>
                            <a href="member-dashboard.php?page=settings" class="mt-3 inline-block text-xs font-medium text-primary hover:text-blue-700">Edit Profil</a>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-700">Total Publikasi</h2>
                            <i class="fas fa-book text-2xl text-green-500/70"></i>
                        </div>
                        <p class="mt-4 text-5xl font-extrabold text-green-600"><?php echo $publikasi_count; ?></p>
                        <p class="text-sm text-gray-500">Dokumen yang telah diunggah.</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-700">Aktivitas Terakhir</h2>
                            <i class="fas fa-history text-2xl text-yellow-500/70"></i>
                        </div>
                        <div class="mt-4 space-y-2">
                            <?php if (!empty($publikasi_data)): 
                                $latest_status = $publikasi_data[0]['status'] ?? 'pending';
                            ?>
                                <p class="text-sm text-gray-700 truncate">Publikasi terbaru: **<?php echo htmlspecialchars($publikasi_data[0]['judul']); ?>**</p>
                                <p class="text-xs text-gray-500">Status: <span class="font-medium text-<?php echo $latest_status === 'approved' ? 'green-500' : ($latest_status === 'rejected' ? 'red-500' : 'yellow-500'); ?>"><?php echo ucfirst($latest_status); ?></span></p>
                                <p class="text-xs text-gray-500">Tanggal: <?php echo isset($publikasi_data[0]['created_at']) ? date('d M Y', strtotime($publikasi_data[0]['created_at'])) : (isset($publikasi_data[0]['tanggal_terbit']) ? date('d M Y', strtotime($publikasi_data[0]['tanggal_terbit'])) : '-'); ?></p>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">Belum ada aktivitas publikasi.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php elseif ($active_page === 'publikasi'):
                // ----------------------------------------------------
                // KONTEN KELOLA PUBLIKASI (Tabel)
                // ----------------------------------------------------
            ?>
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
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Judul</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Penulis</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Terbit</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Deskripsi (Snippet)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($publikasi_data)): ?>
                                <?php foreach ($publikasi_data as $publikasi):
                                    $status = $publikasi['status'] ?? 'pending';
                                    $status_class = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800'
                                    ][$status] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                    <tr data-id="<?php echo $publikasi['id_publikasi']; ?>"
                                        data-judul="<?php echo htmlspecialchars($publikasi['judul']); ?>"
                                        data-penulis="<?php echo htmlspecialchars($publikasi['penulis']); ?>"
                                        data-tanggal_terbit="<?php echo htmlspecialchars($publikasi['tanggal_terbit']); ?>"
                                        data-file_publikasi="<?php echo htmlspecialchars($publikasi['file_publikasi']); ?>"
                                        data-deskripsi="<?php echo htmlspecialchars($publikasi['deskripsi']); ?>"
                                        data-id_anggota="<?php echo htmlspecialchars($publikasi['id_anggota']); ?>">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 line-clamp-2"><?php echo htmlspecialchars($publikasi['judul']); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500"><?php echo htmlspecialchars($publikasi['penulis']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d F Y', strtotime($publikasi['tanggal_terbit'])); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <a href="<?php echo htmlspecialchars($publikasi['file_publikasi']); ?>" target="_blank" class="text-primary hover:text-blue-700">
                                                <i class="fas fa-file-pdf mr-1"></i> Lihat File
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 line-clamp-2 max-w-sm" style="max-width: 300px;"><?php echo htmlspecialchars($publikasi['deskripsi']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                            <button onclick="openEditPublikasiModal(this)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-gray-100">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="member-dashboard.php?page=publikasi&action=delete&id=<?php echo $publikasi['id_publikasi']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus publikasi ini? File juga akan terhapus dari server.')" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-gray-100">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada data publikasi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($active_page === 'settings'):
                // ----------------------------------------------------
                // KONTEN SETTINGS (Form Ubah Data Diri)
                // ----------------------------------------------------
            ?>
                <h1 class="text-3xl font-bold text-gray-800 mb-6">Pengaturan Akun & Data Diri</h1>

                <div class="bg-white p-8 rounded-xl shadow-lg sm:max-w-xl sm:mx-auto">
                    <form action="member-dashboard.php?page=settings" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_profile">
                        <input type="hidden" name="current_foto_profil" value="<?php echo htmlspecialchars($member_data['foto_profil'] ?? ''); ?>">

                        <div class="space-y-6">
                            <div class="flex items-center space-x-4">
                                <img src="<?php echo htmlspecialchars($member_data['foto_profil'] ?? '../assets/img/user-default.png'); ?>" alt="Foto Profil Saat Ini" class="h-24 w-24 rounded-full object-cover border-2 border-gray-300 p-0.5">
                                <div>
                                    <label for="foto_profil" class="block text-sm font-medium text-gray-700">Ganti Foto Profil</label>
                                    <input type="file" name="foto_profil" id="foto_profil" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                    <p class="mt-1 text-xs text-gray-500">Maks. 2MB, format JPG, PNG.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="nama_gelar" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                    <input type="text" name="nama_gelar" id="nama_gelar" value="<?php echo htmlspecialchars($member_data['nama_gelar'] ?? ''); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                                </div>

                                <div>
                                    <label for="nim" class="block text-sm font-medium text-gray-700">Nomor Induk Mahasiswa (NIM)</label>
                                    <input type="text" name="nim" id="nim" value="<?php echo htmlspecialchars($member_data['nim'] ?? ''); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                                </div>
                            </div>

                            <div class="border-t pt-4 border-gray-200">
                                <p class="text-sm font-semibold text-gray-800 mb-3">Informasi Akademik</p>
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <label for="jurusan" class="block text-sm font-medium text-gray-700">Jurusan</label>
                                        <input type="text" id="jurusan" value="<?php echo htmlspecialchars($member_data['jurusan'] ?? ''); ?>" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm p-2 cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label for="prodi" class="block text-sm font-medium text-gray-700">Program Studi</label>
                                        <input type="text" id="prodi" value="<?php echo htmlspecialchars($member_data['prodi'] ?? ''); ?>" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm p-2 cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label for="tahun_angkatan" class="block text-sm font-medium text-gray-700">Tahun Angkatan</label>
                                        <input type="text" id="tahun_angkatan" value="<?php echo htmlspecialchars($member_data['tahun_angkatan'] ?? ''); ?>" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm p-2 cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label for="kelas" class="block text-sm font-medium text-gray-700">Kelas</label>
                                        <input type="text" name="kelas" id="kelas" value="<?php echo htmlspecialchars($member_data['kelas'] ?? ''); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                                    </div>
                                </div>
                            </div>

                            <div class="border-t pt-4 border-gray-200">
                                <p class="text-sm font-semibold text-gray-800 mb-3">Informasi Kontak & Status</p>
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="text" id="email" value="<?php echo htmlspecialchars($member_data['email'] ?? ''); ?>" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm p-2 cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label for="no_telp" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                        <input type="text" name="no_telp" id="no_telp" value="<?php echo htmlspecialchars($member_data['no_telp'] ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                                    </div>
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700">Status Keanggotaan</label>
                                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                                            <option value="aktif" <?php echo ($member_data['status'] ?? '') === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                            <option value="luar_lab" <?php echo ($member_data['status'] ?? '') === 'luar_lab' ? 'selected' : ''; ?>>Nonaktif</option>
                                            <option value="alumni" <?php echo ($member_data['status'] ?? '') === 'alumni' ? 'selected' : ''; ?>>Lulus/Alumni</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:text-sm">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan Data Diri
                            </button>
                        </div>
                    </form>

                    <div class="border-t pt-6 mt-8 border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Ganti Password</h2>
                        <form action="member-dashboard.php?page=settings" method="POST">
                            <input type="hidden" name="action" value="update_password">
                            <div class="space-y-4">
                                <div>
                                    <label for="old_password" class="block text-sm font-medium text-gray-700">Password Lama</label>
                                    <input type="password" name="old_password" id="old_password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                                </div>
                                <div>
                                    <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                                    <input type="password" name="new_password" id="new_password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                                </div>
                                <div>
                                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                                    <input type="password" name="confirm_password" id="confirm_password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                                </div>
                            </div>
                            <div class="mt-6">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                                    <i class="fas fa-key mr-2"></i> Ubah Password
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

            <?php elseif ($active_page === 'panduan'):
                // ----------------------------------------------------
                // KONTEN PANDUAN MEMBER
                // ----------------------------------------------------
            ?>
                <h1 class="text-3xl font-bold text-gray-800 mb-6">Panduan Penggunaan Member Dashboard</h1>

                <div class="bg-white p-8 rounded-xl shadow-lg space-y-6">
                    <p class="text-lg text-gray-700">Selamat datang di Panduan Member! Halaman ini berisi instruksi tentang cara menggunakan fitur-fitur utama dashboard Anda.</p>

                    <div class="p-4 bg-gray-50 border-l-4 border-primary rounded-lg">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">1. Menu Dashboard</h2>
                        <p class="text-gray-600">Menampilkan ringkasan akun Anda:</p>
                        <ul class="list-disc list-inside ml-4 mt-2 text-sm text-gray-600 space-y-1">
                            <li>**Data Diri:** Menampilkan Nama, NIM, dan Foto Profil saat ini.</li>
                            <li>**Total Publikasi:** Jumlah dokumen publikasi yang telah Anda unggah.</li>
                            <li>**Aktivitas Terakhir:** Menampilkan judul publikasi terbaru yang Anda unggah.</li>
                        </ul>
                    </div>

                    <div class="p-4 bg-gray-50 border-l-4 border-green-500 rounded-lg">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">2. Menu Kelola Publikasi</h2>
                        <p class="text-gray-600">Tempat Anda mengelola semua dokumen publikasi:</p>
                        <ul class="list-disc list-inside ml-4 mt-2 text-sm text-gray-600 space-y-1">
                            <li>**Tambah Publikasi Baru:** Gunakan tombol ini untuk mengunggah dokumen baru (PDF/DOCX) dan mengisi detailnya (Judul, Penulis, Deskripsi).</li>
                            <li>**Edit (Ikon Pensil):** Untuk mengubah detail publikasi atau mengganti file dokumen.</li>
                            <li>**Hapus (Ikon Sampah):** Untuk menghapus publikasi dan filenya secara permanen.</li>
                            <li>**Verifikasi (Ikon Centang Ganda):** (Fungsi untuk Admin/Member dengan izin verifikasi) Untuk menyetujui, menolak, atau melihat status publikasi.</li>
                        </ul>
                    </div>

                    <div class="p-4 bg-gray-50 border-l-4 border-yellow-500 rounded-lg">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">3. Menu Settings</h2>
                        <p class="text-gray-600">Halaman ini digunakan untuk memperbarui data diri Anda:</p>
                        <ul class="list-disc list-inside ml-4 mt-2 text-sm text-gray-600 space-y-1">
                            <li>Anda dapat mengubah Nama Lengkap, NIM, **Kelas**, **Nomor Telepon**, **Status Keanggotaan**, dan mengunggah Foto Profil baru.</li>
                            <li>**Jurusan, Program Studi, dan Tahun Angkatan** tidak dapat diubah (read-only).</li>
                            <li>Anda juga dapat mengubah **Password** melalui formulir terpisah.</li>
                            <li>Setelah selesai, klik **Simpan Perubahan Data Diri** atau **Ubah Password**.</li>
                        </ul>
                    </div>

                    <p class="pt-4 text-sm text-gray-500 italic">Jika Anda mengalami kesulitan, silakan hubungi administrator sistem.</p>
                </div>

            <?php else: ?>
                <div class="p-4 text-gray-500">Memuat halaman...</div>
                <script>
                    window.location.href = 'member-dashboard.php?page=dashboard';
                </script>
            <?php endif;
            // --- END: TAMPILAN KONTEN PER HALAMAN ---
            ?>
        </main>

        <footer class="mt-10 pt-6 border-t border-gray-200 text-center text-sm text-gray-500">
            &copy; <?php echo $current_year; ?> Lab Data Technologies. All rights reserved.
        </footer>
    </div>

    <div id="addPublikasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[1001] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="member-dashboard.php?page=publikasi" method="POST" enctype="multipart/form-data"> <input type="hidden" name="action" value="add_publikasi">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Tambah Publikasi Baru</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="judul" class="block text-sm font-medium text-gray-700">Judul Publikasi</label>
                                <input type="text" name="judul" id="judul" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="penulis" class="block text-sm font-medium text-gray-700">Penulis/Organisasi</label>
                                <input type="text" name="penulis" id="penulis" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="tanggal_terbit" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                                <input type="date" name="tanggal_terbit" id="tanggal_terbit" value="<?php echo date('Y-m-d'); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="file_publikasi" class="block text-sm font-medium text-gray-700">File Publikasi (PDF/DOCX)</label>
                                <input type="file" name="file_publikasi" id="file_publikasi" accept=".pdf,.docx,.doc" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                            </div>
                            <div>
                                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi Singkat/Abstrak</label>
                                <textarea name="deskripsi" id="deskripsi" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                            Tambah Publikasi
                        </button>
                        <button type="button" onclick="closeAddPublikasiModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editPublikasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[1001] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="member-dashboard.php?page=publikasi" method="POST" enctype="multipart/form-data"> <input type="hidden" name="action" value="edit_publikasi">
                    <input type="hidden" name="id_publikasi" id="edit_id_publikasi">
                    <input type="hidden" name="current_file_publikasi" id="edit_current_file_publikasi">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Publikasi</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="edit_judul" class="block text-sm font-medium text-gray-700">Judul Publikasi</label>
                                <input type="text" name="judul" id="edit_judul" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_penulis" class="block text-sm font-medium text-gray-700">Penulis/Organisasi</label>
                                <input type="text" name="penulis" id="edit_penulis" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_tanggal_terbit" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                                <input type="date" name="tanggal_terbit" id="edit_tanggal_terbit" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                            </div>
                            <div>
                                <label for="edit_file_publikasi" class="block text-sm font-medium text-gray-700">File Publikasi Baru (Opsional)</label>
                                <input type="file" name="file_publikasi" id="edit_file_publikasi" accept=".pdf,.docx,.doc" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                                <p class="mt-1 text-xs text-gray-500">File lama: <span id="current_file_name"></span></p>
                            </div>
                            <div>
                                <label for="edit_deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi Singkat/Abstrak</label>
                                <textarea name="deskripsi" id="edit_deskripsi" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2"></textarea>
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
            
    <script>
        // Utility function
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const yyyy = date.getFullYear();
            let mm = date.getMonth() + 1; // Months start at 0
            let dd = date.getDate();
            if (dd < 10) dd = '0' + dd;
            if (mm < 10) mm = '0' + mm;
            return yyyy + '-' + mm + '-' + dd;
        }

        // --- PUBLIKASI MODAL FUNCTIONS ---

        function openAddPublikasiModal() {
            document.getElementById('addPublikasiModal').classList.remove('hidden');
            document.body.classList.add('modal-open');
        }

        function closeAddPublikasiModal() {
            document.getElementById('addPublikasiModal').classList.add('hidden');
            document.body.classList.remove('modal-open');
        }

        function openEditPublikasiModal(button) {
            const row = button.closest('tr');
            const id = row.getAttribute('data-id');
            const judul = row.getAttribute('data-judul');
            const penulis = row.getAttribute('data-penulis');
            const tanggal_terbit = row.getAttribute('data-tanggal_terbit');
            const file_publikasi = row.getAttribute('data-file_publikasi');
            const deskripsi = row.getAttribute('data-deskripsi');
            const id_anggota = row.getAttribute('data-id_anggota');

            document.getElementById('edit_id_publikasi').value = id;
            document.getElementById('edit_judul').value = judul;
            document.getElementById('edit_penulis').value = penulis;
            document.getElementById('edit_tanggal_terbit').value = tanggal_terbit;
            document.getElementById('edit_current_file_publikasi').value = file_publikasi;
            document.getElementById('current_file_name').textContent = file_publikasi.split('/').pop();
            document.getElementById('edit_deskripsi').value = deskripsi;

            document.getElementById('editPublikasiModal').classList.remove('hidden');
            document.body.classList.add('modal-open');
        }

        function closeEditPublikasiModal() {
            document.getElementById('editPublikasiModal').classList.add('hidden');
            document.body.classList.remove('modal-open');
            document.getElementById('edit_judul').value = '';
            document.getElementById('edit_penulis').value = '';
            document.getElementById('edit_tanggal_terbit').value = '';
            document.getElementById('edit_file_publikasi').value = '';
            document.getElementById('edit_deskripsi').value = '';
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

        const toggleButton = document.querySelector('.toggle-btn');
        if (toggleButton) {
            toggleButton.onclick = toggleSidebar;
        }
    </script>
</body>

</html>