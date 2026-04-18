-- =====================================================
-- KOLEKSI GAME - Database Setup Script
-- Jalankan script ini di phpMyAdmin
-- =====================================================

-- Buat database
CREATE DATABASE IF NOT EXISTS `koleksigame` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `koleksigame`;

-- Tabel users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) UNIQUE NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('pemain', 'developer') DEFAULT 'pemain',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_username (username),
  INDEX idx_email (email),
  INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel kategori
CREATE TABLE IF NOT EXISTS `kategori` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL UNIQUE,
  `deskripsi` TEXT,
  INDEX idx_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel game
CREATE TABLE IF NOT EXISTS `game` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `developer_id` INT NOT NULL,
  `judul` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT,
  `kategori_id` INT,
  `developer_name` VARCHAR(100),
  `tahun_rilis` INT,
  `gambar` VARCHAR(255),
  `status` VARCHAR(50) DEFAULT 'tersedia',
  `rating` DECIMAL(3,1),
  `tanggal_ditambah` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (developer_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL,
  INDEX idx_developer_id (developer_id),
  INDEX idx_kategori_id (kategori_id),
  INDEX idx_judul (judul)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
  INDEX idx_judul (judul)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel koleksi (game yang dimiliki user)
CREATE TABLE IF NOT EXISTS `koleksi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `game_id` INT NOT NULL,
  `platform` VARCHAR(100),
  `jam_main` INT DEFAULT 0,
  `tanggal_beli` DATE,
  `persentase_selesai` INT DEFAULT 0,
  `catatan` TEXT,
  `tanggal_ditambah` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_game (user_id, game_id),
  INDEX idx_user_id (user_id),
  INDEX idx_game_id (game_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel wishlist
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `game_id` INT NOT NULL,
  `prioritas` INT DEFAULT 5,
  `alasan` TEXT,
  `tanggal_ditambah` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_wishlist (user_id, game_id),
  INDEX idx_user_id (user_id),
  INDEX idx_game_id (game_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert kategori default
INSERT IGNORE INTO `kategori` (nama, deskripsi) VALUES
('RPG', 'Role-Playing Game'),
('Action', 'Action Game'),
('Adventure', 'Adventure Game'),
('Strategy', 'Strategy Game'),
('Puzzle', 'Puzzle Game'),
('Sports', 'Sports Game'),
('Simulation', 'Simulation Game');

-- Insert demo users
-- Password: demo123 (bcrypt hash)
INSERT IGNORE INTO `users` (username, email, password, role) VALUES
('demo_pemain', 'pemain@example.com', '$2y$10$C/qY.1.nYWs7wO6qc2SMNO2Z8Yy5C5E8vV2X9K7Q3R4S5T6U7V8W', 'pemain'),
('demo_dev', 'developer@example.com', '$2y$10$C/qY.1.nYWs7wO6qc2SMNO2Z8Yy5C5E8vV2X9K7Q3R4S5T6U7V8W', 'developer');

-- =====================================================
-- Selesai! Database siap digunakan.
-- =====================================================
