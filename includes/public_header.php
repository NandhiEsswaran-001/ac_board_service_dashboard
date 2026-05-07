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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css?v=<?= $cssVer ?>">
</head>
<body class="landing-page">
<header class="site-header">
    <div class="site-utility-bar">
        <div class="container site-utility-inner">
            <a href="tel:+919087333397"><i class="fas fa-phone-volume"></i> Call Us: 90873 33397</a>
            <a href="https://wa.me/919500933390" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> WhatsApp: 95009 33390</a>
            <span><i class="fas fa-location-dot"></i> Coimbatore Service Coverage</span>
            <div class="site-utility-social">
                <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
            </div>
        </div>
    </div>
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
