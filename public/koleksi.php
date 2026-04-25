<?php
require_once __DIR__ . '/../includes/functions.php';

// Require login
requireLogin();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_koleksi') {
            updateKoleksi($pdo, $_POST['koleksi_id'], $_POST['jam_main'], $_POST['persentase_selesai']);
            header('Location: koleksi.php?success=1');
            exit;
        }
        
        if ($_POST['action'] === 'delete_koleksi') {
            hapusDariKoleksi($pdo, $_POST['koleksi_id']);
            header('Location: koleksi.php?deleted=1');
            exit;
        }
        
        if ($_POST['action'] === 'update_status') {
            updateStatusGame($pdo, $_POST['game_id'], $_POST['status']);
            header('Location: koleksi.php?success=1');
            exit;
        }
    }
}

$koleksi = getKoleksiUser($pdo);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koleksi Saya - Koleksi Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">🎮 Koleksi Game</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="koleksi.php">Koleksi Saya</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="wishlist.php">Wishlist</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <?= isDeveloper() ? '🎮' : '👤' ?> <?= htmlspecialchars($_SESSION['username']) ?> 
                            <small style="opacity:0.7;">(<?= ucfirst(getUserRole()) ?>)</small>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="logout.php">Keluar</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Data berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            Game berhasil dihapus dari koleksi!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <h1 class="mb-4">Koleksi Game Saya</h1>

        <?php if (empty($koleksi)): ?>
        <div class="alert alert-info">
            <p>Anda belum memiliki game di koleksi. <a href="index.php">Tambah game sekarang</a></p>
        </div>
        <?php else: ?>

        <div class="row">
            <?php foreach ($koleksi as $k): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="position-relative">
                        <?php 
                        $img_path = !empty($k['gambar']) ? 'http://localhost/web%20pro%20S2/koleksigame/uploads/games/' . $k['gambar'] : 'http://localhost/web%20pro%20S2/koleksigame/uploads/games/placeholder.png';
                        ?>
                        <img src="<?php echo $img_path; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-<?php echo $k['status'] === 'tamat' ? 'success' : 'warning'; ?>">
                                <?php echo $k['status'] === 'tamat' ? '✓ Tamat' : '◯ Belum'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($k['judul']); ?></h5>
                        
                        <p class="card-text">
                            <small class="text-muted">
                                <strong>Kategori:</strong> <?php echo $k['kategori_nama'] ?? 'N/A'; ?><br>
                                <strong>Developer:</strong> <?php echo $k['developer_name'] ?? $k['developer_username'] ?? 'N/A'; ?><br>
                                <strong>Platform:</strong> <?php echo $k['platform'] ?? 'N/A'; ?><br>
                            </small>
                        </p>

                        <div class="mb-3">
                            <label class="form-label"><strong>Progress</strong></label>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $k['persentase_selesai']; ?>%">
                                    <?php echo $k['persentase_selesai']; ?>%
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $k['id']; ?>">
                            Edit
                        </button>
                        
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete_koleksi">
                            <input type="hidden" name="koleksi_id" value="<?php echo $k['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus dari koleksi?')">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?php echo $k['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit - <?php echo htmlspecialchars($k['judul']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="action" value="update_koleksi">
                                <input type="hidden" name="koleksi_id" value="<?php echo $k['id']; ?>">

                                <div class="mb-3">
                                    <label for="jam_main" class="form-label">Jam Main</label>
                                    <input type="number" class="form-control" id="jam_main" name="jam_main" value="<?php echo $k['jam_main']; ?>" min="0">
                                </div>

                                <div class="mb-3">
                                    <label for="persentase_selesai" class="form-label">Persentase Selesai (%)</label>
                                    <input type="number" class="form-control" id="persentase_selesai" name="persentase_selesai" value="<?php echo $k['persentase_selesai']; ?>" min="0" max="100">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
