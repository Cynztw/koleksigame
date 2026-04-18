<?php
require_once __DIR__ . '/../includes/functions.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        if (loginUser($pdo, $username, $password)) {
            $redirect = $_GET['redirect'] ?? 'index.php';
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    }
}

// Handle register
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = $_POST['reg_username'] ?? '';
    $email = $_POST['reg_email'] ?? '';
    $password = $_POST['reg_password'] ?? '';
    $confirm_password = $_POST['reg_confirm_password'] ?? '';
    $role = $_POST['reg_role'] ?? 'pemain';
    
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        $result = registerUser($pdo, $username, $email, $password, $role);
        if ($result['success']) {
            $success = $result['message'];
            // Clear form untuk ditampilkan tab login
            $username = '';
            $email = '';
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Koleksi Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 1.5rem;
            text-align: center;
        }

        .login-header h2 {
            font-weight: 700;
            margin: 0;
            font-size: 1.8rem;
        }

        .login-header p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
        }

        .login-form {
            padding: 2rem 1.5rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-label {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.6rem;
            display: block;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            padding: 0.8rem;
            border-radius: 10px;
            border: none;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
            color: #9ca3af;
            font-size: 0.9rem;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            background: white;
            position: relative;
            padding: 0 0.5rem;
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
            color: #dc2626;
            border-left: 4px solid #ef4444;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1));
            color: #059669;
            border-left: 4px solid #10b981;
        }

        .tab-buttons {
            display: flex;
            gap: 0;
            margin-bottom: 1.5rem;
        }

        .tab-btn {
            flex: 1;
            padding: 0.7rem;
            border: 2px solid #e5e7eb;
            background: white;
            color: #6b7280;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .login-footer {
            text-align: center;
            padding: 1rem 1.5rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        .login-footer p {
            margin: 0;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h2>🎮 Koleksi Game</h2>
            <p>Kelola koleksi game favoritmu</p>
        </div>

        <div class="login-form">
            <!-- Tab buttons -->
            <div class="tab-buttons">
                <button class="tab-btn active" onclick="switchTab('login')">Masuk</button>
                <button class="tab-btn" onclick="switchTab('register')">Daftar</button>
            </div>

            <!-- Login Tab -->
            <div id="login" class="tab-content active">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required autofocus>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>

                    <button type="submit" class="btn-login">Masuk</button>
                </form>
            </div>

            <!-- Register Tab -->
            <div id="register" class="tab-content">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ❌ <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        ✅ <?= htmlspecialchars($success) ?>
                        <br><br><strong>Sekarang masuk dengan akun baru Anda di tab "Masuk"</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="reg_username" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="reg_email" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipe Akun</label>
                        <select class="form-control" name="reg_role" required>
                            <option value="pemain">👤 Pemain (Manage koleksi & wishlist)</option>
                            <option value="developer">🎮 Developer (Bisa tambah game)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="reg_password" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" name="reg_confirm_password" required>
                    </div>

                    <button type="submit" class="btn-login">Daftar</button>
                </form>
            </div>
        </div>

        <div class="login-footer">
            <p><strong>Demo Accounts:</strong><br>
            👤 <strong>demo_pemain / demo123</strong> (Pemain)<br>
            🎮 <strong>demo_dev / demo123</strong> (Developer)</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function switchTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tab).classList.add('active');
            event.target.classList.add('active');
        }

        // Auto-switch to register tab jika ada error/success di register
        window.addEventListener('load', function() {
            <?php if (!empty($error) || !empty($success)): ?>
                document.querySelectorAll('.tab-btn')[1].click();
            <?php endif; ?>
        });
    </script>
</body>
</html>
