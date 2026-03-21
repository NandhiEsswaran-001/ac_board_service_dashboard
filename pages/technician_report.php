<?php
require_once '../includes/config.php';
$pageTitle = 'Technician Report';

if (!isOwner()) {
    header('Location: dashboard.php');
    exit;
}

$db = getDB();

$filterMonth = intval($_GET['month'] ?? date('n'));
$filterYear  = intval($_GET['year']  ?? date('Y'));
$filterTech  = intval($_GET['tech']  ?? 0);

// Whitelist the role filter — never interpolate raw input
$allowedRoles = ['', 'staff', 'technician'];
$filterRole   = in_array($_GET['role'] ?? '', $allowedRoles) ? ($_GET['role'] ?? '') : '';

if ($filterMonth < 1 || $filterMonth > 12) $filterMonth = (int)date('n');
if ($filterYear < 2020 || $filterYear > 2040) $filterYear = (int)date('Y');

$monthName   = date('F', mktime(0, 0, 0, $filterMonth, 1, $filterYear));
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $filterMonth, $filterYear);

$allStaff = $db->query("SELECT id, full_name, role FROM users
                         WHERE role IN ('staff','technician')
                         ORDER BY role, full_name")->fetchAll();

// Build role filter using a parameter, not string interpolation
if ($filterRole === 'staff') {
    $roleSql   = "u.role = ?";
    $roleParam = 'staff';
} elseif ($filterRole === 'technician') {
    $roleSql   = "u.role = ?";
    $roleParam = 'technician';
} else {
    $roleSql   = "u.role IN ('staff','technician')";
    $roleParam = null;
}

$techSql   = $filterTech ? "AND u.id = ?" : "";

$sql = "
    SELECT
        u.id,
        u.full_name,
        u.role,
        COUNT(DISTINCT fs.service_date)                                              AS days_worked,
        COUNT(fs.id)                                                                 AS total_services,
        SUM(CASE WHEN fs.status='Completed' THEN 1 ELSE 0 END)                      AS completed_services,
        SUM(CASE WHEN fs.payment_status='Paid' THEN fs.service_amount ELSE 0 END)   AS revenue_collected,
        SUM(fs.service_amount)                                                       AS total_billed
    FROM users u
    LEFT JOIN field_services fs
        ON  fs.assigned_employee = u.id
        AND MONTH(fs.service_date) = ?
        AND YEAR(fs.service_date)  = ?
    WHERE $roleSql $techSql
    GROUP BY u.id, u.full_name, u.role
    ORDER BY days_worked DESC, total_services DESC
";

$params = [$filterMonth, $filterYear];
if ($roleParam !== null) $params[] = $roleParam;
if ($filterTech)         $params[] = $filterTech;

$stmt = $db->prepare($sql);
$stmt->execute($params);
$techStats = $stmt->fetchAll();

function getTechDailyBreakdown($db, $techId, $month, $year) {
    $stmt = $db->prepare("
        SELECT
            fs.service_date,
            COUNT(fs.id)                                                         AS services_count,
            SUM(fs.service_amount)                                               AS daily_amount,
            GROUP_CONCAT(fs.customer_name ORDER BY fs.id SEPARATOR ', ')        AS customers
        FROM field_services fs
        WHERE fs.assigned_employee = ?
          AND MONTH(fs.service_date) = ?
          AND YEAR(fs.service_date)  = ?
        GROUP BY fs.service_date
        ORDER BY fs.service_date ASC
    ");
    $stmt->execute([$techId, $month, $year]);
    return $stmt->fetchAll();
}

$totalServices = array_sum(array_column($techStats, 'total_services'));
$totalRevenue  = array_sum(array_column($techStats, 'revenue_collected'));
$activeCount   = count(array_filter($techStats, fn($t) => $t['days_worked'] > 0));

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <span class="card-title">📊 Technician Work Report</span>
        <span style="font-size:12.5px;color:#7f8c8d;"><?= $monthName . ' ' . $filterYear ?></span>
    </div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="form-grid-3">
                <div class="form-group">
                    <label>Month</label>
                    <select name="month">
                        <?php
                        $months = ['January','February','March','April','May','June',
                                   'July','August','September','October','November','December'];
                        foreach ($months as $i => $m):
                        ?>
                        <option value="<?= $i+1 ?>" <?= ($filterMonth == $i+1) ? 'selected' : '' ?>><?= $m ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Year</label>
                    <select name="year">
                        <?php for ($y = date('Y')+1; $y >= 2022; $y--): ?>
                        <option value="<?= $y ?>" <?= ($filterYear == $y) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Filter by Role</label>
                    <select name="role">
                        <option value="" <?= $filterRole==='' ? 'selected' : '' ?>>All Staff &amp; Technicians</option>
                        <option value="technician" <?= $filterRole==='technician' ? 'selected' : '' ?>>Technicians Only</option>
                        <option value="staff" <?= $filterRole==='staff' ? 'selected' : '' ?>>Staff Only</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-top:12px;">
                <label>Specific Person</label>
                <select name="tech">
                    <option value="0">All People</option>
                    <?php foreach ($allStaff as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= ($filterTech == $u['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['full_name']) ?> (<?= ucfirst($u['role']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">🔍 Apply Filter</button>
                <a href="technician_report.php" class="btn btn-light">↺ Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="report-summary-grid">
    <div class="stat-card blue">
        <div class="stat-number"><?= $daysInMonth ?></div>
        <div class="stat-label">Days in <?= $monthName ?></div>
    </div>
    <div class="stat-card orange">
        <div class="stat-number"><?= $activeCount ?></div>
        <div class="stat-label">Active This Month</div>
    </div>
    <div class="stat-card green">
        <div class="stat-number"><?= $totalServices ?></div>
        <div class="stat-label">Total Services</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-number" style="font-size:17px;"><?= formatAmount($totalRevenue) ?></div>
        <div class="stat-label">Revenue Collected</div>
    </div>
</div>

<?php if (empty($techStats)): ?>
<div class="card"><p class="no-data">No staff or technicians found for the selected filters.</p></div>
<?php else: ?>

<div class="tech-report-grid">
<?php foreach ($techStats as $t):
    $initials    = strtoupper(mb_substr($t['full_name'], 0, 1));
    $pct         = $daysInMonth > 0 ? round(($t['days_worked'] / $daysInMonth) * 100) : 0;
    $dailyRows   = getTechDailyBreakdown($db, $t['id'], $filterMonth, $filterYear);
    $avatarColor = ($t['role'] === 'technician') ? '#16a085' : '#2980b9';
?>
<div class="tech-card">
    <div class="tech-card-header">
        <div class="tech-avatar" style="background:<?= $avatarColor ?>;"><?= $initials ?></div>
        <div>
            <div class="tech-name"><?= htmlspecialchars($t['full_name']) ?></div>
            <div class="tech-role-badge"><?= ucfirst($t['role']) ?></div>
        </div>
    </div>

    <div class="tech-stats-row">
        <div class="tech-stat-item">
            <div class="tech-stat-num blue"><?= $t['days_worked'] ?></div>
            <div class="tech-stat-lbl">Days Worked</div>
        </div>
        <div class="tech-stat-item">
            <div class="tech-stat-num"><?= $t['total_services'] ?></div>
            <div class="tech-stat-lbl">Total Jobs</div>
        </div>
        <div class="tech-stat-item">
            <div class="tech-stat-num green"><?= $t['completed_services'] ?></div>
            <div class="tech-stat-lbl">Completed</div>
        </div>
    </div>

    <div class="tech-days-row">
        <span class="tech-days-label">Attendance this month</span>
        <span class="tech-days-val"><?= $t['days_worked'] ?> / <?= $daysInMonth ?> (<?= $pct ?>%)</span>
    </div>
    <div class="tech-days-bar-wrap">
        <div class="tech-days-bar-bg">
            <div class="tech-days-bar-fill" style="width:<?= $pct ?>%;"></div>
        </div>
    </div>

    <?php if ($t['total_services'] > 0): ?>
    <div class="tech-revenue-row">
        <span style="font-size:12px;color:#546e7a;font-weight:600;">Revenue Collected</span>
        <span style="font-size:13px;font-weight:800;color:#27ae60;"><?= formatAmount($t['revenue_collected']) ?></span>
    </div>
    <div class="tech-revenue-row" style="border-top:none;padding-top:4px;padding-bottom:10px;">
        <span style="font-size:12px;color:#546e7a;font-weight:600;">Total Billed</span>
        <span style="font-size:13px;font-weight:700;color:#1a2535;"><?= formatAmount($t['total_billed']) ?></span>
    </div>
    <?php endif; ?>

    <?php if (!empty($dailyRows)): ?>
    <div class="tech-service-list">
        <div class="tech-service-list-title">Daily Breakdown</div>
        <?php foreach ($dailyRows as $day): ?>
        <div class="tech-service-list-item">
            <div>
                <span style="color:#7f8c8d;font-size:11.5px;white-space:nowrap;"><?= date('d M', strtotime($day['service_date'])) ?></span>
                <span style="margin-left:6px;font-size:12px;color:#2c3e50;"><?= htmlspecialchars(mb_substr($day['customers'], 0, 28)) ?><?= mb_strlen($day['customers']) > 28 ? '…' : '' ?></span>
            </div>
            <div style="text-align:right;flex-shrink:0;margin-left:6px;">
                <span class="badge" style="background:#2980b9;font-size:10px;"><?= $day['services_count'] ?> job<?= $day['services_count'] > 1 ? 's' : '' ?></span>
                <?php if ($day['daily_amount'] > 0): ?>
                <div style="font-size:11px;color:#27ae60;font-weight:700;margin-top:2px;"><?= formatAmount($day['daily_amount']) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="padding:10px 14px 12px;color:#95a5a6;font-style:italic;font-size:12.5px;">No services assigned this month.</p>
    <?php endif; ?>
</div>
<?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Summary Table — <?= $monthName . ' ' . $filterYear ?></span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th><th>Role</th><th>Days Worked</th><th>Total Jobs</th>
                    <th>Completed</th><th>Attendance</th><th>Billed</th><th>Collected</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($techStats as $t):
                    $pct = $daysInMonth > 0 ? round(($t['days_worked'] / $daysInMonth) * 100) : 0;
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($t['full_name']) ?></strong></td>
                    <td><?= statusBadge(ucfirst($t['role'])) ?></td>
                    <td>
                        <strong style="color:<?= $t['days_worked'] > 0 ? '#2980b9' : '#bdc3c7' ?>;"><?= $t['days_worked'] ?></strong>
                        <span style="color:#95a5a6;font-size:12px;"> / <?= $daysInMonth ?></span>
                    </td>
                    <td><?= $t['total_services'] ?: '-' ?></td>
                    <td><?= $t['completed_services'] ?: '-' ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;min-width:80px;">
                            <div style="flex:1;height:5px;background:#e8edf2;border-radius:3px;">
                                <div style="height:100%;width:<?= $pct ?>%;background:#2980b9;border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;font-weight:700;color:#1a2535;"><?= $pct ?>%</span>
                        </div>
                    </td>
                    <td><?= $t['total_services'] > 0 ? formatAmount($t['total_billed']) : '-' ?></td>
                    <td style="color:#27ae60;font-weight:700;"><?= $t['total_services'] > 0 ? formatAmount($t['revenue_collected']) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>
