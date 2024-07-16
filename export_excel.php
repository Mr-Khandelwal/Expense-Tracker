<?php
include("session.php");

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

$html = '<table>
    <tr><td colspan="8" style="text-align:center; font-weight:bold; font-size:16px;">' . $project_name . '</td></tr>
    <tr>
        <td>Expense Date</td>
        <td>Expenditure for</td>
        <td>Actual Amount</td>
        <td>Billed/Unbilled</td>
        <td>Expense Category</td>
        <td>Payment By</td>
        <td>Payment Type</td>
        <td>Remarks</td>
    </tr>';

mysqli_data_seek($exp_fetched, 0);
while ($row = mysqli_fetch_array($exp_fetched)) {
    $html .= '<tr>
        <td>' . $row['expensedate'] . '</td>
        <td>' . $row['expenditurefor'] . '</td>
        <td>' . $row['expense'] . '</td>
        <td>' . $row['BillType'] . '</td>
        <td>' . $row['expensecategory'] . '</td>
        <td>' . $row['PaymentBy'] . '</td>
        <td>' . $row['PaymentType'] . '</td>
        <td>' . $row['remarks'] . '</td>
    </tr>';
}

$html .= '</table>';

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename=' . $project_name . '.xls');

echo $html;
?>
