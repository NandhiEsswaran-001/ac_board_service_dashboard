<?php
require_once '../includes/config.php';
ensureBoardServiceImageColumns();
$pageTitle = 'View Board Entry';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: board_list.php'); exit; }

$success = '';
$error   = '';

// DELETE must be POST + CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verifyCsrf();
    if (!isOwner()) {
        $error = 'Only admin can delete board entries.';
    } else {
        $del_id = intval($_POST['delete_id']);
        if ($del_id === $id) {
            getDB()->prepare("DELETE FROM board_services WHERE id=?")->execute([$del_id]);
            header('Location: board_list.php');
            exit;
        }
    }
}

$db    = getDB();
$board = $db->prepare("SELECT * FROM board_services WHERE id=?");
$board->execute([$id]);
$b = $board->fetch();
if (!$b) { header('Location: board_list.php'); exit; }

if (isset($_GET['created'])) {
    $success = 'Board entry saved successfully! Entry #' . $b['id'];
}

$wa_received  = "HOT & COLD ENGINEERING\nAIR CONDITIONER PCB SERVICE CENTER\n(ALL TYPES OF INVERTER / NON INVERTER)\n97, 1st floor, 7th street, tatabad, near 6 Corner,\nCoimbatore - 641012\n\n-------------------- RECEIPT --------------------\nJOB NO : {$b['id']}\nDATE   : " . date('d M Y', strtotime($b['created_at'])) . "\nSTATUS : PENDING\n-------------------------------------------------\nNAME   : {$b['customer_name']}\nPHONE  : {$b['phone']}\nADDRESS: " . ($b['address'] ? $b['address'] : '-') . "\n-------------------------------------------------\nBRAND / ERROR     : " . trim(($b['ac_brand'] ? $b['ac_brand'] : '-') . ($b['ac_model'] ? ' ' . $b['ac_model'] : '')) . "\nCUSTOMER REMARKS : " . ($b['customer_remarks'] ? $b['customer_remarks'] : '-') . "\n-------------------------------------------------\nAMOUNT IN RS.     : " . number_format($b['approx_amount'], 2) . "\n-------------------------------------------------\nFOR HOT & COLD ENG\nAUTHORISED PERSON";
$wa_completed = "HOT & COLD ENGINEERING\nAIR CONDITIONER PCB SERVICE CENTER\n(ALL TYPES OF INVERTER / NON INVERTER)\n97, 1st floor, 7th street, tatabad, near 6 Corner,\nCoimbatore - 641012\n\n-------------------- RECEIPT --------------------\nJOB NO : {$b['id']}\nDATE   : " . date('d M Y', strtotime($b['created_at'])) . "\nSTATUS : COMPLETED\n-------------------------------------------------\nNAME   : {$b['customer_name']}\nPHONE  : {$b['phone']}\nADDRESS: " . ($b['address'] ? $b['address'] : '-') . "\n-------------------------------------------------\nBRAND / ERROR     : " . trim(($b['ac_brand'] ? $b['ac_brand'] : '-') . ($b['ac_model'] ? ' ' . $b['ac_model'] : '')) . "\nCUSTOMER REMARKS : " . ($b['customer_remarks'] ? $b['customer_remarks'] : '-') . "\nREPLACED PARTS   : " . ($b['parts_replaced'] ? $b['parts_replaced'] : '-') . "\n-------------------------------------------------\nAMOUNT IN RS.     : " . number_format($b['final_amount'] > 0 ? $b['final_amount'] : $b['approx_amount'], 2) . "\nRECEIVED GOODS   : " . date('d M Y') . "\n-------------------------------------------------\nFOR HOT & COLD ENG\nAUTHORISED PERSON";

$wa_received_url  = whatsappLink((string)($b['phone'] ?? ''), $wa_received);
$wa_completed_url = whatsappLink((string)($b['phone'] ?? ''), $wa_completed);

include '../includes/header.php';
?>

