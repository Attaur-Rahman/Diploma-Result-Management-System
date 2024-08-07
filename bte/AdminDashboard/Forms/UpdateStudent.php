<?php
// Including the database connection file
include "../Backend/connectdb.php";

// SQL query to fetch data from the branches table with branch_id
$sql_for_branch = "SELECT branch_id, branch_name FROM branches";
$sql_for_institute = "SELECT institute_id, institute_name FROM institutes";

// Execute the query to fetch branches and institutes
$insert_branch = $conn->query($sql_for_branch);
$insert_institute = $conn->query($sql_for_institute);

// Temporarily disable foreign key checks
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");

// Initialize variables to store student details
$register_number = "";
$student_name = "";
$father_name = "";
$branch_id = "";
$institute_id = "";

// Check if the register number is provided in the URL
if (isset($_GET['updatestudentwithregno'])) {
    // Get the register number from the URL parameter
    $register_number = $_GET['updatestudentwithregno'];

    // SQL query to fetch student details based on the register number
    $sql = "SELECT * FROM `students` WHERE register_number='$register_number'";
    $result = mysqli_query($conn, $sql);

    // Fetch student details if the query was successful
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $register_number = $row['register_number'];
        $student_name = $row['student_name'];
        $father_name = $row['father_name'];
        $branch_id = $row['branch_id'];
        $institute_id = $row['institute_id'];
    } else {
        // Handle the case where no student record was found
        echo "Error: Student record not found.";
        exit(); // Terminate the script
    }
}

// Check if the form is submitted for updating student details
if (isset($_POST['student_update'])) {
    // Retrieve data from the form submission
    $regNo = $_POST['regNo'];
    $sName = $_POST['sName'];
    $fName = $_POST['fName'];
    $branch = $_POST['branch'];
    $institute = $_POST['institute'];

    // Update query with the WHERE clause to update student details
    $sql = "UPDATE `students` SET 
            register_number=UPPER('$regNo'), 
            student_name=UPPER('$sName'), 
            father_name=UPPER('$fName'), 
            branch_id='$branch', 
            institute_id='$institute' 
            WHERE register_number='$register_number'";

    // Execute the update query
    $result = mysqli_query($conn, $sql);

    // Update register_number in results table
    $sql_update_results = "UPDATE `results` SET register_number=UPPER('$regNo') WHERE register_number='$register_number'";
    $result_update_results = mysqli_query($conn, $sql_update_results);

    // Check if the update was successful
    if ($result) {
        // Redirect to Students page with updated parameter
        header("Location: ../Students?updated=1");
    } else {
        echo "Error updating student record: " . $conn->error;
    }

    // Re-enable foreign key checks
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Student</title>
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

        .btn-submit {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-submit:hover {
            background-color: #218838;
            border-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <form method="post">
                <h2 class="text-center mb-4">Update Student</h2>
                <div class="mb-3">
                    <label for="regNo" class="form-label">Register Number</label>
                    <input type="text" class="form-control" id="regNo" placeholder="Enter Student's Register Number" autocomplete="off" name="regNo" value="<?php echo htmlspecialchars($register_number); ?>">
                </div>
                <div class="mb-3">
                    <label for="sName" class="form-label">Student Name</label>
                    <input type="text" class="form-control" id="sName" placeholder="Enter Student Name" autocomplete="off" name="sName" value="<?php echo htmlspecialchars($student_name); ?>">
                </div>
                <div class="mb-3">
                    <label for="fName" class="form-label">Father Name</label>
                    <input type="text" class="form-control" id="fName" placeholder="Enter Father Name" autocomplete="off" name="fName" value="<?php echo htmlspecialchars($father_name); ?>">
                </div>
                <div class="mb-3">
                    <?php
                    // Display dropdown for branch selection
                    if ($insert_branch->num_rows > 0) {
                        echo '<label for="branch" class="form-label">Branch Name</label>';
                        echo '<select name="branch" id="branch" class="form-select" aria-label="Default select example" required >';
                        echo '<option value="">Select</option>';

                        // Iterate through each branch and display it as an option
                        while ($row = $insert_branch->fetch_assoc()) {
                            $selected = ($row['branch_id'] == $branch_id) ? 'selected' : ''; // Assuming $selected_branch_id holds the value you want to select
                            echo '<option value="' . htmlspecialchars($row['branch_id']) . '" ' . $selected . '>' . htmlspecialchars($row['branch_id']) . ' - ' . htmlspecialchars($row['branch_name']) . '</option>';
                        }

                        echo '</select>';
                    } else {
                        echo '<option value="" disabled>No branches found in the database.</option>';
                    }
                    ?>

                </div>
                <div class="mb-3">
                    <label for="institute" class="form-label">Institute ID & Name</label>
                    <input type="text" id="search_input_institute" class="form-control" placeholder="Search by ID or name">
                    <select name="institute" id="institute" class="form-select mt-2" aria-label="Select Institute" required>
                        <option value="">Select Institute</option>
                        <?php
                        // Populate dropdown with institute options
                        if ($insert_institute->num_rows > 0) {
                            while ($row = $insert_institute->fetch_assoc()) {
                                $selected = ($row['institute_id'] == $institute_id) ? 'selected' : ''; // Check if the option should be selected
                                echo '<option value="' . htmlspecialchars($row['institute_id']) . '" ' . $selected . '>' . htmlspecialchars($row['institute_id'] . ' - ' . $row['institute_name']) . '</option>';
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
                    <button type="submit" class="btn btn-primary mt-2" name="student_update">Update Student</button>
                </div>

            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>