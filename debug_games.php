<?php
require_once __DIR__ . '/includes/config.php';

echo "<h2>DEBUG: Cek Semua Game di Database</h2>";

// Cek semua game
$stmt = $pdo->query("SELECT id, judul, status, gambar, developer_id FROM game");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($games)) {
    echo "<p style='color:red;'><strong>Belum ada game di database!</strong></p>";
} else {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Judul</th><th>Status</th><th>Gambar</th><th>Developer ID</th></tr>";
    foreach ($games as $g) {
        echo "<tr>";
        echo "<td>" . $g['id'] . "</td>";
        echo "<td>" . htmlspecialchars($g['judul']) . "</td>";
        echo "<td><strong>" . $g['status'] . "</strong></td>";
        echo "<td>" . ($g['gambar'] ? "✓ " . htmlspecialchars($g['gambar']) : "❌ KOSONG") . "</td>";
        echo "<td>" . $g['developer_id'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<h2>DEBUG: Cek Available Games untuk Pemain</h2>";

$stmt = $pdo->prepare("SELECT g.*, k.nama as kategori_nama, u.username as developer_username 
                      FROM game g 
                      LEFT JOIN kategori k ON g.kategori_id = k.id
                      LEFT JOIN users u ON g.developer_id = u.id
                      WHERE g.status = 'tersedia'
                      ORDER BY g.tanggal_ditambah DESC");
$stmt->execute();
$available = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($available)) {
    echo "<p style='color:red;'><strong>Tidak ada game dengan status 'tersedia'!</strong></p>";
} else {
    echo "<p>Total: <strong>" . count($available) . " game</strong></p>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Judul</th><th>Developer</th><th>Gambar</th></tr>";
    foreach ($available as $g) {
        echo "<tr>";
        echo "<td>" . $g['id'] . "</td>";
        echo "<td>" . htmlspecialchars($g['judul']) . "</td>";
        echo "<td>" . htmlspecialchars($g['developer_username']) . "</td>";
        echo "<td>" . ($g['gambar'] ? "✓ " . htmlspecialchars($g['gambar']) : "❌ KOSONG") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<h2>File yang ada di folder uploads:</h2>";
$upload_dir = __DIR__ . '/assets/uploads/games/';
if (is_dir($upload_dir)) {
    $files = scandir($upload_dir);
    echo "<ul>";
    foreach ($files as $f) {
        if ($f !== '.' && $f !== '..') {
            echo "<li>" . $f . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color:red;'>Folder tidak ada!</p>";
}
?>
