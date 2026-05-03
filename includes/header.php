<?php
// includes/header.php - Shared header/nav/sidebar
$categories = getKategori($pdo); // Assume $pdo available from page
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Steam Clone</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<nav class="steam-nav">
    <div class="nav-brand">
        <i class="fas fa-gamepad"></i> STEAM CLONE
    </div>
    <div class="nav-user">
        <span class="username"><?= htmlspecialchars($username ?? $_SESSION['username']) ?></span>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</nav>

<div class="main-wrap">
    <aside class="steam-sidebar">
        <div class="sidebar-section">
            <h3><i class="fas fa-bars"></i> Main Menu</h3>
            <ul class="sidebar-menu">
                <li><a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="koleksi.php" class="<?= $current_page == 'koleksi.php' ? 'active' : '' ?>"><i class="fas fa-gamepad"></i> Koleksi</a></li>
                <li><a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a></li>
            </ul>
        </div>
        <div class="sidebar-section">
            <h3><i class="fas fa-tags"></i> Categories</h3>
            <ul class="sidebar-menu">
                <?php foreach($categories as $cat): ?>
                    <li><a href="#"><i class="fas fa-circle"></i> <?= htmlspecialchars($cat['nama']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>

    <main class="main-content">

