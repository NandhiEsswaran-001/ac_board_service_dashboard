<?php
require_once '../includes/config.php';
ensureBoardServiceImageColumns();
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

$remark_items_left = [
    'Indoor PCB',
    'Display PCB',
    'Room Sensor-ID',
    'Coil Sensor-ID',
    'Remote',
    'Indoor Motor',
    'Transformer',
    'Swing MTR'
];

$remark_items_right = [
    'Outdoor PCB',
    'Dis Sensor-OD',
    'Coil Sensor-OD',
    'AMB Sensor-OD',
    'OLP',
    'Outdoor Motor',
    'Reactor',
    'Stabilizer',
    'Washing Machine PCB',
    'Refrigerator PCB',
    'Wiring and Kit',
    'Compressor Jack',
    'EEV'
];

$remark_items = array_merge($remark_items_left, $remark_items_right);
$remark_row_count = max(count($remark_items_left), count($remark_items_right));

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
        try {
            $image_one_path = handleBoardImageUpload('board_image_1');
            $image_two_path = handleBoardImageUpload('board_image_2');

            $db   = getDB();
            $stmt = $db->prepare("INSERT INTO board_services
                (customer_name, phone, address, ac_brand, ac_model, problem,
                 customer_remarks, remark_checks, parts_inside, image_one_path, image_two_path,
                 approx_amount, status, payment_status, payment_amount, created_by)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,'Pending',?,?,?)");
            $stmt->execute([$customer_name, $phone, $address, $ac_brand, $ac_model,
                            $problem, $customer_remarks, $remark_checks_str, $parts_inside,
                            $image_one_path, $image_two_path, $approx_amount, $payment_status, $payment_amount, $_SESSION['user_id']]);
            $newId   = $db->lastInsertId();
            unset($_SESSION['board_new_token']);
            header('Location: board_view.php?id=' . $newId . '&created=1');
            exit;
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        }
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
        <form method="POST" enctype="multipart/form-data">
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
                        <?php for ($row = 0; $row < $remark_row_count; $row++): ?>
                            <div class="checklist-row">
                                <?php if (isset($remark_items_left[$row])): ?>
                                    <?php $item = $remark_items_left[$row]; ?>
                                    <label class="check-item">
                                        <span class="check-index"><?= $row + 1 ?></span>
                                        <input type="checkbox" name="remark_checks[]" value="<?= htmlspecialchars($item) ?>"
                                            <?= in_array($item, $remark_checks, true) ? 'checked' : '' ?>>
                                        <span><?= htmlspecialchars($item) ?></span>
                                    </label>
                                <?php else: ?>
                                    <span class="check-item check-item-empty" aria-hidden="true"></span>
                                <?php endif; ?>

                                <?php if (isset($remark_items_right[$row])): ?>
                                    <?php $item = $remark_items_right[$row]; ?>
                                    <label class="check-item">
                                        <span class="check-index"><?= count($remark_items_left) + $row + 1 ?></span>
                                        <input type="checkbox" name="remark_checks[]" value="<?= htmlspecialchars($item) ?>"
                                            <?= in_array($item, $remark_checks, true) ? 'checked' : '' ?>>
                                        <span><?= htmlspecialchars($item) ?></span>
                                    </label>
                                <?php else: ?>
                                    <span class="check-item check-item-empty" aria-hidden="true"></span>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label>Parts Inside Board</label>
                    <textarea name="parts_inside"
                              placeholder="List the parts/components found inside the board"><?= htmlspecialchars($_POST['parts_inside'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Board Image 1</label>
                    <input type="file" name="board_image_1" accept="image/*" capture="environment">
                    <button type="button" class="btn btn-secondary" onclick="takePhoto(1)">📷 Take Photo</button>
                </div>
                <div class="form-group">
                    <label>Board Image 2</label>
                    <input type="file" name="board_image_2" accept="image/*" capture="environment">
                    <button type="button" class="btn btn-secondary" onclick="takePhoto(2)">📷 Take Photo</button>
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

<!-- Camera Modal -->
<div id="cameraModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: white; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 400px; text-align: center;">
        <span class="close" onclick="closeModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <h3>Take Photo</h3>
        <video id="cameraVideo" autoplay style="width: 100%; max-width: 300px;"></video>
        <canvas id="cameraCanvas" style="display: none;"></canvas>
        <br><br>
        <button type="button" class="btn btn-primary" id="captureBtn">📸 Capture</button>
        <button type="button" class="btn btn-light" onclick="closeModal()">Cancel</button>
    </div>
</div>

<script>
let currentImageField = null;
let stream = null;

function takePhoto(fieldNum) {
    currentImageField = fieldNum;
    const modal = document.getElementById('cameraModal');
    const video = document.getElementById('cameraVideo');
    modal.style.display = 'block';

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(function(mediaStream) {
            stream = mediaStream;
            video.srcObject = mediaStream;
        })
        .catch(function(err) {
            alert('Camera access denied or not available: ' + err.message);
            closeModal();
        });
}

function closeModal() {
    const modal = document.getElementById('cameraModal');
    const video = document.getElementById('cameraVideo');
    modal.style.display = 'none';
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    video.srcObject = null;
}

document.getElementById('captureBtn').addEventListener('click', function() {
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    const ctx = canvas.getContext('2d');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);

    canvas.toBlob(function(blob) {
        const file = new File([blob], `board_image_${currentImageField}.jpg`, { type: 'image/jpeg' });
        const input = document.querySelector(`input[name="board_image_${currentImageField}"]`);
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        closeModal();
    }, 'image/jpeg');
});

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


