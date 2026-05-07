<?php
require_once '../includes/config.php';
require_once '../includes/fpdf/fpdf.php';

$id = intval($_GET['id'] ?? 0);
$type = $_GET['type'] ?? 'received';

if (!$id) {
    die('Invalid request');
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM board_services WHERE id=?");
$stmt->execute([$id]);
$b = $stmt->fetch();

if (!$b) {
    die('Board entry not found');
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Helvetica', '', 10);

$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(0, 6, 'HOT & COLD ENGINEERING', 0, 1, 'C');
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(0, 5, 'AIR CONDITIONER PCB SERVICE CENTER', 0, 1, 'C');
$pdf->Cell(0, 5, '(ALL TYPES OF INVERTER / NON INVERTER)', 0, 1, 'C');
$pdf->Ln(2);
$pdf->Cell(0, 5, '97, 1st floor, 7th street, tatabad, near 6 Corner,', 0, 1, 'C');
$pdf->Cell(0, 5, 'Coimbatore - 641012', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 6, '-------------------- RECEIPT --------------------', 0, 1, 'C');
$pdf->Ln(2);

$status = ($type === 'completed') ? 'COMPLETED' : 'PENDING';
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(40, 5, 'JOB NO', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $b['id'], 0, 1);

$pdf->Cell(40, 5, 'DATE', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, date('d M Y', strtotime($b['created_at'])), 0, 1);

$pdf->Cell(40, 5, 'STATUS', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $status, 0, 1);

$pdf->Ln(3);
$pdf->Cell(0, 5, '-------------------------------------------------', 0, 1, 'C');

$pdf->Cell(40, 5, 'NAME', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $b['customer_name'], 0, 1);

$pdf->Cell(40, 5, 'PHONE', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $b['phone'], 0, 1);

$pdf->Cell(40, 5, 'ADDRESS', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $b['address'] ?: '-', 0, 1);

$pdf->Ln(3);
$pdf->Cell(0, 5, '-------------------------------------------------', 0, 1, 'C');

$brandModel = trim(($b['ac_brand'] ? $b['ac_brand'] : '-') . ($b['ac_model'] ? ' ' . $b['ac_model'] : ''));
$pdf->Cell(40, 5, 'BRAND / ERROR', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $brandModel, 0, 1);

$pdf->Cell(40, 5, 'CUSTOMER REMARKS', 0, 0);
$pdf->Cell(5, 5, ':', 0, 0);
$pdf->Cell(0, 5, $b['customer_remarks'] ?: '-', 0, 1);

if ($type === 'completed' && $b['parts_replaced']) {
    $pdf->Cell(40, 5, 'REPLACED PARTS', 0, 0);
    $pdf->Cell(5, 5, ':', 0, 0);
    $pdf->Cell(0, 5, $b['parts_replaced'], 0, 1);
}

$pdf->Ln(3);
$pdf->Cell(0, 5, '-------------------------------------------------', 0, 1, 'C');

if ($type === 'completed') {
    $amount = $b['final_amount'] > 0 ? $b['final_amount'] : $b['approx_amount'];
    $pdf->Cell(40, 5, 'AMOUNT IN RS.', 0, 0);
    $pdf->Cell(5, 5, ':', 0, 0);
    $pdf->Cell(0, 5, number_format($amount, 2), 0, 1);

    $pdf->Cell(40, 5, 'RECEIVED GOODS', 0, 0);
    $pdf->Cell(5, 5, ':', 0, 0);
    $pdf->Cell(0, 5, date('d M Y'), 0, 1);
} else {
    $pdf->Cell(40, 5, 'AMOUNT IN RS.', 0, 0);
    $pdf->Cell(5, 5, ':', 0, 0);
    $pdf->Cell(0, 5, number_format($b['approx_amount'], 2), 0, 1);
}

$pdf->Ln(3);
$pdf->Cell(0, 5, '-------------------------------------------------', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 5, 'FOR HOT & COLD ENG', 0, 1, 'C');
$pdf->Cell(0, 5, 'AUTHORISED PERSON', 0, 1, 'C');

$filename = 'board_receipt_' . $id . '_' . $type . '.pdf';
$pdf->Output('D', $filename);