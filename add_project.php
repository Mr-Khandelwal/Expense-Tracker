<?php
include("session.php");

if (isset($_POST['submit'])) {
    $project_name = mysqli_real_escape_string($con, $_POST['project_name']);
    $investment = mysqli_real_escape_string($con, $_POST['investment']);

    // Check if fields are not empty
    if (!empty($project_name) && !empty($investment)) {
        $query = "INSERT INTO projects ( project_name, investment_limit) VALUES ('$project_name', '$investment')";
        $result = mysqli_query($con, $query);

        if ($result) {
            $message = "Project added successfully!";
        } else {
            $message = "Failed to add project. Please try again.";
        }
    } else {
        $message = "Please fill in all fields.";
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
    <title>Expense Manager - Add Project</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="js/feather.min.js"></script>
</head>

<body>
    <div class="d-flex" id="wrapper">
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
                <a href="manage_expense.php" class="list-group-item list-group-item-action"><span data-feather="dollar-sign"></span> Manage Expenses</a>
                <a href="view.php" class="list-group-item list-group-item-action sidebar-active"><span data-feather="eye"></span> View Expenses</a>
                <a href="add_project.php" class="list-group-item list-group-item-action sidebar-active"><span data-feather="folder-plus"></span> Add Project</a>
            </div>
            <div class="sidebar-heading">Settings</div>
            <div class="list-group list-group-flush">
                <a href="profile.php" class="list-group-item list-group-item-action"><span data-feather="user"></span> Profile</a>
                <a href="logout.php" class="list-group-item list-group-item-action"><span data-feather="power"></span> Logout</a>
            </div>
        </div>
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
                <h3 class="mt-4 text-center">Add Project</h3>
                <hr>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <?php if (isset($message)) { ?>
                            <div class="alert alert-info"><?php echo $message; ?></div>
                        <?php } ?>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="project_name">Project Name</label>
                                <input type="text" name="project_name" id="project_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="investment">Investment Amount</label>
                                <input type="number" name="investment" id="investment" class="form-control" required>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Add Project</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery.slim.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script>
        $(document).ready(function() {
            feather.replace();
            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
            });
        });
    </script>
</body>
</html>
