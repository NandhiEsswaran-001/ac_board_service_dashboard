<?php
require_once '../includes/config.php';
$pageTitle = 'All Field Services';

$db = getDB();
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$totalStmt = $db->query("SELECT COUNT(*) FROM field_services");
$totalRecords = $totalStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

$stmt = $db->prepare("SELECT fs.*, u.full_name AS emp_name FROM field_services fs LEFT JOIN users u ON fs.assigned_employee=u.id ORDER BY fs.service_date DESC, fs.created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$services = $stmt->fetchAll();

$baseUrl = 'field_list.php?';

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Field Service Records</span>
        <a href="field_new.php" class="btn btn-primary btn-sm">+ New Field Service</a>
    </div>
    <div class="card-body">
        <div class="filter-bar">
            <input type="text" id="tableSearch" placeholder="Search customer, phone, problem...">
            <select id="statusFilter">
                <option value="">All Status</option>
                <option value="Scheduled">Scheduled</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select>
        </div>
    </div>
    <div class="table-wrap">
        <?php if ($services): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Technician</th>
                    <th>Service Date</th>
                    <th>Problem</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $s): ?>
                <tr data-status="<?= htmlspecialchars($s['status']) ?>">
                    <td><?= $s['id'] ?></td>
                    <td><strong><?= htmlspecialchars($s['customer_name']) ?></strong></td>
                    <td><?= htmlspecialchars($s['phone']) ?></td>
                    <td><?= htmlspecialchars($s['emp_name'] ?? 'Unassigned') ?></td>
                    <td><?= formatDate($s['service_date']) ?></td>
                    <td style="max-width:140px;"><?= htmlspecialchars(mb_substr($s['problem'],0,45)) ?><?= strlen($s['problem'])>45?'...':'' ?></td>
                    <td><?= $s['service_amount'] > 0 ? formatAmount($s['service_amount']) : '-' ?></td>
                    <td><?= statusBadge($s['payment_status']) ?></td>
                    <td><?= statusBadge($s['status']) ?></td>
                    <td>
                        <div class="flex-gap">
                            <a href="field_view.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-primary">View</a>
                            <a href="field_edit.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="no-data">No field services found. <a href="field_new.php">Create the first entry.</a></p>
        <?php endif; ?>
        <?= renderPagination($page, $totalPages, $baseUrl) ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
