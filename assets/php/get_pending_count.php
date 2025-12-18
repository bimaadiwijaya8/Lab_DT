<?php
require_once 'db_connect.php';
require_once 'approval_helper.php';

header('Content-Type: application/json');

try {
    $pdo = Database::getConnection();
    
    $total_pending = 0;
    
    $tables = ['berita', 'publikasi', 'galeri', 'pengumuman', 'fasilitas', 'anggota'];
    
    foreach ($tables as $table) {
        try {
            $sql = "SELECT COUNT(*) as count FROM $table WHERE approval_status = 'pending' OR status = 'pending'";
            $stmt = $pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_pending += (int)$result['count'];
        } catch (Exception $e) {
            continue;
        }
    }
    
    echo json_encode([
        'success' => true,
        'count' => $total_pending
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'count' => 0,
        'error' => $e->getMessage()
    ]);
}
