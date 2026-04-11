<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    $dest = isOwner() ? 'pages/dashboard.php' : 'pages/board_list.php';
    header('Location: ' . $dest);
    exit;
}

$publicPage = 'services';
$publicTitle = APP_NAME . ' | Services';
require 'includes/public_header.php';
?>

<section class="info-section services-section">
    <div class="container">
        <div class="section-intro">
            <span class="eyebrow">Services</span>
            <h2>Core service areas for home and commercial appliance support.</h2>
            <p>
                We keep the presentation simple and clear so customers can quickly understand the main
                service categories we handle.
            </p>
        </div>

        <div class="service-grid">
            <article class="service-card">
                <h3>AC Service</h3>
                <p>Installation support, troubleshooting, repair and maintenance for inverter and non-inverter AC units.</p>
            </article>
            <article class="service-card">
                <h3>Washing Machine</h3>
                <p>Routine service, issue diagnosis and repair for common washing machine electrical and board problems.</p>
            </article>
            <article class="service-card">
                <h3>Fridge Service</h3>
                <p>Refrigerator cooling checks, electrical repair and PCB-related service support for reliable performance.</p>
            </article>
            <article class="service-card">
                <h3>Commercial AC</h3>
                <p>Service support for commercial cooling systems with attention to uptime, stable performance and practical maintenance.</p>
            </article>
            <article class="service-card service-card-wide">
                <h3>All Brand AC Support</h3>
                <p>
                    Service available for multiple AC brands including leading residential and commercial systems,
                    with PCB chip-level attention where needed.
                </p>
            </article>
        </div>
    </div>
</section>

<?php require 'includes/public_footer.php'; ?>
