<?php
// Including the database connection file
include "../AdminDashboard/Backend/connectdb.php";

// Initialize the variable to store the subject code for filtering
$search_subject_code = '';

// Check if the search form is submitted
if (isset($_POST['search'])) {
    // Get the subject code from the form input
    $search_subject_code = $_POST['subject_code'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Subjects</title>
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
    // Display success, error, or update messages based on URL parameters
    if (isset($_GET['added']) && $_GET['added'] == 1) {
        echo '<div id="successMessage" class="alert alert-success alert-dismissible fade show success-message" role="alert">
        <strong>Success!</strong> Added New Subject!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    } elseif (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
        echo '<div id="successMessage" class="alert alert-danger alert-dismissible fade show success-message" role="alert">
        <strong>Success!</strong> Deleted Existing Subject!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    } elseif (isset($_GET['updated']) && $_GET['updated'] == 1) {
        echo '<div id="successMessage" class="alert alert-info alert-dismissible fade show success-message" role="alert">
        <strong>Success!</strong> Updated Existing Subject!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    }

    // Remove the success, deleted, updated parameter from the URL
    echo "<script>history.replaceState({}, '', window.location.href.split('?')[0]);</script>";
    ?>
    <div class="container my-5">
        <!-- Back button to navigate to the dashboard -->
        <a href="../AdminDashboard/Dashboard" class="btn btn-outline-dark">Back</a>
        <!-- Button to add new subjects -->
        <a href="./Forms/AddSubjects" class="btn btn-success">Add Subjects</a>
        <!-- Note about the consequences of deleting a subject -->
        <h5 class="my-2 text-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>NOTE: Deleting the subject will also delete associated result records</span>
        </h5>

        <!-- Search form -->
        <form method="POST" class="mt-3" id="searchForm">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search by Subject Code" name="subject_code" id="subjectCodeInput" value="<?php echo $search_subject_code; ?>">
                <button class="btn btn-warning text-white" type="submit" name="search">Search</button>
            </div>
        </form>

        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="text-center align-middle table-dark">
                        <th scope="col">Sl.No.</th>
                        <th scope="col">Subject Code</th>
                        <th scope="col">Subject Name</th>
                        <th scope="col">Operations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // SQL query to retrieve subjects data from the database
                    $sql = "SELECT subject_code, subject_name FROM subjects";

                    // Add WHERE clause if search subject_code is provided
                    if (!empty($search_subject_code)) {
                        $sql .= " WHERE subject_code LIKE '%$search_subject_code%'";
                    }

                    $sql .= " ORDER BY subject_code ASC";

                    // Execute the query
                    $result = mysqli_query($conn, $sql);

                    if ($result) {
                        // Initialize a counter variable for serial number
                        $serial_number = 1;

                        // Loop through the results
                        while ($row = mysqli_fetch_assoc($result)) {
                            $subject_code = $row['subject_code'];
                            $subject_name = $row['subject_name'];

                            // Display table rows with subject details
                            echo '<tr class="align-middle text-center">';
                            echo '<td>' . $serial_number . '</td>';
                            echo '<td>' . $subject_code . '</td>';
                            echo '<td>' . $subject_name . '</td>';
                            echo '<td>';
                            echo '<div class="d-inline-flex">';
                            // Button to update subject details
                            echo '<a href="../AdminDashboard/Forms/UpdateSubject?updatesubjectwithcode=' . $subject_code . '" class="btn btn-primary mx-2">Update</a>';
                            // Button to delete subject
                            echo '<a href="../AdminDashboard/Backend/delete?deletesubjectwithcode=' . $subject_code . '" class="btn btn-danger">Delete</a>';
                            echo '</div>';
                            echo '</td>';
                            echo '</tr>';

                            // Increment the serial number for the next row
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

        // Submit the form when the input field is cleared
        document.addEventListener('DOMContentLoaded', function() {
            var subjectCodeInput = document.getElementById('subjectCodeInput');
            subjectCodeInput.addEventListener('input', function(event) {
                if (event.target.value.trim() === '') {
                    document.getElementById('searchForm').submit();
                }
            });
        });
    </script>
</body>

</html>