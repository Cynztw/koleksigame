-- =====================================================
-- MIGRATION SCRIPT: Ubah struktur game table
-- Jalankan di phpMyAdmin jika sudah ada data lama
-- =====================================================

-- Step 1: Rename user_id menjadi developer_id
ALTER TABLE game CHANGE COLUMN user_id developer_id INT NOT NULL;

-- Step 2: Rename developer menjadi developer_name
ALTER TABLE game CHANGE COLUMN developer developer_name VARCHAR(100);

-- Step 3: Update status nilai default
ALTER TABLE game MODIFY COLUMN status VARCHAR(50) DEFAULT 'tersedia';

-- =====================================================
-- Selesai! Database sudah siap dengan struktur baru.
-- =====================================================
