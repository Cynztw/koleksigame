<?php
// includes/functions.php - FULL RECOVERY & tambahKeKoleksi

// 1. Session & Auth
function logoutUser() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION = array();
    session_destroy();
    header("Location: login.php");
    exit;
}

function requireLogin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function getUserRole() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return $_SESSION['role'] ?? 'pemain';
}

function getCurrentUserId() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return $_SESSION['user_id'] ?? null;
}

// 2. Data Functions
function getKategori($pdo) {
    return $pdo->query("SELECT id, nama FROM kategori ORDER BY nama ASC")->fetchAll();
}

function getAllGames($pdo) {
    $sql = "SELECT g.*, k.nama as kategori_nama 
            FROM game g JOIN kategori k ON g.kategori_id = k.id 
            ORDER BY g.judul ASC";
    return $pdo->query($sql)->fetchAll();
}

function getWishlist($pdo, $user_id) {
    $sql = "SELECT w.*, g.*, k.nama as kategori_nama 
            FROM wishlist w 
            JOIN game g ON w.game_id = g.id 
            JOIN kategori k ON g.kategori_id = k.id 
            WHERE w.user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// 3. Action Functions
function tambahKeKoleksi($pdo, $user_id, $game_id) {
    $sql = "INSERT IGNORE INTO koleksi (user_id, game_id, platform, progress) VALUES (?, ?, '', '')";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$user_id, $game_id]);
}

function buatGame($pdo, $judul, $kategori_id, $developer, $user_id) {
    $sql = "INSERT INTO game (judul, kategori_id, developer, gambar) VALUES (?, ?, ?, 'assets/uploads/games/placeholder.png')";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$judul, $kategori_id, $developer]);
}

?>

