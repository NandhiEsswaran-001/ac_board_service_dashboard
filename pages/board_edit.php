<?php
require_once '../includes/config.php';
ensureBoardServiceImageColumns();
$pageTitle = 'Edit Board Entry';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: board_list.php'); exit; }

$db   = getDB();
$stmt = $db->prepare("SELECT * FROM board_services WHERE id=?");
$stmt->execute([$id]);
$b = $stmt->fetch();
if (!$b) { header('Location: board_list.php'); exit; }

$success = '';
$error   = '';
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
$saved_checks = array_filter(array_map('trim', explode(',', (string)($b['remark_checks'] ?? ''))));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

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
    $parts_replaced = trim($_POST['parts_replaced'] ?? '');
    $final_amount   = floatval($_POST['final_amount'] ?? 0);
    $notes          = trim($_POST['notes'] ?? '');
    $payment_status = trim($_POST['payment_status'] ?? ($b['payment_status'] ?? 'Pending'));
    $payment_amount = floatval($_POST['payment_amount'] ?? ($b['payment_amount'] ?? 0));
    $image_one_path = $b['image_one_path'] ?? null;
    $image_two_path = $b['image_two_path'] ?? null;

    if (!is_array($remark_checks)) { $remark_checks = []; }
    $remark_checks = array_values(array_intersect($remark_checks, $remark_items));
    $remark_checks_str = implode(', ', $remark_checks);

    $allowed = ['Pending', 'In Process', 'Completed', 'Delivered', 'Return'];
    $status  = in_array($_POST['status'] ?? '', $allowed) ? $_POST['status'] : $b['status'];
    $payment_allowed = ['Pending', 'Paid', 'Partial'];
    if (!in_array($payment_status, $payment_allowed, true)) {
        $payment_status = $b['payment_status'] ?? 'Pending';
    }
    if ($payment_status !== 'Partial') {
        $payment_amount = 0;
    } elseif ($payment_amount < 0) {
        $payment_amount = 0;
    }

    if (!$customer_name || !$phone) {
        $error = 'Customer name and phone are required.';
    } else {
        try {
            $newImageOne = handleBoardImageUpload('board_image_1');
            $newImageTwo = handleBoardImageUpload('board_image_2');

            if ($newImageOne) {
                $image_one_path = $newImageOne;
            }
            if ($newImageTwo) {
                $image_two_path = $newImageTwo;
            }

            $upd = $db->prepare("UPDATE board_services SET
                customer_name=?, phone=?, address=?, ac_brand=?, ac_model=?,
                problem=?, customer_remarks=?, remark_checks=?, parts_inside=?, image_one_path=?, image_two_path=?, approx_amount=?, parts_replaced=?,
                final_amount=?, status=?, payment_status=?, payment_amount=?, notes=? WHERE id=?");
            $upd->execute([$customer_name, $phone, $address, $ac_brand, $ac_model,
                           $problem, $customer_remarks, $remark_checks_str, $parts_inside, $image_one_path, $image_two_path, $approx_amount, $parts_replaced,
                           $final_amount, $status, $payment_status, $payment_amount, $notes, $id]);
            header('Location: board_edit.php?id=' . $id . '&updated=1');
            exit;
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        }
    }
}

if (isset($_GET['updated'])) {
    $success = 'Board entry updated successfully.';
}

include '../includes/header.php';
?>

