<?php
// Include the database connection file
include "../AdminDashboard/Backend/connectdb.php";

// Initialize variables to store search parameters
$search_register_number = '';
$search_semester_id = '';

// Check if the search form is submitted
if (isset($_POST['search'])) {
    // Get the search parameters from the form inputs
    $search_register_number = $_POST['register_number'];
    $search_semester_id = $_POST['semester_id'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Results</title>
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
    // Display success messages based on URL parameters
    if (isset($_GET['added']) && $_GET['added'] == 1) {
        echo '<div id="successMessage" class="alert alert-success alert-dismissible fade show success-message" role="alert">
        <strong>Success!</strong> Added New Result!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    } elseif (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
        echo '<div id="successMessage" class="alert alert-danger alert-dismissible fade show success-message" role="alert">
        <strong>Success!</strong> Deleted Existing Result!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } elseif (isset($_GET['updated']) && $_GET['updated'] == 1) {
        echo '<div id="successMessage" class="alert alert-info alert-dismissible fade show success-message" role="alert">
        <strong>Success!</strong> Updated Existing Result!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
    // Remove the success, deleted, updated parameter from the URL to avoid showing the message again on page reload
    echo "<script>history.replaceState({}, '', window.location.href.split('?')[0]);</script>";
    ?>
    <div class="container my-5">
        <a href="../AdminDashboard/Dashboard.php" class="btn btn-outline-dark">Back</a>
        <a href="./Forms/AddResults.php" class="btn btn-success">Add Results</a>
        <!-- Search form -->
        <form method="POST" class="mt-3" id="searchForm">
            <div class="row g-3">
                <div class="col-md">
                    <input type="text" class="form-control" placeholder="Search by Register Number" name="register_number" id="registerNumberInput" value="<?php echo $search_register_number; ?>">
                </div>
                <div class="col-auto align-self-center">
                    <p class="m-0">AND</p>
                </div>
                <div class="col-md">
                    <select class="form-select" name="semester_id" id="semesterIdInput">
                        <option value="">Select Semester</option>
                        <?php
                        // Fetch semester IDs from the database
                        $semester_query = "SELECT DISTINCT semester_id FROM results ORDER BY semester_id ASC";
                        $semester_result = mysqli_query($conn, $semester_query);
                        if ($semester_result) {
                            while ($row = mysqli_fetch_assoc($semester_result)) {
                                $semester_id = $row['semester_id'];
                                echo '<option value="' . $semester_id . '"';
                                if ($search_semester_id == $semester_id) {
                                    echo ' selected';
                                }
                                echo '>' . $semester_id . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button class="btn btn-warning text-white w-100" type="submit" name="search">Search</button>
                </div>
            </div>
        </form>

        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="text-center align-middle table-dark">
                        <th scope="col">Sl.No.</th>
                        <th scope="col">Student Reg No & Name</th>
                        <th scope="col">Semester</th>
                        <th scope="col">Subject Code</th>
                        <th scope="col">Exam Marks</th>
                        <th scope="col">IA Marks</th>
                        <th scope="col">Operations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Build the SQL query with JOIN to fetch data
                    $sql = "SELECT r.register_number, s.student_name, r.semester_id, r.subject_code, r.exam_marks, r.ia_marks, sub.subject_name 
                    FROM results r
                    INNER JOIN students s ON r.register_number = s.register_number
                    INNER JOIN subjects sub ON r.subject_code = sub.subject_code";

                    // Add WHERE clause if search parameters are provided
                    $conditions = array();
                    if (!empty($search_register_number)) {
                        $conditions[] = "r.register_number LIKE '%$search_register_number%'";
                    }
                    if (!empty($search_semester_id)) {
                        $conditions[] = "r.semester_id = '$search_semester_id'";
                    }
                    if (!empty($conditions)) {
                        $sql .= " WHERE " . implode(" AND ", $conditions);
                    }

                    $sql .= " ORDER BY r.semester_id ASC";

                    // Execute the query
                    $result = mysqli_query($conn, $sql);

                    // Check if the query was successful
                    if ($result) {
                        // Initialize a counter variable
                        $serial_number = 1;

                        // Loop through the results
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Fetch data from the row
                            $register_number = $row['register_number'];
                            $student_name = $row['student_name'];
                            $semester_id = $row['semester_id'];
                            $subject_code = $row['subject_code'];
                            $subject_name = $row['subject_name'];
                            $exam_marks = $row['exam_marks'];
                            $ia_marks = $row['ia_marks'];

                            // Display the row data
                            echo '<tr class="align-middle text-center">';
                            echo '<td>' . $serial_number . '</td>';
                            // Concatenate registration number and name
                            echo '<td>' . $register_number . ' - ' . $student_name . '</td>';
                            echo '<td>' . $semester_id . '</td>';
                            echo '<td>' . $subject_code . ' - ' . $subject_name . '</td>';
                            echo '<td>' . $exam_marks . '</td>';
                            echo '<td>' . $ia_marks . '</td>';
                            echo '<td>';
                            echo '<div class="d-inline-flex">';
                            // Link to update result
                            echo '<a href="../AdminDashboard/Forms/UpdateResult.php?updateresultwithregno=' . $register_number . '&updateresultwithscode=' . $subject_code . '" class="btn btn-primary mx-2">Update</a>';
                            // Link to delete result
                            echo '<a href="../AdminDashboard/Backend/delete.php?deleteresultwithregno=' . $register_number . '&deleteresultwithscode=' . $subject_code . '" class="btn btn-danger">Delete</a>';
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

    <!-- Bootstrap JavaScript library -->
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
    </script>
</body>

</html>