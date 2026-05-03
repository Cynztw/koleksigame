<?php
// public/index.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$user_id = getCurrentUserId();
$username = $_SESSION['username'];
$role = getUserRole();

$categories = getKategori($pdo);

// Statistik[cite: 13]
$total_game = $pdo->query("SELECT COUNT(*) FROM game")->fetchColumn();
$stmt_kol = $pdo->prepare("SELECT COUNT(*) FROM koleksi WHERE user_id = ?");
$stmt_kol->execute([$user_id]);
$total_koleksi = $stmt_kol->fetchColumn();

$msg = "";
if ($role == 'developer' && isset($_POST['tambah_game_baru'])) {
    // Kita kirim 5 data: PDO, Judul, Kategori, Nama, dan ID User[cite: 11]
    $res = buatGame($pdo, $_POST['judul'], $_POST['kategori_id'], $username, $user_id);
    $msg = $res ? "✅ Game berhasil dipublikasikan!" : "❌ Gagal memproses data.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Steam Dashboard - Fix</title>
    <style>
        :root { --s-bg: #1b2838; --s-nav: #171a21; --s-blue: #66c0f4; --s-border: #2a475e; --s-text: #c7d5e0; --s-muted: #8f98a0; }
        body { background: var(--s-bg); color: var(--s-text); font-family: 'Segoe UI', sans-serif; margin: 0; }
        .steam-nav { background: var(--s-nav); height: 65px; display: flex; align-items: center; padding: 0 2rem; border-bottom: 1px solid #000; }
        .main-wrap { display: grid; grid-template-columns: 260px 1fr; gap: 30px; padding: 30px; max-width: 1200px; margin: 0 auto; }
        .steam-sidebar { background: rgba(0,0,0,0.2); border: 1px solid var(--s-border); padding: 20px; border-radius: 4px; }
        .sidebar-menu { list-style: none; padding: 0; margin-bottom: 20px; }
        .sidebar-menu li a { color: var(--s-text); text-decoration: none; padding: 10px; display: block; border-radius: 3px; }
        .sidebar-menu li a:hover, .active { background: rgba(102, 192, 244, 0.1); color: var(--s-blue); }
        .stat-card { background: rgba(42, 71, 94, 0.3); border: 1px solid var(--s-border); padding: 20px; border-radius: 4px; text-align: center; }
        .stat-val { font-size: 2rem; font-weight: bold; color: var(--s-blue); display: block; }
        .s-panel { background: rgba(0,0,0,0.3); border: 1px solid var(--s-border); border-radius: 4px; }
        .s-header { background: rgba(42, 71, 94, 0.5); padding: 12px 20px; font-weight: bold; border-bottom: 1px solid var(--s-border); }
        .s-body { padding: 25px; }
        .form-input { width: 100%; padding: 12px; background: #2a475e; border: 1px solid #1b2838; color: white; margin-top: 8px; margin-bottom: 20px; border-radius: 3px; box-sizing: border-box; }
        .btn-publish { background: #67c1f5; color: #171a21; border: none; padding: 12px 30px; font-weight: bold; cursor: pointer; border-radius: 2px; width: 100%; }
    </style>
</head>
<body>

<nav class="steam-nav">
    <div style="font-weight: bold; letter-spacing: 2px; font-size: 1.2rem;">STEAM CLONE</div>
    <div style="margin-left: auto; text-align: right;">
        <span style="display: block; font-weight: bold;"><?= htmlspecialchars($username) ?></span>
        <a href="logout.php" style="color: #ff4d4d; font-size: 0.75rem; text-decoration: none;">LOGOUT</a>
    </div>
</nav>

<div class="main-wrap">
    <aside class="steam-sidebar">
        <div style="color: var(--s-blue); font-size: 0.75rem; margin-bottom: 10px; font-weight: bold;">MAIN MENU</div>
        <ul class="sidebar-menu">
            <li><a href="index.php" class="active">🏠 Dashboard</a></li>
            <li><a href="koleksi.php">🎮 My Collection</a></li>
        </ul>

        <div style="color: var(--s-blue); font-size: 0.75rem; margin-bottom: 10px; font-weight: bold;">CATEGORIES</div>
        <ul class="sidebar-menu">
            <?php foreach($categories as $cat): ?>
                <li><a href="#"># <?= htmlspecialchars($cat['nama']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <main>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div class="stat-card">
                <span style="font-size: 0.75rem; color: var(--s-muted);">SYSTEM GAMES</span>
                <span class="stat-val"><?= $total_game ?></span>
            </div>
            <div class="stat-card">
                <span style="font-size: 0.75rem; color: var(--s-muted);">YOUR GAMES</span>
                <span class="stat-val"><?= $total_koleksi ?></span>
            </div>
        </div>

        <div class="s-panel">
            <div class="s-header"><?= ($role == 'developer') ? "🛠️ PUBLISH GAME" : "🎮 RECOMMENDATIONS" ?></div>
            <div class="s-body">
                <?php if($msg): ?> <div style="margin-bottom:20px; color: var(--s-blue);"><?= $msg ?></div> <?php endif; ?>

                <?php if($role == 'developer'): ?>
                    <form method="POST">
                        <label>Game Title</label>
                        <input type="text" name="judul" class="form-input" placeholder="Name of your game..." required>
                        
                        <label>Category</label>
                        <select name="kategori_id" class="form-input">
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <button type="submit" name="tambah_game_baru" class="btn-publish">SUBMIT TO SYSTEM</button>
                    </form>
                <?php else: ?>
                    <p style="color: var(--s-muted);">Browse games and add them to your collection.</p>
                    <a href="koleksi_2.php" class="btn-publish" style="text-decoration: none; display: block; text-align: center;">BROWSE GAMES</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

</body>
</html>