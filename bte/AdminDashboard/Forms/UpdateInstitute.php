<?php
// Include the database connection file
include "../Backend/connectdb.php";

// Check if the form is submitted
if (isset($_POST['institute_update'])) {
    // Get the institute ID from the form
    $insId = $_POST['insId'];
    // Escape special characters to prevent SQL injection
    $insId = mysqli_real_escape_string($conn, $insId);

    // Get the institute name from the form
    $insName = $_POST['insName'];
    // Escape special characters to prevent SQL injection
    $insName = mysqli_real_escape_string($conn, $insName);

    // Get the institute ID from the URL parameter
    $insid = $_GET['updateinstitutewithid'];
    // Escape special characters to prevent SQL injection
    $insid = mysqli_real_escape_string($conn, $insid);

    // Update query with the WHERE clause to update specific institute
    $sql = "UPDATE `institutes` SET 
            institute_id='$insId', 
            institute_name=UPPER('$insName') 
            WHERE institute_id='$insid'";

    // Execute the update query
    $result = mysqli_query($conn, $sql);

    // Check if the update was successful
    if ($result) {
        // Update the institute_id in the students table
        $sql_update_students = "UPDATE students SET institute_id='$insId' WHERE institute_id='$insid'";
        $result_update_students = mysqli_query($conn, $sql_update_students);

        if ($result_update_students) {
            // Redirect to Institutes page with updated parameter in the URL
            header("Location: ../Institutes?updated=1");
            // Exit to prevent further execution
            exit();
        } else {
            // Display error message if updating students' institute_id fails
            echo "Error updating students' institute_id: " . mysqli_error($conn);
        }
    } else {
        // Display error message if the update fails
        echo "Error updating institute record: " . mysqli_error($conn);
    }
}

// Get the institute ID from the URL parameter
$insid = $_GET['updateinstitutewithid'];
// Escape special characters to prevent SQL injection
$insid = mysqli_real_escape_string($conn, $insid);

// Query to fetch institute details for pre-filling the form
$sql = "SELECT * FROM `institutes` WHERE institute_id='$insid'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$institute_id = $row['institute_id'];
$institute_name = $row['institute_name'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Institute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        /* CSS styles */
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
            input[type="text"].form-control {
                width: 300px;
            }
        }

        @media (min-width: 768px) {
            input[type="text"].form-control {
                width: 400px;
            }
        }

        @media (min-width: 992px) {
            input[type="text"].form-control {
                width: 500px;
            }
        }

        @media (min-width: 1200px) {
            input[type="text"].form-control {
                width: 600px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <form method="post">
                <h2 class="text-center mb-4">Update Institute</h2>
                <div class="mb-3">
                    <label for="ins_id" class="form-label">Institute ID</label>
                    <input type="text" class="form-control" id="ins_id" placeholder="Enter Institute Id" autocomplete="off" name="insId" value="<?php echo $institute_id; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="ins_name" class="form-label">Institute Name</label>
                    <input type="text" class="form-control" id="ins_name" placeholder="Enter Institute Name" autocomplete="off" name="insName" value="<?php echo $institute_name; ?>" required>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-primary mt-2" name="institute_update">Update Institute</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>