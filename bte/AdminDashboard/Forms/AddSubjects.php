<?php
// Including the database connection file
include "../Backend/connectdb.php";

// Check if the form is submitted
if (isset($_POST['subject_submit'])) {
    // Retrieve subject code and name from the form
    $sCode = $_POST['sCode'];
    $sName = $_POST['sName'];

    // SQL query to insert subject data into the database
    $sql = "INSERT INTO subjects (subject_code, subject_name) 
    VALUES (UPPER('$sCode'), UPPER('$sName'))";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        // Redirect to the subjects page with a success message
        header('Location: ../Subjects?added=1');
        exit(); // Stop further execution of the script
    } else {
        // Display an error message if the query fails
        echo "Error inserting subject record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add New Subjects</title>
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
                <h2 class="text-center mb-4">Add New Subject</h2>
                <div class="mb-3">
                    <label for="s_code" class="form-label">Subject Code</label>
                    <input type="text" class="form-control" id="s_code" placeholder="Enter Subject Code" autocomplete="off" name="sCode" required>
                </div>
                <div class="mb-3">
                    <label for="s_name" class="form-label">Subject Name</label>
                    <input type="text" class="form-control" id="s_name" placeholder="Enter Subject Name" autocomplete="off" name="sName" required>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-success mt-2" name="subject_submit">Add Subject</button>
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