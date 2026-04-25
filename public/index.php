<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/upload.php';

// Require login
requireLogin();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_game') {
            // Require developer role
            if (!isDeveloper()) {
                die('<div style="padding:20px; background:#fee; color:#c33; font-family:Arial; border-radius:5px; margin:20px;">
                    ❌ Akses ditolak! Hanya developer yang bisa menambah game.
                    <br><br><a href="index.php">← Kembali</a>
                </div>');
            }
            
            $gambar = '';
            
            // Handle file upload
            if (isset($_FILES['gambar']) && $_FILES['gambar']['size'] > 0) {
                $upload_result = uploadGameImage($_FILES['gambar']);
                if ($upload_result['success']) {
                    $gambar = $upload_result['filename'];
                }
            }
            
            $game_id = buatGame($pdo, $_POST['judul'], $_POST['kategori_id'], $_POST['developer_name'] ?? '', $_POST['tahun_rilis'] ?? null, $gambar);
            
            if (isset($_POST['tambah_koleksi'])) {
                tambahKeKoleksi($pdo, $game_id, $_POST['platform_koleksi'] ?? 'PC', 0, 0);
            }
            
            if (isset($_POST['tambah_wishlist'])) {
                tambahKeWishlist($pdo, $game_id, $_POST['prioritas'] ?? 5, $_POST['alasan'] ?? '');
            }
            
            header('Location: index.php?success=1');
            exit;
        }
        
        if ($_POST['action'] === 'add_to_koleksi') {
            tambahKeKoleksi($pdo, $_POST['game_id'], $_POST['platform'] ?? 'PC');
            
            // Jika checkbox "Juga tambah ke Wishlist" dicentang
            if (isset($_POST['add_wishlist_checkbox'])) {
                tambahKeWishlist($pdo, $_POST['game_id'], $_POST['wishlist_prioritas'] ?? 5, '');
            }
            
            header('Location: index.php?success=1');
            exit;
        }
        
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
    }
}

// Get appropriate data based on role
if (isDeveloper()) {
    $games = getGames($pdo);  // Developer's own games
} else {
    $games = getAvailableGames($pdo);  // All available games for pemain
}

