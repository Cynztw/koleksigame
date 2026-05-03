<?php
// public/koleksi.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$user_id = getCurrentUserId();
$username = $_SESSION['username'];

// Ambil data koleksi dengan JOIN ke tabel game[cite: 11, 13]
try {
    $sql = "SELECT k.id, g.judul, k.platform, k.progress 
            FROM koleksi k 
            JOIN game g ON k.game_id = g.id 
            WHERE k.user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $koleksi = $stmt->fetchAll();
} catch (PDOException $e) {
    // Jika masih error, tampilkan pesan yang lebih manusiawi
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Koleksi Saya - Steam Clone</title>
    <style>
        :root { --s-bg: #1b2838; --s-nav: #171a21; --s-blue: #66c0f4; --s-border: #2a475e; --s-text: #c7d5e0; --s-muted: #8f98a0; }
        body { background: var(--s-bg); color: var(--s-text); font-family: 'Segoe UI', sans-serif; margin: 0; }
        .steam-nav { background: var(--s-nav); height: 65px; display: flex; align-items: center; padding: 0 2rem; border-bottom: 1px solid #000; }
        .main-wrap { display: grid; grid-template-columns: 260px 1fr; gap: 40px; padding: 40px; max-width: 1300px; margin: 0 auto; }
        .steam-sidebar { background: rgba(0,0,0,0.2); border: 1px solid var(--s-border); padding: 25px; border-radius: 4px; }
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu li { margin-bottom: 15px; }
        .sidebar-menu li a { color: var(--s-text); text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .active { color: white; font-weight: bold; }
        .s-table-wrap { background: rgba(0,0,0,0.3); border: 1px solid var(--s-border); border-radius: 4px; overflow: hidden; }
        .s-table-header { background: rgba(42, 71, 94, 0.5); padding: 12px 20px; font-weight: bold; font-size: 0.8rem; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px 20px; color: var(--s-blue); border-bottom: 1px solid var(--s-border); }
        td { padding: 15px 20px; border-bottom: 1px solid rgba(42, 71, 94, 0.3); }
        .btn-edit { background: #ebebeb; color: #333; border: none; padding: 4px 12px; border-radius: 2px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

<nav class="steam-nav">
    <div style="font-weight: bold; letter-spacing: 2px;">KOLEKSI GAME</div>
</nav>

<div class="main-wrap">
    <aside class="steam-sidebar">
        <div style="color: var(--s-blue); font-size: 0.8rem; font-weight: bold; margin-bottom: 20px;">LIBRARY</div>
        <ul class="sidebar-menu">
            <li><a href="index.php">🏠 Dashboard</a></li>
            <li><a href="koleksi.php" class="active">🎮 Koleksi Saya</a></li>
            <li><a href="#">🎁 Wishlist</a></li>
        </ul>
    </aside>

    <main>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h1 style="margin: 0;">Koleksi Game Saya</h1>
            <a href="koleksi_2.php" style="background: #5c7e10; color: #d2efa9; text-decoration: none; padding: 8px 15px; border-radius: 2px; font-weight: bold;">+ TAMBAH GAME</a>
        </div>

        <div class="s-table-wrap">
            <div class="s-table-header">DAFTAR GAME YANG DIMILIKI</div>
            <table>
                <thead>
                    <tr>
                        <th>Judul Game</th>
                        <th>Platform</th>
                        <th>Progress</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($koleksi): ?>
                        <?php foreach($koleksi as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['judul']) ?></td>
                            <td><?= htmlspecialchars($item['platform']) ?></td>
                            <td><?= htmlspecialchars($item['progress']) ?></td>
                            <td style="text-align: center;"><button class="btn-edit">Edit</button></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align: center; padding: 30px; color: var(--s-muted);">Library kosong.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>