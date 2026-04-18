<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Halaman ini untuk setup database MySQL
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database - Koleksi Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .card-header {
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px 15px 0 0;
            color: white;
        }
        .check-item {
            padding: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        .check-item:last-child {
            border-bottom: none;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">✅ Setup Database MySQL</h2>
            </div>
            <div class="card-body">
                <h5 class="mb-3">Status Koneksi Database</h5>
                
                <?php
                // Cek status database
                $checks = [];
                
                // 1. Cek koneksi MySQL
                try {
                    $testPdo = new PDO(
                        'mysql:host=' . DB_HOST,
                        DB_USER,
                        DB_PASS
                    );
                    $checks['mysql'] = ['status' => true, 'text' => 'MySQL Server aktif'];
                } catch (Exception $e) {
                    $checks['mysql'] = ['status' => false, 'text' => 'MySQL tidak aktif: ' . $e->getMessage()];
                }
                
                // 2. Cek database ada
                try {
                    $pdo->query("SELECT 1");
                    $checks['database'] = ['status' => true, 'text' => 'Database "koleksigame" sudah dibuat'];
                } catch (Exception $e) {
                    $checks['database'] = ['status' => false, 'text' => 'Database belum ada: ' . $e->getMessage()];
                }
                
                // 3. Cek  tabel users ada
                try {
                    $pdo->query("SELECT 1 FROM users LIMIT 1");
                    $checks['tables'] = ['status' => true, 'text' => 'Semua tabel sudah dibuat'];
                } catch (Exception $e) {
                    $checks['tables'] = ['status' => false, 'text' => 'Tabel belum dibuat'];
                }
                
                // 4. Cek kategori ada
                try {
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM kategori");
                    $result = $stmt->fetch();
                    if ($result['total'] > 0) {
                        $checks['kategori'] = ['status' => true, 'text' => 'Kategori sudah tersedia (' . $result['total'] . ' items)'];
                    } else {
                        $checks['kategori'] = ['status' => false, 'text' => 'Kategori belum ada'];
                    }
                } catch (Exception $e) {
                    $checks['kategori'] = ['status' => false, 'text' => 'Tabel kategori masih error'];
                }
                
                // Tampilkan result
                foreach ($checks as $check => $data) {
                    $icon = $data['status'] ? '✅' : '❌';
                    $class = $data['status'] ? 'success' : 'error';
                    echo '<div class="check-item"><span class="' . $class . '">' . $icon . ' ' . $data['text'] . '</span></div>';
                }
                
                // Jika semua ok
                $all_ok = array_reduce($checks, function($carry, $item) {
                    return $carry && $item['status'];
                }, true);
                ?>
                
                <hr>
                
                <?php if ($all_ok): ?>
                <div class="alert alert-success" role="alert">
                    <h6 class="alert-heading">✅ Setup Berhasil!</h6>
                    <p class="mb-2">Database MySQL sudah siap digunakan.</p>
                    <p>Klik tombol di bawah untuk mulai menggunakan aplikasi.</p>
                </div>
                <a href="index.php" class="btn btn-success btn-lg w-100">
                    ▶️ Buka Dashboard
                </a>
                <?php else: ?>
                <div class="alert alert-warning" role="alert">
                    <h6 class="alert-heading">⚠️ Ada Beberapa Masalah</h6>
                    <p class="mb-0">Pastikan Laragon sudah jalan dan MySQL aktif.</p>
                </div>
                <button class="btn btn-warning w-100" onclick="location.reload()">
                    🔄 Cek Ulang
                </button>
                <?php endif; ?>
                
                <hr class="my-4">
                
                <h6 class="mb-3">Informasi Koneksi</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Host</strong></td>
                            <td><?php echo DB_HOST; ?></td>
                        </tr>
                        <tr>
                            <td><strong>User</strong></td>
                            <td><?php echo DB_USER; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Database</strong></td>
                            <td><?php echo DB_NAME; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Charset</strong></td>
                            <td>utf8mb4</td>
                        </tr>
                    </table>
                </div>
                
                <div class="alert alert-info" role="alert">
                    <small>
                        <strong>Troubleshooting:</strong><br>
                        1. Pastikan Laragon sudah Start All ✓<br>
                        2. Cek MySQL Service aktif ✓<br>
                        3. Refresh halaman (F5) ✓
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
