<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    $dest = isDashboardUser() ? 'pages/dashboard.php' : 'pages/board_list.php';
    header('Location: ' . $dest);
    exit;
}

$publicPage = 'contact';
$publicTitle = APP_NAME . ' | Contact Us';
require 'includes/public_header.php';
?>

<section class="page-hero-lite page-hero-contact">
    <div class="container page-hero-inner">
        <span class="page-kicker">Get In Touch</span>
        <h1>Talk to us directly for bookings, support and service coordination.</h1>
        <p>
            This contact page now follows the same lighter, cleaner template language with stronger
            service channels, business hours and booking guidance.
        </p>
    </div>
</section>

<section class="public-call-banner">
    <div class="container public-call-banner-inner">
        <div>
            <h2>Call us directly for the fastest booking response.</h2>
            <p>Real support, practical communication and quick local coordination.</p>
        </div>
        <div class="public-call-links">
            <a href="tel:+919087333397"><i class="fas fa-phone-volume"></i> 90873 33397</a>
            <a href="tel:+919500933390"><i class="fas fa-headset"></i> 95009 33390</a>
        </div>
    </div>
</section>

<section class="info-section public-contact-channels">
    <div class="container">
        <div class="section-intro public-section-intro">
            <span class="section-tag">Contact Channels</span>
            <h2 class="section-title">Multiple ways to reach the team.</h2>
            <p>Choose the channel that works best for service booking, support or location details.</p>
        </div>

        <div class="public-channel-grid">
            <a href="tel:+919087333397" class="public-channel-card">
                <span class="public-channel-icon public-channel-icon-hot"><i class="fas fa-phone"></i></span>
                <h3>Service Line</h3>
                <strong>90873 33397</strong>
                <p>For new service bookings, appliance issues and general enquiries.</p>
            </a>
            <a href="tel:+919500933390" class="public-channel-card">
                <span class="public-channel-icon public-channel-icon-cool"><i class="fas fa-headset"></i></span>
                <h3>Support Line</h3>
                <strong>95009 33390</strong>
                <p>For follow-up, service coordination and support communication.</p>
            </a>
            <a href="https://wa.me/919500933390" target="_blank" rel="noopener" class="public-channel-card">
                <span class="public-channel-icon public-channel-icon-chat"><i class="fab fa-whatsapp"></i></span>
                <h3>WhatsApp</h3>
                <strong>Message Us</strong>
                <p>Share the problem, appliance type and location for faster booking flow.</p>
            </a>
            <article class="public-channel-card">
                <span class="public-channel-icon public-channel-icon-loc"><i class="fas fa-location-dot"></i></span>
                <h3>Address</h3>
                <strong>Coimbatore</strong>
                <p>97, 1st floor, 7th street, tatabad, near 6 Corner, Coimbatore - 641012</p>
            </article>
            <article class="public-channel-card">
                <span class="public-channel-icon public-channel-icon-time"><i class="fas fa-clock"></i></span>
                <h3>Working Hours</h3>
                <strong>Mon - Sat</strong>
                <p>9:00 AM - 7:00 PM. Sunday support can be coordinated by appointment.</p>
            </article>
            <article class="public-channel-card">
                <span class="public-channel-icon public-channel-icon-build"><i class="fas fa-building-circle-check"></i></span>
                <h3>Branch Locations</h3>
                <strong>Madukkarai / Kozhinjampara</strong>
                <p>Additional local support presence for easier service coordination.</p>
            </article>
        </div>
    </div>
</section>

<section class="public-area-section">
    <div class="container public-area-layout">
        <div class="public-area-visual">
            <i class="fas fa-map-location-dot"></i>
            <span>Coimbatore Service Coverage</span>
        </div>
        <div class="public-area-copy">
            <span class="section-tag">Service Area</span>
            <h2 class="section-title">Coverage across Coimbatore and nearby areas.</h2>
            <p>
                We support home and business customers across major areas of Coimbatore with practical
                scheduling and local service response.
            </p>
            <div class="public-area-tags">
                <span>RS Puram</span>
                <span>Gandhipuram</span>
                <span>Peelamedu</span>
                <span>Saibaba Colony</span>
                <span>Singanallur</span>
                <span>Ganapathy</span>
                <span>Hope College</span>
                <span>More Nearby Areas</span>
            </div>
        </div>
    </div>
</section>

<section class="public-hours-section">
    <div class="container">
        <div class="section-intro public-section-intro public-section-intro-invert">
            <span class="section-tag section-tag-invert">Working Hours</span>
            <h2 class="section-title section-title-invert">When we are available for service support.</h2>
            <p>Our booking lines are active and our service handling stays focused on quick coordination.</p>
        </div>

        <div class="public-hours-grid">
            <article class="public-hours-card">
                <h3>Service Schedule</h3>
                <div class="public-hours-row"><span>Monday - Friday</span><strong>9:00 AM - 7:00 PM</strong></div>
                <div class="public-hours-row"><span>Saturday</span><strong>9:00 AM - 6:00 PM</strong></div>
                <div class="public-hours-row"><span>Sunday</span><strong>By Appointment</strong></div>
            </article>
            <article class="public-hours-card">
                <h3>Response Commitment</h3>
                <div class="public-hours-row"><span>Phone Response</span><strong>Immediate</strong></div>
                <div class="public-hours-row"><span>WhatsApp Response</span><strong>Within 1 Hour</strong></div>
                <div class="public-hours-row"><span>Technician Dispatch</span><strong>Fast Local Handling</strong></div>
            </article>
        </div>
    </div>
</section>

<section class="info-section public-booking-section">
    <div class="container">
        <div class="section-intro public-section-intro">
            <span class="section-tag">Booking Steps</span>
            <h2 class="section-title">How to book a service visit.</h2>
            <p>A cleaner replacement for a dense contact form, while still guiding the user toward action.</p>
        </div>

        <div class="public-booking-grid">
            <article class="public-booking-step">
                <strong>01</strong>
                <h3>Call or WhatsApp</h3>
                <p>Share the appliance type, issue and your location details.</p>
            </article>
            <article class="public-booking-step">
                <strong>02</strong>
                <h3>Confirm service details</h3>
                <p>We guide the next step and coordinate a suitable inspection or service visit.</p>
            </article>
            <article class="public-booking-step">
                <strong>03</strong>
                <h3>Technician support</h3>
                <p>The complaint is inspected and the practical repair path is explained clearly.</p>
            </article>
        </div>
    </div>
</section>

<?php require 'includes/public_footer.php'; ?>
