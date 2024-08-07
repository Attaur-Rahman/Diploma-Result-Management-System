<?php
// Including the database connection file
include "../Backend/connectdb.php";

// Temporarily disable foreign key checks to avoid constraint violations during updates
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");

// Get the branch ID from the URL parameter
$bid = $_GET['updatebranchwithid'];
$sql = "SELECT * FROM `branches` WHERE branch_id='$bid'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$branch_id = $row['branch_id'];
$branch_name = $row['branch_name'];

// Check if the form is submitted for updating branch details
if (isset($_POST['branch_update'])) {
    // Retrieve data from the form submission
    $bId = $_POST['bid'];
    $bname = $_POST['bname'];

    // Update query with the WHERE clause to update branch details
    $sql = "UPDATE `branches` SET 
            branch_id=UPPER('$bId'), 
            branch_name=UPPER('$bname') 
            WHERE branch_id='$bid'";

    // Execute the update query
    $result = mysqli_query($conn, $sql);

    // Check if the update was successful
    if ($result) {
        // Update the branch_id in the students table
        $sql_update_students = "UPDATE students SET branch_id=UPPER('$bId') WHERE branch_id='$bid'";
        $result_update_students = mysqli_query($conn, $sql_update_students);

        // Check if updating students' branch_id was successful
        if ($result_update_students) {
            // Redirect to Branches page with updated parameter
            header("Location: ../Branches?updated=1");
        } else {
            // Display an error message if updating students' branch_id fails
            echo "Error updating students' branch_id: " . $conn->error;
        }
    } else {
        // Display an error message if the update fails
        echo "Error updating branch record: " . $conn->error;
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
    <title>Update Branch</title>
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
                <h2 class="text-center mb-4">Update Branch</h2>
                <div class="mb-3">
                    <label for="b_id" class="form-label">Branch ID</label>
                    <input type="text" class="form-control" id="b_id" placeholder="Enter Branch Id" autocomplete="off" name="bid" value="<?php echo $branch_id; ?>">
                </div>
                <div class="mb-3">
                    <label for="b_name" class="form-label">Branch Name</label>
                    <input type="text" class="form-control" id="b_name" placeholder="Enter Branch Name" autocomplete="off" name="bname" value="<?php echo $branch_name; ?>">
                </div>
                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-primary mt-2" name="branch_update">Update branch</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>