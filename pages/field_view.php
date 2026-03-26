<?php
require_once '../includes/config.php';
$pageTitle = 'View Field Service';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: field_list.php'); exit; }

$db = getDB();
$stmt = $db->prepare("SELECT fs.*, u.full_name AS emp_name FROM field_services fs LEFT JOIN users u ON fs.assigned_employee=u.id WHERE fs.id=?");
$stmt->execute([$id]);
$s = $stmt->fetch();
if (!$s) { header('Location: field_list.php'); exit; }

$success = '';
if (isset($_GET['created'])) {
    $success = 'Field service entry created! Entry #' . $s['id'];
}

$wa_items = '-';
if (!empty($s['service_call_items'])) {
    $decoded = json_decode($s['service_call_items'], true);
    if (is_array($decoded)) {
        $wa_items = $decoded ? implode(', ', $decoded) : '-';
    } else {
        $wa_items = trim((string)$s['service_call_items']) ?: '-';
    }
}

$wa_scheduled = "HOT & COLD ENGINEERING\nAIR CONDITIONER SERVICE CENTER\n(ALL TYPES OF INVERTER / NON INVERTER)\nNo.488/490, KARPAGA VINAYAGAR MANSION,\n7TH STREET EXT'N, GANDHIPURAM, COIMBATORE 641012\n\n-------------------- RECEIPT --------------------\nJOB NO : {$s['id']}\nDATE   : " . date('d M Y', strtotime($s['service_date'])) . "\nSTATUS : SCHEDULED\n-------------------------------------------------\nNAME   : {$s['customer_name']}\nPHONE  : {$s['phone']}\nADDRESS: " . ($s['address'] ? $s['address'] : '-') . "\n-------------------------------------------------\nPRODUCT NAME        : " . ($s['ac_type'] ? $s['ac_type'] : '-') . "\nPRODUCT COMPANY     : " . ($s['product_company'] ? $s['product_company'] : '-') . "\nDATE OF PURCHASE    : " . ($s['purchase_date'] ? formatDate($s['purchase_date']) : '-') . "\nUNIT LOCATION       : " . ($s['unit_location'] ? $s['unit_location'] : '-') . "\nSERVICE REPORT NO   : " . ($s['service_report_no'] ? $s['service_report_no'] : '-') . "\nTECHNICIAN          : " . ($s['emp_name'] ? $s['emp_name'] : 'Unassigned') . "\nCOMPLAINT           : " . ($s['problem'] ? $s['problem'] : '-') . "\n-------------------------------------------------\nFOR HOT & COLD ENG\nAUTHORISED PERSON";
$wa_completed = "HOT & COLD ENGINEERING\nAIR CONDITIONER SERVICE CENTER\n(ALL TYPES OF INVERTER / NON INVERTER)\nNo.488/490, KARPAGA VINAYAGAR MANSION,\n7TH STREET EXT'N, GANDHIPURAM, COIMBATORE 641012\n\n-------------------- RECEIPT --------------------\nJOB NO : {$s['id']}\nDATE   : " . date('d M Y', strtotime($s['service_date'])) . "\nSTATUS : COMPLETED\n-------------------------------------------------\nNAME   : {$s['customer_name']}\nPHONE  : {$s['phone']}\nADDRESS: " . ($s['address'] ? $s['address'] : '-') . "\n-------------------------------------------------\nPRODUCT NAME        : " . ($s['ac_type'] ? $s['ac_type'] : '-') . "\nPRODUCT COMPANY     : " . ($s['product_company'] ? $s['product_company'] : '-') . "\nDATE OF PURCHASE    : " . ($s['purchase_date'] ? formatDate($s['purchase_date']) : '-') . "\nUNIT LOCATION       : " . ($s['unit_location'] ? $s['unit_location'] : '-') . "\nSERVICE REPORT NO   : " . ($s['service_report_no'] ? $s['service_report_no'] : '-') . "\nTECHNICIAN          : " . ($s['emp_name'] ? $s['emp_name'] : 'Unassigned') . "\nCOMPLAINT           : " . ($s['problem'] ? $s['problem'] : '-') . "\n-------------------------------------------------\nJOB DONE            : " . ($s['work_done'] ? $s['work_done'] : '-') . "\nREPLACED SPARES     : " . ($s['parts_used'] ? $s['parts_used'] : '-') . "\nSERVICE CHARGE      : " . ($s['service_charge'] ? $s['service_charge'] : '-') . "\nSERVICE CALL ITEMS  : {$wa_items}\nWARRANTY            : " . ($s['warranty_text'] ? $s['warranty_text'] : '-') . "\n-------------------------------------------------\nAMOUNT IN RS.       : " . number_format($s['service_amount'], 2) . "\nPAYMENT STATUS      : " . ($s['payment_status'] ? $s['payment_status'] : '-') . "\n-------------------------------------------------\nFOR HOT & COLD ENG\nAUTHORISED PERSON";
$wa_scheduled_url = whatsappLink((string)($s['phone'] ?? ''), $wa_scheduled);
$wa_completed_url = whatsappLink((string)($s['phone'] ?? ''), $wa_completed);
$map_link = trim($s['map_link'] ?? '');

