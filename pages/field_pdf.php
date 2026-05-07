<?php
require_once '../includes/config.php';
require_once '../includes/fpdf/fpdf.php';

$id = intval($_GET['id'] ?? 0);
$type = $_GET['type'] ?? 'scheduled';

if (!$id) {
    die('Invalid request');
}

$db = getDB();
$stmt = $db->prepare("SELECT fs.*, u.full_name AS emp_name FROM field_services fs LEFT JOIN users u ON fs.assigned_employee=u.id WHERE fs.id=?");
$stmt->execute([$id]);
$s = $stmt->fetch();

if (!$s) {
    die('Field service not found');
}

$wa_items = '-';
if (!empty($s['service_call_items'])) {
    $decoded = json_decode($s['service_call_items'], true);
    if (is_array($decoded)) {
        $wa_items = $decoded ? implode(', ', $decoded) : '-';
    } else {
        $wa_items = trim((string)$s['service_call_items']) ?: '-';
    }
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Helvetica', '', 10);

$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(0, 6, 'HOT & COLD ENGINEERING', 0, 1, 'C');
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(0, 5, 'AIR CONDITIONER SERVICE CENTER', 0, 1, 'C');
$pdf->Cell(0, 5, '(ALL TYPES OF INVERTER / NON INVERTER)', 0, 1, 'C');
$pdf->Ln(2);
$pdf->Cell(0, 5, '97, 1st floor, 7th street, tatabad, near 6 Corner,', 0, 1, 'C');
$pdf->Cell(0, 5, 'Coimbatore - 641012', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 6, '-------------------- RECEIPT --------------------', 0, 1, 'C');
$pdf->Ln(2);

$status = ($type === 'completed') ? 'COMPLETED' : 'SCHEDULED';
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(50, 5, 'JOB NO', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $s['id'], 0, 1);

$pdf->Cell(50, 5, 'DATE', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, date('d M Y', strtotime($s['service_date'])), 0, 1);

$pdf->Cell(50, 5, 'STATUS', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $status, 0, 1);

$pdf->Ln(3);
$pdf->Cell(0, 5, '-------------------------------------------------', 0, 1, 'C');

$pdf->Cell(50, 5, 'NAME', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $s['customer_name'], 0, 1);

$pdf->Cell(50, 5, 'PHONE', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $s['phone'], 0, 1);

$pdf->Cell(50, 5, 'ADDRESS', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $s['address'] ?: '-', 0, 1);

$pdf->Ln(3);
$pdf->Cell(0, 5, '-------------------------------------------------', 0, 1, 'C');

$pdf->Cell(50, 5, 'PRODUCT NAME', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $s['ac_type'] ?: '-', 0, 1);

$pdf->Cell(50, 5, 'PRODUCT COMPANY', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $s['product_company'] ?: '-', 0, 1);

$pdf->Cell(50, 5, 'DATE OF PURCHASE', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $s['purchase_date'] ? date('d M Y', strtotime($s['purchase_date'])) : '-', 0, 1);

$pdf->Cell(50, 5, 'UNIT LOCATION', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $s['unit_location'] ?: '-', 0, 1);

$pdf->Cell(50, 5, 'SERVICE REPORT NO', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $s['service_report_no'] ?: '-', 0, 1);

$pdf->Cell(50, 5, 'TECHNICIAN', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $s['emp_name'] ?: 'Unassigned', 0, 1);

$pdf->Cell(50, 5, 'COMPLAINT', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $s['problem'] ?: '-', 0, 1);

if ($type === 'completed') {
    $pdf->Ln(3);
    $pdf->Cell(0, 5, '-------------------------------------------------', 0, 1, 'C');

    $pdf->Cell(50, 5, 'JOB DONE', 0, 0);
    $pdf->Cell(5, 5, ':', 0, 0);
    $pdf->Cell(0, 5, $s['work_done'] ?: '-', 0, 1);

    $pdf->Cell(50, 5, 'REPLACED SPARES', 0, 0);
    $pdf->Cell(5, 5, ':', 0, 0);
    $pdf->Cell(0, 5, $s['parts_used'] ?: '-', 0, 1);

    $pdf->Cell(50, 5, 'SERVICE CHARGE', 0, 0);
    $pdf->Cell(5, 5, ':', 0, 0);
    $pdf->Cell(0, 5, $s['service_charge'] ?: '-', 0, 1);

    $pdf->Cell(50, 5, 'SERVICE CALL ITEMS', 0, 0);
    $pdf->Cell(5, 5, ':', 0, 0);
    $pdf->Cell(0, 5, $wa_items, 0, 1);

    $pdf->Cell(50, 5, 'WARRANTY', 0, 0);
    $pdf->Cell(5, 5, ':', 0, 0);
    $pdf->Cell(0, 5, $s['warranty_text'] ?: '-', 0, 1);

    $pdf->Ln(3);
    $pdf->Cell(0, 5, '-------------------------------------------------', 0, 1, 'C');

    $pdf->Cell(50, 5, 'AMOUNT IN RS.', 0, 0);
    $pdf->Cell(5, 5, ':', 0, 0);
    $pdf->Cell(0, 5, number_format($s['service_amount'], 2), 0, 1);

    $pdf->Cell(50, 5, 'PAYMENT STATUS', 0, 0);
    $pdf->Cell(5, 5, ':', 0, 0);
    $pdf->Cell(0, 5, $s['payment_status'] ?: '-', 0, 1);
}

$pdf->Ln(3);
$pdf->Cell(0, 5, '-------------------------------------------------', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 5, 'FOR HOT & COLD ENG', 0, 1, 'C');
$pdf->Cell(0, 5, 'AUTHORISED PERSON', 0, 1, 'C');

$filename = 'field_service_' . $id . '_' . $type . '.pdf';
$pdf->Output('D', $filename);