<div class="flex-gap mb-16">
    <a href="board_list.php" class="btn btn-light btn-sm">← Back to List</a>
    <a href="board_edit.php?id=<?= $b['id'] ?>" class="btn btn-warning btn-sm">✏ Edit / Update Status</a>

    <?php if (isOwner()): ?>
    <form method="POST" style="display:inline"
          onsubmit="return confirmDelete(event, <?= $b['id'] ?>)">
        <?= csrfField() ?>
        <input type="hidden" name="delete_id" value="<?= $b['id'] ?>">
        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
    </form>
    <?php endif; ?>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Board Entry #<?= $b['id'] ?></span>
        <?= statusBadge($b['status']) ?>
    </div>
    <div class="card-body">

        <div class="section-heading">Customer Information</div>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Customer Name</label>
                <div class="value"><?= htmlspecialchars($b['customer_name']) ?></div>
            </div>
            <div class="detail-item">
                <label>Phone Number</label>
                <div class="value"><?= htmlspecialchars($b['phone']) ?></div>
            </div>
            <div class="detail-item">
                <label>Address</label>
                <div class="value"><?= $b['address'] ? htmlspecialchars($b['address']) : '-' ?></div>
            </div>
            <div class="detail-item">
                <label>Date Received</label>
                <div class="value"><?= date('d M Y, h:i A', strtotime($b['created_at'])) ?></div>
            </div>
        </div>

        <hr class="divider">
        <div class="section-heading">Board Details</div>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Brand</label>
                <div class="value"><?= $b['ac_brand'] ? htmlspecialchars($b['ac_brand']) : '-' ?></div>
            </div>
            <div class="detail-item">
                <label>Model</label>
                <div class="value"><?= $b['ac_model'] ? htmlspecialchars($b['ac_model']) : '-' ?></div>
            </div>
            <div class="detail-item detail-full">
                <label>Problem Description</label>
                <div class="value"><?= nl2br(htmlspecialchars($b['problem'])) ?></div>
            </div>
            <?php if (!empty($b['customer_remarks'])): ?>
            <div class="detail-item detail-full">
                <label>Customer Remarks</label>
                <div class="value"><?= nl2br(htmlspecialchars($b['customer_remarks'])) ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($b['remark_checks'])): ?>
            <div class="detail-item detail-full">
                <label>Checklist (Customer Reported / Observed)</label>
                <div class="tag-list">
                    <?php foreach (array_filter(array_map('trim', explode(',', $b['remark_checks']))) as $tag): ?>
                        <span class="tag"><?= htmlspecialchars($tag) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="detail-item detail-full">
                <label>Parts Inside Board</label>
                <div class="value"><?= $b['parts_inside'] ? nl2br(htmlspecialchars($b['parts_inside'])) : '-' ?></div>
            </div>
            <?php if (!empty($b['image_one_path']) || !empty($b['image_two_path'])): ?>
            <div class="detail-item detail-full">
                <label>Board Images</label>
                <div class="board-image-gallery">
                    <div class="board-image-gallery-head">
                        <span>Attached board photos</span>
                        <small>Click any image to open full size</small>
                    </div>
                    <div class="board-image-grid">
                    <?php if (!empty($b['image_one_path'])): ?>
                    <a href="../<?= htmlspecialchars($b['image_one_path']) ?>" target="_blank" rel="noopener noreferrer" class="board-image-card">
                        <img src="../<?= htmlspecialchars($b['image_one_path']) ?>" alt="Board image 1" class="board-image-preview">
                        <span class="board-image-caption">Board Image 1</span>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($b['image_two_path'])): ?>
                    <a href="../<?= htmlspecialchars($b['image_two_path']) ?>" target="_blank" rel="noopener noreferrer" class="board-image-card">
                        <img src="../<?= htmlspecialchars($b['image_two_path']) ?>" alt="Board image 2" class="board-image-preview">
                        <span class="board-image-caption">Board Image 2</span>
                    </a>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <hr class="divider">
        <div class="section-heading">Financial Details</div>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Approximate Amount</label>
                <div class="value"><?= formatAmount($b['approx_amount']) ?></div>
            </div>
            <div class="detail-item">
                <label>Final Amount</label>
                <div class="value"><?= $b['final_amount'] > 0 ? formatAmount($b['final_amount']) : '-' ?></div>
            </div>
            <div class="detail-item">
                <label>Payment Status</label>
                <div class="value"><?= !empty($b['payment_status']) ? statusBadge($b['payment_status']) : '-' ?></div>
            </div>
            <?php if (($b['payment_status'] ?? '') === 'Partial'): ?>
            <div class="detail-item">
                <label>Partial Payment Amount</label>
                <div class="value"><?= ($b['payment_amount'] ?? 0) > 0 ? formatAmount($b['payment_amount']) : '-' ?></div>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($b['parts_replaced'] || $b['notes']): ?>
        <hr class="divider">
        <div class="section-heading">Repair Details</div>
        <div class="detail-grid">
            <?php if ($b['parts_replaced']): ?>
            <div class="detail-item detail-full">
                <label>Parts Replaced</label>
                <div class="value"><?= nl2br(htmlspecialchars($b['parts_replaced'])) ?></div>
            </div>
            <?php endif; ?>
            <?php if ($b['notes']): ?>
            <div class="detail-item detail-full">
                <label>Notes</label>
                <div class="value"><?= nl2br(htmlspecialchars($b['notes'])) ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- WhatsApp Section -->
<div class="card">
    <div class="card-header"><span class="card-title">📱 WhatsApp Notifications</span></div>
    <div class="card-body">
        <div class="wa-grid">
            <div>
                <p style="font-weight:700;margin-bottom:8px;font-size:13px;">📥 Service Received Message</p>
                <div style="background:#f8fafc;border:1px solid #d5dce6;border-radius:4px;padding:10px;font-size:12.5px;white-space:pre-line;color:#2c3e50;min-height:80px;"><?= htmlspecialchars($wa_received) ?></div>
                <a class="btn btn-whatsapp btn-sm mt-10" href="<?= htmlspecialchars($wa_received_url) ?>">📤 Send WhatsApp</a>
            </div>
            <div>
                <p style="font-weight:700;margin-bottom:8px;font-size:13px;">✅ Repair Completed Message</p>
                <div style="background:#f8fafc;border:1px solid #d5dce6;border-radius:4px;padding:10px;font-size:12.5px;white-space:pre-line;color:#2c3e50;min-height:80px;"><?= htmlspecialchars($wa_completed) ?></div>
                <a class="btn btn-whatsapp btn-sm mt-10" href="<?= htmlspecialchars($wa_completed_url) ?>">📤 Send WhatsApp</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(e, id) {
    if (!confirm('Delete board entry #' + id + '?')) { e.preventDefault(); return false; }
    if (!confirm('This cannot be undone. Delete now?')) { e.preventDefault(); return false; }
    return true;
}
</script>

<?php include '../includes/footer.php'; ?>
