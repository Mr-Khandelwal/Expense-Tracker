<?php
include("session.php");
$update = false;
$del = false;
$expenseamount = "";
$expensedate = date("Y-m-d");
$expenditurefor="";
$BillType ="";
$PaymentBy ="";
$PaymentType="Cash";
$remarks="";
$expensecategory = "";
$project_id = "";

$projects_query = "SELECT project_id, project_name FROM projects";
$projects_result = mysqli_query($con, $projects_query);


if (isset($_POST['add'])) {
    $expenseamount = $_POST['expenseamount'];
    $expensedate = $_POST['expensedate'];
    $expensecategory = $_POST['expensecategory'];
    $project_id = $_POST['project_id'];

    $expenditurefor=$_POST['expenditurefor'];
    $BillType =$_POST['BillType'];
    $PaymentBy =$_POST['PaymentBy'];
    $PaymentType =$_POST['PaymentType'];
    $remarks==$_POST['remarks'];

    // Fetch the investment limit and current total expenses for the selected project
    $query = "SELECT investment_limit FROM projects WHERE project_id = $project_id";
    $project = mysqli_fetch_assoc(mysqli_query($con, $query));

    $query = "SELECT SUM(expense) AS total_expense FROM expenses WHERE project_id = $project_id";
    $total_expense = mysqli_fetch_assoc(mysqli_query($con, $query))['total_expense'];

    // Check if the new expense exceeds the investment limit
    if (($total_expense + $expenseamount) > $project['investment_limit']) {
        echo "Error: Adding this expense would exceed the project's investment limit.";
    } else {
        // Insert the new expense into the database
        $expenses = "INSERT INTO expenses (user_id, expense, expensedate, expensecategory, project_id,expenditurefor,BillType,PaymentBy,PaymentType,remarks) VALUES ('$userid', '$expenseamount', '$expensedate', '$expensecategory', '$project_id','$expenditurefor','$BillType','$PaymentBy','$PaymentType','$remarks')";
        $result = mysqli_query($con, $expenses) or die("Something Went Wrong!");
        header('location: add_expense.php');
    }
}

// Existing update and delete logic...

