<?php
include("session.php");

// Fetching projects
$projects_query = "SELECT project_id, project_name, investment_limit FROM projects WHERE user_id = '$userid'";
$projects_result = mysqli_query($con, $projects_query);

$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
$projects_query = "SELECT project_id, project_name FROM projects";
$projects_result = mysqli_query($con, $projects_query);

// Initialize variables
$total_expense = 0;
$remaining_amount = 0;
$project_name = '';
$investment_limit = 0;

if ($project_id) {
    // Fetching total expenses for the selected project
    $total_expenses_query = "SELECT SUM(expense) AS total_expense FROM expenses WHERE user_id = '$userid' AND project_id = '$project_id'";
    $total_expenses_result = mysqli_query($con, $total_expenses_query);
    $total_expense_row = mysqli_fetch_assoc($total_expenses_result);
    $total_expense = $total_expense_row['total_expense'] ?? 0;

    // Fetching investment limit for the selected project
    $investment_limit_query = "SELECT investment_limit FROM projects WHERE project_id = '$project_id'";
    $investment_limit_result = mysqli_query($con, $investment_limit_query);
    $investment_limit_row = mysqli_fetch_assoc($investment_limit_result);
    $investment_limit = $investment_limit_row['investment_limit'] ?? 0;

    $remaining_amount = $investment_limit - $total_expense;

    // Fetching project name
    $project_query = "SELECT project_name FROM projects WHERE project_id = '$project_id'";
    $project_result = mysqli_query($con, $project_query);
    $project_data = mysqli_fetch_assoc($project_result);
    $project_name = $project_data['project_name'] ?? '';
}

// Fetching expenses for the selected project
$expenses_query = "SELECT * FROM expenses WHERE user_id = '$userid' AND project_id = '$project_id'";
$exp_fetched = mysqli_query($con, $expenses_query);

