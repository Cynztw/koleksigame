<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

header('Content-Type: application/json');

if ($_POST && isset($_POST['koleksi_id'])) {
    $koleksi_id = (int)$_POST['koleksi_id'];
    $platform = trim($_POST['platform'] ?? '');
    $progress = trim($_POST['progress'] ?? '');

    $success = updateProgress($pdo, $koleksi_id, $platform, $progress);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Progress updated!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}
?>