include '../includes/header.php';
?>

<div class="flex-gap mb-16">
    <a href="field_list.php" class="btn btn-light btn-sm">← Back to List</a>
    <a href="field_edit.php?id=<?= $s['id'] ?>" class="btn btn-warning btn-sm">✏ Edit / Update</a>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Field Service #<?= $s['id'] ?></span>
        <div class="flex-gap">
            <?= statusBadge($s['status']) ?>
            <?= statusBadge($s['payment_status']) ?>
        </div>
    </div>
    <div class="card-body">

        <div class="section-heading">Customer Information</div>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Customer Name</label>
                <div class="value"><?= htmlspecialchars($s['customer_name']) ?></div>
            </div>
            <div class="detail-item">
                <label>Phone</label>
                <div class="value"><?= htmlspecialchars($s['phone']) ?></div>
            </div>
            <div class="detail-item detail-full">
                <label>Address</label>
                <div class="value"><?= nl2br(htmlspecialchars($s['address'])) ?></div>
            </div>
        </div>

        <hr class="divider">
        <div class="section-heading">Service Details</div>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Service Date</label>
                <div class="value"><?= formatDate($s['service_date']) ?></div>
            </div>
            <div class="detail-item">
                <label>Service Report No.</label>
                <div class="value"><?= $s['service_report_no'] ? '#' . htmlspecialchars($s['service_report_no']) : '-' ?></div>
            </div>
            <div class="detail-item">
                <label>Assigned Technician</label>
                <div class="value"><?= htmlspecialchars($s['emp_name'] ?? 'Unassigned') ?></div>
            </div>
            <div class="detail-item">
                <label>Name of Product</label>
                <div class="value"><?= $s['ac_type'] ? htmlspecialchars($s['ac_type']) : '-' ?></div>
            </div>
            <div class="detail-item">
                <label>Product of the Company</label>
                <div class="value"><?= $s['product_company'] ? htmlspecialchars($s['product_company']) : '-' ?></div>
            </div>
            <div class="detail-item">
                <label>Date of Purchase</label>
                <div class="value"><?= formatDate($s['purchase_date'] ?? '') ?></div>
            </div>
            <div class="detail-item">
                <label>Unit Location</label>
                <div class="value"><?= $s['unit_location'] ? htmlspecialchars($s['unit_location']) : '-' ?></div>
            </div>
            <div class="detail-item detail-full">
                <label>Complaint</label>
                <div class="value"><?= nl2br(htmlspecialchars($s['problem'])) ?></div>
            </div>
        </div>

