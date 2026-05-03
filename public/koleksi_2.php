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
    if (tambahKeKoleksi($pdo, $user_id, $game_id)) {
        $msg = "<div class='alert alert-success'>✅ Game berhasil ditambahkan ke library!</div>";
    } else {
        $msg = "<div class='alert alert-warning'>⚠️ Game sudah ada di library.</div>";
    }
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
                        <form method="POST" style="margin-top: 15px;">
                            <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
                            <button type="submit" name="add_to_library" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i> Tambah ke Library
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
