<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

header('Content-Type: application/json');

if (isset($_POST['koleksi_id'])) {
    $koleksi_id = (int)$_POST['koleksi_id'];
    $success = deleteFromKoleksi($pdo, $koleksi_id);
    
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Game removed from library' : 'Delete failed'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No ID']);
}
?>

