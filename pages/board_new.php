<?php
require_once '../includes/config.php';
$pageTitle = 'New Board Entry';

$success      = '';
$error        = '';
$newId        = null;
$customer_name = '';
$phone         = '';
$problem       = '';
$approx_amount = 0;
$customer_remarks = '';
$remark_checks = [];
$payment_status = 'Pending';
$payment_amount = 0;

$remark_items = [
    'Compressor Jack',
    'OLP',
    'EEV',
    'Remote',
    'Indoor PCB',
    'Outdoor PCB',
    'Display PCB',
    'Dis Sensor-OD',
    'Room Sensor-ID',
    'Coil Sensor-OD',
    'Back Cover-OD',
    'Swing MTR',
    'Coil Sensor-ID',
    'AMB Sensor-OD',
    'Transformer',
    'Indoor Motor',
    'Outdoor Motor',
    'Reactor'
];

if (empty($_SESSION['board_new_token'])) {
    $_SESSION['board_new_token'] = bin2hex(random_bytes(16));
}
$form_token = $_SESSION['board_new_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $posted_token = $_POST['form_token'] ?? '';
    if (!$posted_token || !hash_equals($_SESSION['board_new_token'] ?? '', $posted_token)) {
        $error = 'Duplicate or invalid submission detected. Please try again.';
    } else {
        // Rotate token early to reduce chances of double-submit races.
        $_SESSION['board_new_token'] = bin2hex(random_bytes(16));
        $form_token = $_SESSION['board_new_token'];
    }

    $customer_name  = trim($_POST['customer_name'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');
    $address        = trim($_POST['address'] ?? '');
    $ac_brand       = trim($_POST['ac_brand'] ?? '');
    $ac_model       = trim($_POST['ac_model'] ?? '');
    $problem        = trim($_POST['problem'] ?? '');
    $customer_remarks = trim($_POST['customer_remarks'] ?? '');
    $remark_checks = $_POST['remark_checks'] ?? [];
    $parts_inside   = trim($_POST['parts_inside'] ?? '');
    $approx_amount  = floatval($_POST['approx_amount'] ?? 0);
    $payment_status = trim($_POST['payment_status'] ?? 'Pending');
    $payment_amount = floatval($_POST['payment_amount'] ?? 0);

    if (!is_array($remark_checks)) { $remark_checks = []; }
    $remark_checks = array_values(array_intersect($remark_checks, $remark_items));
    $remark_checks_str = implode(', ', $remark_checks);
    $payment_allowed = ['Pending', 'Paid', 'Partial'];
    if (!in_array($payment_status, $payment_allowed, true)) {
        $payment_status = 'Pending';
    }
    if ($payment_status !== 'Partial') {
        $payment_amount = 0;
    } elseif ($payment_amount < 0) {
        $payment_amount = 0;
    }

    if ($error) {
        // Skip validation when submission is invalid.
    } elseif (!$customer_name || !$phone) {
        $error = 'Customer name and phone are required.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("INSERT INTO board_services
            (customer_name, phone, address, ac_brand, ac_model, problem,
             customer_remarks, remark_checks, parts_inside, approx_amount, status, payment_status, payment_amount, created_by)
            VALUES (?,?,?,?,?,?,?,?,?,?,'Pending',?,?,?)");
        $stmt->execute([$customer_name, $phone, $address, $ac_brand, $ac_model,
                        $problem, $customer_remarks, $remark_checks_str, $parts_inside,
                        $approx_amount, $payment_status, $payment_amount, $_SESSION['user_id']]);
        $newId   = $db->lastInsertId();
        unset($_SESSION['board_new_token']);
        header('Location: board_view.php?id=' . $newId . '&created=1');
        exit;
    }
}

include '../includes/header.php';
?>

<?php if ($error): ?>
<div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><span class="card-title">Board Entry Form</span></div>
    <div class="card-body">
        <form method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="form_token" value="<?= htmlspecialchars($form_token) ?>">

            <div class="section-heading">Customer Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Customer Name *</label>
                    <input type="text" name="customer_name"
                           value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>"
                           placeholder="Full name" required>
                </div>
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="tel" name="phone"
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                           placeholder="10-digit mobile number" required>
                </div>
                <div class="form-group full-width">
                    <label>Address</label>
                    <textarea name="address" placeholder="Customer address"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                </div>
            </div>

            <hr class="divider">
            <div class="section-heading">Board Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Brand</label>
                    <input type="text" name="ac_brand"
                           value="<?= htmlspecialchars($_POST['ac_brand'] ?? '') ?>"
                           placeholder="e.g. Daikin, LG, Voltas">
                </div>
                <div class="form-group">
                    <label>Model</label>
                    <input type="text" name="ac_model"
                           value="<?= htmlspecialchars($_POST['ac_model'] ?? '') ?>"
                           placeholder="Model number">
                </div>
                <div class="form-group full-width">
                    <label>Problem Description</label>
                    <textarea name="problem" placeholder="Describe the problem with the board"
                              ><?= htmlspecialchars($_POST['problem'] ?? '') ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Customer Remarks</label>
                    <textarea name="customer_remarks" class="textarea-lg"
                              placeholder="Customer's words or observations"><?= htmlspecialchars($_POST['customer_remarks'] ?? '') ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Checklist (Customer Reported / Observed)</label>
                    <div class="checklist-grid">
                        <?php foreach ($remark_items as $item): ?>
                            <label class="check-item">
                                <input type="checkbox" name="remark_checks[]" value="<?= htmlspecialchars($item) ?>"
                                    <?= in_array($item, $remark_checks, true) ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($item) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label>Parts Inside Board</label>
                    <textarea name="parts_inside"
                              placeholder="List the parts/components found inside the board"><?= htmlspecialchars($_POST['parts_inside'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Approximate Amount (₹)</label>
                    <input type="number" name="approx_amount"
                           value="<?= htmlspecialchars($_POST['approx_amount'] ?? '') ?>"
                           placeholder="0.00" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="payment_status">
                        <?php foreach (['Pending', 'Paid', 'Partial'] as $ps): ?>
                            <option value="<?= $ps ?>" <?= ($payment_status === $ps) ? 'selected' : '' ?>><?= $ps ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" data-partial-amount style="display:none;">
                    <label>Partial Payment Amount (???)</label>
                    <input type="number" name="payment_amount"
                           value="<?= htmlspecialchars($_POST['payment_amount'] ?? '') ?>"
                           min="0" step="0.01" placeholder="0.00">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" data-submit-btn>💾 Save Board Entry</button>
                <a href="board_list.php" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.querySelector('form');
    if (!form) return;
    form.addEventListener('submit', function() {
        var btn = form.querySelector('[data-submit-btn]');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Saving...';
        }
    });
});
</script>
<?php include '../includes/footer.php'; ?>


