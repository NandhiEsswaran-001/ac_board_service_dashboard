<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    $dest = isDashboardUser() ? 'pages/dashboard.php' : 'pages/board_list.php';
    header('Location: ' . $dest);
    exit;
}

$publicPage = 'home';
$publicTitle = APP_NAME . ' | Home';
require 'includes/public_header.php';
?>

<section class="hero-section home-hero-section">
    <div class="hero-slider" id="heroSlider">
        <div class="hero-slide active" style="background-image: url('assets/services/service-hero-1.jpg');">
            <div class="container home-hero-shell">
                <div class="home-hero-copy">
                    <span class="eyebrow"><i class="fas fa-shield-heart"></i> Fast doorstep service for AC and appliance care</span>
                    <h1>Expert AC repair and maintenance with quick local response.</h1>
                    <p>
                        Hot &amp; Cold Engineering supports homes, offices and shops with AC repair,
                        preventive maintenance, refrigerator service and washing machine support.
                    </p>

                    <div class="home-hero-actions">
                        <a href="services.php" class="btn btn-primary btn-lg">Explore Services</a>
                        <a href="contact.php" class="btn btn-light btn-lg">Book a Visit</a>
                    </div>
                </div>

                <aside class="home-hero-panel home-hero-panel-minimal">
                    <div class="home-hero-panel-head">
                        <h2>Book AC service in minutes</h2>
                        <p>Call, WhatsApp or request a visit through the contact page for fast service coordination.</p>
                    </div>
                    <div class="home-hero-mini-points">
                        <span><i class="fas fa-headset"></i> Call support</span>
                        <span><i class="fab fa-whatsapp"></i> WhatsApp booking</span>
                        <span><i class="fas fa-house-circle-check"></i> On-site visit</span>
                    </div>
                </aside>
            </div>
        </div>

        <div class="hero-slide" style="background-image: url('assets/services/service-hero-3.jpg');">
            <div class="container home-hero-shell">
                <div class="home-hero-copy">
                    <span class="eyebrow"><i class="fas fa-snowflake"></i> Planned maintenance for better cooling performance</span>
                    <h1>Keep your cooling systems efficient with routine maintenance support.</h1>
                    <p>
                        We handle inverter AC, split AC and commercial cooling checks with maintenance plans,
                        airflow diagnosis and performance-focused servicing.
                    </p>

                    <div class="home-hero-actions">
                        <a href="commercial-support.php" class="btn btn-primary btn-lg">Commercial Support</a>
                        <a href="residential-support.php" class="btn btn-light btn-lg">Residential Support</a>
                    </div>
                </div>

                <aside class="home-hero-panel home-hero-panel-minimal">
                    <div class="home-hero-panel-head">
                        <h2>Preventive service for fewer breakdowns</h2>
                        <p>Built for service businesses that need strong trust signals and clear conversion paths in the hero area.</p>
                    </div>
                    <div class="home-hero-mini-points">
                        <span><i class="fas fa-building"></i> AMC support</span>
                        <span><i class="fas fa-fan"></i> Cooling checks</span>
                        <span><i class="fas fa-circle-check"></i> Routine maintenance</span>
                    </div>
                </aside>
            </div>
        </div>

        <div class="hero-slide" style="background-image: url('assets/services/service-hero-6.jpg');">
            <div class="container home-hero-shell">
                <div class="home-hero-copy">
                    <span class="eyebrow"><i class="fas fa-microchip"></i> Board-level diagnosis and appliance repair expertise</span>
                    <h1>Advanced diagnosis for PCB issues and complex appliance faults.</h1>
                    <p>
                        From inverter AC PCB service to appliance issue tracing, we combine practical testing,
                        repair-first support and direct customer communication.
                    </p>

                    <div class="home-hero-actions">
                        <a href="inverter-ac-pcb-service.php" class="btn btn-primary btn-lg">PCB Service</a>
                        <a href="about.php" class="btn btn-light btn-lg">About Company</a>
                    </div>
                </div>

                <aside class="home-hero-panel home-hero-panel-accent home-hero-panel-minimal">
                    <div class="home-hero-panel-head">
                        <h2>Repair support that feels dependable</h2>
                        <p>Animated transitions help each message land clearly instead of competing at once.</p>
                    </div>
                    <div class="home-hero-mini-points">
                        <span><i class="fas fa-gears"></i> PCB diagnostics</span>
                        <span><i class="fas fa-clipboard-list"></i> Repair-first support</span>
                        <span><i class="fas fa-phone-volume"></i> Easy contact</span>
                    </div>
                </aside>
            </div>
        </div>

        <button class="hero-nav hero-nav-prev" id="heroPrev" aria-label="Previous slide"><i class="fas fa-chevron-left"></i></button>
        <button class="hero-nav hero-nav-next" id="heroNext" aria-label="Next slide"><i class="fas fa-chevron-right"></i></button>

    </div>
