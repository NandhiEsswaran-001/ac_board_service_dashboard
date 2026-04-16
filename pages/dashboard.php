<?php
require_once '../includes/config.php';
ensureCallRegistersTable();

$pageTitle = 'Dashboard';

if (!isDashboardUser()) {
    header('Location: board_list.php');
    exit;
}

$db = getDB();

if (isTechnician()) {
    $technicianId = (int)($_SESSION['user_id'] ?? 0);

    $assignedCallsStmt = $db->prepare(
        "SELECT cr.*, u.full_name AS technician_name
         FROM call_registers cr
         LEFT JOIN users u ON cr.assigned_technician_id = u.id
         WHERE cr.assigned_technician_id = ?
         ORDER BY FIELD(cr.status, 'Assigned', 'New', 'In Progress', 'Completed'), cr.created_at DESC"
    );
    $assignedCallsStmt->execute([$technicianId]);
    $assignedCalls = $assignedCallsStmt->fetchAll();

    $assignedCount = count($assignedCalls);
    $openCount = 0;
    foreach ($assignedCalls as $call) {
        if (($call['status'] ?? '') !== 'Completed') {
            $openCount++;
        }
    }

    include '../includes/header.php';
    ?>

    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-number"><?= $assignedCount ?></div>
            <div class="stat-label">Assigned Calls</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-number"><?= $openCount ?></div>
            <div class="stat-label">Open Calls</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">My Call Register Notifications</span>
        </div>
        <div class="table-wrap">
            <?php if ($assignedCalls): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Product</th>
                        <th>Complaint</th>
                        <th>Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignedCalls as $call): ?>
                    <tr>
                        <td><?= (int)$call['id'] ?></td>
                        <td><?= htmlspecialchars($call['customer_name']) ?></td>
                        <td><?= htmlspecialchars($call['phone']) ?></td>
                        <td><?= htmlspecialchars($call['product_name'] ?: '-') ?></td>
                        <td><?= htmlspecialchars(mb_substr((string)$call['complaint'], 0, 70)) ?><?= mb_strlen((string)$call['complaint']) > 70 ? '...' : '' ?></td>
                        <td><?= htmlspecialchars(mb_substr((string)$call['address'], 0, 70)) ?><?= mb_strlen((string)$call['address']) > 70 ? '...' : '' ?></td>
                        <td><?= statusBadge($call['status'] ?: 'Assigned') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="no-data">No call-register assignments yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php
    include '../includes/footer.php';
    return;
}

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
$callTotal  = $db->query("SELECT COUNT(*) FROM call_registers")->fetchColumn();

$boardAll = $db->query("SELECT COALESCE(SUM(
                            CASE
                                WHEN payment_status='Paid' THEN (CASE WHEN final_amount>0 THEN final_amount ELSE approx_amount END)
                                WHEN payment_status='Partial' THEN payment_amount
                                ELSE 0
                            END
                        ),0)
                        FROM board_services
                        WHERE status IN ('Completed','Delivered')")->fetchColumn();
$fieldAll = $db->query("SELECT COALESCE(SUM(
                            CASE
                                WHEN payment_status='Paid' THEN service_amount
                                WHEN payment_status='Partial' THEN payment_amount
                                ELSE 0
                            END
                        ),0)
                        FROM field_services")->fetchColumn();
$totalRevenueAll = $boardAll + $fieldAll;

