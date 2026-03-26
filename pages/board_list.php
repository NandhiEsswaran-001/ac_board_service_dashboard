<?php
require_once '../includes/config.php';
$pageTitle = 'All Board Services';

$success = '';
$error   = '';

if (isset($_GET['deleted'])) {
    $success = 'Board entry deleted.';
}

// DELETE must be POST + CSRF (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verifyCsrf();
    if (!isOwner()) {
        $error = 'Only admin can delete board entries.';
    } else {
        $del_id = intval($_POST['delete_id']);
        if ($del_id > 0) {
            $del = getDB()->prepare("DELETE FROM board_services WHERE id=?");
            $del->execute([$del_id]);
            header('Location: board_list.php?deleted=1');
            exit;
        }
    }
}

$db = getDB();
$boards = $db->query("SELECT * FROM board_services ORDER BY created_at DESC")->fetchAll();

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Board Service Records</span>
        <a href="board_new.php" class="btn btn-primary btn-sm">+ New Board Entry</a>
    </div>
    <div class="card-body">
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <div class="filter-bar">
            <input type="text" id="tableSearch" placeholder="Search customer, phone number, problem...">
            <select id="statusFilter">
                <option value="">All Status</option>
                <option value="Pending">Pending</option>
                <option value="In Process">In Process</option>
                <option value="Completed">Completed</option>
                <option value="Delivered">Delivered</option>
            </select>
        </div>
    </div>
    <div class="table-wrap">
        <?php if ($boards): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer Name</th>
                    <th>Phone Number</th>
                    <th>Brand</th>
                    <th>Problem Description</th>
                    <th>Approx. Amt</th>
                    <th>Final Amt</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($boards as $b): ?>
                <tr data-status="<?= htmlspecialchars($b['status']) ?>">
                    <td><?= $b['id'] ?></td>
                    <td><strong><?= htmlspecialchars($b['customer_name']) ?></strong></td>
                    <td><?= htmlspecialchars($b['phone']) ?></td>
                    <td><?= htmlspecialchars($b['ac_brand']) ?: '-' ?></td>
                    <td style="max-width:160px;"><?= htmlspecialchars(mb_substr($b['problem'],0,50)) ?><?= strlen($b['problem'])>50?'...':'' ?></td>
                    <td><?= formatAmount($b['approx_amount']) ?></td>
                    <td><?= $b['final_amount'] > 0 ? formatAmount($b['final_amount']) : '-' ?></td>
                    <td><?= statusBadge($b['status']) ?></td>
                    <td><?= !empty($b['payment_status']) ? statusBadge($b['payment_status']) : '-' ?></td>
                    <td><?= date('d M Y', strtotime($b['created_at'])) ?></td>
                    <td>
                        <div class="flex-gap">
                            <a href="board_view.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-primary">View</a>
                            <a href="board_edit.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <?php if (isOwner()): ?>
                                <form method="POST" style="display:inline">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="delete_id" value="<?= $b['id'] ?>">
                                    <button type="submit"
                                            class="btn btn-sm btn-danger confirm-action"
                                            data-confirm="Delete board entry #<?= $b['id'] ?>?"
                                            data-confirm2="This cannot be undone. Delete now?">Delete</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="no-data">No board entries found. <a href="board_new.php">Create the first entry.</a></p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
