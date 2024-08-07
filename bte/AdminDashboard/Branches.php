<?php
// Including the database connection file
include "../AdminDashboard/Backend/connectdb.php";

// Initialize the variable to store the branch id for filtering
$search_branch_id = '';

// Check if the search form is submitted
if (isset($_POST['search'])) {
    // Get the branch id from the form input
    $search_branch_id = $_POST['branch_id'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Branches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .success-message {
            display: none;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
        }
    </style>
</head>

<body>
    <?php
    // Check if a success message should be displayed
    if (isset($_GET['added']) && $_GET['added'] == 1) {
        echo '<div id="successMessage" class="alert alert-success alert-dismissible fade show success-message" role="alert">
        <strong>Success!</strong> Added New Branch!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    } elseif (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
        echo '<div id="successMessage" class="alert alert-danger alert-dismissible fade show success-message" role="alert">
        <strong>Success!</strong> Deleted Existing Branch!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    } elseif (isset($_GET['updated']) && $_GET['updated'] == 1) {
        echo '<div id="successMessage" class="alert alert-info alert-dismissible fade show success-message" role="alert">
        <strong>Success!</strong> Updated Existing Branch!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    }

    // Remove the success, deleted, updated parameter from the URL
    echo "<script>history.replaceState({}, '', window.location.href.split('?')[0]);</script>";
    ?>
    <div class="container my-5">
        <a href="../AdminDashboard/Dashboard" class="btn btn-outline-dark">Back</a>
        <a href="./Forms/AddBranches" class="btn btn-success">Add Branches</a>
        <h5 class="my-2 text-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>NOTE: Deleting the branch will also delete associated student records</span>
        </h5>

        <!-- Search form -->
        <form method="POST" class="mt-3" id="searchForm">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search by Branch ID" name="branch_id" id="branchIdInput" value="<?php echo $search_branch_id; ?>">
                <button class="btn btn-warning text-white" type="submit" name="search">Search</button>
            </div>
        </form>

        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="text-center align-middle table-dark">
                        <th scope="col">Sl.No.</th>
                        <th scope="col">Branch Id</th>
                        <th scope="col">Branch Name</th>
                        <th scope="col">Operations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // SQL query to fetch branches ordered by branch_id
                    $sql = "SELECT branch_id, branch_name FROM branches";

                    // Add WHERE clause if search branch_id is provided
                    if (!empty($search_branch_id)) {
                        $sql .= " WHERE branch_id LIKE '%$search_branch_id%'";
                    }

                    $sql .= " ORDER BY branch_id ASC";

                    $result = mysqli_query($conn, $sql);

                    if ($result && mysqli_num_rows($result) > 0) {
                        // Initialize a counter variable
                        $serial_number = 1;

                        // Loop through the results
                        while ($row = mysqli_fetch_assoc($result)) {
                            $branch_id = $row['branch_id'];
                            $branch_name = $row['branch_name'];

                            // Display the row data including branch ID and branch name
                            echo '<tr class="align-middle text-center">';
                            echo '<td>' . $serial_number . '</td>';
                            echo '<td>' . $branch_id . '</td>';
                            echo '<td>' . $branch_name . '</td>';
                            echo '<td>';
                            echo '<div class="d-inline-flex">';
                            echo '<a href="../AdminDashboard/Forms/UpdateBranch?updatebranchwithid=' . $branch_id . '" class="btn btn-primary mx-2">Update</a>';
                            echo '<a href="../AdminDashboard/Backend/delete?deletebranchwithid=' . $branch_id . '" class="btn btn-danger">Delete</a>';
                            echo '</div>';
                            echo '</td>';
                            echo '</tr>';

                            // Increment the serial number
                            $serial_number++;
                        }
                    } else {
                        // Display a message if no branches are found
                        echo '<tr><td colspan="4" class="text-center">No branches found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        // Script to display success message for a few seconds
        document.addEventListener('DOMContentLoaded', function() {
            var successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.display = 'block';
                // Hide the success message after 3 seconds
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 3000); // Adjust the time in milliseconds (3 seconds in this case)
            }
        });

        // Automatically submit the form when the input field is cleared
        document.addEventListener('DOMContentLoaded', function() {
            var branchIdInput = document.getElementById('branchIdInput');
            branchIdInput.addEventListener('input', function(event) {
                if (event.target.value.trim() === '') {
                    document.getElementById('searchForm').submit();
                }
            });
        });
    </script>
</body>

</html>