<?php
// Include the database connection file
include "../Backend/connectdb.php";

// SQL queries to fetch data for dropdown menus
$sql_for_regno = "SELECT register_number, student_name FROM students";
$sql_for_semester = "SELECT semester_id FROM semesters";
$sql_for_subject = "SELECT subject_code, subject_name FROM subjects";

// Execute SQL queries to fetch data
$insert_regno = $conn->query($sql_for_regno);
$insert_semester = $conn->query($sql_for_semester);
$insert_subject = $conn->query($sql_for_subject);

// Temporarily disable foreign key checks to avoid constraints
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");

// Check if updateresultwithregno and updateresultwithscode parameters are set in the URL
if (isset($_GET['updateresultwithregno']) && isset($_GET['updateresultwithscode'])) {
    // Get register number and subject code from the URL parameters
    $register_number = $_GET['updateresultwithregno'];
    $subject_code = $_GET['updateresultwithscode'];

    // SQL query to fetch result details based on register number and subject code
    $sql = "SELECT * FROM `results` WHERE register_number='$register_number' AND subject_code='$subject_code'";
    $result = mysqli_query($conn, $sql);

    // Fetch the result row
    $row = mysqli_fetch_assoc($result);

    // Assign fetched data to variables
    $register_number = $row['register_number'];
    $semester_id = $row['semester_id'];
    $subject_code = $row['subject_code'];
    $exam_marks = $row['exam_marks'];
    $ia_marks = $row['ia_marks'];
}

// Check if result_update form is submitted
if (isset($_POST['result_update'])) {
    // Get form data
    $regNo = $_POST['regNo'];
    $sId = $_POST['sId'];
    $sCode = $_POST['sCode'];
    $eMarks = $_POST['eMarks'];
    $iaMarks = $_POST['iaMarks'];

    // Update query with WHERE clause to specify the record to be updated
    $sql = "UPDATE `results` SET 
            register_number='$regNo', 
            semester_id='$sId', 
            subject_code='$sCode', 
            exam_marks='$eMarks', 
            ia_marks='$iaMarks' 
            WHERE register_number='$register_number' AND subject_code='$subject_code'";

    // Execute the update query
    $result = mysqli_query($conn, $sql);

    // Check if the update was successful
    if ($result) {
        // Redirect to the manage results page with a success message
        header("Location: ../MResults?updated=1");
        exit(); // Exit script to prevent further execution
    } else {
        // Display error message if the update fails
        echo "Error updating result record: " . $conn->error;
    }

    // Re-enable foreign key checks after update
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Result</title>
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
                <h2 class="text-center mb-4">Update Result</h2>
                <div class="mb-3">
                    <label for="r_no" class="form-label">Student Reg No & Name</label>
                    <select name="regNo" id="r_no" class="form-select" aria-label="Select RegNo" required>
                        <option value="">Select Student</option>
                        <?php
                        // Populate register number dropdown with data fetched from the database
                        if ($insert_regno->num_rows > 0) {
                            while ($row = $insert_regno->fetch_assoc()) {
                                $selected = ($row['register_number'] == $register_number) ? 'selected' : ''; // Check if the option should be selected
                                echo '<option value="' . htmlspecialchars($row['register_number']) . '" ' . $selected . '>' . htmlspecialchars($row['register_number']) . ' - ' . htmlspecialchars($row['student_name']) . '</option>';
                            }
                        } else {
                            echo '<option disabled>No register number found in the database.</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="s_no" class="form-label">Semester</label>
                    <select name="sId" id="s_no" class="form-select" aria-label="Select SNo" required>
                        <option value="">Select Semester</option>
                        <?php
                        // Populate semester dropdown with data fetched from the database
                        if ($insert_semester->num_rows > 0) {
                            while ($row = $insert_semester->fetch_assoc()) {
                                $selected = ($row['semester_id'] == $semester_id) ? 'selected' : ''; // Check if the option should be selected
                                echo '<option value="' . htmlspecialchars($row['semester_id']) . '" ' . $selected . '>' . htmlspecialchars($row['semester_id']) . '</option>';
                            }
                        } else {
                            echo '<option disabled>No semester found in the database.</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="s_code" class="form-label">Subject Code & Name</label>
                    <input type="text" id="search_input" class="form-control" placeholder="Search by code or name">
                    <select name="sCode" id="s_code" class="form-select mt-2" aria-label="Select SCode" required>
                        <option value="">Select Subject</option>
                        <?php
                        // Display options for subject code dropdown
                        if ($insert_subject->num_rows > 0) {
                            while ($row = $insert_subject->fetch_assoc()) {
                                $selected = ($row['subject_code'] == $subject_code) ? 'selected' : ''; // Check if the option should be selected
                                echo '<option value="' . htmlspecialchars($row['subject_code']) . '" ' . $selected . '>' . htmlspecialchars($row['subject_code'] . ' - ' . $row['subject_name']) . '</option>';
                            }
                        } else {
                            echo '<option value="" disabled>No subjects found</option>';
                        }
                        ?>
                    </select>
                </div>

                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    // Get the input field and dropdown
                    var input = $('#search_input');
                    var select = $('#s_code');

                    // Add event listener to the input field
                    input.on('keyup', function() {
                        var searchValue = $(this).val().toLowerCase();
                        select.find('option').each(function() {
                            var optionText = $(this).text().toLowerCase();
                            var optionValue = $(this).val().toLowerCase();
                            if (optionText.indexOf(searchValue) > -1 || optionValue.indexOf(searchValue) > -1) {
                                select.val($(this).val());
                                return false; // stop searching after first match
                            }
                        });
                    });
                </script>
                <div class="mb-3">
                    <label for="e_marks" class="form-label">Exam Marks</label>
                    <input type="text" class="form-control" id="e_marks" placeholder="Enter Exam Marks" autocomplete="off" name="eMarks" value="<?php echo $exam_marks; ?>">
                </div>
                <div class="mb-3">
                    <label for="ia_marks" class="form-label">IA Marks</label>
                    <input type="text" class="form-control" id="ia_marks" placeholder="Enter IA Marks" autocomplete="off" name="iaMarks" value="<?php echo $ia_marks; ?>" required>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-primary mt-2" name="result_update">Update Result</button>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <a href="../MResults" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>