if ($revFilterMode === 'range') {
    $boardRevenueStmt = $db->prepare("SELECT COALESCE(SUM(
                                          CASE
                                              WHEN payment_status='Paid' THEN (CASE WHEN final_amount>0 THEN final_amount ELSE approx_amount END)
                                              WHEN payment_status='Partial' THEN payment_amount
                                              ELSE 0
                                          END
                                      ),0)
                                      FROM board_services
                                      WHERE status IN ('Completed','Delivered')
                                        AND DATE(created_at) BETWEEN ? AND ?");
    $boardRevenueStmt->execute([$revFrom, $revTo]);
    $boardRevenue = $boardRevenueStmt->fetchColumn();

    $fieldRevenueStmt = $db->prepare("SELECT COALESCE(SUM(
                                          CASE
                                              WHEN payment_status='Paid' THEN service_amount
                                              WHEN payment_status='Partial' THEN payment_amount
                                              ELSE 0
                                          END
                                      ),0)
                                      FROM field_services
                                      WHERE service_date BETWEEN ? AND ?");
    $fieldRevenueStmt->execute([$revFrom, $revTo]);
    $fieldRevenue = $fieldRevenueStmt->fetchColumn();
} else {
    $boardRevenueStmt = $db->prepare("SELECT COALESCE(SUM(
                                          CASE
                                              WHEN payment_status='Paid' THEN (CASE WHEN final_amount>0 THEN final_amount ELSE approx_amount END)
                                              WHEN payment_status='Partial' THEN payment_amount
                                              ELSE 0
                                          END
                                      ),0)
                                      FROM board_services
                                      WHERE status IN ('Completed','Delivered')
                                        AND MONTH(created_at) = ?
                                        AND YEAR(created_at) = ?");
    $boardRevenueStmt->execute([$revMonth, $revYear]);
    $boardRevenue = $boardRevenueStmt->fetchColumn();

    $fieldRevenueStmt = $db->prepare("SELECT COALESCE(SUM(
                                          CASE
                                              WHEN payment_status='Paid' THEN service_amount
                                              WHEN payment_status='Partial' THEN payment_amount
                                              ELSE 0
                                          END
                                      ),0)
                                      FROM field_services
                                      WHERE MONTH(service_date) = ?
                                      AND YEAR(service_date) = ?");
    $fieldRevenueStmt->execute([$revMonth, $revYear]);
    $fieldRevenue = $fieldRevenueStmt->fetchColumn();
}

$totalRevenue = $boardRevenue + $fieldRevenue;

$recentBoards = $db->query("SELECT * FROM board_services ORDER BY created_at DESC LIMIT 7")->fetchAll();
$recentFields = $db->query("SELECT fs.*, u.full_name AS emp_name FROM field_services fs LEFT JOIN users u ON fs.assigned_employee=u.id ORDER BY fs.created_at DESC LIMIT 7")->fetchAll();
$recentCalls  = $db->query("SELECT cr.*, u.full_name AS technician_name FROM call_registers cr LEFT JOIN users u ON cr.assigned_technician_id=u.id ORDER BY cr.created_at DESC LIMIT 7")->fetchAll();

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
        <div class="stat-number"><?= $callTotal ?></div>
        <div class="stat-label">Call Register Entries</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-number" style="font-size:18px;"><?= formatAmount($totalRevenueAll) ?></div>
        <div class="stat-label">Total Revenue (All Time)</div>
    </div>
</div>

<div class="dash-grid">

<div class="card">
    <div class="card-header">
        <span class="card-title">Recent Call Register</span>
        <a href="call_register_list.php" class="btn btn-sm btn-light">View All</a>
    </div>
    <div class="table-wrap">
        <?php if ($recentCalls): ?>
        <table>
            <thead>
                <tr><th>#</th><th>Customer</th><th>Technician</th><th>Product</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($recentCalls as $call): ?>
                <tr>
                    <td><?= (int)$call['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($call['customer_name']) ?></strong><br>
                        <small style="color:#7f8c8d;"><?= htmlspecialchars($call['phone']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($call['technician_name'] ?: 'Unassigned') ?></td>
                    <td><?= htmlspecialchars($call['product_name'] ?: '-') ?></td>
                    <td><?= statusBadge($call['status'] ?: 'Assigned') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="no-data">No call register entries yet.</p>
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
                    <td><?= htmlspecialchars(mb_substr((string)$b['problem'], 0, 30)) ?><?= mb_strlen((string)$b['problem']) > 30 ? '...' : '' ?></td>
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

</div>

<?php include '../includes/footer.php'; ?>
