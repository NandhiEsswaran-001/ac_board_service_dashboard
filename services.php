<?php
require_once 'includes/config.php';
require_once 'includes/service_catalog.php';

if (isset($_SESSION['user_id'])) {
    $dest = isDashboardUser() ? 'pages/dashboard.php' : 'pages/board_list.php';
    header('Location: ' . $dest);
    exit;
}

$services = getServiceCatalog();
$serviceIcons = [
    'ac-service' => 'fa-fan',
    'commercial-ac-service' => 'fa-building',
    'fridge-service' => 'fa-temperature-low',
    'washing-machine-service' => 'fa-soap',
    'inverter-ac-pcb-service' => 'fa-microchip',
    'all-brand-ac-support' => 'fa-layer-group',
    'residential-support' => 'fa-house',
    'commercial-support' => 'fa-city',
];

$publicPage = 'services';
$publicTitle = APP_NAME . ' | Services';
require 'includes/public_header.php';
?>

<section class="page-hero-lite page-hero-services">
    <div class="container page-hero-inner">
        <span class="page-kicker">Our Services</span>
        <h1>Complete repair and service support across cooling systems and appliances.</h1>
        <p>
            Explore AC, appliance, commercial and PCB service categories presented in a cleaner,
            more structured layout inspired by your reference design.
        </p>
    </div>
</section>

<section class="info-section public-services-overview">
    <div class="container">
        <div class="section-intro public-section-intro">
            <span class="section-tag">Service Categories</span>
            <h2 class="section-title">Core solutions we handle for homes and businesses.</h2>
            <p>Each category is designed to make it easier to understand the kind of support available.</p>
        </div>

        <div class="public-services-grid">
            <?php foreach ($services as $slug => $service): ?>
                <?php $icon = $serviceIcons[$slug] ?? 'fa-screwdriver-wrench'; ?>
                <a class="public-service-block" href="<?= htmlspecialchars($service['page']) ?>">
                    <span class="public-service-icon"><i class="fas <?= htmlspecialchars($icon) ?>"></i></span>
                    <div class="public-service-copy">
                        <span class="public-service-kicker"><?= htmlspecialchars($service['hero_eyebrow']) ?></span>
                        <h3><?= htmlspecialchars($service['title']) ?></h3>
                        <p><?= htmlspecialchars($service['card_text']) ?></p>
                        <ul class="public-service-list">
                            <?php foreach (array_slice($service['hero_points'], 0, 3) as $point): ?>
                                <li><?= htmlspecialchars($point) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <span class="service-card-cta">View service details</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="public-process-section">
    <div class="container">
        <div class="section-intro public-section-intro">
            <span class="section-tag">Service Process</span>
            <h2 class="section-title">How service requests move from complaint to completion.</h2>
            <p>A simple and transparent process that works across most service categories.</p>
        </div>

        <div class="public-process-grid">
            <article class="public-process-card">
                <strong>01</strong>
                <h3>Share the issue</h3>
                <p>Tell us the appliance type, problem and location through call or contact page.</p>
            </article>
            <article class="public-process-card">
                <strong>02</strong>
                <h3>Diagnosis and inspection</h3>
                <p>We inspect the complaint and identify the likely cause before recommending work.</p>
            </article>
            <article class="public-process-card">
                <strong>03</strong>
                <h3>Repair or maintenance path</h3>
                <p>The service plan is handled clearly based on the condition of the unit.</p>
            </article>
            <article class="public-process-card">
                <strong>04</strong>
                <h3>Completion and support</h3>
                <p>The unit is checked after work so the result is practical and dependable.</p>
            </article>
        </div>
    </div>
</section>

<section class="public-brand-band">
    <div class="container">
        <div class="section-intro public-section-intro public-section-intro-invert">
            <span class="section-tag section-tag-invert">Brands</span>
            <h2 class="section-title section-title-invert">Support across major brands and common model types.</h2>
            <p>Multi-brand familiarity remains one of the strongest trust signals across our service offering.</p>
        </div>
        <div class="brand-carousel" aria-label="Supported brands carousel">
            <div class="brand-carousel-track">
                <img src="assets/brands/brands 1.png" alt="Haier" class="brand-item">
                <img src="assets/brands/brands 2.png" alt="O General" class="brand-item">
                <img src="assets/brands/brands 3.png" alt="Daikin" class="brand-item">
                <img src="assets/brands/brands 4.png" alt="Mitsubishi Electric" class="brand-item">
                <img src="assets/brands/brands 5.png" alt="Hitachi" class="brand-item">
                <img src="assets/brands/brands 6.png" alt="LG" class="brand-item">
                <img src="assets/brands/brands 7.png" alt="Blue Star" class="brand-item">
                <img src="assets/brands/brands 8.png" alt="Voltas" class="brand-item">
                <img src="assets/brands/brands 9.png" alt="Carrier" class="brand-item">
                <img src="assets/brands/brands 10.png" alt="Samsung" class="brand-item">
                <img src="assets/brands/brands 11.png" alt="Whirlpool" class="brand-item">
                <img src="assets/brands/brands 12.png" alt="Godrej" class="brand-item">
                <img src="assets/brands/brands 13.png" alt="IFB" class="brand-item">
                <img src="assets/brands/brands 1.png" alt="Haier" class="brand-item">
                <img src="assets/brands/brands 2.png" alt="O General" class="brand-item">
                <img src="assets/brands/brands 3.png" alt="Daikin" class="brand-item">
                <img src="assets/brands/brands 4.png" alt="Mitsubishi Electric" class="brand-item">
                <img src="assets/brands/brands 5.png" alt="Hitachi" class="brand-item">
                <img src="assets/brands/brands 6.png" alt="LG" class="brand-item">
                <img src="assets/brands/brands 7.png" alt="Blue Star" class="brand-item">
                <img src="assets/brands/brands 8.png" alt="Voltas" class="brand-item">
                <img src="assets/brands/brands 9.png" alt="Carrier" class="brand-item">
                <img src="assets/brands/brands 10.png" alt="Samsung" class="brand-item">
                <img src="assets/brands/brands 11.png" alt="Whirlpool" class="brand-item">
                <img src="assets/brands/brands 12.png" alt="Godrej" class="brand-item">
                <img src="assets/brands/brands 13.png" alt="IFB" class="brand-item">
            </div>
        </div>
    </div>
</section>

<section class="public-cta-band">
    <div class="container public-cta-band-inner">
        <div>
            <span class="section-tag">Book Service</span>
            <h2 class="section-title">Need help choosing the right service category?</h2>
            <p>Contact our team and we will guide you to the right page or support path.</p>
        </div>
        <div class="public-cta-actions">
            <a href="contact.php" class="btn btn-primary btn-lg">Contact Our Team</a>
            <a href="tel:+919087333397" class="btn btn-light btn-lg">Call 90873 33397</a>
        </div>
    </div>
</section>

<?php require 'includes/public_footer.php'; ?>
