<?php
// public/wishlist.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$user_id = getCurrentUserId();
$username = $_SESSION['username'];

// Ambil data wishlist
try {
    $wishlist = getWishlist($pdo, $user_id);
} catch (PDOException $e) {
    // Jika tabel belum ada, kita buat otomatis biar gak stuck
    $pdo->exec("CREATE TABLE IF NOT EXISTS wishlist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        game_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $wishlist = [];
}
?>



<head>
    <meta charset="UTF-8">
    <title>Wishlist Saya - Steam Clone</title>
    <style>
        :root { --s-bg: #1b2838; --s-nav: #171a21; --s-blue: #66c0f4; --s-border: #2a475e; --s-text: #c7d5e0; --s-muted: #8f98a0; }
        body { background: var(--s-bg); color: var(--s-text); font-family: 'Segoe UI', sans-serif; margin: 0; }
        .steam-nav { background: var(--s-nav); height: 65px; display: flex; align-items: center; padding: 0 2rem; border-bottom: 1px solid #000; }
        .main-wrap { display: grid; grid-template-columns: 260px 1fr; gap: 40px; padding: 40px; max-width: 1300px; margin: 0 auto; }
        
        /* Sidebar sesuai gambar lo */
        .steam-sidebar { background: rgba(0,0,0,0.2); border: 1px solid var(--s-border); padding: 25px; border-radius: 4px; }
        .sidebar-title { color: var(--s-blue); font-size: 0.8rem; font-weight: bold; margin-bottom: 20px; text-transform: uppercase; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li { margin-bottom: 15px; }
        .sidebar-menu li a { color: var(--s-text); text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .active { color: white !important; font-weight: bold; }

        /* Wishlist Items */
        .wish-container { background: rgba(0,0,0,0.3); border: 1px solid var(--s-border); border-radius: 4px; }
        .wish-header { background: rgba(42, 71, 94, 0.5); padding: 12px 20px; font-weight: bold; font-size: 0.8rem; }
        .wish-list { padding: 0; margin: 0; list-style: none; }
        .wish-item { display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid rgba(42, 71, 94, 0.3); }
        .btn-buy { background: #5c7e10; color: #d2efa9; border: none; padding: 8px 15px; border-radius: 2px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<nav class="steam-nav">
    <div style="font-weight: bold; letter-spacing: 2px;">STEAM CLONE</div>
</nav>

<div class="main-wrap">
    <aside class="steam-sidebar">
        <div class="sidebar-title">LIBRARY</div>
        <ul class="sidebar-menu">
            <li><a href="index.php">🏠 Dashboard</a></li>
            <li><a href="koleksi.php">🎮 Koleksi Saya</a></li>
            <li><a href="wishlist.php" class="active">🎁 Wishlist</a></li>
        </ul>
    </aside>

    <main>
        <h1 style="margin-top: 0;">Wishlist Saya</h1>
        <div class="wish-container">
            <div class="wish-header">GAME YANG KAMU INGINKAN</div>
            <ul class="wish-list">
                <?php if (!empty($wishlist)): ?>
                    <?php foreach ($wishlist as $item): ?>
                        <li class="wish-item">
                            <div>
                                <div style="color: var(--s-blue); font-size: 0.75rem;"><?= htmlspecialchars($item['kategori_nama']) ?></div>
                                <div style="font-weight: bold; font-size: 1.1rem;"><?= htmlspecialchars($item['judul']) ?></div>
                            </div>
                            <button class="btn-buy">Tambahkan Ke Koleksi</button>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="padding: 40px; text-align: center; color: var(--s-muted);">
                        Belum ada game di wishlist lo. <br>
                        <a href="koleksi_2.php" style="color: var(--s-blue); text-decoration: none;">Cari game sekarang?</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </main>
</div>

</body>
</html>