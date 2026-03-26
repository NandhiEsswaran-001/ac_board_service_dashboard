<?php
require_once '../includes/config.php';
$pageTitle = 'Sales Report';

if (!isOwner()) {
    header('Location: dashboard.php');
    exit;
}

function normalizeDate($value, $fallback) {
    $dt = DateTime::createFromFormat('Y-m-d', $value);
    return ($dt && $dt->format('Y-m-d') === $value) ? $value : $fallback;
}

$today      = date('Y-m-d');
$monthStart = date('Y-m-01');

$filterFrom = normalizeDate($_GET['from'] ?? $monthStart, $monthStart);
$filterTo   = normalizeDate($_GET['to'] ?? $today, $today);
$filterType = in_array($_GET['type'] ?? 'all', ['all','board','field'], true) ? ($_GET['type'] ?? 'all') : 'all';

$boardStatusAllowed   = ['', 'Pending', 'In Process', 'Completed', 'Delivered'];
$fieldStatusAllowed   = ['', 'Scheduled', 'In Progress', 'Completed'];
$paymentStatusAllowed = ['', 'Pending', 'Paid', 'Partial'];

$filterBoardStatus   = in_array($_GET['board_status'] ?? '', $boardStatusAllowed, true) ? ($_GET['board_status'] ?? '') : '';
$filterFieldStatus   = in_array($_GET['field_status'] ?? '', $fieldStatusAllowed, true) ? ($_GET['field_status'] ?? '') : '';
$filterPaymentStatus = in_array($_GET['payment_status'] ?? '', $paymentStatusAllowed, true) ? ($_GET['payment_status'] ?? '') : '';

$db = getDB();
$rows = [];
$boardAmount = 0;
$fieldAmount = 0;

if ($filterType !== 'field') {
    $sql = "SELECT id, customer_name, phone, created_at, status, payment_status, payment_amount, approx_amount, final_amount
            FROM board_services
            WHERE DATE(created_at) BETWEEN ? AND ?";
    $params = [$filterFrom, $filterTo];
    if ($filterBoardStatus !== '') {
        $sql .= " AND status = ?";
        $params[] = $filterBoardStatus;
    }
    if ($filterPaymentStatus !== '') {
        $sql .= " AND payment_status = ?";
        $params[] = $filterPaymentStatus;
    }
    $sql .= " ORDER BY created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $boards = $stmt->fetchAll();

    foreach ($boards as $b) {
        $fullAmount = ($b['final_amount'] > 0) ? $b['final_amount'] : $b['approx_amount'];
        if ($b['payment_status'] === 'Paid') {
            $amount = $fullAmount;
        } elseif ($b['payment_status'] === 'Partial') {
            $amount = (float)($b['payment_amount'] ?? 0);
        } else {
            $amount = 0;
        }
        $boardAmount += $amount;
        $rows[] = [
            'type' => 'Board Service',
            'date' => date('Y-m-d', strtotime($b['created_at'])),
            'customer_name' => $b['customer_name'],
            'phone' => $b['phone'],
            'status' => $b['status'],
            'payment_status' => $b['payment_status'] ?? '',
            'amount' => $amount,
            'link' => 'board_view.php?id=' . $b['id'],
        ];
    }
}

if ($filterType !== 'board') {
    $sql = "SELECT id, customer_name, phone, service_date, status, payment_status, payment_amount, service_amount
            FROM field_services
            WHERE service_date BETWEEN ? AND ?";
    $params = [$filterFrom, $filterTo];
    if ($filterFieldStatus !== '') {
        $sql .= " AND status = ?";
        $params[] = $filterFieldStatus;
    }
    if ($filterPaymentStatus !== '') {
        $sql .= " AND payment_status = ?";
        $params[] = $filterPaymentStatus;
    }
    $sql .= " ORDER BY service_date DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $fields = $stmt->fetchAll();

    foreach ($fields as $f) {
        if ($f['payment_status'] === 'Paid') {
            $amount = (float)$f['service_amount'];
        } elseif ($f['payment_status'] === 'Partial') {
            $amount = (float)($f['payment_amount'] ?? 0);
        } else {
            $amount = 0;
        }
        $fieldAmount += $amount;
        $rows[] = [
            'type' => 'Field Service',
            'date' => $f['service_date'],
            'customer_name' => $f['customer_name'],
            'phone' => $f['phone'],
            'status' => $f['status'],
            'payment_status' => $f['payment_status'],
            'amount' => $amount,
            'link' => 'field_view.php?id=' . $f['id'],
        ];
    }
}

