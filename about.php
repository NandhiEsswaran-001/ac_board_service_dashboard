<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    $dest = isOwner() ? 'pages/dashboard.php' : 'pages/board_list.php';
    header('Location: ' . $dest);
    exit;
}

$publicPage = 'about';
$publicTitle = APP_NAME . ' | About Us';
require 'includes/public_header.php';
?>

<section class="info-section">
    <div class="container">
        <div class="section-intro">
            <span class="eyebrow">About Us</span>
            <h2>Traditional business values with practical electronic service expertise.</h2>
            <p>
                We focus on dependable diagnosis, clear service communication and smooth support for
                residential and commercial cooling appliances. Our work is centered on repair accuracy,
                timely handling and trusted local service.
            </p>
        </div>

        <div class="about-grid">
            <div class="about-card about-card-primary">
                <h3>What We Handle</h3>
                <p>
                    Inverter AC, non-inverter AC, refrigerators, washing machines, commercial AC systems
                    and PCB chip-level work across multiple leading brands.
                </p>
            </div>
            <div class="about-card about-card-secondary">
                <h3>Why Customers Choose Us</h3>
                <p>
                    Clean service process, experienced troubleshooting, traditional business reliability
                    and support that fits both household and service-center needs.
                </p>
            </div>
        </div>
    </div>
</section>

<?php require 'includes/public_footer.php'; ?>
