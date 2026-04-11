<?php
$publicPage = $publicPage ?? 'home';
$publicTitle = $publicTitle ?? APP_NAME;
$cssFile = __DIR__ . '/../css/style.css';
$cssVer = file_exists($cssFile) ? filemtime($cssFile) : time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($publicTitle) ?></title>
    <link rel="stylesheet" href="css/style.css?v=<?= $cssVer ?>">
</head>
<body class="landing-page">
<header class="site-header">
    <div class="container header-inner">
        <a href="index.php" class="brand-mark">
            <img src="assets/hot-cold-logo.jpg" alt="Hot and Cold Engineering logo">
        </a>

        <button class="public-menu-btn" id="publicMenuBtn" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="publicNav">
            <span></span><span></span><span></span>
        </button>

        <nav class="landing-nav" id="publicNav">
            <a href="index.php" class="<?= $publicPage === 'home' ? 'active' : '' ?>">Home</a>
            <a href="about.php" class="<?= $publicPage === 'about' ? 'active' : '' ?>">About Us</a>
            <a href="services.php" class="<?= $publicPage === 'services' ? 'active' : '' ?>">Services</a>
            <a href="contact.php" class="<?= $publicPage === 'contact' ? 'active' : '' ?>">Contact Us</a>
            <a href="login.php" class="btn btn-primary">Login</a>
        </nav>
    </div>
</header>

<div class="public-nav-overlay" id="publicNavOverlay"></div>

<main>
