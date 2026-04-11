<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    $dest = isOwner() ? 'pages/dashboard.php' : 'pages/board_list.php';
    header('Location: ' . $dest);
    exit;
}

$publicPage = 'home';
$publicTitle = APP_NAME . ' | Home';
require 'includes/public_header.php';
?>

<section class="hero-section">
    <div class="container hero-grid hero-grid-single">
        <div class="hero-copy">
            <span class="eyebrow">Trusted Electronic Service Support In Coimbatore</span>
            <h1>Reliable AC and appliance service with a clean, traditional business feel.</h1>
            <p>
                Hot &amp; Cold Engineering provides inverter AC, non-inverter AC, refrigerator,
                washing machine and PCB chip-level service support for homes and commercial spaces.
            </p>

            <div class="hero-points">
                <div class="hero-point">All brand AC service</div>
                <div class="hero-point">Commercial AC support</div>
                <div class="hero-point">Chip-level diagnosis and repair</div>
            </div>

            <div class="hero-actions">
                <a href="services.php" class="btn btn-primary btn-lg">Explore Services</a>
                <a href="contact.php" class="btn btn-light btn-lg">Contact Us</a>
                <a href="login.php" class="btn btn-secondary btn-lg">Login</a>
            </div>

            <div class="hero-contact-strip">
                <span>Call: <a href="tel:+919087333397">90873 33397</a></span>
                <span>Support: <a href="tel:+919500933390">95009 33390</a></span>
            </div>
        </div>
    </div>
</section>

<section class="info-section">
    <div class="container">
        <div class="section-intro">
            <span class="eyebrow">Welcome</span>
            <h2>Professional service for cooling appliances and electronic boards.</h2>
            <p>
                We keep our website clean and straightforward, just like our service process. Customers can
                quickly understand what we do, where we work and how to reach us.
            </p>
        </div>

        <div class="service-grid">
            <article class="service-card">
                <h3>Residential Support</h3>
                <p>Dependable service for AC, fridge and washing machine needs with a practical service-center approach.</p>
            </article>
            <article class="service-card">
                <h3>Commercial Support</h3>
                <p>Commercial AC servicing focused on smooth operation, reliable response and maintenance support.</p>
            </article>
        </div>
    </div>
</section>

<section class="brand-section">
    <div class="container">
        <div class="section-intro brand-section-intro">
            <span class="eyebrow">Brands</span>
            <h2>Brands we support</h2>
            <p>
                Service support is available for a wide range of trusted AC and home appliance brands.
            </p>
        </div>

        <div class="brand-showcase">
            <img src="assets/brands.png" alt="Supported brands including O General, Daikin, Mitsubishi Electric, Hitachi, LG, Blue Star, Voltas, Carrier, Samsung, Godrej, Whirlpool, Lloyd, IFB and Haier.">
        </div>
    </div>
</section>

<?php require 'includes/public_footer.php'; ?>
