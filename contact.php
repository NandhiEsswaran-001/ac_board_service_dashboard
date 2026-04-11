<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    $dest = isOwner() ? 'pages/dashboard.php' : 'pages/board_list.php';
    header('Location: ' . $dest);
    exit;
}

$publicPage = 'contact';
$publicTitle = APP_NAME . ' | Contact Us';
require 'includes/public_header.php';
?>

<section class="info-section contact-section">
    <div class="container">
        <div class="section-intro">
            <span class="eyebrow">Contact Us</span>
            <h2>Reach our service center for enquiries, support and service coordination.</h2>
            <p>
                For service updates, repair enquiries and support requests, customers can connect using the
                phone numbers and address from the business card details.
            </p>
        </div>

        <div class="contact-grid">
            <div class="contact-card">
                <h3>Phone Numbers</h3>
                <p><a href="tel:+919087333397">90873 33397</a></p>
                <p><a href="tel:+919500933390">95009 33390</a></p>
            </div>
            <div class="contact-card">
                <h3>Address</h3>
                <p>488/490, 7th Street Extn, Karpaga Vinayagar Mansion, Gandhipuram, Coimbatore - 12</p>
            </div>
            <div class="contact-card">
                <h3>Branch Locations</h3>
                <p>Madukkarai</p>
                <p>Kozhinjampara</p>
            </div>
            <div class="contact-card">
                <h3>Business Focus</h3>
                <p>PCB chip level service center for AC, fridge, washing machine and commercial AC related repairs.</p>
            </div>
        </div>
    </div>
</section>

<?php require 'includes/public_footer.php'; ?>