<?php if ($s['work_done'] || $s['parts_used'] || ($s['service_charge'] ?? '') || ($s['service_call_items'] ?? '') || ($s['warranty_text'] ?? '')): ?>
        <hr class="divider">
        <div class="section-heading">Service Update</div>
        <div class="detail-grid">
            <div class="detail-item detail-full">
                <label>Job Done</label>
                <div class="value"><?= nl2br(htmlspecialchars($s['work_done'])) ?></div>
            </div>
            <div class="detail-item">
                <label>Replaced Spares</label>
                <div class="value"><?= $s['parts_used'] ? nl2br(htmlspecialchars($s['parts_used'])) : '-' ?></div>
            </div>
            <div class="detail-item">
                <label>Service Charge</label>
                <div class="value"><?= htmlspecialchars($s['service_charge'] ?? '-') ?></div>
            </div>
            <div class="detail-item detail-full">
                <label>Service Call Checklist</label>
                <div class="value">
                    <?php
                        $items = [];
                        if (!empty($s['service_call_items'])) {
                            $decoded = json_decode($s['service_call_items'], true);
                            if (is_array($decoded)) {
                                $items = $decoded;
                            } else {
                                $items = $s['service_call_items'];
                            }
                        }
                    ?>
                    <?php if (is_array($items)): ?>
                        <?= $items ? htmlspecialchars(implode(', ', $items)) : '-' ?>
                    <?php else: ?>
                        <?= $items ? nl2br(htmlspecialchars($items)) : '-' ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="detail-item">
                <label>Ampere</label>
                <div class="value"><?= $s['ampere'] ? htmlspecialchars($s['ampere']) : '-' ?></div>
            </div>
            <div class="detail-item">
                <label>Voltage</label>
                <div class="value"><?= $s['voltage'] ? htmlspecialchars($s['voltage']) : '-' ?></div>
            </div>
            <div class="detail-item">
                <label>Grill Temp</label>
                <div class="value"><?= $s['grill_temp'] ? htmlspecialchars($s['grill_temp']) : '-' ?></div>
            </div>
            <div class="detail-item">
                <label>S/D Pressure</label>
                <div class="value"><?= $s['sd_pressure'] ? htmlspecialchars($s['sd_pressure']) : '-' ?></div>
            </div>
            <div class="detail-item">
                <label>Warranty</label>
                <div class="value"><?= $s['warranty_text'] ? htmlspecialchars($s['warranty_text']) : '-' ?></div>
            </div>
            <div class="detail-item">
                <label>Service Amount</label>
                <div class="value"><?= $s['service_amount'] > 0 ? formatAmount($s['service_amount']) : '-' ?></div>
            </div>
            <div class="detail-item">
                <label>Payment Status</label>
                <div class="value"><?= statusBadge($s['payment_status']) ?></div>
            </div>
            <?php if (($s['payment_status'] ?? '') === 'Partial'): ?>
            <div class="detail-item">
                <label>Partial Payment Amount</label>
                <div class="value"><?= ($s['payment_amount'] ?? 0) > 0 ? formatAmount($s['payment_amount']) : '-' ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <hr class="divider">
        <div class="section-heading">Location</div>
        <?php if ($map_link): ?>
            <a href="<?= htmlspecialchars($map_link) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($map_link) ?></a>
        <?php else: ?>
            <p class="no-data">No map link provided.</p>
        <?php endif; ?>

    </div>
</div>

<!-- WhatsApp -->
<div class="card">
    <div class="card-header"><span class="card-title">📱 WhatsApp Notifications</span></div>
    <div class="card-body">
        <div class="wa-grid">
            <div>
                <p style="font-weight:700;margin-bottom:8px;font-size:13px;">📅 Service Scheduled Message</p>
                <div style="background:#f8fafc;border:1px solid #d5dce6;border-radius:4px;padding:10px;font-size:12.5px;white-space:pre-line;color:#2c3e50;min-height:80px;"><?= htmlspecialchars($wa_scheduled) ?></div>
                <a class="btn btn-whatsapp btn-sm mt-10" href="<?= htmlspecialchars($wa_scheduled_url) ?>">📤 Send WhatsApp</a>
            </div>
            <div>
                <p style="font-weight:700;margin-bottom:8px;font-size:13px;">✅ Service Completed Message</p>
                <div style="background:#f8fafc;border:1px solid #d5dce6;border-radius:4px;padding:10px;font-size:12.5px;white-space:pre-line;color:#2c3e50;min-height:80px;"><?= htmlspecialchars($wa_completed) ?></div>
                <a class="btn btn-whatsapp btn-sm mt-10" href="<?= htmlspecialchars($wa_completed_url) ?>">📤 Send WhatsApp</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