usort($rows, function ($a, $b) {
    return strcmp($b['date'], $a['date']);
});

$totalAmount = $boardAmount + $fieldAmount;
$totalCount  = count($rows);
$boardCount  = count(array_filter($rows, fn($r) => $r['type'] === 'Board Service'));
$fieldCount  = count(array_filter($rows, fn($r) => $r['type'] === 'Field Service'));

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Sales Report</span>
        <span style="font-size:12.5px;color:#7f8c8d;"><?= date('d M Y', strtotime($filterFrom)) ?> – <?= date('d M Y', strtotime($filterTo)) ?></span>
    </div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="form-grid-3">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" name="from" value="<?= htmlspecialchars($filterFrom) ?>">
                </div>
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" name="to" value="<?= htmlspecialchars($filterTo) ?>">
                </div>
                <div class="form-group">
                    <label>Service Type</label>
                    <select name="type">
                        <option value="all" <?= $filterType==='all' ? 'selected' : '' ?>>All Sales</option>
                        <option value="board" <?= $filterType==='board' ? 'selected' : '' ?>>Board Service</option>
                        <option value="field" <?= $filterType==='field' ? 'selected' : '' ?>>Field Service</option>
                    </select>
                </div>
            </div>
            <div class="form-grid-3" style="margin-top:12px;">
                <div class="form-group">
                    <label>Board Status</label>
                    <select name="board_status">
                        <?php foreach ($boardStatusAllowed as $st): ?>
                            <option value="<?= $st ?>" <?= $filterBoardStatus===$st ? 'selected' : '' ?>><?= $st==='' ? 'All Board Status' : $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Field Status</label>
                    <select name="field_status">
                        <?php foreach ($fieldStatusAllowed as $st): ?>
                            <option value="<?= $st ?>" <?= $filterFieldStatus===$st ? 'selected' : '' ?>><?= $st==='' ? 'All Field Status' : $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="payment_status">
                        <?php foreach ($paymentStatusAllowed as $st): ?>
                            <option value="<?= $st ?>" <?= $filterPaymentStatus===$st ? 'selected' : '' ?>><?= $st==='' ? 'All Payment Status' : $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="sales_report.php" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="report-summary-grid">
    <div class="stat-card blue">
        <div class="stat-number"><?= $totalCount ?></div>
        <div class="stat-label">Total Sales</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-number"><?= $boardCount ?></div>
        <div class="stat-label">Board Services</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-number"><?= $fieldCount ?></div>
        <div class="stat-label">Field Services</div>
    </div>
    <div class="stat-card green">
        <div class="stat-number" style="font-size:17px;"><?= formatAmount($totalAmount) ?></div>
        <div class="stat-label">Total Sales Amount</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Sales Detail View</span>
    </div>
    <div class="table-wrap">
        <?php if (empty($rows)): ?>
            <p class="no-data">No sales found for the selected filters.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= date('d M Y', strtotime($r['date'])) ?></td>
                    <td>
                        <span class="badge" style="background:<?= $r['type']==='Board Service' ? '#3498db' : '#16a085' ?>;">
                            <?= $r['type']==='Board Service' ? 'Board' : 'Field' ?>
                        </span>
                    </td>
                    <td><strong><?= htmlspecialchars($r['customer_name']) ?></strong></td>
                    <td><?= htmlspecialchars($r['phone']) ?></td>
                    <td><?= statusBadge($r['status']) ?></td>
                    <td><?= $r['payment_status'] ? statusBadge($r['payment_status']) : '-' ?></td>
                    <td><?= ($r['amount'] > 0) ? formatAmount($r['amount']) : '-' ?></td>
                    <td><a href="<?= htmlspecialchars($r['link']) ?>" class="btn btn-sm btn-primary">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