if (isset($_POST['update'])) {
    $id = $_GET['edit'];
    $expenseamount = $_POST['expenseamount'];
    $expensedate = $_POST['expensedate'];
    $expensecategory = $_POST['expensecategory'];
    $project_id = $_POST['project_id']; 

    $expenditurefor=$_POST['expenditurefor'];
    $BillType =$_POST['BillType'];
    $PaymentBy =$_POST['PaymentBy'];
    $PaymentType =$_POST['PaymentType'];
    $remarks==$_POST['remarks'];

    $sql = "UPDATE expenses SET expense='$expenseamount', expensedate='$expensedate', expensecategory='$expensecategory', project_id='$project_id',expenditurefor ='$expenditurefor',BillType='$BillType',PaymentBy='$PaymentBy',PaymentType='$PaymentType',remarks='$remarks' WHERE user_id='$userid' AND expense_id='$id'";
    if (mysqli_query($con, $sql)) {
        echo "Records were updated successfully.";
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
    header('location: manage_expense.php');
}

if (isset($_POST['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM expenses WHERE user_id='$userid' AND expense_id='$id'";
    if (mysqli_query($con, $sql)) {
        echo "Records were deleted successfully.";
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
    header('location: manage_expense.php');
}

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update = true;
    $record = mysqli_query($con, "SELECT * FROM expenses WHERE user_id='$userid' AND expense_id=$id");
    if (mysqli_num_rows($record) == 1) {
        $n = mysqli_fetch_array($record);
        $expenseamount = $n['expense'];
        $expensedate = $n['expensedate'];
        $expensecategory = $n['expensecategory'];
        $project_id = $n['project_id'];
    
        $expenditurefor=$n['expenditurefor'];
        $BillType =$n['BillType'];
        $PaymentBy =$n['PaymentBy'];
        $PaymentType =$n['PaymentType'];
        $remarks==$n['remarks'];
        
    } else {
        echo ("WARNING: AUTHORIZATION ERROR: Trying to Access Unauthorized data");
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $del = true;
    $record = mysqli_query($con, "SELECT * FROM expenses WHERE user_id='$userid' AND expense_id=$id");

    if (mysqli_num_rows($record) == 1) {
        $n = mysqli_fetch_array($record);
        $expenseamount = $n['expense'];
        $expensedate = $n['expensedate'];
        $expensecategory = $n['expensecategory'];
        $project_id = $n['project_id'];
        $expenditurefor=$n['expenditurefor'];
        $BillType =$n['BillType'];
        $PaymentBy =$n['PaymentBy'];
        $PaymentType =$n['PaymentType'];
        $remarks==$n['remarks'];
    } else {
        echo ("WARNING: AUTHORIZATION ERROR: Trying to Access Unauthorized data");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Expense Manager - Dashboard</title>

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
                <a href="add_expense.php" class="list-group-item list-group-item-action sidebar-active"><span data-feather="plus-square"></span> Add Expenses</a>
                <a href="view.php" class="list-group-item list-group-item-action sidebar"><span data-feather="eye"></span> View Expenses</a>
                <a href="manage_expense.php" class="list-group-item list-group-item-action"><span data-feather="dollar-sign"></span> Manage Expenses</a>
                <a href="add_project.php" class="list-group-item list-group-item-action sidebar"><span data-feather="folder-plus"></span> Add Project</a>
            </div>
            <div class="sidebar-heading">Settings </div>
            <div class="list-group list-group-flush">
                <a href="profile.php" class="list-group-item list-group-item-action "><span data-feather="user"></span> Profile</a>
                <a href="logout.php" class="list-group-item list-group-item-action "><span data-feather="power"></span> Logout</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">

            <nav class="navbar navbar-expand-lg navbar-light  border-bottom">


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
                <a class="dropdown-item" href="profile.php">Your Profile</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container">
                <h3 class="mt-4 text-center">Add Your Daily Expenses</h3>
                <hr>
                <div class="row ">

                    <div class="col-md-3"></div>

                    <div class="col-md" style="margin:0 auto;">
                        <form action="" method="POST">

                        <!-- project fetchingg -->
                        <div class="form-group row">
                                <label for="project_id" class="col-sm-6 col-form-label"><b>Select Project</b></label>
                                <div class="col-sm-12">
                                    <select class="form-control" name="project_id" id="project_id" required>
                                        <option value="">Select Project</option>
                                        <?php while ($row = mysqli_fetch_array($projects_result)) : ?>
                                            <option value="<?php echo $row['project_id']; ?>" <?php echo ($project_id == $row['project_id']) ? 'selected' : ''; ?>>
                                                <?php echo $row['project_name']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="expenseamount" class="col-sm-6 col-form-label"><b>Enter Amount(Rs)</b></label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control col-sm-12" value="<?php echo $expenseamount; ?>" id="expenseamount" name="expenseamount" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="expensedate" class="col-sm-6 col-form-label"><b>Date</b></label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control col-sm-12" value="<?php echo $expensedate; ?>" name="expensedate" id="expensedate" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="expensecategory" class="col-sm-6 col-form-label"><b>Expense Category</b></label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="expensecategory" id="expensecategory" required onchange="toggleOtherCategory(this.value)">
                                        <option value="">Select Category</option>
                                        <option value="Transport" <?php echo ($expensecategory == 'Transport') ? 'selected' : ''; ?>>Transport</option>
                                        <option value="Food" <?php echo ($expensecategory == 'Food') ? 'selected' : ''; ?>>Food</option>
                                        <option value="Accomodation" <?php echo ($expensecategory == 'Accomodation') ? 'selected' : ''; ?>>Accomodationl</option>
                                        <option value="Misc" <?php echo ($expensecategory == 'Misc') ? 'selected' : ''; ?>>Miscellaneous</option>
                                    </select>
                                    <!-- <input type="text" class="form-control mt-2" name="expensecategory_other" id="expensecategory_other" placeholder="Enter Category" style="display: none;" value="<?php echo ($expensecategory != 'Entertainment' && $expensecategory != 'Food' && $expensecategory != 'Fuel' && $expensecategory != 'Travel' && $expensecategory != '') ? $expensecategory : ''; ?>"> -->
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="expenditurefor" class="col-sm-6 col-form-label"><b>Expenditure For</b></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control col-sm-12" value="<?php echo $expenditurefor; ?>" name="expenditurefor" id="expenditurefor" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="BillType" class="col-sm-6 col-form-label"><b>Bill / Unbilled</b></label>
                                <div class="col-md-6">
                                <select class="form-control" name="BillType" id="BillType" required onchange="toggleOtherCategory(this.value)">
                                        <option value="UnBilled" <?php echo ($BillType == 'UnBilled') ? 'selected' : ''; ?>>UnBilled</option>
                                        <option value="Billed" <?php echo ($BillType == 'Billed') ? 'selected' : ''; ?>>Billed</option>
                                </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="PaymentBy" class="col-sm-6 col-form-label"><b>Payment By</b></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control col-sm-12" value="<?php echo $PaymentBy; ?>" name="PaymentBy" id="PaymentBy" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="PaymentType" class="col-sm-6 col-form-label"><b>Payment Type</b></label>
                                <div class="col-md-6">
                                <select class="form-control" name="PaymentType" id="PaymentType" required onchange="toggleOtherCategory(this.value)">
                                        <option value="Cash" <?php echo ($BillType == 'UnBilled') ? 'selected' : ''; ?>>Cash</option>
                                        <option value="Online (UPI/ NetBanking)" <?php echo ($BillType == 'Billed') ? 'selected' : ''; ?>>Online (UPI/ NetBanking)</option>
                                        <option value="Card" <?php echo ($BillType == 'Billed') ? 'selected' : ''; ?>>Card</option>
                                </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="remarks" class="col-sm-6 col-form-label"><b>Remarks</b></label>
                                <div class="col-md-6">
                                    <input type="remarks" class="form-control col-sm-12" value="<?php echo $remarks; ?>" name="remarks" id="remarks" >
                                </div>
                            </div>
                            
                            <!-- <script>
                                function toggleOtherCategory(value) {
                                    if (value === "Other") {
                                        document.getElementById('expensecategory_other').style.display = 'block';
                                    } else {
                                        document.getElementById('expensecategory_other').style.display = 'none';
                                    }
                                }
                                window.onload = function () {
                                    toggleOtherCategory(document.getElementById('expensecategory').value);
                                };
                            </script> -->

                            <div class="form-group">
                                <?php if ($update == true) : ?>
                                    <button class="btn btn-success btn-block" type="submit" name="update">Update</button>
                                <?php elseif ($del == true) : ?>
                                    <button class="btn btn-danger btn-block" type="submit" name="delete">Delete</button>
                                <?php else : ?>
                                    <button class="btn btn-warning btn-block" type="submit" name="add">Add Expense</button>
                                <?php endif ?>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-3"></div>
                    
                </div>
            </div>
        </div>
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
        feather.replace();
    </script>
    <script>

    </script>
</body>
</html>