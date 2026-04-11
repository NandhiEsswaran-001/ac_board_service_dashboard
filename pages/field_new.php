<?php
require_once '../includes/config.php';
$pageTitle = 'New Field Service';

$db        = getDB();
$employees = $db->query("SELECT id, full_name FROM users WHERE role IN ('staff','technician') ORDER BY full_name")->fetchAll();

$error        = '';
$newId        = null;
$customer_name = '';
$phone         = '';
$problem       = '';
$service_date  = '';
$service_report_no = 0;

$nextReportNo = (int)$db->query("SELECT COALESCE(MAX(service_report_no),0)+1 FROM field_services")->fetchColumn();
$service_report_no = (int)($_POST['service_report_no'] ?? $nextReportNo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $customer_name     = trim($_POST['customer_name'] ?? '');
    $phone             = trim($_POST['phone'] ?? '');
    $address           = trim($_POST['address'] ?? '');
    $map_link          = trim($_POST['map_link'] ?? '');
    $service_date      = $_POST['service_date'] ?? '';
    $assigned_employee = intval($_POST['assigned_employee'] ?? 0);
    $ac_type           = trim($_POST['ac_type'] ?? '');
    $product_company   = trim($_POST['product_company'] ?? '');
    $purchase_date     = $_POST['purchase_date'] ?? '';
    $unit_location     = trim($_POST['unit_location'] ?? '');
    $problem           = trim($_POST['problem'] ?? '');
    $work_done         = trim($_POST['work_done'] ?? '');
    $parts_used        = trim($_POST['parts_used'] ?? '');
    $service_charge    = ($_POST['service_charge'] ?? 'No') === 'Yes' ? 'Yes' : 'No';
    $warranty_text     = trim($_POST['warranty_text'] ?? '');
    $ampere            = trim($_POST['ampere'] ?? '');
    $voltage           = trim($_POST['voltage'] ?? '');
    $grill_temp        = trim($_POST['grill_temp'] ?? '');
    $sd_pressure       = trim($_POST['sd_pressure'] ?? '');
    $service_call_items_text = trim($_POST['service_call_items'] ?? '');
    $service_amount    = floatval($_POST['service_amount'] ?? 0);
    $payment_amount    = floatval($_POST['payment_amount'] ?? 0);
    $notes             = '';

    if ($service_date === '') {
        $service_date = null;
    }

    $validStatuses  = ['Scheduled', 'In Progress', 'Completed'];
    $validPayments  = ['Pending', 'Paid', 'Partial'];
    $status         = in_array($_POST['status'] ?? '', $validStatuses)         ? $_POST['status']         : 'Scheduled';
    $payment_status = in_array($_POST['payment_status'] ?? '', $validPayments) ? $_POST['payment_status'] : 'Pending';
    if ($payment_status !== 'Partial') {
        $payment_amount = 0;
    } elseif ($payment_amount < 0) {
        $payment_amount = 0;
    }

    if (!$customer_name || !$phone) {
        $error = 'Customer name and phone are required.';
    } else {
        $stmt = $db->prepare("INSERT INTO field_services
            (service_report_no, customer_name, phone, address, map_link, service_date, assigned_employee,
             ac_type, product_company, purchase_date, unit_location, problem, work_done,
             parts_used, service_charge, service_call_items, ampere, voltage, grill_temp, sd_pressure, warranty_text, service_amount,
             payment_status, payment_amount, status, notes, created_by)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $service_report_no,
            $customer_name,
            $phone,
            $address,
            $map_link,
            $service_date,
            $assigned_employee ?: null,
            $ac_type,
            $product_company,
            $purchase_date ?: null,
            $unit_location,
            $problem,
            $work_done,
            $parts_used,
            $service_charge,
            $service_call_items_text,
            $ampere,
            $voltage,
            $grill_temp,
            $sd_pressure,
            $warranty_text,
            $service_amount,
            $payment_status,
            $payment_amount,
            $status,
            $notes,
            $_SESSION['user_id']
        ]);
        $newId   = $db->lastInsertId();
        header('Location: field_view.php?id=' . $newId . '&created=1');
        exit;
    }
}

include '../includes/header.php';
?>

