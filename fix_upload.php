<?php
require_once __DIR__ . '/includes/config.php';

echo "<h1>🔧 Fix Upload Foto</h1>";

// 1. Copy files dari folder lama ke folder baru
echo "<h2>1. Migrasi File Foto</h2>";

$old_dir = __DIR__ . '/assets/uploads/games/';
$new_dir = __DIR__ . '/uploads/games/';

if (!is_dir($old_dir)) {
    echo "<p style='color:red;'>❌ Folder lama tidak ada: $old_dir</p>";
} else {
    $files = array_diff(scandir($old_dir), ['.', '..', 'placeholder.png']);
    if (empty($files)) {
        echo "<p>Tidak ada file yang perlu dimigrasikan</p>";
    } else {
        foreach ($files as $file) {
            $old_file = $old_dir . $file;
            $new_file = $new_dir . $file;
            if (copy($old_file, $new_file)) {
                echo "<p>✓ Copy: $file</p>";
            } else {
                echo "<p style='color:orange;'>⚠️ Gagal copy: $file</p>";
            }
        }
    }
}

// 2. Copy placeholder.png juga ke folder baru
$placeholder_old = $old_dir . 'placeholder.png';
$placeholder_new = $new_dir . 'placeholder.png';
if (file_exists($placeholder_old) && !file_exists($placeholder_new)) {
    copy($placeholder_old, $placeholder_new);
    echo "<p>✓ Copy placeholder.png</p>";
}

// 3. Cek file di database vs di disk
echo "<h2>2. Verifikasi Database vs Disk</h2>";

$stmt = $pdo->query("SELECT id, judul, gambar FROM game WHERE gambar IS NOT NULL AND gambar != ''");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<p>Total game dengan foto di database: <strong>" . count($games) . "</strong></p>";
echo "<ul>";
foreach ($games as $g) {
    $file_path = $new_dir . $g['gambar'];
    $exists = file_exists($file_path) ? '✓' : '❌';
    echo "<li>$exists {$g['judul']} → {$g['gambar']}</li>";
}
echo "</ul>";

// 4. Info path yang benar
echo "<h2>3. Path yang Sekarang Digunakan</h2>";
echo "<pre>";
echo "Upload Folder: /uploads/games/\n";
echo "URL Prefix: http://localhost/web%20pro%20S2/koleksigame/uploads/games/\n";
echo "File yang ada:\n";
$files_in_new = scandir($new_dir);
foreach ($files_in_new as $f) {
    if ($f !== '.' && $f !== '..') {
        echo "  - $f\n";
    }
}
echo "</pre>";

echo "<hr>";
echo "<p><a href='public/koleksi.php' class='btn btn-primary'>👉 Lihat Halaman Koleksi</a></p>";
?>
