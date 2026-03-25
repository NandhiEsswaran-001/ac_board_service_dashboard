<?php
require_once '../includes/config.php';
$pageTitle = 'Edit Field Service';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: field_list.php'); exit; }

$db   = getDB();
$stmt = $db->prepare("SELECT * FROM field_services WHERE id=?");
$stmt->execute([$id]);
$s = $stmt->fetch();
if (!$s) { header('Location: field_list.php'); exit; }

$employees = $db->query("SELECT id, full_name FROM users WHERE role IN ('staff','technician') ORDER BY full_name")->fetchAll();

$success = '';
$error   = '';
$service_call_items_text = '';
if (!empty($s['service_call_items'])) {
    $decoded = json_decode($s['service_call_items'], true);
    if (is_array($decoded)) {
        $service_call_items_text = implode("\n", $decoded);
    } else {
        $service_call_items_text = $s['service_call_items'];
    }
}

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
    $notes             = $s['notes'] ?? '';

    $validStatuses  = ['Scheduled', 'In Progress', 'Completed'];
    $validPayments  = ['Pending', 'Paid', 'Partial'];
    $status         = in_array($_POST['status'] ?? '', $validStatuses)         ? $_POST['status']         : $s['status'];
    $payment_status = in_array($_POST['payment_status'] ?? '', $validPayments) ? $_POST['payment_status'] : $s['payment_status'];

    if (!$customer_name || !$phone || !$address || !$service_date || !$problem) {
        $error = 'Customer name, phone, address, date, and problem are required.';
    } else {
        $upd = $db->prepare("UPDATE field_services SET
            customer_name=?, phone=?, address=?, map_link=?, service_date=?, assigned_employee=?,
            ac_type=?, product_company=?, purchase_date=?, unit_location=?, problem=?, work_done=?,
            parts_used=?, service_charge=?, service_call_items=?, ampere=?, voltage=?, grill_temp=?, sd_pressure=?, warranty_text=?, service_amount=?,
            payment_status=?, status=?, notes=? WHERE id=?");
        $upd->execute([$customer_name, $phone, $address, $map_link, $service_date,
                       $assigned_employee ?: null, $ac_type, $product_company, $purchase_date ?: null,
                       $unit_location, $problem, $work_done, $parts_used, $service_charge,
                       $service_call_items_text, $ampere, $voltage, $grill_temp, $sd_pressure, $warranty_text, $service_amount, $payment_status,
                       $status, $notes, $id]);
        header('Location: field_edit.php?id=' . $id . '&updated=1');
        exit;
    }
}

if (isset($_GET['updated'])) {
    $success = 'Field service updated successfully.';
}

include '../includes/header.php';
?>

