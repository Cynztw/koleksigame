<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

header('Content-Type: application/json');

if (isset($_FILES['image']) && isset($_POST['koleksi_id'])) {
    $koleksi_id = (int)$_POST['koleksi_id'];
    $file = $_FILES['image'];
    
    $upload_dir = __DIR__ . '/../assets/uploads/games/';
    
    // Debug
    error_log("Upload attempt for koleksi_id: $koleksi_id, file size: " . $file['size']);
    
    if ($file['error'] === 0 && $file['size'] < 5*1024*1024) { // 5MB
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Only JPG/PNG/WEBP allowed']);
            exit;
        }
        
        $filename = 'game_' . $koleksi_id . '_' . time() . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Resize image if too large (optional)
            $image_info = getimagesize($filepath);
            if ($image_info[0] > 800) {
                // Simple resize logic here if needed
            }
            
            updateKoleksiImage($pdo, $koleksi_id, "assets/uploads/games/$filename");
            echo json_encode(['success' => true, 'image' => "assets/uploads/games/$filename"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Upload failed - check permissions']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'File too large or error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file or ID']);
}
?>

