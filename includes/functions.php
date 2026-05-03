<?php
// includes/functions.php

// 1. Fungsi Logout (YANG BIKIN ERROR TADI)
function logoutUser() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    // Hapus semua data sesi
    $_SESSION = array();
    
    // Hancurkan sesi secara permanen
    session_destroy();
    
    // Tendang balik ke halaman login
    header("Location: login.php");
    exit;
}

// 2. Fungsi Keamanan & Sesi
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

// 3. Fungsi Database (Kategori, Game, dll)
function getKategori($pdo) {
    return $pdo->query("SELECT MIN(id) as id, nama FROM kategori GROUP BY nama ORDER BY nama ASC")->fetchAll();
}

function getAllGames($pdo) {
    $sql = "SELECT g.*, k.nama as kategori_nama 
            FROM game g 
            JOIN kategori k ON g.kategori_id = k.id 
            ORDER BY g.judul ASC";
    return $pdo->query($sql)->fetchAll();
}

// 4. Fungsi Wishlist & Koleksi
function getWishlist($pdo, $user_id) {
    $sql = "SELECT w.id as wishlist_id, g.*, k.nama as kategori_nama 
            FROM wishlist w 
            JOIN game g ON w.game_id = g.id 
            JOIN kategori k ON g.kategori_id = k.id 
            WHERE w.user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}
?>