<div class="flex-gap mb-16">
    <a href="field_view.php?id=<?= $id ?>" class="btn btn-light btn-sm">← Back to View</a>
    <a href="field_list.php" class="btn btn-light btn-sm">All Field Services</a>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Edit Field Service #<?= $id ?></span>
        <?= statusBadge($s['status']) ?>
    </div>
    <div class="card-body">
        <form method="POST">
            <?= csrfField() ?>

            <div class="section-heading">Service Report</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Service Report No.</label>
                    <div class="report-no">#<?= htmlspecialchars($s['service_report_no'] ?: '-') ?></div>
                </div>
            </div>

            <div class="section-heading">Customer Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Customer Name *</label>
                    <input type="text" name="customer_name" value="<?= htmlspecialchars($s['customer_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone *</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($s['phone']) ?>" required>
                </div>
                <div class="form-group full-width">
                    <label>Address *</label>
                    <textarea name="address" required><?= htmlspecialchars($s['address']) ?></textarea>
                </div>
            </div>

            <hr class="divider">
            <div class="section-heading">Service Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Service Date *</label>
                    <input type="date" name="service_date" value="<?= $s['service_date'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Assigned Technician</label>
                    <select name="assigned_employee">
                        <option value="">-- Select Technician --</option>
                        <?php foreach ($employees as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $s['assigned_employee'] == $e['id'] ? 'selected' : '' ?>>
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
                                <?= ($s['ac_type'] === $opt) ? 'checked' : '' ?>>
                            <span><?= $opt ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Product of the Company</label>
                    <input type="text" name="product_company" value="<?= htmlspecialchars($s['product_company'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Date of Purchase</label>
                    <input type="date" name="purchase_date" value="<?= htmlspecialchars($s['purchase_date'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Unit Location</label>
                    <input type="text" name="unit_location" value="<?= htmlspecialchars($s['unit_location'] ?? '') ?>">
                </div>
                <div class="form-group full-width">
                    <label>Complaint *</label>
                    <textarea name="problem" class="textarea-lg" required><?= htmlspecialchars($s['problem']) ?></textarea>
                </div>
            </div>

            <hr class="divider">
            <div class="section-heading">Service Update (After Visit)</div>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Job Done</label>
                    <textarea name="work_done" class="textarea-lg" placeholder="Describe work done at customer location"><?= htmlspecialchars($s['work_done']) ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Replaced Spares</label>
                    <textarea name="parts_used" class="textarea-lg" placeholder="List parts/materials used"><?= htmlspecialchars($s['parts_used']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Service Charge</label>
                    <div class="option-group">
                        <?php foreach (['Yes','No'] as $opt): ?>
                        <label class="option-pill">
                            <input type="radio" name="service_charge" value="<?= $opt ?>"
                                <?= (($s['service_charge'] ?? 'No') === $opt) ? 'checked' : '' ?>>
                            <span><?= $opt ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label>Service Call Checklist</label>
                    <textarea name="service_call_items" class="textarea-lg"
                              placeholder="Enter checklist items, notes, or observations"><?= htmlspecialchars($_POST['service_call_items'] ?? $service_call_items_text) ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Readings</label>
                    <div class="boxed-panel">
                        <div class="boxed-title">Electrical / Temperature</div>
                        <div class="boxed-grid">
                            <div>
                                <label>Ampere</label>
                                <input type="text" name="ampere" value="<?= htmlspecialchars($s['ampere'] ?? '') ?>" placeholder="e.g. 2.3 A">
                            </div>
                            <div>
                                <label>Voltage</label>
                                <input type="text" name="voltage" value="<?= htmlspecialchars($s['voltage'] ?? '') ?>" placeholder="e.g. 220 V">
                            </div>
                            <div>
                                <label>Grill Temp</label>
                                <input type="text" name="grill_temp" value="<?= htmlspecialchars($s['grill_temp'] ?? '') ?>" placeholder="e.g. 18°C">
                            </div>
                            <div>
                                <label>S/D Pressure</label>
                                <input type="text" name="sd_pressure" value="<?= htmlspecialchars($s['sd_pressure'] ?? '') ?>" placeholder="e.g. 60 psi">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Warranty</label>
                    <input type="text" name="warranty_text" value="<?= htmlspecialchars($s['warranty_text'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Service Amount (₹)</label>
                    <input type="number" name="service_amount" value="<?= $s['service_amount'] ?>" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="payment_status">
                        <?php foreach (['Pending', 'Paid', 'Partial'] as $p): ?>
                        <option value="<?= $p ?>" <?= $s['payment_status'] === $p ? 'selected' : '' ?>><?= $p ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Service Status</label>
                    <select name="status">
                        <?php foreach (['Scheduled', 'In Progress', 'Completed'] as $st): ?>
                        <option value="<?= $st ?>" <?= $s['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label>Google Maps URL</label>
                    <input type="url" name="map_link" value="<?= htmlspecialchars($s['map_link'] ?? '') ?>"
                           placeholder="Paste Google Maps URL (optional)">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                <a href="field_view.php?id=<?= $id ?>" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
