<?php
// Including the database connection file
include "../AdminDashboard/Backend/connectdb.php";

// Initialize the variable to store the register number for filtering
$search_register_number = '';

// Check if the search form is submitted
if (isset($_POST['search'])) {
    // Get the register number from the form input
    $search_register_number = $_POST['register_number'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        /* CSS for success message */
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
    // Displaying success message based on URL parameters
    if (isset($_GET['added']) && $_GET['added'] == 1) {
        // Display success message for adding a new student
        echo '<div id="successMessage" class="alert alert-success alert-dismissible fade show success-message" role="alert">
        <strong>Success!</strong> Added New Student!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    } elseif (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
        // Display success message for deleting an existing student
        echo '<div id="successMessage" class="alert alert-danger alert-dismissible fade show success-message" role="alert">
        <strong>Success!</strong> Deleted Existing Student!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    } elseif (isset($_GET['updated']) && $_GET['updated'] == 1) {
        // Display success message for updating an existing student
        echo '<div id="successMessage" class="alert alert-info alert-dismissible fade show success-message" role="alert">
        <strong>Success!</strong> Updated Existing Student!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    }
    // Remove the success, updated, deleted parameter from the URL
    echo "<script>history.replaceState({}, '', window.location.href.split('?')[0]);</script>";
    ?>
    <div class="container my-5">
        <a href="../AdminDashboard/Dashboard.php" class="btn btn-outline-dark">Back</a>
        <a href="./Forms/AddStudents.php" class="btn btn-success">Add Student</a>
        <h5 class="my-2 text-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>NOTE: Deleting the student will also delete associated result records</span>
        </h5>

        <!-- Search form -->
        <form method="POST" class="mt-3" id="searchForm">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search by Register Number" name="register_number" id="registerNumberInput" value="<?php echo $search_register_number; ?>">
                <button class="btn btn-warning text-white" type="submit" name="search">Search</button>
            </div>
        </form>

        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="text-center align-middle table-dark">
                        <th scope="col">Sl.No.</th>
                        <th scope="col">Register Number</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Father Name</th>
                        <th scope="col">Branch</th>
                        <th scope="col">Institute</th>
                        <th scope="col">Operations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query to fetch student data along with branch and institute details
                    $sql = "SELECT
                    students.register_number,
                    students.student_name,
                    students.father_name,
                    branches.branch_id,
                    branches.branch_name, 
                    institutes.institute_id,
                    institutes.institute_name
                FROM
                    students
                JOIN
                    branches ON students.branch_id = branches.branch_id
                JOIN
                    institutes ON students.institute_id = institutes.institute_id";

                    // Add WHERE clause if search register number is provided
                    if (!empty($search_register_number)) {
                        $sql .= " WHERE students.register_number LIKE '%$search_register_number%'";
                    }

                    $sql .= " ORDER BY students.register_number ASC";
                    $result = mysqli_query($conn, $sql);

                    if ($result) {
                        // Initialize a counter variable
                        $serial_number = 1;

                        // Loop through the results
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Extracting data from the result row
                            $register_number = $row['register_number'];
                            $student_name = $row['student_name'];
                            $father_name = $row['father_name'];
                            $branch_id = $row['branch_id'];
                            $branch_name = $row['branch_name'];
                            $institute_id = $row['institute_id'];
                            $institute_name = $row['institute_name'];

                            // Displaying the row data including branch ID and branch name
                            echo '<tr class="align-middle text-center">';
                            echo '<td>' . $serial_number . '</td>';
                            echo '<td>' . $register_number . '</td>';
                            echo '<td>' . $student_name . '</td>';
                            echo '<td>' . $father_name . '</td>';
                            echo '<td>' . $branch_id . ' - ' . $branch_name . '</td>';
                            echo '<td>' . $institute_id . ' - ' . $institute_name . '</td>';
                            echo '<td>';
                            echo '<div class="d-inline-flex">';
                            echo '<a href="../AdminDashboard/Forms/UpdateStudent.php?updatestudentwithregno=' . $register_number . '" class="btn btn-primary mx-2">Update</a>';
                            echo '<a href="../AdminDashboard/Backend/delete.php?deletestudentwithregno=' . $register_number . '" class="btn btn-danger">Delete</a>';
                            echo '</div>';
                            echo '</td>';
                            echo '</tr>';

                            // Increment the serial number
                            $serial_number++;
                        }
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

        // Submit the form when the input field is cleared
        document.addEventListener('DOMContentLoaded', function() {
            var registerNumberInput = document.getElementById('registerNumberInput');
            registerNumberInput.addEventListener('input', function(event) {
                if (event.target.value.trim() === '') {
                    document.getElementById('searchForm').submit();
                }
            });
        });
    </script>
</body>

</html>