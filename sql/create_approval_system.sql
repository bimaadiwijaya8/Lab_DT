-- SQL Script untuk Approval System
-- Sistem ini memungkinkan admin untuk menyetujui/menolak perubahan dari editor dan member

-- 1. Tabel untuk menyimpan pending changes (perubahan yang menunggu approval)
CREATE TABLE IF NOT EXISTS pending_changes (
    id_pending SERIAL PRIMARY KEY,
    table_name VARCHAR(50) NOT NULL,
    record_id INTEGER,
    action_type VARCHAR(20) NOT NULL CHECK (action_type IN ('create', 'update', 'delete')),
    old_data JSONB,
    new_data JSONB,
    user_id INTEGER NOT NULL,
    user_role VARCHAR(20) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected')),
    reason_rejection TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP,
    reviewed_by INTEGER,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
);

-- 2. Update tabel berita - pastikan ada kolom status dan approval fields
ALTER TABLE berita 
    ADD COLUMN IF NOT EXISTS approval_status VARCHAR(20) DEFAULT 'approved' CHECK (approval_status IN ('pending', 'approved', 'rejected')),
    ADD COLUMN IF NOT EXISTS created_by INTEGER,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN IF NOT EXISTS approved_by INTEGER,
    ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP;

-- 3. Update tabel publikasi - pastikan ada kolom approval
ALTER TABLE publikasi 
    ADD COLUMN IF NOT EXISTS approval_status VARCHAR(20) DEFAULT 'approved' CHECK (approval_status IN ('pending', 'approved', 'rejected')),
    ADD COLUMN IF NOT EXISTS created_by INTEGER,
    ADD COLUMN IF NOT EXISTS approved_by INTEGER,
    ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP;

-- 4. Update tabel galeri - pastikan ada kolom approval
ALTER TABLE galeri 
    ADD COLUMN IF NOT EXISTS approval_status VARCHAR(20) DEFAULT 'approved' CHECK (approval_status IN ('pending', 'approved', 'rejected')),
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN IF NOT EXISTS created_by INTEGER,
    ADD COLUMN IF NOT EXISTS approved_by INTEGER,
    ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP;

-- 5. Update tabel pengumuman - pastikan ada kolom approval
ALTER TABLE pengumuman 
    ADD COLUMN IF NOT EXISTS approval_status VARCHAR(20) DEFAULT 'approved' CHECK (approval_status IN ('pending', 'approved', 'rejected')),
    ADD COLUMN IF NOT EXISTS created_by INTEGER,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN IF NOT EXISTS approved_by INTEGER,
    ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP;

-- 6. Update tabel fasilitas - pastikan ada kolom approval
ALTER TABLE fasilitas 
    ADD COLUMN IF NOT EXISTS approval_status VARCHAR(20) DEFAULT 'approved' CHECK (approval_status IN ('pending', 'approved', 'rejected')),
    ADD COLUMN IF NOT EXISTS approved_by INTEGER,
    ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP;

-- 7. Update tabel anggota - pastikan ada kolom approval
ALTER TABLE anggota 
    ADD COLUMN IF NOT EXISTS approval_status VARCHAR(20) DEFAULT 'approved' CHECK (approval_status IN ('pending', 'approved', 'rejected')),
    ADD COLUMN IF NOT EXISTS created_by INTEGER,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN IF NOT EXISTS approved_by INTEGER,
    ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP;

-- 8. Tambah foreign key constraints
ALTER TABLE berita ADD CONSTRAINT fk_berita_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE berita ADD CONSTRAINT fk_berita_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL;

ALTER TABLE publikasi ADD CONSTRAINT fk_publikasi_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE publikasi ADD CONSTRAINT fk_publikasi_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL;

ALTER TABLE galeri ADD CONSTRAINT fk_galeri_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE galeri ADD CONSTRAINT fk_galeri_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL;

ALTER TABLE pengumuman ADD CONSTRAINT fk_pengumuman_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE pengumuman ADD CONSTRAINT fk_pengumuman_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL;

ALTER TABLE fasilitas ADD CONSTRAINT fk_fasilitas_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL;

ALTER TABLE anggota ADD CONSTRAINT fk_anggota_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE anggota ADD CONSTRAINT fk_anggota_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL;

-- 9. Create indexes untuk performa
CREATE INDEX IF NOT EXISTS idx_pending_changes_status ON pending_changes(status);
CREATE INDEX IF NOT EXISTS idx_pending_changes_user ON pending_changes(user_id);
CREATE INDEX IF NOT EXISTS idx_berita_approval ON berita(approval_status);
CREATE INDEX IF NOT EXISTS idx_publikasi_approval ON publikasi(approval_status);
CREATE INDEX IF NOT EXISTS idx_galeri_approval ON galeri(approval_status);
CREATE INDEX IF NOT EXISTS idx_pengumuman_approval ON pengumuman(approval_status);

-- 10. Function untuk auto-update timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- 11. Triggers untuk auto-update timestamp
DROP TRIGGER IF EXISTS update_berita_updated_at ON berita;
CREATE TRIGGER update_berita_updated_at BEFORE UPDATE ON berita
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_publikasi_updated_at ON publikasi;
CREATE TRIGGER update_publikasi_updated_at BEFORE UPDATE ON publikasi
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_galeri_updated_at ON galeri;
CREATE TRIGGER update_galeri_updated_at BEFORE UPDATE ON galeri
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_pengumuman_updated_at ON pengumuman;
CREATE TRIGGER update_pengumuman_updated_at BEFORE UPDATE ON pengumuman
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_anggota_updated_at ON anggota;
CREATE TRIGGER update_anggota_updated_at BEFORE UPDATE ON anggota
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

COMMENT ON TABLE pending_changes IS 'Tabel untuk menyimpan semua perubahan yang menunggu approval dari admin';
COMMENT ON COLUMN pending_changes.action_type IS 'Jenis aksi: create, update, atau delete';
COMMENT ON COLUMN pending_changes.old_data IS 'Data lama sebelum perubahan (untuk update dan delete)';
COMMENT ON COLUMN pending_changes.new_data IS 'Data baru setelah perubahan (untuk create dan update)';
