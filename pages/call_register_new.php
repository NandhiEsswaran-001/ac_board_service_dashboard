<?php
require_once '../includes/config.php';
requireOwner();
ensureCallRegistersTable();

$pageTitle = 'New Call Register';

$db = getDB();
$technicians = $db->query("SELECT id, full_name FROM users WHERE role='technician' ORDER BY full_name")->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $customer_name = trim($_POST['customer_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $complaint = trim($_POST['complaint'] ?? '');
    $assigned_technician_id = (int)($_POST['assigned_technician_id'] ?? 0);
    $phone = trim($_POST['phone'] ?? '');
    $product_name = trim($_POST['product_name'] ?? '');

    if ($customer_name === '' || $phone === '' || $complaint === '' || $assigned_technician_id <= 0) {
        $error = 'Customer name, phone, complaint, and assigned technician are required.';
    } else {
        $stmt = $db->prepare(
            "INSERT INTO call_registers
                (customer_name, address, complaint, assigned_technician_id, phone, product_name, status, created_by)
             VALUES (?,?,?,?,?,?, 'Assigned', ?)"
        );
        $stmt->execute([
            $customer_name,
            $address,
            $complaint,
            $assigned_technician_id,
            $phone,
            $product_name,
            $_SESSION['user_id']
        ]);

        header('Location: call_register_view.php?id=' . $db->lastInsertId() . '&created=1');
        exit;
    }
}

include '../includes/header.php';
?>

<?php if ($error): ?>
<div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Service Manager - Call Register</span>
    </div>
    <div class="card-body">
        <form method="POST">
            <?= csrfField() ?>

            <div class="form-grid">
                <div class="form-group">
                    <label>Customer Name *</label>
                    <input type="text" name="customer_name" value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" value="<?= htmlspecialchars($_POST['product_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Assigned Technician *</label>
                    <select name="assigned_technician_id" required>
                        <option value="">-- Select Technician --</option>
                        <?php foreach ($technicians as $tech): ?>
                        <option value="<?= (int)$tech['id'] ?>" <?= (string)($tech['id']) === (string)($_POST['assigned_technician_id'] ?? '') ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tech['full_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label>Address</label>
                    <textarea name="address" placeholder="Customer address"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Complaint *</label>
                    <textarea name="complaint" class="textarea-lg" placeholder="Customer complaint / issue" required><?= htmlspecialchars($_POST['complaint'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Call Register</button>
                <a href="call_register_list.php" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
