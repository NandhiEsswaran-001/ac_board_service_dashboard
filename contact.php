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

<section class="contact-hero-section">
    <div class="contact-hero-card">
        <div class="contact-hero-overlay">
            <span class="eyebrow">Contact Us</span>
            <h1>Reach our service center for enquiries, support and service coordination.</h1>
            <p>
                For service updates, repair enquiries and support requests, customers can connect using the
                phone numbers and address from the business card details.
            </p>
        </div>
    </div>
</section>

<section class="info-section contact-section">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-card">
                <h3>Phone Numbers</h3>
                <p><a href="tel:+919087333397">90873 33397</a></p>
                <p><a href="tel:+919500933390">95009 33390</a></p>
            </div>
            <div class="contact-card">
                <h3>Address</h3>
                <p>97, 1st floor, 7th street, tatabad, near 6 Corner,<br>Coimbatore - 641012</p>
            </div>
            <div class="contact-card">
                <h3>Branch Locations</h3>
                <p>Madukkarai</p>
                <p>Kozhinjampara</p>
            </div>
            <div class="contact-card">
                <h3>Business Focus</h3>
                <p>PCB chip level service center for AC, fridge, washing machine and commercial AC related repairs.</p>
                <p>Serving customers since 2013.</p>
            </div>
        </div>
    </div>
</section>

<?php require 'includes/public_footer.php'; ?>