</section>

<section class="public-stat-row home-stat-row-clean">
    <div class="container public-stat-grid">
        <article class="public-stat-card">
            <strong>12+</strong>
            <span>Years Experience</span>
        </article>
        <article class="public-stat-card">
            <strong>3250+</strong>
            <span>Customers Served</span>
        </article>
        <article class="public-stat-card">
            <strong>15+</strong>
            <span>Brands Supported</span>
        </article>
        <article class="public-stat-card">
            <strong>24h</strong>
            <span>Fast Response Focus</span>
        </article>
    </div>
</section>

<section class="info-section home-clean-services">
    <div class="container">
        <div class="section-intro public-section-intro">
            <span class="section-tag">What We Do</span>
            <h2 class="section-title">Repair and service support across cooling systems and appliances.</h2>
            <p>Browse the main service categories without the extra clutter.</p>
        </div>

        <div class="public-services-grid">
            <a href="ac-service.php" class="public-service-block">
                <span class="public-service-icon"><i class="fas fa-fan"></i></span>
                <div class="public-service-copy">
                    <span class="public-service-kicker">Cooling Support</span>
                    <h3>AC Service &amp; Repair</h3>
                    <p>Routine service, diagnostics, gas checks and repair support for home and business cooling systems.</p>
                    <ul class="public-service-list">
                        <li>Split and window AC support</li>
                        <li>Performance and cooling checks</li>
                        <li>Preventive maintenance</li>
                    </ul>
                    <span class="service-card-cta">Explore AC services</span>
                </div>
            </a>

            <a href="commercial-support.php" class="public-service-block">
                <span class="public-service-icon"><i class="fas fa-building"></i></span>
                <div class="public-service-copy">
                    <span class="public-service-kicker">Commercial Support</span>
                    <h3>Business and AMC Service</h3>
                    <p>Commercial cooling support with maintenance discipline, uptime focus and planned handling.</p>
                    <ul class="public-service-list">
                        <li>Office and retail support</li>
                        <li>Maintenance planning</li>
                        <li>Operational continuity focus</li>
                    </ul>
                    <span class="service-card-cta">View commercial support</span>
                </div>
            </a>

            <a href="fridge-service.php" class="public-service-block">
                <span class="public-service-icon"><i class="fas fa-temperature-low"></i></span>
                <div class="public-service-copy">
                    <span class="public-service-kicker">Refrigerator Care</span>
                    <h3>Fridge Repair Service</h3>
                    <p>Cooling, electrical and control-side diagnosis for everyday refrigerator complaints.</p>
                    <ul class="public-service-list">
                        <li>Cooling issue diagnosis</li>
                        <li>Electrical fault tracing</li>
                        <li>Domestic model support</li>
                    </ul>
                    <span class="service-card-cta">View fridge service</span>
                </div>
            </a>

            <a href="washing-machine-service.php" class="public-service-block">
                <span class="public-service-icon"><i class="fas fa-soap"></i></span>
                <div class="public-service-copy">
                    <span class="public-service-kicker">Laundry Appliance</span>
                    <h3>Washing Machine Repair</h3>
                    <p>Inspection and repair for drain, spin, cycle and control-related washing machine issues.</p>
                    <ul class="public-service-list">
                        <li>Top-load and front-load support</li>
                        <li>Drain and spin issue handling</li>
                        <li>Panel and board checks</li>
                    </ul>
                    <span class="service-card-cta">View washing machine service</span>
                </div>
            </a>

            <a href="inverter-ac-pcb-service.php" class="public-service-block">
                <span class="public-service-icon"><i class="fas fa-microchip"></i></span>
                <div class="public-service-copy">
                    <span class="public-service-kicker">PCB Diagnostics</span>
                    <h3>Chip-Level Board Support</h3>
                    <p>Targeted diagnosis for inverter AC PCB faults and other appliance board-level complaints.</p>
                    <ul class="public-service-list">
                        <li>Board-level inspection</li>
                        <li>Repair-first approach</li>
                        <li>Clear replacement guidance</li>
                    </ul>
                    <span class="service-card-cta">Explore PCB service</span>
                </div>
            </a>

            <a href="services.php" class="public-service-block home-clean-service-accent">
                <span class="public-service-icon"><i class="fas fa-layer-group"></i></span>
                <div class="public-service-copy">
                    <span class="public-service-kicker">Full Service View</span>
                    <h3>See All Services</h3>
                    <p>Open the main services page to browse every category in a more detailed format.</p>
                    <ul class="public-service-list">
                        <li>Home and commercial categories</li>
                        <li>Multi-brand support</li>
                        <li>Direct path to service pages</li>
                    </ul>
                    <span class="service-card-cta">Open services page</span>
                </div>
            </a>
        </div>
    </div>
