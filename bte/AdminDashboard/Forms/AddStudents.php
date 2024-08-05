<?php
// Include the database connection file
include "../Backend/connectdb.php";

// SQL queries to fetch data from the branches and institutes tables
$sql_for_branch = "SELECT branch_id, branch_name FROM branches";
$sql_for_institute = "SELECT institute_id, institute_name FROM institutes";

// Execute the queries to fetch branches and institutes
$result_branch = mysqli_query($conn, $sql_for_branch);
$result_institute = mysqli_query($conn, $sql_for_institute);

// Check if the form is submitted for adding a new student
if (isset($_POST['student_submit'])) {
    // Retrieve data from the form submission
    $regNo = $_POST['regNo'];
    $sName = $_POST['sName'];
    $fName = $_POST['fName'];
    $branch = $_POST['branch'];
    $institute = $_POST['institute'];

    // SQL query to insert a new student record into the database
    $sql = "INSERT INTO students (register_number, student_name, father_name, branch_id, institute_id) 
    VALUES (UPPER('$regNo'), UPPER('$sName'), UPPER('$fName'), '$branch', '$institute')";

    // Execute the insert query
    $result = mysqli_query($conn, $sql);

    // Check if the insert was successful
    if ($result) {
        // Redirect to Students page with added parameter
        header('Location: ../Students.php?added=1');
        exit();
    } else {
        // Display an error message if the insert fails
        echo "Error inserting student record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add New Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <form method="post">
                <h2 class="text-center mb-4">Add New Student</h2>
                <div class="mb-3">
                    <label for="regNo" class="form-label">Register Number</label>
                    <input type="text" class="form-control" id="regNo" placeholder="Enter Student's Register Number" autocomplete="off" name="regNo" required>
                </div>
                <div class="mb-3">
                    <label for="sName" class="form-label">Student Name</label>
                    <input type="text" class="form-control" id="sName" placeholder="Enter Student Name" autocomplete="off" name="sName" required>
                </div>
                <div class="mb-3">
                    <label for="fName" class="form-label">Father Name</label>
                    <input type="text" class="form-control" id="fName" placeholder="Enter Father Name" autocomplete="off" name="fName" required>
                </div>
                <div class="mb-3">
                    <label for="branch" class="form-label">Branch</label>
                    <select name="branch" id="branch" class="form-select" aria-label="Select Branch" required>
                        <option value="">Select</option>
                        <?php
                        // Populate dropdown with branch options
                        if (mysqli_num_rows($result_branch) > 0) {
                            while ($row = mysqli_fetch_assoc($result_branch)) {
                                echo '<option value="' . htmlspecialchars($row['branch_id']) . '">' . htmlspecialchars($row['branch_id']) . ' - ' . htmlspecialchars($row['branch_name']) . '</option>';
                            }
                        } else {
                            echo '<option value="" disabled>No branches found</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="institute" class="form-label">Institute</label>
                    <input type="text" id="search_input_institute" class="form-control" placeholder="Search by institute ID or name">
                    <select name="institute" id="institute" class="form-select mt-2" aria-label="Select Institute" required>
                        <option value="">Select</option>
                        <?php
                        // Populate dropdown with institute options
                        if (mysqli_num_rows($result_institute) > 0) {
                            while ($row = mysqli_fetch_assoc($result_institute)) {
                                echo '<option value="' . htmlspecialchars($row['institute_id']) . '">' . htmlspecialchars($row['institute_id']) . ' - ' . htmlspecialchars($row['institute_name']) . '</option>';
                            }
                        } else {
                            echo '<option value="" disabled>No institutes found</option>';
                        }
                        ?>
                    </select>
                </div>

                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    // Get the input field and dropdown for institute
                    var input_institute = $('#search_input_institute');
                    var select_institute = $('#institute');

                    // Add event listener to the input field for institute
                    input_institute.on('keyup', function() {
                        var searchValueInstitute = $(this).val().toLowerCase();
                        select_institute.find('option').each(function() {
                            var optionTextInstitute = $(this).text().toLowerCase();
                            var optionValueInstitute = $(this).val().toLowerCase();
                            if (optionTextInstitute.indexOf(searchValueInstitute) > -1 || optionValueInstitute.indexOf(searchValueInstitute) > -1) {
                                select_institute.val($(this).val());
                                return false; // stop searching after first match
                            }
                        });
                    });
                </script>

                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-success mt-2" name="student_submit">Add Student</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>