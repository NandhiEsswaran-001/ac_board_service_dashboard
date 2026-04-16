<?php
require_once '../includes/config.php';
requireOwner();
ensureCallRegistersTable();

$pageTitle = 'View Call Register';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: call_register_list.php');
    exit;
}

$db = getDB();
$stmt = $db->prepare(
    "SELECT cr.*, u.full_name AS technician_name
     FROM call_registers cr
     LEFT JOIN users u ON cr.assigned_technician_id = u.id
     WHERE cr.id = ?"
);
$stmt->execute([$id]);
$call = $stmt->fetch();

if (!$call) {
    header('Location: call_register_list.php');
    exit;
}

$success = isset($_GET['created']) ? 'Call register entry created successfully.' : '';
$waMessage = buildCallRegisterWhatsappMessage($call, (string)($call['technician_name'] ?? ''));
$waUrl = whatsappLink((string)($call['phone'] ?? ''), $waMessage);

include '../includes/header.php';
?>

<div class="flex-gap mb-16">
    <a href="call_register_list.php" class="btn btn-light btn-sm">Back to List</a>
    <a href="call_register_new.php" class="btn btn-primary btn-sm">New Call Entry</a>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Call Register #<?= (int)$call['id'] ?></span>
        <?= statusBadge($call['status'] ?: 'Assigned') ?>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <label>Customer Name</label>
                <div class="value"><?= htmlspecialchars($call['customer_name']) ?></div>
            </div>
            <div class="detail-item">
                <label>Phone Number</label>
                <div class="value"><?= htmlspecialchars($call['phone']) ?></div>
            </div>
            <div class="detail-item">
                <label>Assigned Technician</label>
                <div class="value"><?= htmlspecialchars($call['technician_name'] ?: 'Unassigned') ?></div>
            </div>
            <div class="detail-item">
                <label>Product Name</label>
                <div class="value"><?= htmlspecialchars($call['product_name'] ?: '-') ?></div>
            </div>
            <div class="detail-item detail-full">
                <label>Address</label>
                <div class="value"><?= nl2br(htmlspecialchars($call['address'] ?: '-')) ?></div>
            </div>
            <div class="detail-item detail-full">
                <label>Complaint</label>
                <div class="value"><?= nl2br(htmlspecialchars($call['complaint'] ?: '-')) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">WhatsApp Notification</span>
    </div>
    <div class="card-body">
        <div style="background:#f8fafc;border:1px solid #d5dce6;border-radius:4px;padding:10px;font-size:12.5px;white-space:pre-line;color:#2c3e50;min-height:80px;"><?= htmlspecialchars($waMessage) ?></div>
        <div class="mt-10">
            <a class="btn btn-whatsapp btn-sm" href="<?= htmlspecialchars($waUrl) ?>">Send WhatsApp to Customer</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