</section>

<section class="info-section home-clean-story">
    <div class="container public-story-layout">
        <article class="public-story-visual">
            <div class="public-story-card">
                <span class="public-story-stamp">2013</span>
                <h2>Trusted local service</h2>
                <p>We built the business around practical repairs, honest guidance and dependable local support in Coimbatore.</p>
            </div>
            <div class="public-story-badge">
                <strong>Since 2013</strong>
                <span>Consistent service support</span>
            </div>
        </article>

        <article class="public-story-copy">
            <span class="section-tag">Why Choose Us</span>
            <h2 class="section-title">A cleaner homepage focused on trust, service clarity and quick action.</h2>
            <p>Hot &amp; Cold Engineering supports AC, refrigerator, washing machine and PCB complaints with a repair-first workflow built around clear diagnosis.</p>
            <p>We keep the service process simple: understand the issue, inspect properly, explain the practical next step and complete the work with steady communication.</p>
            <div class="public-values-grid home-mini-values">
                <article class="public-value-card">
                    <span class="public-value-icon"><i class="fas fa-comments"></i></span>
                    <h3>Clear communication</h3>
                    <p>Customers should always understand the issue and the service path.</p>
                </article>
                <article class="public-value-card">
                    <span class="public-value-icon"><i class="fas fa-bolt"></i></span>
                    <h3>Fast local support</h3>
                    <p>We focus on quick coordination for homes and business environments.</p>
                </article>
            </div>
        </article>
    </div>
</section>

<section class="public-brand-band home-brand-band-clean">
    <div class="container">
        <div class="section-intro public-section-intro public-section-intro-invert">
            <span class="section-tag section-tag-invert">Brands</span>
            <h2 class="section-title section-title-invert">Support across major AC and appliance brands.</h2>
            <p>Brand confidence stays visible, but in a cleaner section with less visual competition.</p>
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

<section class="public-cta-band home-final-cta">
    <div class="container public-cta-band-inner">
        <div>
            <span class="section-tag">Ready To Book</span>
            <h2 class="section-title">Need a service visit or quick support guidance?</h2>
            <p>Call us directly, message on WhatsApp or browse the full service list.</p>
        </div>
        <div class="public-cta-actions">
            <a href="tel:+919087333397" class="btn btn-primary btn-lg">Call Now</a>
            <a href="https://wa.me/919500933390" target="_blank" rel="noopener" class="btn btn-light btn-lg">WhatsApp Us</a>
            <a href="services.php" class="btn btn-secondary btn-lg">View Services</a>
        </div>
    </div>
</section>

<?php require 'includes/public_footer.php'; ?>