<div class="flex-gap mb-16">
    <a href="board_view.php?id=<?= $id ?>" class="btn btn-light btn-sm">← Back to View</a>
    <a href="board_list.php" class="btn btn-light btn-sm">All Boards</a>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Edit Board Entry #<?= $id ?></span>
        <?= statusBadge($b['status']) ?>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?= csrfField() ?>

            <div class="section-heading">Customer Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Customer Name *</label>
                    <input type="text" name="customer_name" value="<?= htmlspecialchars($b['customer_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($b['phone']) ?>" required>
                </div>
                <div class="form-group full-width">
                    <label>Address</label>
                    <textarea name="address"><?= htmlspecialchars($b['address']) ?></textarea>
                </div>
            </div>

            <hr class="divider">
            <div class="section-heading">Board Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Brand</label>
                    <input type="text" name="ac_brand" value="<?= htmlspecialchars($b['ac_brand']) ?>">
                </div>
                <div class="form-group">
                    <label>Model</label>
                    <input type="text" name="ac_model" value="<?= htmlspecialchars($b['ac_model']) ?>">
                </div>
                <div class="form-group full-width">
                    <label>Problem Description</label>
                    <textarea name="problem"><?= htmlspecialchars($b['problem']) ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Customer Remarks</label>
                    <textarea name="customer_remarks" class="textarea-lg" placeholder="Customer's words or observations"><?= htmlspecialchars($b['customer_remarks'] ?? '') ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Checklist (Customer Reported / Observed)</label>
                    <div class="checklist-grid">
                        <?php foreach ($remark_items as $item): ?>
                            <label class="check-item">
                                <input type="checkbox" name="remark_checks[]" value="<?= htmlspecialchars($item) ?>"
                                    <?= in_array($item, $saved_checks, true) ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($item) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label>Parts Inside Board</label>
                    <textarea name="parts_inside"><?= htmlspecialchars($b['parts_inside']) ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Board Images</label>
                    <div class="board-image-grid">
                        <div class="board-image-slot">
                            <?php if (!empty($b['image_one_path'])): ?>
                            <a href="../<?= htmlspecialchars($b['image_one_path']) ?>" target="_blank" rel="noopener noreferrer" class="board-image-card">
                                <img src="../<?= htmlspecialchars($b['image_one_path']) ?>" alt="Board image 1" class="board-image-preview">
                                <span class="board-image-caption">Saved Image 1</span>
                            </a>
                            <?php else: ?>
                            <div class="board-image-empty">No image added for slot 1</div>
                            <?php endif; ?>
                            <div class="mt-10">
                                <label>Replace / Add Image 1</label>
                                <input type="file" name="board_image_1" accept="image/*">
                            </div>
                        </div>
                        <div class="board-image-slot">
                            <?php if (!empty($b['image_two_path'])): ?>
                            <a href="../<?= htmlspecialchars($b['image_two_path']) ?>" target="_blank" rel="noopener noreferrer" class="board-image-card">
                                <img src="../<?= htmlspecialchars($b['image_two_path']) ?>" alt="Board image 2" class="board-image-preview">
                                <span class="board-image-caption">Saved Image 2</span>
                            </a>
                            <?php else: ?>
                            <div class="board-image-empty">No image added for slot 2</div>
                            <?php endif; ?>
                            <div class="mt-10">
                                <label>Replace / Add Image 2</label>
                                <input type="file" name="board_image_2" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Approximate Amount (₹)</label>
                    <input type="number" name="approx_amount" value="<?= $b['approx_amount'] ?>" min="0" step="0.01">
                </div>
            </div>

            <hr class="divider">
            <div class="section-heading">Repair Update</div>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Parts Replaced</label>
                    <textarea name="parts_replaced" placeholder="List parts that were replaced during repair"><?= htmlspecialchars($b['parts_replaced']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Final Amount (₹)</label>
                    <input type="number" name="final_amount" value="<?= $b['final_amount'] ?>" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="payment_status">
                        <?php foreach (['Pending', 'Paid', 'Partial'] as $ps): ?>
                        <option value="<?= $ps ?>" <?= ($b['payment_status'] ?? 'Pending') === $ps ? 'selected' : '' ?>><?= $ps ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" data-partial-amount style="display:none;">
                    <label>Partial Payment Amount (???)</label>
                    <input type="number" name="payment_amount"
                           value="<?= htmlspecialchars($b['payment_amount'] ?? '0') ?>"
                           min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status">
                        <?php foreach (['Pending', 'In Process', 'Completed', 'Delivered', 'Return'] as $s): ?>
                        <option value="<?= $s ?>" <?= $b['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label>Internal Notes</label>
                    <textarea name="notes" placeholder="Any internal notes for this service"><?= htmlspecialchars($b['notes']) ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                <a href="board_view.php?id=<?= $id ?>" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
