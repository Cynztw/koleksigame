<?php
require_once __DIR__ . '/includes/config.php';

// Update semua game jadi status tersedia
$stmt = $pdo->query("UPDATE game SET status = 'tersedia' WHERE status IS NULL OR status = ''");
$affected = $stmt->rowCount();

echo "<h2>Update Status Game</h2>";
echo "<p>Game yang di-update: <strong>$affected</strong></p>";

// Check result
$stmt = $pdo->query("SELECT id, judul, status FROM game");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Daftar Game Sekarang:</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Judul</th><th>Status</th></tr>";
foreach ($games as $g) {
    echo "<tr><td>" . $g['id'] . "</td><td>" . htmlspecialchars($g['judul']) . "</td><td><strong>" . $g['status'] . "</strong></td></tr>";
}
echo "</table>";

echo "<hr>";
echo "<p><a href='public/index.php'>👉 Kembali ke Dashboard</a></p>";
?>
