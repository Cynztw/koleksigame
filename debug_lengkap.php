<?php
require_once __DIR__ . '/includes/config.php';

echo "<h1>🔍 DEBUG LENGKAP</h1>";

// 1. Cek semua game di database
echo "<h2>1. Semua Game di Database</h2>";
$result = $pdo->query("SELECT * FROM game");
$all_games = $result->fetchAll(PDO::FETCH_ASSOC);

if (empty($all_games)) {
    echo "<p style='color:red;'><strong>❌ TIDAK ADA GAME SAMA SEKALI!</strong></p>";
} else {
    echo "<p>Total: <strong>" . count($all_games) . "</strong> game</p>";
    echo "<pre>";
    foreach ($all_games as $g) {
        echo "- ID: {$g['id']}, Judul: {$g['judul']}, Status: '{$g['status']}', Developer: {$g['developer_id']}\n";
    }
    echo "</pre>";
}

// 2. Cek developer
echo "<h2>2. Semua Developer</h2>";
$result = $pdo->query("SELECT id, username, role FROM users WHERE role = 'developer'");
$devs = $result->fetchAll(PDO::FETCH_ASSOC);
if (empty($devs)) {
    echo "<p style='color:red;'>❌ Tidak ada developer!</p>";
} else {
    echo "<pre>";
    foreach ($devs as $d) {
        echo "- ID: {$d['id']}, Username: {$d['username']}\n";
    }
    echo "</pre>";
}

// 3. Cek pemain
echo "<h2>3. Semua Pemain</h2>";
$result = $pdo->query("SELECT id, username, role FROM users WHERE role = 'pemain'");
$pemains = $result->fetchAll(PDO::FETCH_ASSOC);
if (empty($pemains)) {
    echo "<p style='color:red;'>❌ Tidak ada pemain!</p>";
} else {
    echo "<pre>";
    foreach ($pemains as $p) {
        echo "- ID: {$p['id']}, Username: {$p['username']}\n";
    }
    echo "</pre>";
}

// 4. Query Available Games (sama persis dengan di functions.php)
echo "<h2>4. Query Available Games untuk Pemain</h2>";
$stmt = $pdo->prepare("SELECT g.*, k.nama as kategori_nama, u.username as developer_username 
                      FROM game g 
                      LEFT JOIN kategori k ON g.kategori_id = k.id
                      LEFT JOIN users u ON g.developer_id = u.id
                      WHERE g.status = 'tersedia'
                      ORDER BY g.tanggal_ditambah DESC");
$stmt->execute();
$available = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($available)) {
    echo "<p style='color:red;'><strong>❌ Hasil query: 0 game dengan status 'tersedia'</strong></p>";
} else {
    echo "<p>✓ Hasil query: " . count($available) . " game</p>";
    echo "<pre>";
    foreach ($available as $g) {
        echo "- {$g['judul']} (Dev: {$g['developer_username']})\n";
    }
    echo "</pre>";
}

// 5. Cek status game yang ada
echo "<h2>5. Nilai Status Game di Database</h2>";
$result = $pdo->query("SELECT DISTINCT status FROM game");
$statuses = $result->fetchAll(PDO::FETCH_COLUMN);
echo "<pre>";
for ($i = 0; $i < count($statuses); $i++) {
    echo "[$i] '" . $statuses[$i] . "' (length: " . strlen($statuses[$i]) . ")\n";
}
echo "</pre>";

// 6 Auto fix: Update status jadi tersedia
echo "<h2>6. Auto-Fix: Update Status ke 'tersedia'</h2>";
$stmt = $pdo->query("UPDATE game SET status = 'tersedia'");
$affected = $stmt->rowCount();
echo "<p>✓ Updated: <strong>$affected</strong> row(s)</p>";

// 7. Verifikasi fix
echo "<h2>7. Verifikasi Setelah Fix</h2>";
$stmt = $pdo->prepare("SELECT g.*, u.username as developer_username 
                      FROM game g 
                      LEFT JOIN users u ON g.developer_id = u.id
                      WHERE g.status = 'tersedia'");
$stmt->execute();
$verified = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($verified)) {
    echo "<p style='color:red;'>❌ Masih 0 game!</p>";
} else {
    echo "<p>✓ <strong>" . count($verified) . " game</strong> sekarang tersedia untuk pemain</p>";
    echo "<ul>";
    foreach ($verified as $v) {
        echo "<li>" . htmlspecialchars($v['judul']) . " (oleh: " . htmlspecialchars($v['developer_username']) . ")</li>";
    }
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='public/index.php?refresh=1'>👉 Refresh Dashboard Pemain</a></p>";
?>