<?php if ($error): ?>
<div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><span class="card-title">Field Service Entry Form</span></div>
    <div class="card-body">
        <form method="POST">
            <?= csrfField() ?>

            <div class="section-heading">Service Report</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Service Report No.</label>
                    <div class="report-no">#<?= htmlspecialchars($service_report_no) ?></div>
                    <input type="hidden" name="service_report_no" value="<?= htmlspecialchars($service_report_no) ?>">
                </div>
            </div>

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
                           placeholder="10-digit mobile" required>
                </div>
                <div class="form-group full-width">
                    <label>Address</label>
                    <textarea name="address" placeholder="Customer's full address for visit"
                              ><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                </div>
            </div>

            <hr class="divider">
            <div class="section-heading">Service Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Service Date</label>
                    <input type="date" name="service_date"
                           value="<?= htmlspecialchars($_POST['service_date'] ?? date('Y-m-d')) ?>">
                </div>
                <div class="form-group">
                    <label>Assigned Technician</label>
                    <select name="assigned_employee">
                        <option value="">-- Select Technician --</option>
                        <?php foreach ($employees as $e): ?>
                        <option value="<?= $e['id'] ?>"
                            <?= (($_POST['assigned_employee'] ?? '') == $e['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['full_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Name of Product</label>
                    <div class="option-group">
                        <?php foreach (['AC', 'Refrigerator', 'Washing Machine', 'Commercial'] as $opt): ?>
                        <label class="option-pill">
                            <input type="radio" name="ac_type" value="<?= $opt ?>"
                                <?= (($_POST['ac_type'] ?? '') === $opt) ? 'checked' : '' ?>>
                            <span><?= $opt ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Product of the Company</label>
                    <input type="text" name="product_company"
                           value="<?= htmlspecialchars($_POST['product_company'] ?? '') ?>"
                           placeholder="Brand / Company name">
                </div>
                <div class="form-group">
                    <label>Date of Purchase</label>
                    <input type="date" name="purchase_date"
                           value="<?= htmlspecialchars($_POST['purchase_date'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Unit Location</label>
                    <input type="text" name="unit_location"
                           value="<?= htmlspecialchars($_POST['unit_location'] ?? '') ?>"
                           placeholder="e.g. Living room, Shop floor">
                </div>
                <div class="form-group full-width">
                    <label>Complaint</label>
                    <textarea name="problem" class="textarea-lg"
                              placeholder="Describe the issue reported by the customer"
                              ><?= htmlspecialchars($_POST['problem'] ?? '') ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Job Done</label>
                    <textarea name="work_done" class="textarea-lg"
                              placeholder="Work completed at site"><?= htmlspecialchars($_POST['work_done'] ?? '') ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Replaced Spares</label>
                    <textarea name="parts_used" class="textarea-lg"
                              placeholder="List replaced spares / parts used"><?= htmlspecialchars($_POST['parts_used'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Service Charge</label>
                    <div class="option-group">
                        <?php foreach (['Yes','No'] as $opt): ?>
                        <label class="option-pill">
                            <input type="radio" name="service_charge" value="<?= $opt ?>"
                                <?= (($_POST['service_charge'] ?? 'No') === $opt) ? 'checked' : '' ?>>
                            <span><?= $opt ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label>Service Call Checklist</label>
                    <textarea name="service_call_items" class="textarea-lg"
                              placeholder="Enter checklist items, notes, or observations"><?= htmlspecialchars($_POST['service_call_items'] ?? '') ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Readings</label>
                    <div class="boxed-panel">
                        <div class="boxed-title">Electrical / Temperature</div>
                        <div class="boxed-grid">
                            <div>
                                <label>Ampere</label>
                                <input type="text" name="ampere" value="<?= htmlspecialchars($_POST['ampere'] ?? '') ?>" placeholder="e.g. 2.3 A">
                            </div>
                            <div>
                                <label>Voltage</label>
                                <input type="text" name="voltage" value="<?= htmlspecialchars($_POST['voltage'] ?? '') ?>" placeholder="e.g. 220 V">
                            </div>
                            <div>
                                <label>Grill Temp</label>
                                <input type="text" name="grill_temp" value="<?= htmlspecialchars($_POST['grill_temp'] ?? '') ?>" placeholder="e.g. 18°C">
                            </div>
                            <div>
                                <label>S/D Pressure</label>
                                <input type="text" name="sd_pressure" value="<?= htmlspecialchars($_POST['sd_pressure'] ?? '') ?>" placeholder="e.g. 60 psi">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Warranty</label>
                    <input type="text" name="warranty_text"
                           value="<?= htmlspecialchars($_POST['warranty_text'] ?? '') ?>"
                           placeholder="e.g. 6 months">
                </div>
                <div class="form-group">
                    <label>Service Amount (INR)</label>
                    <input type="number" name="service_amount"
                           value="<?= htmlspecialchars($_POST['service_amount'] ?? '0') ?>"
                           min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="payment_status">
                        <?php foreach (['Pending', 'Paid', 'Partial'] as $p): ?>
                        <option value="<?= $p ?>" <?= (($_POST['payment_status'] ?? 'Pending') === $p) ? 'selected' : '' ?>><?= $p ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" data-partial-amount style="display:none;">
                    <label>Partial Payment Amount (INR)</label>
                    <input type="number" name="payment_amount"
                           value="<?= htmlspecialchars($_POST['payment_amount'] ?? '0') ?>"
                           min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Service Status</label>
                    <select name="status">
                        <?php foreach (['Scheduled', 'In Progress', 'Completed'] as $st): ?>
                        <option value="<?= $st ?>" <?= (($_POST['status'] ?? 'Scheduled') === $st) ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label>Google Maps URL</label>
                    <input type="url" name="map_link"
                           value="<?= htmlspecialchars($_POST['map_link'] ?? '') ?>"
                           placeholder="Paste Google Maps URL (optional)">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Save Field Service</button>
                <a href="field_list.php" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
