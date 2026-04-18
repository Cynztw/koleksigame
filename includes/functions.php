<?php
require_once __DIR__ . '/config.php';

// Manajemen session
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

// Check if user is developer
function isDeveloper() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'developer';
}

// Check if user is pemain
function isPemain() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'pemain';
}

// Get user role
function getUserRole() {
    return $_SESSION['role'] ?? 'pemain';
}

// Redirect to login if not authenticated
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

// Require developer role
function requireDeveloper() {
    requireLogin();
    if (!isDeveloper()) {
        die('<div style="padding:20px; background:#fee; color:#c33; font-family:Arial; border-radius:5px; margin:20px;">
            ❌ Akses ditolak! Hanya developer yang bisa menambah game.
            <br><br><a href="index.php">← Kembali</a>
        </div>');
    }
}

// Login user
function loginUser($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}

// Register user baru
function registerUser($pdo, $username, $email, $password, $role = 'pemain') {
    try {
        // Validasi role
        if (!in_array($role, ['pemain', 'developer'])) {
            $role = 'pemain';
        }
        
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'error' => 'Username atau email sudah terdaftar'];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$username, $email, $hashedPassword, $role]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Registrasi berhasil! Silakan login.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Terjadi kesalahan: ' . $e->getMessage()];
    }
    
    return ['success' => false, 'error' => 'Registrasi gagal'];
}

// Logout user
function logoutUser() {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Dapatkan ID user saat ini
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Dapatkan semua game yang dibuat oleh developer (untuk developer saja)
function getGames($pdo) {
    $user_id = getCurrentUserId();
    // Hanya tampilkan game yang dibuat oleh developer ini
    $stmt = $pdo->prepare("SELECT g.*, k.nama as kategori_nama, u.username as developer_username 
                          FROM game g 
                          LEFT JOIN kategori k ON g.kategori_id = k.id
                          LEFT JOIN users u ON g.developer_id = u.id
                          WHERE g.developer_id = ? ORDER BY g.tanggal_ditambah DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Dapatkan semua game yang tersedia (dari semua developer)
function getAvailableGames($pdo) {
    $stmt = $pdo->prepare("SELECT g.*, k.nama as kategori_nama, u.username as developer_username 
                          FROM game g 
                          LEFT JOIN kategori k ON g.kategori_id = k.id
                          LEFT JOIN users u ON g.developer_id = u.id
                          WHERE g.status = 'tersedia'
                          ORDER BY g.tanggal_ditambah DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Dapatkan kategori
function getKategori($pdo) {
    $stmt = $pdo->query("SELECT * FROM kategori ORDER BY nama ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Buat game baru (untuk developer)
function buatGame($pdo, $judul, $kategori_id, $developer_name = '', $tahun_rilis = null, $gambar = '') {
    $developer_id = getCurrentUserId();
    
    try {
        $stmt = $pdo->prepare("INSERT INTO game (developer_id, judul, kategori_id, developer_name, tahun_rilis, gambar, status) 
                              VALUES (?, ?, ?, ?, ?, ?, 'tersedia')");
        $stmt->execute([$developer_id, $judul, $kategori_id, $developer_name, $tahun_rilis, $gambar]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        return false;
    }
}

// Tambah game ke koleksi
function tambahKeKoleksi($pdo, $game_id, $platform, $jam_main = 0, $persentase_selesai = 0) {
    $user_id = getCurrentUserId();
    
    try {
        $stmt = $pdo->prepare("INSERT INTO koleksi (user_id, game_id, platform, jam_main, persentase_selesai) 
                              VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$user_id, $game_id, $platform, $jam_main, $persentase_selesai]);
    } catch (PDOException $e) {
        return false;
    }
}

// Dapatkan koleksi pengguna
function getKoleksiUser($pdo) {
    $user_id = getCurrentUserId();
    $stmt = $pdo->prepare("SELECT k.*, g.judul, g.kategori_id, kat.nama as kategori_nama, g.developer_name,
                          g.status, g.rating, u.username as developer_username FROM koleksi k 
                          JOIN game g ON k.game_id = g.id
                          LEFT JOIN kategori kat ON g.kategori_id = kat.id
                          LEFT JOIN users u ON g.developer_id = u.id
                          WHERE k.user_id = ? ORDER BY k.tanggal_ditambah DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Dapatkan wishlist pengguna
function getWishlistUser($pdo) {
    $user_id = getCurrentUserId();
    $stmt = $pdo->prepare("SELECT w.*, g.judul, g.kategori_id, kat.nama as kategori_nama, g.developer_name,
                          g.rating, u.username as developer_username FROM wishlist w 
                          JOIN game g ON w.game_id = g.id
                          LEFT JOIN kategori kat ON g.kategori_id = kat.id
                          LEFT JOIN users u ON g.developer_id = u.id
                          WHERE w.user_id = ? ORDER BY w.prioritas ASC, w.tanggal_ditambah DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Tambah ke wishlist
function tambahKeWishlist($pdo, $game_id, $prioritas = 5, $alasan = '') {
    $user_id = getCurrentUserId();
    
    try {
        $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, game_id, prioritas, alasan) 
                              VALUES (?, ?, ?, ?)");
        return $stmt->execute([$user_id, $game_id, $prioritas, $alasan]);
    } catch (PDOException $e) {
        return false;
    }
}

// Hapus dari wishlist
function hapusDariWishlist($pdo, $wishlist_id) {
    $user_id = getCurrentUserId();
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE id = ? AND user_id = ?");
    return $stmt->execute([$wishlist_id, $user_id]);
}

// Perbarui status game
function updateStatusGame($pdo, $game_id, $status) {
    $user_id = getCurrentUserId();
    $stmt = $pdo->prepare("UPDATE game SET status = ? WHERE id = ? AND user_id = ?");
    return $stmt->execute([$status, $game_id, $user_id]);
}

// Perbarui koleksi
function updateKoleksi($pdo, $koleksi_id, $jam_main, $persentase_selesai) {
    $user_id = getCurrentUserId();
    $stmt = $pdo->prepare("UPDATE koleksi SET jam_main = ?, persentase_selesai = ? 
                          WHERE id = ? AND user_id = ?");
    return $stmt->execute([$jam_main, $persentase_selesai, $koleksi_id, $user_id]);
}

// Hapus dari koleksi
function hapusDariKoleksi($pdo, $koleksi_id) {
    $user_id = getCurrentUserId();
    $stmt = $pdo->prepare("DELETE FROM koleksi WHERE id = ? AND user_id = ?");
    return $stmt->execute([$koleksi_id, $user_id]);
}

// Buat kategori default
function buatKategoriDefault($pdo) {
    $kategori = ['RPG', 'Action', 'Adventure', 'Strategy', 'Puzzle', 'Sports', 'Simulation'];
    
    foreach ($kategori as $k) {
        try {
            $stmt = $pdo->prepare("INSERT INTO kategori (nama) VALUES (?)");
            $stmt->execute([$k]);
        } catch (PDOException $e) {
            // Already exists
        }
    }
}

// Inisialisasi data default
buatKategoriDefault($pdo);
?>
