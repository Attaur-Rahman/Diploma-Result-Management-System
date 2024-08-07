<?php
// Including the database connection file
include "../Backend/connectdb.php";

// Temporarily disable foreign key checks to avoid constraints violation
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");

// Get the subject code from the URL parameter
$subcode = $_GET['updatesubjectwithcode'];

// Query to select the subject details based on the subject code
$sql = "SELECT * FROM `subjects` WHERE subject_code='$subcode'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$subject_code = $row['subject_code'];
$subject_name = $row['subject_name'];

// Check if the subject update form is submitted
if (isset($_POST['subject_update'])) {
    // Retrieve the updated subject code and name from the form
    $scode = $_POST['scode'];
    $sname = $_POST['sname'];

    // Update query with the WHERE clause
    $sql = "UPDATE `subjects` SET 
            subject_code=UPPER('$scode'), 
            subject_name=UPPER('$sname') 
            WHERE subject_code='$subcode'";

    // Execute the update query
    $result = mysqli_query($conn, $sql);

    // Check if the update was successful
    if ($result) {
        // Update the subject_code in the results table
        $sql_update_results = "UPDATE results SET subject_code=UPPER('$scode') WHERE subject_code='$subcode'";
        $result_update_results = mysqli_query($conn, $sql_update_results);

        // Check if the results update was successful
        if ($result_update_results) {
            // Redirect to the Subjects page with a success message
            header("Location: ../Subjects?updated=1");
            exit(); // Stop further execution of the script
        } else {
            // Display an error message if the results update fails
            echo "Error updating subjects' subject_code: " . $conn->error;
        }
    } else {
        // Display an error message if the update query fails
        echo "Error updating subject record: " . $conn->error;
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
    <title>Update Subject</title>
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

        /* Adjust the width of the input field */
        @media (min-width: 576px) {

            /* Small devices (576px and up) */
            input[type="text"].form-control {
                width: 300px;
                /* Adjust the width as needed */
            }
        }

        @media (min-width: 768px) {

            /* Medium devices (768px and up) */
            input[type="text"].form-control {
                width: 400px;
                /* Adjust the width as needed */
            }
        }

        @media (min-width: 992px) {

            /* Large devices (992px and up) */
            input[type="text"].form-control {
                width: 500px;
                /* Adjust the width as needed */
            }
        }

        @media (min-width: 1200px) {

            /* Extra large devices (1200px and up) */
            input[type="text"].form-control {
                width: 600px;
                /* Adjust the width as needed */
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <form method="post">
                <h2 class="text-center mb-4">Update Subject</h2>
                <div class="mb-3">
                    <label for="s_code" class="form-label">Subject Code</label>
                    <input type="text" class="form-control" id="s_code" placeholder="Enter Subject Code" autocomplete="off" name="scode" value="<?php echo $subject_code; ?>">
                </div>
                <div class="mb-3">
                    <label for="s_name" class="form-label">Subject Name</label>
                    <input type="text" class="form-control" id="s_name" placeholder="Enter Subject Name" autocomplete="off" name="sname" value="<?php echo $subject_name; ?>">
                </div>
                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-primary mt-2" name="subject_update">Update Subject</button>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <a href="../Subjects" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>