<?php
require_once 'includes/config.php';
require_once 'includes/service_catalog.php';

if (isset($_SESSION['user_id'])) {
    $dest = isDashboardUser() ? 'pages/dashboard.php' : 'pages/board_list.php';
    header('Location: ' . $dest);
    exit;
}

$serviceSlug = $serviceSlug ?? '';
$catalog = getServiceCatalog();
$service = $catalog[$serviceSlug] ?? null;

if (!$service) {
    http_response_code(404);
    $publicPage = 'services';
    $publicTitle = APP_NAME . ' | Service Not Found';
    require 'includes/public_header.php';
    ?>
    <section class="info-section">
        <div class="container">
            <div class="section-intro">
                <span class="eyebrow">Not Found</span>
                <h2>That service page is not available.</h2>
                <p>Please return to our service listing to explore the available support options.</p>
            </div>
            <a href="services.php" class="btn btn-primary">Back to Services</a>
        </div>
    </section>
    <?php
    require 'includes/public_footer.php';
    return;
}

$publicPage = 'services';
$publicTitle = APP_NAME . ' | ' . $service['title'];
require 'includes/public_header.php';
?>

<section class="service-detail-hero">
    <img src="<?php echo $service['hero_image']; ?>" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0;">
    <div style="position:absolute;inset:0;background:linear-gradient(120deg,rgba(5,16,38,0.84),rgba(22,70,121,0.56));z-index:1;"></div>
    <div class="container" style="position:relative;z-index:2;">
        <div class="service-detail-hero-copy">
            <span class="eyebrow"><?= htmlspecialchars($service['hero_eyebrow']) ?></span>
            <h1><?= htmlspecialchars($service['hero_title']) ?></h1>
            <p><?= htmlspecialchars($service['hero_text']) ?></p>

            <div class="hero-actions">
                <a href="contact.php" class="btn btn-primary btn-lg">Book Service</a>
                <a href="services.php" class="btn btn-light btn-lg">Back to Services</a>
            </div>
        </div>
    </div>
</section>

<section class="info-section service-detail-section">
    <div class="container service-detail-layout">
        <div class="service-detail-main">
            <div class="section-intro service-detail-intro">
                <span class="eyebrow">Service Overview</span>
                <h2><?= htmlspecialchars($service['overview_title']) ?></h2>
                <p><?= htmlspecialchars($service['overview_text']) ?></p>
            </div>

            <div class="public-booking-grid service-highlight-grid">
                <?php foreach ($service['hero_points'] as $index => $point): ?>
                    <article class="public-booking-step service-highlight-step">
                        <strong><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></strong>
                        <h3><?= htmlspecialchars($point) ?></h3>
                        <p>Handled through a practical workflow built around clear diagnosis and dependable support.</p>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="service-detail-panels">
                <article class="service-detail-panel">
                    <h3>What This Service Covers</h3>
                    <ul class="service-list">
                        <?php foreach ($service['inclusions'] as $item): ?>
                            <li><?= htmlspecialchars($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>

                <article class="service-detail-panel">
                    <h3>How We Handle It</h3>
                    <ul class="service-list">
                        <?php foreach ($service['process'] as $item): ?>
                            <li><?= htmlspecialchars($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            </div>
        </div>

        <aside class="service-detail-side">
            <article class="service-detail-panel service-detail-panel-accent">
                <h3>Why Choose This Service</h3>
                <ul class="service-list">
                    <?php foreach ($service['why'] as $item): ?>
                        <li><?= htmlspecialchars($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>

            <article class="service-detail-panel service-detail-cta">
                <span class="eyebrow">Ready To Reach Us?</span>
                <h3>Discuss your service requirement with our team.</h3>
                <p>Share the complaint, unit type and any previous repair history so we can guide the next step clearly.</p>
                <div class="service-detail-actions">
                    <a href="tel:+919087333397" class="btn btn-primary">Call 90873 33397</a>
                    <a href="contact.php" class="btn btn-secondary">Contact Page</a>
                </div>
            </article>
        </aside>
    </div>
</section>

<?php require 'includes/public_footer.php'; ?>
