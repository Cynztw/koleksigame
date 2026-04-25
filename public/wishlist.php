<?php
require_once __DIR__ . '/../includes/functions.php';

// Require login
requireLogin();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_to_wishlist') {
            tambahKeWishlist($pdo, $_POST['game_id'], $_POST['prioritas'] ?? 5, $_POST['alasan'] ?? '');
            header('Location: index.php?success=1');
            exit;
        }
        
        if ($_POST['action'] === 'delete_wishlist') {
            hapusDariWishlist($pdo, $_POST['wishlist_id']);
            header('Location: wishlist.php?deleted=1');
            exit;
        }
    }
}

$wishlist = getWishlistUser($pdo);

// Get games sesuai role
if (isDeveloper()) {
    $games = getGames($pdo);  // Developer's own games
} else {
    $games = getAvailableGames($pdo);  // All available games for pemain
}

$kategori = getKategori($pdo);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - Koleksi Game</title>
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
                        <a class="nav-link" href="koleksi.php">Koleksi Saya</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="wishlist.php">Wishlist</a>
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
            Game berhasil ditambahkan ke wishlist!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            Game berhasil dihapus dari wishlist!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <h1 class="mb-4">Wishlist Game</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Tambah Game ke Wishlist</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="add_to_wishlist">
                            
                            <div class="mb-3">
                                <label for="game_id" class="form-label">Pilih Game</label>
                                <select class="form-select" id="game_id" name="game_id" required>
                                    <option value="">-- Pilih Game --</option>
                                    <?php foreach ($games as $g): ?>
                                    <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['judul']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="prioritas" class="form-label">Prioritas (1-10)</label>
                                <select class="form-select" id="prioritas" name="prioritas">
                                    <option value="1">1 - Sangat Tinggi</option>
                                    <option value="3">3 - Tinggi</option>
                                    <option value="5" selected>5 - Normal</option>
                                    <option value="7">7 - Rendah</option>
                                    <option value="10">10 - Sangat Rendah</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="alasan" class="form-label">Alasan (Opsional)</label>
                                <textarea class="form-control" id="alasan" name="alasan" rows="3" placeholder="Kenapa ingin game ini?"></textarea>
                            </div>

                            <button type="submit" class="btn btn-warning w-100">Tambah ke Wishlist</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Informasi Wishlist</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Total Game:</strong> <?php echo count($wishlist); ?></p>
                        <p class="text-muted">Kelola game yang ingin Anda beli di masa depan.</p>
                        <div class="alert alert-info" role="alert">
                            <small>Prioritas 1 = Paling Tinggi | Prioritas 10 = Paling Rendah</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="mt-5 mb-4">Daftar Wishlist</h3>

        <?php if (empty($wishlist)): ?>
        <div class="alert alert-info">
            <p>Wishlist Anda kosong. <a href="index.php">Tambahkan game</a> dari dashboard.</p>
        </div>
        <?php else: ?>

        <div class="row">
            <?php foreach ($wishlist as $w): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title"><?php echo htmlspecialchars($w['judul']); ?></h5>
                            <?php 
                            $prioritas_label = [
                                1 => '<span class="badge bg-danger">Sangat Tinggi</span>',
                                3 => '<span class="badge bg-warning text-dark">Tinggi</span>',
                                5 => '<span class="badge bg-secondary">Normal</span>',
                                7 => '<span class="badge bg-info">Rendah</span>',
                                10 => '<span class="badge bg-light text-dark">Sangat Rendah</span>'
                            ];
                            echo $prioritas_label[$w['prioritas']] ?? '<span class="badge bg-secondary">' . $w['prioritas'] . '</span>';
                            ?>
                        </div>
                        
                        <p class="text-muted small mb-2">
                            <strong>Developer:</strong> <?php echo htmlspecialchars($w['developer_username'] ?? 'Unknown'); ?>
                        </p>
                        
                        <p class="text-muted small mb-2">
                            <strong>Kategori:</strong> <?php echo htmlspecialchars($w['kategori_nama'] ?? 'N/A'); ?>
                        </p>

                        <?php if ($w['alasan']): ?>
                        <div class="card bg-light mb-3" style="font-size: 0.9rem;">
                            <div class="card-body p-2">
                                <small><strong>Alasan:</strong> <?php echo htmlspecialchars($w['alasan']); ?></small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <small class="text-muted d-block mb-3">
                            Ditambahkan: <?php echo date('d M Y', strtotime($w['tanggal_ditambah'])); ?>
                        </small>

                        <div class="d-grid gap-2">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_wishlist">
                                <input type="hidden" name="wishlist_id" value="<?php echo $w['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus dari wishlist?')">🗑️ Hapus dari Wishlist</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <hr class="my-5">

        <div class="row">
            <div class="col-md-12">
                <h4>Ringkasan Wishlist</h4>
                <div class="alert alert-info">
                    <p class="mb-0">📊 <strong>Total Game:</strong> <?php echo count($wishlist); ?> | 
                    <strong>Prioritas Tertinggi:</strong> 
                    <?php 
                    $highest = null;
                    foreach ($wishlist as $w) {
                        if ($w['prioritas'] == 1) {
                            $highest = htmlspecialchars($w['judul']);
                            break;
                        }
                    }
                    echo $highest ?? '-';
                    ?>
                    </p>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
