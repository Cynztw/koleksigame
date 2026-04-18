<?php
// Konfigurasi database MySQL
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'koleksigame');
define('UPLOADS_PATH', __DIR__ . '/../assets/images/');

// Koneksi ke MySQL
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    // Buat database jika belum ada
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    
    // Pilih database
    $pdo->exec("USE " . DB_NAME);
    
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}

// Buat tabel jika belum ada
function initDatabase($pdo) {
    $tables = [
        // Tabel users
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('pemain', 'developer') DEFAULT 'pemain',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        // Tabel kategori
        "CREATE TABLE IF NOT EXISTS kategori (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama VARCHAR(100) NOT NULL,
            deskripsi TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        // Tabel game
        "CREATE TABLE IF NOT EXISTS game (
            id INT AUTO_INCREMENT PRIMARY KEY,
            developer_id INT NOT NULL,
            judul VARCHAR(255) NOT NULL,
            deskripsi TEXT,
            kategori_id INT,
            developer_name VARCHAR(100),
            tahun_rilis INT,
            gambar VARCHAR(255),
            status VARCHAR(50) DEFAULT 'tersedia',
            rating DECIMAL(3,1),
            tanggal_ditambah TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (developer_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL,
            INDEX idx_developer_id (developer_id),
            INDEX idx_kategori_id (kategori_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        // Tabel koleksi (game yang dimiliki)
        "CREATE TABLE IF NOT EXISTS koleksi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            game_id INT NOT NULL,
            platform VARCHAR(100),
            jam_main INT DEFAULT 0,
            tanggal_beli DATE,
            persentase_selesai INT DEFAULT 0,
            catatan TEXT,
            tanggal_ditambah TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_game (user_id, game_id),
            INDEX idx_user_id (user_id),
            INDEX idx_game_id (game_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        // Tabel wishlist
        "CREATE TABLE IF NOT EXISTS wishlist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            game_id INT NOT NULL,
            prioritas INT DEFAULT 5,
            alasan TEXT,
            tanggal_ditambah TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_wishlist (user_id, game_id),
            INDEX idx_user_id (user_id),
            INDEX idx_game_id (game_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ];
    
    foreach ($tables as $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            // Tabel mungkin sudah ada
        }
    }
    
}

// Jalankan inisialisasi database
initDatabase($pdo);

// Migrasi: Tambah kolom role jika belum ada
try {
    // Check apakah kolom role sudah ada
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'role'");
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        // Tambah kolom role jika belum ada
        $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('pemain', 'developer') DEFAULT 'pemain'");
    }
} catch (PDOException $e) {
    // Kolom mungkin sudah ada
}

// Migrasi: Ubah struktur game table dari user_id ke developer_id
try {
    // Check apakah kolom user_id masih ada (old structure)
    $stmt = $pdo->prepare("SHOW COLUMNS FROM game LIKE 'user_id'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Rename user_id menjadi developer_id
        $pdo->exec("ALTER TABLE game CHANGE COLUMN user_id developer_id INT NOT NULL");
    }
    
    // Check apakah kolom developer masih ada (old structure)
    $stmt = $pdo->prepare("SHOW COLUMNS FROM game LIKE 'developer'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Rename developer menjadi developer_name
        $pdo->exec("ALTER TABLE game CHANGE COLUMN developer developer_name VARCHAR(100)");
    }
} catch (PDOException $e) {
    // Migration mungkin sudah dilakukan
}

// Buat demo users jika belum ada
try {
    // Demo pemain
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'demo_pemain'");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $demo_password = password_hash('demo123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['demo_pemain', 'pemain@example.com', $demo_password, 'pemain']);
    }
    
    // Demo developer
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'demo_dev'");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $demo_password = password_hash('demo123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['demo_dev', 'developer@example.com', $demo_password, 'developer']);
    }
} catch (PDOException $e) {
    // Users demo mungkin sudah ada
}
?>
