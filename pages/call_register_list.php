<?php
require_once '../includes/config.php';
requireOwner();
ensureCallRegistersTable();

$pageTitle = 'Call Register';

$db = getDB();
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$totalStmt = $db->query("SELECT COUNT(*) FROM call_registers");
$totalRecords = $totalStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

$stmt = $db->prepare("SELECT cr.*, u.full_name AS technician_name
     FROM call_registers cr
     LEFT JOIN users u ON cr.assigned_technician_id = u.id
     ORDER BY cr.created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$calls = $stmt->fetchAll();

$baseUrl = 'call_register_list.php?';

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Service Manager - Call Register</span>
        <a href="call_register_new.php" class="btn btn-primary btn-sm">+ New Call Entry</a>
    </div>
    <div class="table-wrap">
        <?php if ($calls): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Product</th>
                    <th>Complaint</th>
                    <th>Technician</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($calls as $call): ?>
                <tr>
                    <td><?= (int)$call['id'] ?></td>
                    <td><?= htmlspecialchars($call['customer_name']) ?></td>
                    <td><?= htmlspecialchars($call['phone']) ?></td>
                    <td><?= htmlspecialchars($call['product_name'] ?: '-') ?></td>
                    <td><?= htmlspecialchars(mb_substr((string)$call['complaint'], 0, 60)) ?><?= mb_strlen((string)$call['complaint']) > 60 ? '...' : '' ?></td>
                    <td><?= htmlspecialchars($call['technician_name'] ?: 'Unassigned') ?></td>
                    <td><?= statusBadge($call['status'] ?: 'Assigned') ?></td>
                    <td><a href="call_register_view.php?id=<?= (int)$call['id'] ?>" class="btn btn-sm btn-primary">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="no-data">No call register entries found. <a href="call_register_new.php">Create the first entry.</a></p>
        <?php endif; ?>
        <?= renderPagination($page, $totalPages, $baseUrl) ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
