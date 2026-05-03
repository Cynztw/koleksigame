<?php
// public/index.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$user_id = getCurrentUserId();
$username = $_SESSION['username'];
$role = getUserRole();

$categories = getKategori($pdo);

// Statistik
$total_game = $pdo->query("SELECT COUNT(*) FROM game")->fetchColumn();
$stmt_kol = $pdo->prepare("SELECT COUNT(*) FROM koleksi WHERE user_id = ?");
$stmt_kol->execute([$user_id]);
$total_koleksi = $stmt_kol->fetchColumn();

$msg = "";
if ($role == 'developer' && isset($_POST['tambah_game_baru'])) {
    $res = buatGame($pdo, $_POST['judul'], $_POST['kategori_id'], $username, $user_id);
    $msg = $res ? "<div class='alert alert-success'>✅ Game berhasil dipublikasikan!</div>" : "<div class='alert alert-danger'>❌ Gagal memproses data.</div>";
}
$page_title = "Dashboard";
?>

<?php include '../includes/header.php'; ?>

<h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-label"><i class="fas fa-globe"></i> System Games</span>
        <span class="stat-value"><?= $total_game ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label"><i class="fas fa-gamepad"></i> Your Collection</span>
        <span class="stat-value"><?= $total_koleksi ?></span>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <i class="fas fa-<?= $role == 'developer' ? 'hammer' : 'star' ?>"></i>
        <?= $role == 'developer' ? 'Publish New Game' : 'Recommendations' ?>
    </div>
    <div class="panel-body">
        <?= $msg ?>

        <?php if($role == 'developer'): ?>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-tag"></i> Game Title</label>
                    <input type="text" name="judul" class="form-control" placeholder="Enter game title..." required>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-list"></i> Category</label>
                    <select name="kategori_id" class="form-control">
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="tambah_game_baru" class="btn btn-primary w-100">
                    <i class="fas fa-rocket"></i> Submit to Steam
                </button>
            </form>
        <?php else: ?>
            <p class="text-muted mb-4">Discover amazing games and add them to your library!</p>
            <a href="koleksi_2.php" class="btn btn-success w-100 text-center">
                <i class="fas fa-store"></i> Browse Store
            </a>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
