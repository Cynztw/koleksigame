<?php
// public/koleksi_2.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$user_id = getCurrentUserId();
$username = $_SESSION['username'];

// Ambil semua game dari database global
$games = getAllGames($pdo);

$msg = "";
if (isset($_POST['add_to_library'])) {
    $game_id = $_POST['game_id'];
    // Masukkan ke koleksi user
    if (tambahKeKoleksi($pdo, $user_id, $game_id)) {
        $msg = "✅ Berhasil! Game udah masuk ke Library lo.";
    } else {
        $msg = "⚠️ Game ini udah ada di Library lo.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Store - Tambah Game</title>
    <style>
        :root { --s-bg: #1b2838; --s-nav: #171a21; --s-blue: #66c0f4; --s-border: #2a475e; --s-text: #c7d5e0; --s-muted: #8f98a0; }
        body { background: var(--s-bg); color: var(--s-text); font-family: 'Segoe UI', sans-serif; margin: 0; }
        .steam-nav { background: var(--s-nav); height: 65px; display: flex; align-items: center; padding: 0 2rem; border-bottom: 1px solid #000; }
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .back-btn { color: var(--s-muted); text-decoration: none; font-size: 0.9rem; margin-bottom: 20px; display: inline-block; }
        .game-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .game-card { background: rgba(42, 71, 94, 0.3); border: 1px solid var(--s-border); border-radius: 4px; padding: 20px; }
        .game-title { font-size: 1.1rem; font-weight: bold; display: block; margin-bottom: 5px; color: white; }
        .btn-add { background: #5c7e10; color: #d2efa9; border: none; padding: 10px; width: 100%; font-weight: bold; cursor: pointer; border-radius: 2px; }
        .alert { background: rgba(102, 192, 244, 0.1); border: 1px solid var(--s-blue); color: var(--s-blue); padding: 15px; margin-bottom: 20px; border-radius: 3px; }
    </style>
</head>
<body>

<nav class="steam-nav"><div style="font-weight: bold;">STEAM STORE</div></nav>

<div class="container">
    <a href="koleksi.php" class="back-btn">← Balik ke Koleksi Saya</a>
    <h1>Jelajahi Game</h1>
    <?php if($msg): ?><div class="alert"><?= $msg ?></div><?php endif; ?>
    <div class="game-grid">
        <?php foreach($games as $game): ?>
            <div class="game-card">
                <span style="font-size: 0.7rem; color: var(--s-blue);"><?= htmlspecialchars($game['kategori_nama']) ?></span>
                <span class="game-title"><?= htmlspecialchars($game['judul']) ?></span>
                <form method="POST">
                    <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
                    <button type="submit" name="add_to_library" class="btn-add">TAMBAH KE LIBRARY</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>