// PDF generation logic
if (isset($_POST['download_pdf'])) {
    require_once('tcpdf/tcpdf.php'); // Include TCPDF library

    class PDF extends TCPDF {
        public function Header() {
            // Header content goes here
        }

        public function Footer() {
            // Footer content goes here
        }
    }

    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Project Expenses - '.$project_name);
    $pdf->SetSubject('Project Expenses');
    $pdf->SetKeywords('Expense, Project, PDF');

    // Set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Project Expenses - '.$project_name, 'Generated on: '.date('Y-m-d H:i:s'));

    // Set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Add a page
    $pdf->AddPage();

    // Content
    $pdf->SetFont('helvetica', '', 10);

    // Table header
    // $header = array('Date', 'Amount', 'Expense Category');
    // $pdf->MultiCell(0, 10, 'Project Name: '.$project_name, 0, 'L');
    // $pdf->Ln();
    // $pdf->SetFont('', 'B');
    // $pdf->Cell(40, 10, 'Date', 1, 0, 'C', 0);
    // $pdf->Cell(40, 10, 'Amount', 1, 0, 'C', 0);
    // $pdf->Cell(60, 10, 'Expense Category', 1, 1, 'C', 0);

    // Print project name
    $pdf->MultiCell(0, 10, 'Project Name: '.$project_name, 0, 'L');
    $pdf->Ln();

    // Table headers
    $pdf->SetFont('', 'B');
    $pdf->Cell(40, 10, 'Date', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Amount', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Expense Category', 1, 1, 'C');
    $pdf->SetFont('', '');

    // Table rows
    // $pdf->SetFont('', '');
    // $count = 1;
    // while ($row = mysqli_fetch_array($exp_fetched)) {
    //     $pdf->Cell(40, 10, $row['expensedate'], 1, 0, 'C', 0);
    //     $pdf->Cell(40, 10, 'Rs '.$row['expense'], 1, 0, 'C', 0);
    //     $pdf->Cell(60, 10, $row['expensecategory'], 1, 1, 'C', 0);
    // }
    while ($row = mysqli_fetch_assoc($exp_fetched)) {
        $pdf->Cell(40, 10, $row['expensedate'], 1, 0, 'C');
        $pdf->Cell(40, 10, 'Rs '.$row['expense'], 1, 0, 'C');
        $pdf->Cell(60, 10, $row['expensecategory'], 1, 1, 'C');
    }

    // Summary section
    $pdf->Ln();
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Summary', 0, 1, 'L');
    $pdf->SetFont('', '');
    $pdf->Cell(0, 10, 'Investment Amount: Rs '.number_format($investment_limit, 2), 0, 1, 'L');
    $pdf->Cell(0, 10, 'Total Expense: Rs '.number_format($total_expense, 2), 0, 1, 'L');
    $pdf->Cell(0, 10, 'Remaining Amount: Rs '.number_format($investment_limit - $total_expense, 2), 0, 1, 'L');

    // Close and output PDF document
    $pdf->Output('project_expenses_'.$project_name.'.pdf', 'D');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Expense Manager - View Expenses</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">

    <!-- Feather JS for Icons -->
    <script src="js/feather.min.js"></script>
</head>

<body>

    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->
        <div class="border-right" id="sidebar-wrapper">
            <div class="user">
                <img class="img img-fluid rounded-circle" src="<?php echo $userprofile ?>" width="120">
                <h5><?php echo $username ?></h5>
                <p><?php echo $useremail ?></p>
            </div>
            <div class="sidebar-heading">Management</div>
            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item list-group-item-action"><span data-feather="home"></span> Dashboard</a>
                <a href="add_expense.php" class="list-group-item list-group-item-action"><span data-feather="plus-square"></span> Add Expenses</a>
                <a href="view.php" class="list-group-item list-group-item-action sidebar-active"><span data-feather="eye"></span> View Expenses</a>
                <a href="manage_expense.php" class="list-group-item list-group-item-action"><span data-feather="dollar-sign"></span> Manage Expenses</a>
                <a href="add_project.php" class="list-group-item list-group-item-action"><span data-feather="folder-plus"></span> Add Project</a>
            </div>
            <div class="sidebar-heading">Settings</div>
            <div class="list-group list-group-flush">
                <a href="profile.php" class="list-group-item list-group-item-action"><span data-feather="user"></span> Profile</a>
                <a href="logout.php" class="list-group-item list-group-item-action"><span data-feather="power"></span> Logout</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light border-bottom">
                <button class="toggler" type="button" id="menu-toggle" aria-expanded="false">
                    <span data-feather="menu"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="img img-fluid rounded-circle" src="<?php echo $userprofile ?>" width="25">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#">Your Profile</a>
                                <a class="dropdown-item" href="#">Edit Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid">
                <h3 class="mt-4 text-center">View Expenses</h3>
                <hr>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <form method="POST" action="view.php">
                            <div class="form-group">
                                <label for="project_id">Select Project</label>
                                <select class="form-control" id="project_id" name="project_id" onchange="this.form.submit()">
                                    <option value="">Select Project</option>
                                    <?php while ($project = mysqli_fetch_array($projects_result)) { ?>
                                        <option value="<?php echo $project['project_id']; ?>" <?php if ($project_id == $project['project_id']) echo 'selected'; ?>><?php echo $project['project_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </form>
                        <?php if ($project_id) { ?>
                            <div class="text-center">
                                <div class="big-font-group">
                                    <div class="big-font">Investment Amount : <?php echo number_format($investment_limit, 2); ?></div>
                                    <div class="big-font">Total Expense : <?php echo number_format($total_expense, 2); ?></div>
                                    <div class="big-font">Remaining Amount : <?php echo number_format($remaining_amount, 2); ?></div>
                                </div>
                                <form action="view.php" method="post" class="mt-4">
                                    <input type="hidden" name="download_pdf" value="1">
                                    <button type="submit" class="btn btn-primary">Download PDF</button>
                                 </form>
                            </div>
                            <table class="table table-hover table-bordered mt-4">
                                <thead>
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Expense Category</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $count = 1;
                                mysqli_data_seek($exp_fetched, 0);
                                while ($row = mysqli_fetch_array($exp_fetched)) { ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $row['expensedate']; ?></td>
                                        <td><?php echo 'Rs ' . $row['expense']; ?></td>
                                        <td><?php echo $row['expensecategory']; ?></td>
                                    </tr>
                                <?php $count++;
                                }
                                ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="text-center">
                                <p>Select a project to view expenses.</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <style>
                .big-font-group {
                    display: flex;
                    justify-content: space-around;
                    margin-bottom: 20px;
                }
                .big-font {
                    font-size: 1.2rem;
                    font-weight: bold;
                }
            </style>

            <!-- /#page-content-wrapper -->
        </div>
        <!-- /#wrapper -->

        <!-- Bootstrap core JavaScript -->
        <script src="js/jquery.slim.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/Chart.min.js"></script>
        <!-- Menu Toggle Script -->
        <script>
            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
            });
        </script>
        <script>
            feather.replace()
        </script>

    </body>

</html>
