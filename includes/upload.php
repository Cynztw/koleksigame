<?php
// Handle file upload
function uploadGameImage($file) {
    $target_dir = __DIR__ . "/../assets/uploads/games/";
    
    // Buat folder jika belum ada
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Validasi file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!isset($file)) {
        return ['success' => false, 'message' => 'File tidak ada'];
    }
    
    if ($file['size'] == 0) {
        return ['success' => false, 'message' => 'File kosong'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File terlalu besar (max 5MB)'];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Format file tidak didukung (gunakan JPG, PNG, GIF, WEBP)'];
    }
    
    // Generate nama file unik
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = 'game_' . time() . '_' . uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $file_name;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return [
            'success' => true, 
            'filename' => $file_name,
            'path' => '../assets/uploads/games/' . $file_name
        ];
    } else {
        return ['success' => false, 'message' => 'Gagal upload file'];
    }
}

// Dapatkan path gambar
function getGameImagePath($filename) {
    if (empty($filename)) {
        return '../assets/uploads/games/placeholder.png';
    }
    return '../assets/uploads/games/' . $filename;
}

// Hapus gambar
function deleteGameImage($filename) {
    if (!empty($filename)) {
        $file_path = __DIR__ . "/../assets/uploads/games/" . $filename;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
}
?>
