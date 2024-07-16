<?php
include("session.php");

$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
$export_format = isset($_POST['export_format']) ? $_POST['export_format'] : 'excel';

if ($project_id && $export_format) {
    if ($export_format === 'pdf') {
        header("Location: export_pdf.php?project_id=$project_id");
    } else {
        header("Location: export_excel.php?project_id=$project_id");
    }
} else {
    echo "No project selected or invalid export format.";
}
?>
