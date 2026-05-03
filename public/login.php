<?php
session_start();

// 1. KONEKSI DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "koleksigame"; // Pastikan nama DB ini sesuai di phpMyAdmin lo[cite: 7, 9]

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) { die("Koneksi gagal: " . mysqli_connect_error()); }

$error = "";
$success = "";

// 2. LOGIC LOGIN & DAFTAR[cite: 7, 9]
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    if (isset($_POST['type']) && $_POST['type'] == 'daftar') {
        // --- LOGIC DAFTAR ---
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hash biar aman
        
        $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$hashed_password', '$role')";
        if (mysqli_query($conn, $sql)) {
            $success = "Akun berhasil dibuat! Silakan login.";
        } else {
            $error = "Gagal daftar: " . mysqli_error($conn);
        }
    } else {
        // --- LOGIC LOGIN ---
        $query = "SELECT * FROM users WHERE username='$username' AND role='$role'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            // Verifikasi password hash
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['user_id'] = $row['id'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan untuk peran $role!";
        }
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Steam Style</title>
    <style>
        :root {
            --s-bg: #1b2838; --s-blue: #66c0f4; --s-border: #2a475e;
            --s-text: #c7d5e0; --s-muted: #8f98a0; --s-dark-blue: #171a21;
        }
        body {
            background-color: var(--s-bg); color: var(--s-text);
            font-family: sans-serif; margin: 0; min-height: 100vh;
            display: flex; justify-content: center; align-items: center;
            padding: 40px 20px; overflow-y: auto; /* FIX SCROLL */
        }
        .login-container { width: 100%; max-width: 450px; background: var(--s-dark-blue); border: 1px solid var(--s-border); border-radius: 4px; }
        .login-header { padding: 30px; text-align: center; background: linear-gradient(to bottom, rgba(42, 71, 94, 0.4), transparent); }
        .login-tabs { display: flex; border-bottom: 1px solid var(--s-border); }
        .tab-btn { flex: 1; padding: 15px; background: rgba(0,0,0,0.2); border: none; color: var(--s-muted); cursor: pointer; font-weight: bold; }
        .tab-btn.active { background: var(--s-blue); color: var(--s-dark-blue); }
        .login-body { padding: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 0.75rem; color: var(--s-muted); text-transform: uppercase; }
        .form-control { width: 100%; padding: 12px; background: #2a475e; border: 1px solid transparent; color: #fff; border-radius: 2px; box-sizing: border-box; }
        .role-selector { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .role-option { background: rgba(255,255,255,0.05); border: 1px solid var(--s-border); padding: 10px; text-align: center; cursor: pointer; border-radius: 4px; font-size: 0.85rem; }
        .role-option input { display: none; }
        .role-option.selected { border-color: var(--s-blue); background: rgba(102, 192, 244, 0.1); color: var(--s-blue); }
        .btn-submit { width: 100%; padding: 15px; background: linear-gradient(to right, #47bfff 0%, #1a44c2 100%); border: none; color: #fff; font-weight: bold; cursor: pointer; border-radius: 2px; }
        .msg { padding: 10px; border-radius: 2px; margin-bottom: 20px; font-size: 0.85rem; text-align: center; }
        .error-msg { background: #5a2a2a; color: #ff4d4d; }
        .success-msg { background: #2a5a2a; color: #4dff4d; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <div style="font-size: 40px;">🕹️</div>
        <h2>KOLEKSI GAME</h2>
    </div>

    <div class="login-tabs">
        <button class="tab-btn active" id="t-masuk" onclick="switchTab('masuk')">MASUK</button>
        <button class="tab-btn" id="t-daftar" onclick="switchTab('daftar')">DAFTAR</button>
    </div>

    <div class="login-body">
        <?php if($error): ?> <div class="msg error-msg"><?= $error ?></div> <?php endif; ?>
        <?php if($success): ?> <div class="msg success-msg"><?= $success ?></div> <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="type" id="form-type" value="login">
            
            <div class="form-group">
                <label>Pilih Peran</label>
                <div class="role-selector">
                    <label class="role-option selected" id="opt-pemain">
                        <input type="radio" name="role" value="pemain" checked onclick="selectRole('pemain')"> 🎮 Pemain
                    </label>
                    <label class="role-option" id="opt-dev">
                        <input type="radio" name="role" value="developer" onclick="selectRole('developer')"> 🛠️ Developer
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div id="email-field" class="form-group" style="display: none;">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn-submit" id="btn-text">MASUK KE AKUN</button>
        </form>
    </div>
</div>

<script>
    function selectRole(role) {
        document.getElementById('opt-pemain').classList.remove('selected');
        document.getElementById('opt-dev').classList.remove('selected');
        document.getElementById('opt-' + (role === 'pemain' ? 'pemain' : 'dev')).classList.add('selected');
    }

    function switchTab(type) {
        document.getElementById('t-masuk').classList.toggle('active', type === 'masuk');
        document.getElementById('t-daftar').classList.toggle('active', type === 'daftar');
        document.getElementById('email-field').style.display = (type === 'daftar' ? 'block' : 'none');
        document.getElementById('form-type').value = type;
        document.getElementById('btn-text').innerText = (type === 'daftar' ? 'DAFTAR SEKARANG' : 'MASUK KE AKUN');
    }
</script>

</body>
</html>