<?php
requireLogin();
sendNoCacheHeaders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?><?= APP_NAME ?></title>
    <?php
    $root = $rootPath ?? '../';
    $cssFile = __DIR__ . '/' . $root . 'css/style.css';
    $cssVer = file_exists($cssFile) ? filemtime($cssFile) : time();
    ?>
    <link rel="stylesheet" href="<?= $root ?>css/style.css?v=<?= $cssVer ?>">
</head>
<body>

<div class="app-wrapper">

<!-- Mobile Top Bar -->
<div class="mobile-topbar">
    <button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu">
        <span></span><span></span><span></span>
    </button>
    <div class="mobile-logo">
        <span style="color:#3498db;font-size:20px;">❄</span>
        <strong style="color:#ecf0f1;font-size:14px;margin-left:6px;">AC Service</strong>
    </div>
    <div style="width:40px;"></div>
</div>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo-icon">❄</div>
        <div class="logo-text">
            <strong>AC Service</strong>
            <small>Management</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php if (isOwner()): ?>
        <div class="nav-section-label">MAIN</div>
        <a href="<?= $rootPath ?? '../' ?>pages/dashboard.php" class="nav-item <?= (basename($_SERVER['PHP_SELF'])=='dashboard.php')?'active':'' ?>">
            <span class="nav-icon">▪</span> Dashboard
        </a>
        <?php endif; ?>

        <div class="nav-section-label">BOARD SERVICE</div>
        <a href="<?= $rootPath ?? '../' ?>pages/board_list.php" class="nav-item <?= (basename($_SERVER['PHP_SELF'])=='board_list.php')?'active':'' ?>">
            <span class="nav-icon">▪</span> All Boards
        </a>
        <a href="<?= $rootPath ?? '../' ?>pages/board_new.php" class="nav-item <?= (basename($_SERVER['PHP_SELF'])=='board_new.php')?'active':'' ?>">
            <span class="nav-icon">▪</span> New Board Entry
        </a>

        <div class="nav-section-label">FIELD SERVICE</div>
        <a href="<?= $rootPath ?? '../' ?>pages/field_list.php" class="nav-item <?= (basename($_SERVER['PHP_SELF'])=='field_list.php')?'active':'' ?>">
            <span class="nav-icon">▪</span> All Field Services
        </a>
        <a href="<?= $rootPath ?? '../' ?>pages/field_new.php" class="nav-item <?= (basename($_SERVER['PHP_SELF'])=='field_new.php')?'active':'' ?>">
            <span class="nav-icon">▪</span> New Field Service
        </a>

        <?php if (isOwner()): ?>
        <div class="nav-section-label">REPORTS</div>
        <a href="<?= $rootPath ?? '../' ?>pages/technician_report.php" class="nav-item <?= (basename($_SERVER['PHP_SELF'])=='technician_report.php')?'active':'' ?>">
            <span class="nav-icon">▪</span> Technician Report
        </a>
        <?php endif; ?>

        <?php if (isOwner()): ?>
        <div class="nav-section-label">ADMIN</div>
        <a href="<?= $rootPath ?? '../' ?>pages/users.php" class="nav-item <?= (basename($_SERVER['PHP_SELF'])=='users.php')?'active':'' ?>">
            <span class="nav-icon">▪</span> Manage Users
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?></span>
            <span class="user-role"><?= ucfirst($_SESSION['role'] ?? '') ?></span>
        </div>
        <a href="<?= $rootPath ?? '../' ?>logout.php" class="btn-logout">Logout</a>
    </div>
</aside>

<!-- Main Content -->
<main class="main-content">
    <div class="topbar">
        <h1 class="page-title"><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard' ?></h1>
        <div class="topbar-right">
            <span class="date-display"><?= date('l, d F Y') ?></span>
        </div>
    </div>
    <div class="content-area">