$kategori = getKategori($pdo);
$koleksi = getKoleksiUser($pdo);
$wishlist = getWishlistUser($pdo);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koleksi Game - Dashboard</title>
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
                        <a class="nav-link active" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="koleksi.php">Koleksi Saya</a>
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
            Data berhasil disimpan!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="mb-4">Dashboard</h1>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Game</h5>
                                <h2><?php echo count($games); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Koleksi Saya</h5>
                                <h2><?php echo count($koleksi); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Wishlist</h5>
                                <h2><?php echo count($wishlist); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <?php if (isDeveloper()): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">🎮 Tambah Game Baru</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="add_game">
                            
                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Game</label>
                                <input type="text" class="form-control" id="judul" name="judul" required>
                            </div>

                            <div class="mb-3">
                                <label for="kategori_id" class="form-label">Kategori</label>
                                <select class="form-select" id="kategori_id" name="kategori_id">
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($kategori as $k): ?>
                                    <option value="<?php echo $k['id']; ?>"><?php echo $k['nama']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="developer_name" class="form-label">Nama Studio / Developer</label>
                                <input type="text" class="form-control" id="developer_name" name="developer_name" placeholder="Cth: Valve, FromSoftware">
                            </div>

                            <div class="mb-3">
                                <label for="gambar" class="form-label">Gambar Cover Game</label>
                                <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, GIF (Max 5MB)</small>
                                <div id="preview-gambar" style="margin-top: 10px;"></div>
                            </div>

                            <div class="mb-3">
                                <label for="tahun_rilis" class="form-label">Tahun Rilis</label>
                                <input type="number" class="form-control" id="tahun_rilis" name="tahun_rilis" min="1980">
                            </div>

                            <div class="mb-3">
                                <label for="platform_default" class="form-label">Platform Tersedia</label>
                                <select class="form-select" id="platform_default" name="platform_default" multiple>
                                    <option value="PC" selected>PC</option>
                                    <option value="PS4">PlayStation 4</option>
                                    <option value="PS5">PlayStation 5</option>
                                    <option value="Xbox One">Xbox One</option>
                                    <option value="Xbox X/S">Xbox Series X/S</option>
                                    <option value="Nintendo Switch">Nintendo Switch</option>
                                    <option value="Mobile">Mobile</option>
                                </select>
                                <small class="text-muted">Pilih satu atau lebih platform</small>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="tambah_koleksi" name="tambah_koleksi">
                                <label class="form-check-label" for="tambah_koleksi">
                                    Tambah ke Koleksi Saya
                                </label>
                            </div>

                            <div id="koleksi_options" style="display:none;">
                                <div class="mb-3">
                                    <label for="platform_koleksi" class="form-label">Platform Koleksi Saya</label>
                                    <input type="text" class="form-control" id="platform_koleksi" name="platform_koleksi" placeholder="e.g: Steam, Epic Games...">
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="tambah_wishlist" name="tambah_wishlist">
                                <label class="form-check-label" for="tambah_wishlist">
                                    Tambah ke Wishlist
                                </label>
                            </div>

                            <div id="wishlist_options" style="display:none;">
                                <div class="mb-3">
                                    <label for="prioritas" class="form-label">Prioritas (1-10)</label>
                                    <select class="form-select" id="prioritas" name="prioritas">
                                        <option value="5">5 - Normal</option>
                                        <option value="1">1 - Sangat Tinggi</option>
                                        <option value="3">3 - Tinggi</option>
                                        <option value="7">7 - Rendah</option>
                                        <option value="10">10 - Sangat Rendah</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="alasan" class="form-label">Alasan</label>
                                    <textarea class="form-control" id="alasan" name="alasan" rows="2"></textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Tambah Game</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php elseif (isPemain()): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">➕ Tambah Game ke Koleksi</h5>
                    </div>
                    <div class="card-body">
                        <!-- DEBUG -->
                        <div style="background: #fff3cd; padding: 10px; margin-bottom: 10px; border-radius: 4px; font-size: 0.85rem;">
                            Role: <strong><?= htmlspecialchars(getUserRole()) ?></strong> | 
                            Games: <strong><?= count($games) ?></strong>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="add_to_koleksi">
                            
                            <div class="mb-3">
                                <label for="available_games" class="form-label">Pilih Game</label>
                                <select class="form-select" id="available_games" name="game_id" required>
                                    <option value="">-- Pilih Game --</option>
                                    <?php foreach ($games as $g): ?>
                                    <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['judul']) ?> (<?= htmlspecialchars($g['developer_name'] ?? $g['developer_username'] ?? 'Unknown') ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="platform" class="form-label">Platform</label>
                                <select class="form-select" id="platform" name="platform">
                                    <option value="PC">PC</option>
                                    <option value="PS4">PlayStation 4</option>
                                    <option value="PS5">PlayStation 5</option>
                                    <option value="Xbox One">Xbox One</option>
                                    <option value="Xbox X/S">Xbox Series X/S</option>
                                    <option value="Nintendo Switch">Nintendo Switch</option>
                                    <option value="Mobile">Mobile</option>
                                </select>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="add_wishlist_checkbox" name="add_wishlist_checkbox">
                                <label class="form-check-label" for="add_wishlist_checkbox">
                                    Juga tambah ke Wishlist
                                </label>
                            </div>

                            <div id="wishlist_priority" style="display:none;">
                                <div class="mb-3">
                                    <label for="wishlist_prioritas" class="form-label">Prioritas (1-10)</label>
                                    <select class="form-select" id="wishlist_prioritas" name="wishlist_prioritas">
                                        <option value="5">5 - Normal</option>
                                        <option value="1">1 - Sangat Tinggi</option>
                                        <option value="3">3 - Tinggi</option>
                                        <option value="7">7 - Rendah</option>
                                        <option value="10">10 - Sangat Rendah</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-info w-100">Tambah ke Koleksi</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Koleksi Terbaru (5)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($koleksi)): ?>
                        <p class="text-muted">Belum ada game di koleksi</p>
                        <?php else: ?>
                        <div class="row">
                            <?php $count = 0; foreach ($koleksi as $k): if ($count >= 5) break; $count++; 
                                $img_path = !empty($k['gambar']) ? 'http://localhost/web%20pro%20S2/koleksigame/uploads/games/' . $k['gambar'] : 'http://localhost/web%20pro%20S2/koleksigame/uploads/games/placeholder.png';
                            ?>
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <img src="<?php echo $img_path; ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?php echo htmlspecialchars($k['judul']); ?></h6>
                                        <small class="text-muted"><?php echo $k['platform'] ?? '-'; ?></small><br>
                                        <small class="text-info"><?php echo $k['kategori_nama'] ?? '-'; ?></small>
                                        <div class="mt-2">
                                            <div class="progress" style="height: 15px;">
                                                <div class="progress-bar" style="width: <?php echo $k['persentase_selesai']; ?>%">
                                                    <?php echo $k['persentase_selesai']; ?>%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview gambar
        document.getElementById('gambar')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview-gambar');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.innerHTML = '<img src="' + event.target.result + '" style="max-width: 100%; height: 150px; object-fit: cover; border-radius: 5px;">';
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        });
        
        document.getElementById('tambah_koleksi').addEventListener('change', function() {
            document.getElementById('koleksi_options').style.display = this.checked ? 'block' : 'none';
        });

        document.getElementById('tambah_wishlist').addEventListener('change', function() {
            document.getElementById('wishlist_options').style.display = this.checked ? 'block' : 'none';
        });
        document.getElementById('add_wishlist_checkbox').addEventListener('change', function() {
            document.getElementById('wishlist_priority').style.display = this.checked ? 'block' : 'none';
        });
    </script>
</body>
</html>
