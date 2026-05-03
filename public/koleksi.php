<?php
// public/koleksi.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$user_id = getCurrentUserId();
$username = $_SESSION['username'];

// Ambil data koleksi dengan JOIN ke tabel game
try {
    $sql = "SELECT k.id, g.judul, g.gambar, k.platform, k.progress 
            FROM koleksi k 
            JOIN game g ON k.game_id = g.id 
            WHERE k.user_id = ?
            ORDER BY g.judul";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $koleksi = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
$page_title = "Koleksi Saya";
?>

<?php include '../includes/header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h1><i class="fas fa-gamepad"></i> Koleksi Game Saya (<?= count($koleksi) ?>)</h1>
    <a href="koleksi_2.php" class="btn btn-success">
        <i class="fas fa-plus"></i> Tambah Game
    </a>
</div>

<?php if (empty($koleksi)): ?>
    <div class="panel text-center">
        <div class="panel-body">
            <i class="fas fa-inbox" style="font-size: 4rem; color: var(--s-muted); margin-bottom: 20px;"></i>
            <h3>Library kosong</h3>
            <p class="text-muted">Belum ada game di koleksi. Mulai jelajahi store!</p>
            <a href="koleksi_2.php" class="btn btn-primary">Jelajahi Game</a>
        </div>
    </div>
<?php else: ?>
    <div class="panel">
<div class="panel-header">
            <i class="fas fa-list"></i> Daftar Koleksi
        </div>

        <link rel="stylesheet" href="../assets/css/fix.css">

        <div class="panel-body">
            <div class="games-grid">
                <?php foreach($koleksi as $item): 
                    $progress_percent = strlen($item['progress']) > 0 ? rand(10, 100) : 0; // Placeholder
                ?>
                    <div class="game-card">
<img src="<?= $item['gambar'] ? htmlspecialchars($item['gambar']) : '../assets/uploads/games/placeholder.png' ?>" alt="<?= htmlspecialchars($item['judul']) ?>" class="game-cover" onerror="this.src='../assets/uploads/games/placeholder.png'; this.style.filter='grayscale(1)'">
                        <div class="game-info">
                            <div class="game-title"><?= htmlspecialchars($item['judul']) ?></div>
                            <div class="game-meta">
                                <span class="badge badge-cat"><?= htmlspecialchars($item['platform']) ?></span>
                            </div>
                            <?php if($item['progress']): ?>
                                <div class="progress-wrap">
                                    <small>Progress: <?= htmlspecialchars($item['progress']) ?></small>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?= $progress_percent ?>%"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="edit-section">
                                <button class="btn btn-secondary w-100 edit-btn" data-id="<?= $item['id'] ?>" data-platform="<?= htmlspecialchars($item['platform'] ?? '') ?>" data-progress="<?= htmlspecialchars($item['progress'] ?? '') ?>">
                                    <i class="fas fa-edit"></i> Edit Progress
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Edit Modal -->
            <div id="editModal" class="modal">
                <div class="modal-content">
<div class="modal-header">
                        <h2><i class="fas fa-cog"></i> Edit Game</h2>
                        <span class="close">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            <div class="form-group">
                                <label class="form-label">Platform</label>
                                <select name="platform" class="form-control">
                                    <option value="PC">PC</option>
                                    <option value="PlayStation">PlayStation</option>
                                    <option value="Xbox">Xbox</option>
                                    <option value="Mobile">Mobile</option>
                                    <option value="Nintendo">Nintendo</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Progress Notes</label>
                                <textarea name="progress" class="form-control" rows="3" placeholder="Chapter 3 completed, 15/30 hours..."></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Game Photo</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Upload new screenshot (optional)</small>
                            </div>
                            <input type="hidden" name="koleksi_id" id="editKoleksiId">
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" class="btn btn-primary flex-grow">
                                    <i class="fas fa-save"></i> Save
                                </button>
                                <button type="button" class="btn btn-danger flex-grow delete-btn" id="deleteBtn">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
