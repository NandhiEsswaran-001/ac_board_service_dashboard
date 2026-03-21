<?php
require_once '../includes/config.php';
$pageTitle = 'Dashboard';

if (!isOwner()) {
    header('Location: board_list.php');
    exit;
}

$db = getDB();

$revMonthInput = $_GET['rev_month'] ?? date('Y-m');
$revFromInput  = $_GET['rev_from'] ?? '';
$revToInput    = $_GET['rev_to'] ?? '';

$revYear = (int)date('Y');
$revMonth = (int)date('n');
if (preg_match('/^(\d{4})-(\d{2})$/', $revMonthInput, $m)) {
    $revYear = (int)$m[1];
    $revMonth = (int)$m[2];
}
if ($revMonth < 1 || $revMonth > 12) $revMonth = (int)date('n');
if ($revYear < 2020 || $revYear > 2040) $revYear = (int)date('Y');

$revFrom = null;
$revTo   = null;
if ($revFromInput && preg_match('/^\d{4}-\d{2}-\d{2}$/', $revFromInput)) $revFrom = $revFromInput;
if ($revToInput && preg_match('/^\d{4}-\d{2}-\d{2}$/', $revToInput))     $revTo   = $revToInput;
if ($revFrom && $revTo && $revFrom > $revTo) {
    $tmp = $revFrom;
    $revFrom = $revTo;
    $revTo = $tmp;
}

$revLabel = 'All Time';
$revFilterMode = 'month';
if ($revFrom && $revTo) {
    $revLabel = date('d M Y', strtotime($revFrom)) . ' to ' . date('d M Y', strtotime($revTo));
    $revFilterMode = 'range';
} else {
    $revLabel = date('F Y', mktime(0, 0, 0, $revMonth, 1, $revYear));
}

$pending    = $db->query("SELECT COUNT(*) FROM board_services WHERE status='Pending'")->fetchColumn();
$inprocess  = $db->query("SELECT COUNT(*) FROM board_services WHERE status='In Process'")->fetchColumn();
$completed  = $db->query("SELECT COUNT(*) FROM board_services WHERE status='Completed'")->fetchColumn();
$fieldTotal = $db->query("SELECT COUNT(*) FROM field_services")->fetchColumn();

