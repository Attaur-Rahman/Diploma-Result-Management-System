<?php
// Include the database connection file
include "../Backend/connectdb.php";

// Check if the form is submitted
if (isset($_POST['institute_submit'])) {
    // Get the input values from the form
    $insId = $_POST['insId'];
    $insName = $_POST['insName'];

    // SQL query to insert data into the institutes table
    $sql = "INSERT INTO institutes (institute_id, institute_name) 
    VALUES ('$insId', UPPER('$insName'))";

    // Execute the SQL query
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        // Redirect to Institutes page with added parameter in the URL
        header('Location: ../Institutes.php?added=1');
        // Exit to prevent further execution
        exit();
    } else {
        // Display error message if the query fails
        echo "Error inserting institute record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add New Institutes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        /* Styles for the body and container */
        body {
            background-color: #f8f9fa;
        }

        .container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Styles for the form container */
        .form-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        /* Styles for the form labels */
        .form-label {
            font-weight: bold;
        }

        /* Media queries for adjusting input field width based on screen size */
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
                <h2 class="text-center mb-4">Add New Institute</h2>
                <div class="mb-3">
                    <label for="ins_id" class="form-label">Institute ID</label>
                    <input type="text" class="form-control" id="ins_id" placeholder="Enter Institute Id" autocomplete="off" name="insId" required>
                </div>
                <div class="mb-3">
                    <label for="ins_name" class="form-label">Institute Name</label>
                    <input type="text" class="form-control" id="ins_name" placeholder="Enter Institute Name" autocomplete="off" name="insName" required>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-success mt-2" name="institute_submit">Add Institute</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>