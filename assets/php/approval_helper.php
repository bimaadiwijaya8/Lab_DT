<?php

class ApprovalHelper {
    
    public static function requiresApproval($user_role) {
        return in_array($user_role, ['editor', 'member']);
    }
    
    public static function getApprovalStatus($user_role) {
        return self::requiresApproval($user_role) ? 'pending' : 'approved';
    }
    
    public static function createPendingChange($pdo, $table_name, $action_type, $new_data, $user_id, $user_role, $old_data = null, $record_id = null) {
        try {
            $sql = "INSERT INTO pending_changes (table_name, record_id, action_type, old_data, new_data, user_id, user_role, status, created_at) 
                    VALUES (:table_name, :record_id, :action_type, :old_data, :new_data, :user_id, :user_role, 'pending', NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':table_name' => $table_name,
                ':record_id' => $record_id,
                ':action_type' => $action_type,
                ':old_data' => $old_data ? json_encode($old_data) : null,
                ':new_data' => json_encode($new_data),
                ':user_id' => $user_id,
                ':user_role' => $user_role
            ]);
            
            return $pdo->lastInsertId();
        } catch (Exception $e) {
            error_log("Error creating pending change: " . $e->getMessage());
            return false;
        }
    }
    
    public static function getPendingChanges($pdo, $status = 'pending', $table_name = null) {
        try {
            $sql = "SELECT pc.*, u.username as user_name 
                    FROM pending_changes pc 
                    LEFT JOIN users u ON pc.user_id = u.id 
                    WHERE pc.status = :status";
            
            if ($table_name) {
                $sql .= " AND pc.table_name = :table_name";
            }
            
            $sql .= " ORDER BY pc.created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $params = [':status' => $status];
            
            if ($table_name) {
                $params[':table_name'] = $table_name;
            }
            
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting pending changes: " . $e->getMessage());
            return [];
        }
    }
    
    public static function approveChange($pdo, $pending_id, $admin_id) {
        try {
            $pdo->beginTransaction();
            
            $sql = "SELECT * FROM pending_changes WHERE id_pending = :id AND status = 'pending'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $pending_id]);
            $pending = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$pending) {
                $pdo->rollBack();
                return ['success' => false, 'message' => 'Pending change tidak ditemukan'];
            }
            
            $new_data = json_decode($pending['new_data'], true);
            $table_name = $pending['table_name'];
            $action_type = $pending['action_type'];
            $record_id = $pending['record_id'];
            
            if ($action_type === 'create') {
                $new_data['approval_status'] = 'approved';
                $new_data['approved_by'] = $admin_id;
                $new_data['approved_at'] = date('Y-m-d H:i:s');
                
                $columns = array_keys($new_data);
                $placeholders = array_map(function($col) { return ":$col"; }, $columns);
                
                $insert_sql = "INSERT INTO $table_name (" . implode(', ', $columns) . ") 
                              VALUES (" . implode(', ', $placeholders) . ")";
                
                $insert_stmt = $pdo->prepare($insert_sql);
                foreach ($new_data as $key => $value) {
                    $insert_stmt->bindValue(":$key", $value);
                }
                $insert_stmt->execute();
                
                $new_record_id = $pdo->lastInsertId();
                
                $update_pending = "UPDATE pending_changes 
                                  SET status = 'approved', reviewed_by = :admin_id, reviewed_at = NOW(), record_id = :record_id 
                                  WHERE id_pending = :id";
                $stmt = $pdo->prepare($update_pending);
                $stmt->execute([':admin_id' => $admin_id, ':id' => $pending_id, ':record_id' => $new_record_id]);
                
            } elseif ($action_type === 'update') {
                $new_data['approval_status'] = 'approved';
                $new_data['approved_by'] = $admin_id;
                $new_data['approved_at'] = date('Y-m-d H:i:s');
                
                $set_parts = [];
                foreach ($new_data as $key => $value) {
                    $set_parts[] = "$key = :$key";
                }
                
                $primary_key = self::getPrimaryKey($table_name);
                $update_sql = "UPDATE $table_name SET " . implode(', ', $set_parts) . " WHERE $primary_key = :record_id";
                
                $update_stmt = $pdo->prepare($update_sql);
                foreach ($new_data as $key => $value) {
                    $update_stmt->bindValue(":$key", $value);
                }
                $update_stmt->bindValue(':record_id', $record_id);
                $update_stmt->execute();
                
                $update_pending = "UPDATE pending_changes 
                                  SET status = 'approved', reviewed_by = :admin_id, reviewed_at = NOW() 
                                  WHERE id_pending = :id";
                $stmt = $pdo->prepare($update_pending);
                $stmt->execute([':admin_id' => $admin_id, ':id' => $pending_id]);
                
            } elseif ($action_type === 'delete') {
                $primary_key = self::getPrimaryKey($table_name);
                $delete_sql = "DELETE FROM $table_name WHERE $primary_key = :record_id";
                $delete_stmt = $pdo->prepare($delete_sql);
                $delete_stmt->execute([':record_id' => $record_id]);
                
                $update_pending = "UPDATE pending_changes 
                                  SET status = 'approved', reviewed_by = :admin_id, reviewed_at = NOW() 
                                  WHERE id_pending = :id";
                $stmt = $pdo->prepare($update_pending);
                $stmt->execute([':admin_id' => $admin_id, ':id' => $pending_id]);
            }
            
            $pdo->commit();
            return ['success' => true, 'message' => 'Perubahan berhasil disetujui'];
            
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Error approving change: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public static function rejectChange($pdo, $pending_id, $admin_id, $reason = '') {
        try {
            $sql = "UPDATE pending_changes 
                    SET status = 'rejected', reviewed_by = :admin_id, reviewed_at = NOW(), reason_rejection = :reason 
                    WHERE id_pending = :id AND status = 'pending'";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':admin_id' => $admin_id,
                ':id' => $pending_id,
                ':reason' => $reason
            ]);
            
            if ($result && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Perubahan berhasil ditolak'];
            } else {
                return ['success' => false, 'message' => 'Pending change tidak ditemukan'];
            }
            
        } catch (Exception $e) {
            error_log("Error rejecting change: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public static function getPrimaryKey($table_name) {
        $primary_keys = [
            'berita' => 'id_berita',
            'publikasi' => 'id_publikasi',
            'galeri' => 'id_foto',
            'pengumuman' => 'id_pengumuman',
            'fasilitas' => 'id_fasilitas',
            'anggota' => 'id_anggota',
            'member' => 'id_member'
        ];
        
        return $primary_keys[$table_name] ?? 'id';
    }
    
    public static function getTableDisplayName($table_name) {
        $names = [
            'berita' => 'Berita',
            'publikasi' => 'Publikasi',
            'galeri' => 'Galeri',
            'pengumuman' => 'Pengumuman',
            'fasilitas' => 'Fasilitas',
            'anggota' => 'Anggota'
        ];
        
        return $names[$table_name] ?? ucfirst($table_name);
    }
    
    public static function getActionDisplayName($action_type) {
        $names = [
            'create' => 'Tambah',
            'update' => 'Edit',
            'delete' => 'Hapus'
        ];
        
        return $names[$action_type] ?? ucfirst($action_type);
    }
    
    public static function getPendingCount($pdo, $user_id = null) {
        try {
            $sql = "SELECT COUNT(*) as total FROM pending_changes WHERE status = 'pending'";
            
            if ($user_id) {
                $sql .= " AND user_id = :user_id";
            }
            
            $stmt = $pdo->prepare($sql);
            
            if ($user_id) {
                $stmt->execute([':user_id' => $user_id]);
            } else {
                $stmt->execute();
            }
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
            
        } catch (Exception $e) {
            error_log("Error getting pending count: " . $e->getMessage());
            return 0;
        }
    }
}
