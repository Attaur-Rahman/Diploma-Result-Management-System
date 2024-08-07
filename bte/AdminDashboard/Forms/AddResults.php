<?php
// Include the database connection file
include "../Backend/connectdb.php";

// SQL queries to fetch data for dropdown menus
$sql_for_regno = "SELECT register_number, student_name FROM students";
$sql_for_semester = "SELECT semester_id FROM semesters";
$sql_for_subject = "SELECT subject_code, subject_name FROM subjects";

// Execute SQL queries
$insert_regno = $conn->query($sql_for_regno);
$insert_semester = $conn->query($sql_for_semester);
$insert_subject = $conn->query($sql_for_subject);

// Check if form is submitted
if (isset($_POST['result_submit'])) {
    // Get form data
    $regNo = $_POST['regNo'];
    $sId = $_POST['sId'];
    $sCode = $_POST['sCode'];
    $eMarks = $_POST['eMarks'];
    $iaMarks = $_POST['iaMarks'];

    // SQL query to insert new result record
    $sql = "INSERT INTO results (register_number, semester_id, subject_code, exam_marks, ia_marks) 
            VALUES ('$regNo', '$sId', '$sCode', '$eMarks', '$iaMarks')";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        // Redirect to the manage results page with success message
        header('Location: ../MResults?added=1');
    } else {
        // Display error message if query fails
        echo "Error inserting result record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add New Results</title>
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
                <h2 class="text-center mb-4">Add New Result</h2>
                <div class="mb-3">
                    <label for="r_no" class="form-label">Student Reg No & Name</label>
                    <input type="text" id="search_input_reg" class="form-control" placeholder="Search by register number or name">
                    <select name="regNo" id="r_no" class="form-select mt-2" aria-label="Select RegNo" required>
                        <option value="">Select Student</option>
                        <?php
                        // Display options for register number dropdown
                        if ($insert_regno->num_rows > 0) {
                            while ($row = $insert_regno->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($row['register_number']) . '">' . htmlspecialchars($row['register_number']) . ' - ' . htmlspecialchars($row['student_name']) . '</option>';
                            }
                        } else {
                            echo '<option value="" disabled>No student found</option>';
                        }
                        ?>
                    </select>
                </div>

                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    // Get the input field and dropdown for register number
                    var input_reg = $('#search_input_reg');
                    var select_reg = $('#r_no');

                    // Add event listener to the input field for register number
                    input_reg.on('keyup', function() {
                        var searchValueReg = $(this).val().toLowerCase();
                        select_reg.find('option').each(function() {
                            var optionTextReg = $(this).text().toLowerCase();
                            var optionValueReg = $(this).val().toLowerCase();
                            if (optionTextReg.indexOf(searchValueReg) > -1 || optionValueReg.indexOf(searchValueReg) > -1) {
                                select_reg.val($(this).val());
                                return false; // stop searching after first match
                            }
                        });
                    });
                </script>


                <div class="mb-3">
                    <label for="s_no" class="form-label">Semester</label>
                    <select name="sId" id="s_no" class="form-select" aria-label="Select SNo" required>
                        <option value="">Select Semester</option>
                        <?php
                        // Display options for semester dropdown
                        if ($insert_semester->num_rows > 0) {
                            while ($row = $insert_semester->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($row['semester_id']) . '">' . htmlspecialchars($row['semester_id']) . '</option>';
                            }
                        } else {
                            echo '<option value="" disabled>No semesters found</option>';
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
                                echo '<option value="' . htmlspecialchars($row['subject_code']) . '">' . htmlspecialchars($row['subject_code']) . ' - ' . htmlspecialchars($row['subject_name']) . '</option>';
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
                    <input type="text" class="form-control" id="e_marks" placeholder="Enter Exam Marks" autocomplete="off" name="eMarks">
                </div>
                <div class="mb-3">
                    <label for="ia_marks" class="form-label">IA Marks</label>
                    <input type="text" class="form-control" id="ia_marks" placeholder="Enter IA Marks" autocomplete="off" name="iaMarks" required>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-success mt-2" name="result_submit">Add Result</button>
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