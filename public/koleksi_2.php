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
    $result = tambahKeKoleksi($pdo, $user_id, $game_id);
    $msg = $result ? "<div class='alert alert-success'>✅ Added to Library!</div>" : "<div class='alert alert-warning'>⚠️ Already in Library</div>";
} elseif (isset($_POST['add_to_wishlist'])) {
    $game_id = $_POST['game_id'];
    $result = tambahKeWishlist($pdo, $user_id, $game_id);
    $msg = $result ? "<div class='alert alert-success'>❤️ Added to Wishlist!</div>" : "<div class='alert alert-warning'>⚠️ Already in Wishlist</div>";
}
$page_title = "Steam Store";
?>

<?php include '../includes/header.php'; ?>

<div style="margin-bottom: 30px;">
    <a href="koleksi.php" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Kembali ke Koleksi
    </a>
</div>

<h1><i class="fas fa-store"></i> Steam Store</h1>

<?= $msg ?>

<div class="panel">
    <div class="panel-header">
        <i class="fas fa-gamepad"></i> Jelajahi Semua Game (<?= count($games) ?>)
    </div>
    <div class="panel-body">
        <div class="games-grid">
            <?php foreach($games as $game): ?>
                <div class="game-card">
                    <img src="<?= $game['gambar'] ?: '../assets/uploads/games/placeholder.png' ?>" alt="<?= htmlspecialchars($game['judul']) ?>" class="game-cover">
                    <div class="game-info">
                        <div class="game-title"><?= htmlspecialchars($game['judul']) ?></div>
                        <div class="game-meta">
                            <span class="badge badge-cat"><?= htmlspecialchars($game['kategori_nama']) ?></span>
                        </div>
<div style="gap: 10px; display: flex; flex-direction: column; margin-top: 15px;">
    <form method="POST">
        <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
        <button type="submit" name="add_to_library" class="btn btn-success">
            <i class="fas fa-plus"></i> Library
        </button>
    </form>
    <form method="POST">
        <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
        <button type="submit" name="add_to_wishlist" class="btn btn-secondary">
            <i class="fas fa-heart"></i> Wishlist
        </button>
    </form>
</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
