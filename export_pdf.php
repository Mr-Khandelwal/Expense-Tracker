<?php
include("session.php");
require('fpdf/fpdf.php');

$project_id = isset($_GET['project_id']) ? $_GET['project_id'] : '';

// Fetch project name
$project_query = "SELECT project_name FROM projects WHERE project_id = '$project_id'";
$project_result = mysqli_query($con, $project_query);
$project_data = mysqli_fetch_assoc($project_result);
$project_name = $project_data['project_name'] ?? 'Project';

// Fetch expenses
$expenses_query = "SELECT * FROM expenses WHERE user_id = '$userid' AND project_id = '$project_id'";
$exp_fetched = mysqli_query($con, $expenses_query);

if (!$exp_fetched) {
    die('Invalid query: ' . mysqli_error($con));
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, $project_name, 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(30, 10, 'Expense Date');
$pdf->Cell(42, 10, 'Expenditure for');
$pdf->Cell(18, 10, 'Amount');
$pdf->Cell(20, 10, 'Billing');
$pdf->Cell(20, 10, 'Exp Ctg');
$pdf->Cell(23, 10, 'Payment By');
$pdf->Cell(23, 10, 'Payment via');
$pdf->Cell(25, 10, 'Remarks');
$pdf->Ln();

$pdf->SetFont('Arial', '', 9);
while ($row = mysqli_fetch_array($exp_fetched)) {
    $pdf->Cell(30, 10, $row['expensedate']);
    $pdf->Cell(42, 10, $row['expenditurefor']);
    $pdf->Cell(18, 10, $row['expense']);
    $pdf->Cell(20, 10, $row['BillType']);
    $pdf->Cell(20, 10, $row['expensecategory']);
    $pdf->Cell(23, 10, $row['PaymentBy']);
    $pdf->Cell(23, 10, $row['PaymentType']);
    $pdf->Cell(20, 10, $row['remarks']);
    $pdf->Ln();
}
$pdf->Output('D', $project_name . '.pdf');
?>
