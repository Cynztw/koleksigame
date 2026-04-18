<?php
require_once __DIR__ . '/includes/config.php';

echo "<h1>🔍 Database Debug Test</h1>";
echo "<hr>";

// Test 1: Check koneksi database
echo "<h3>1. Test Koneksi Database</h3>";
try {
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch();
    echo "✅ Database terhubung: <strong>" . $result['db_name'] . "</strong>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "<hr>";

// Test 2: Check users table structure
echo "<h3>2. Struktur Table Users</h3>";
try {
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . $col['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "<hr>";

// Test 3: Check users count
echo "<h3>3. Jumlah Users di Database</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch();
    echo "Total users: <strong>" . $result['total'] . "</strong>";
    
    echo "<h4>Data Users:</h4>";
    $stmt = $pdo->query("SELECT id, username, email, role FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "<p style='color:red;'>❌ Belum ada user di database!</p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['username'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "<hr>";

// Test 4: Test registrasi dummy
echo "<h3>4. Test Registrasi User Baru</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/includes/functions.php';
    
    $username = $_POST['test_username'] ?? '';
    $email = $_POST['test_email'] ?? '';
    $password = $_POST['test_password'] ?? '';
    
    if (!empty($username) && !empty($email) && !empty($password)) {
        $result = registerUser($pdo, $username, $email, $password, 'pemain');
        
        if ($result['success']) {
            echo "<div style='background:#efe; padding:10px; border-radius:5px; color:green;'>";
            echo "✅ " . $result['message'];
            echo "</div>";
        } else {
            echo "<div style='background:#fee; padding:10px; border-radius:5px; color:red;'>";
            echo "❌ " . $result['error'];
            echo "</div>";
        }
    }
}
?>

<form method="POST">
    <h4>Coba registrasi user:</h4>
    <p>
        <label>Username: </label>
        <input type="text" name="test_username" value="testuser123" />
    </p>
    <p>
        <label>Email: </label>
        <input type="email" name="test_email" value="testuser@example.com" />
    </p>
    <p>
        <label>Password: </label>
        <input type="password" name="test_password" value="testpass123" />
    </p>
    <p>
        <button type="submit">Test Register</button>
    </p>
</form>

<hr>
<p><a href="public/login.php">← Kembali ke Login</a></p>
