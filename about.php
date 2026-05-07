<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    $dest = isDashboardUser() ? 'pages/dashboard.php' : 'pages/board_list.php';
    header('Location: ' . $dest);
    exit;
}

$publicPage = 'about';
$publicTitle = APP_NAME . ' | About Us';
require 'includes/public_header.php';
?>

<section class="page-hero-lite page-hero-about">
    <div class="container page-hero-inner">
        <span class="page-kicker">Our Story</span>
        <h1>Built on practical repairs, honest service and long-term customer trust.</h1>
        <p>
            Hot &amp; Cold Engineering has been supporting homes and businesses in and around Coimbatore
            with AC, refrigerator, washing machine and PCB service since 2013.
        </p>
    </div>
</section>

<section class="public-stat-row">
    <div class="container public-stat-grid">
        <article class="public-stat-card">
            <strong>12+</strong>
            <span>Years of Service</span>
        </article>
        <article class="public-stat-card">
            <strong>3250+</strong>
            <span>Customers Assisted</span>
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

<section class="info-section public-story-section">
    <div class="container public-story-layout">
        <article class="public-story-visual">
            <div class="public-story-card">
                <span class="public-story-stamp">2013</span>
                <h2>Where it started</h2>
                <p>
                    What began as a focused AC service operation grew into a broader appliance and PCB repair
                    support business driven by reliability, clarity and steady workmanship.
                </p>
            </div>
            <div class="public-story-badge">
                <strong>12+</strong>
                <span>Years of service</span>
            </div>
        </article>

        <article class="public-story-copy">
            <span class="section-tag">About Us</span>
            <h2 class="section-title">A service company shaped by consistency and repair-first thinking.</h2>
            <p>
                We work with inverter AC, non-inverter AC, refrigerators, washing machines, commercial cooling
                systems and chip-level board issues using a process that stays practical and transparent.
            </p>
            <p>
                Customers trust us because we keep communication simple, diagnose carefully and recommend work
                based on the actual condition of the unit instead of guesswork.
            </p>
            <p>
                The goal has stayed the same since the beginning: show up, understand the problem properly,
                fix what is fixable and support long-term performance.
            </p>
        </article>
    </div>
</section>

<section class="public-timeline-section">
    <div class="container">
        <div class="section-intro public-section-intro">
            <span class="section-tag">Our Journey</span>
            <h2 class="section-title">How the business grew over time.</h2>
            <p>Key milestones that shaped our service approach and technical scope.</p>
        </div>

        <div class="public-timeline">
            <article class="public-timeline-item">
                <span class="public-timeline-dot"></span>
                <span class="public-timeline-year">2013</span>
                <h3>Started with AC service support</h3>
                <p>Focused on residential cooling service, repair visits and trusted local customer handling.</p>
            </article>
            <article class="public-timeline-item">
                <span class="public-timeline-dot"></span>
                <span class="public-timeline-year">2015</span>
                <h3>Expanded into appliance repairs</h3>
                <p>Added refrigerator and washing machine support to serve more complete household needs.</p>
            </article>
            <article class="public-timeline-item">
                <span class="public-timeline-dot public-timeline-dot-accent"></span>
                <span class="public-timeline-year public-timeline-year-accent">2019</span>
                <h3>Stronger board-level and commercial support</h3>
                <p>Built deeper capability around PCB diagnosis and commercial cooling service requirements.</p>
            </article>
            <article class="public-timeline-item">
                <span class="public-timeline-dot public-timeline-dot-accent"></span>
                <span class="public-timeline-year public-timeline-year-accent">Today</span>
                <h3>Reliable multi-service support in Coimbatore</h3>
                <p>Continuing to serve local homes and businesses with clear communication and practical repairs.</p>
            </article>
        </div>
    </div>
</section>

<section class="info-section public-values-section">
    <div class="container">
        <div class="section-intro public-section-intro">
            <span class="section-tag">Our Values</span>
            <h2 class="section-title">What guides every service visit.</h2>
            <p>The working principles behind our service quality and customer experience.</p>
        </div>

        <div class="public-values-grid">
            <article class="public-value-card">
                <span class="public-value-icon"><i class="fas fa-shield-heart"></i></span>
                <h3>Repair with responsibility</h3>
                <p>We recommend work based on actual need, not unnecessary replacement-first decisions.</p>
            </article>
            <article class="public-value-card">
                <span class="public-value-icon"><i class="fas fa-comments"></i></span>
                <h3>Clear communication</h3>
                <p>Customers should understand the issue, the approach and the next step without confusion.</p>
            </article>
            <article class="public-value-card">
                <span class="public-value-icon"><i class="fas fa-screwdriver-wrench"></i></span>
                <h3>Practical technical support</h3>
                <p>We focus on fault isolation, steady workmanship and usable repair outcomes.</p>
            </article>
            <article class="public-value-card">
                <span class="public-value-icon"><i class="fas fa-bolt"></i></span>
                <h3>Fast local response</h3>
                <p>Timely handling matters, especially when cooling systems or daily-use appliances fail.</p>
            </article>
        </div>
    </div>
</section>

<section class="public-cta-band">
    <div class="container public-cta-band-inner">
        <div>
            <span class="section-tag">Need Support?</span>
            <h2 class="section-title">Talk to our team about your service requirement.</h2>
            <p>Book a visit, discuss a repair issue or explore our available service categories.</p>
        </div>
        <div class="public-cta-actions">
            <a href="contact.php" class="btn btn-primary btn-lg">Contact Us</a>
            <a href="services.php" class="btn btn-light btn-lg">View Services</a>
        </div>
    </div>
</section>

<?php require 'includes/public_footer.php'; ?>