$boardAll = $db->query("SELECT COALESCE(SUM(final_amount),0)
                        FROM board_services
                        WHERE status IN ('Completed','Delivered')")->fetchColumn();
$fieldAll = $db->query("SELECT COALESCE(SUM(service_amount),0)
                        FROM field_services
                        WHERE payment_status='Paid'")->fetchColumn();
$totalRevenueAll = $boardAll + $fieldAll;

if ($revFilterMode === 'range') {
    $boardRevenueStmt = $db->prepare("SELECT COALESCE(SUM(final_amount),0)
                                      FROM board_services
                                      WHERE status IN ('Completed','Delivered')
                                        AND DATE(created_at) BETWEEN ? AND ?");
    $boardRevenueStmt->execute([$revFrom, $revTo]);
    $boardRevenue = $boardRevenueStmt->fetchColumn();

    $fieldRevenueStmt = $db->prepare("SELECT COALESCE(SUM(service_amount),0)
                                      FROM field_services
                                      WHERE payment_status='Paid'
                                        AND service_date BETWEEN ? AND ?");
    $fieldRevenueStmt->execute([$revFrom, $revTo]);
    $fieldRevenue = $fieldRevenueStmt->fetchColumn();
} else {
    $boardRevenueStmt = $db->prepare("SELECT COALESCE(SUM(final_amount),0)
                                      FROM board_services
                                      WHERE status IN ('Completed','Delivered')
                                        AND MONTH(created_at) = ?
                                        AND YEAR(created_at) = ?");
    $boardRevenueStmt->execute([$revMonth, $revYear]);
    $boardRevenue = $boardRevenueStmt->fetchColumn();

    $fieldRevenueStmt = $db->prepare("SELECT COALESCE(SUM(service_amount),0)
                                      FROM field_services
                                      WHERE payment_status='Paid'
                                        AND MONTH(service_date) = ?
                                        AND YEAR(service_date) = ?");
    $fieldRevenueStmt->execute([$revMonth, $revYear]);
    $fieldRevenue = $fieldRevenueStmt->fetchColumn();
}

$totalRevenue = $boardRevenue + $fieldRevenue;

$recentBoards = $db->query("SELECT * FROM board_services ORDER BY created_at DESC LIMIT 7")->fetchAll();
$recentFields = $db->query("SELECT fs.*, u.full_name AS emp_name FROM field_services fs LEFT JOIN users u ON fs.assigned_employee=u.id ORDER BY fs.created_at DESC LIMIT 7")->fetchAll();

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Revenue Filters</span>
        <span style="font-size:12px;color:#7f8c8d;"><?= htmlspecialchars($revLabel) ?></span>
    </div>
    <div class="card-body">
        <form method="GET" class="form-grid" style="align-items:end;gap:12px;">
            <div class="form-group">
                <label>Month</label>
                <input type="month" name="rev_month" value="<?= htmlspecialchars($revMonthInput) ?>" required>
            </div>
            <div class="form-group">
                <label>From</label>
                <input type="date" name="rev_from" value="<?= htmlspecialchars($revFromInput) ?>">
            </div>
            <div class="form-group">
                <label>To</label>
                <input type="date" name="rev_to" value="<?= htmlspecialchars($revToInput) ?>">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="dashboard.php" class="btn btn-light">Reset</a>
            </div>
        </form>
        <div style="font-size:12px;color:#7f8c8d;margin-top:6px;">
            Date range overrides month when both are provided.
        </div>
        <div style="margin-top:12px;padding:10px 12px;background:#f8fafc;border:1px solid #d5dce6;border-radius:6px;">
            <div style="font-size:12px;color:#7f8c8d;margin-bottom:4px;">Filtered Revenue (<?= htmlspecialchars($revLabel) ?>)</div>
            <div style="font-size:18px;font-weight:800;color:#1a2535;"><?= formatAmount($totalRevenue) ?></div>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card orange">
        <div class="stat-number"><?= $pending ?></div>
        <div class="stat-label">Pending Boards</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-number"><?= $inprocess ?></div>
        <div class="stat-label">In Process</div>
    </div>
    <div class="stat-card green">
        <div class="stat-number"><?= $completed ?></div>
        <div class="stat-label">Board Completed</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-number"><?= $fieldTotal ?></div>
        <div class="stat-label">Total Field Services</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-number" style="font-size:18px;"><?= formatAmount($totalRevenueAll) ?></div>
        <div class="stat-label">Total Revenue (All Time)</div>
    </div>
</div>

<div class="dash-grid">

<div class="card">
    <div class="card-header">
        <span class="card-title">Recent Board Services</span>
        <a href="board_list.php" class="btn btn-sm btn-light">View All</a>
    </div>
    <div class="table-wrap">
        <?php if ($recentBoards): ?>
        <table>
            <thead>
                <tr><th>#</th><th>Customer</th><th>Problem</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($recentBoards as $b): ?>
                <tr>
                    <td><?= $b['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($b['customer_name']) ?></strong><br>
                        <small style="color:#7f8c8d;"><?= htmlspecialchars($b['phone']) ?></small>
                    </td>
                    <td><?= htmlspecialchars(mb_substr($b['problem'],0,30)) ?>...</td>
                    <td><?= statusBadge($b['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="no-data">No board entries yet.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Recent Field Services</span>
        <a href="field_list.php" class="btn btn-sm btn-light">View All</a>
    </div>
    <div class="table-wrap">
        <?php if ($recentFields): ?>
        <table>
            <thead>
                <tr><th>#</th><th>Customer</th><th>Date</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($recentFields as $f): ?>
                <tr>
                    <td><?= $f['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($f['customer_name']) ?></strong><br>
                        <small style="color:#7f8c8d;"><?= htmlspecialchars($f['emp_name'] ?? 'Unassigned') ?></small>
                    </td>
                    <td><?= formatDate($f['service_date']) ?></td>
                    <td><?= statusBadge($f['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="no-data">No field services yet.</p>
        <?php endif; ?>
    </div>
</div>

</div>

<?php include '../includes/footer.php'; ?>
