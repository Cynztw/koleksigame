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
    $pdo->exec("CREATE TABLE IF NOT EXISTS wishlist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        game_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $wishlist = [];
}

$page_title = "Wishlist";

if (isset($_POST['add_to_collection'])) {
    tambahKeKoleksi($pdo, $user_id, $_POST['game_id']);
    header("Location: wishlist.php");
    exit;
}
?>

<?php include '../includes/header.php'; ?>

<h1><i class="fas fa-heart"></i> Wishlist Saya</h1>

<div class="panel">
    <div class="panel-header">
        <i class="fas fa-gift"></i> Games yang Kamu Inginkan (<?= count($wishlist) ?>)
    </div>
    <div class="panel-body">
        <?php if (!empty($wishlist)): ?>
            <div class="games-grid">
                <?php foreach ($wishlist as $item): ?>
                    <div class="game-card">
                        <img src="<?= htmlspecialchars($item['gambar'] ?? 'assets/uploads/games/placeholder.png') ?>" alt="<?= htmlspecialchars($item['judul']) ?>" class="game-cover">
                        <div class="game-info">
                            <h3 class="game-title"><?= htmlspecialchars($item['judul']) ?></h3>
                            <div class="game-meta">
                                <span class="badge-cat"><?= htmlspecialchars($item['kategori_nama']) ?></span>
                                <span class="text-muted"><?= htmlspecialchars($item['platform'] ?? 'PC') ?></span>
                            </div>
<form method="POST">
    <input type="hidden" name="game_id" value="<?= $item['game_id'] ?>">
    <button type="submit" name="add_to_collection" class="btn btn-success w-100">
        <i class="fas fa-plus"></i> Tambah ke Koleksi
    </button>
</form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center" style="padding: 60px 20px;">
                <i class="fas fa-heart-broken" style="font-size: 4rem; color: var(--s-muted); margin-bottom: 20px;"></i>
                <h3 style="color: var(--s-muted);">Wishlist kosong</h3>
                <p class="text-muted mb-4">Belum ada game di wishlist kamu.</p>
                <a href="koleksi_2.php" class="btn btn-primary">
                    <i class="fas fa-store"></i> Jelajahi Store
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

