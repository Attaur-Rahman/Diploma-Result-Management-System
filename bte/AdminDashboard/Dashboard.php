<?php
include "../AdminDashboard/Backend/connectdb.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            border-radius: 15px 15px 0 0;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>

<body>
    <?php if (isset($_GET['loggedin']) && $_GET['loggedin'] == 1) { ?>
        <p class="alert alert-success" id="successMessage"><strong>Logged In! </strong> Successfully!</p>
        <?php echo "<script>history.replaceState({}, '', window.location.href.split('?')[0]);</script>"; ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    var successMessage = document.getElementById('successMessage');
                    if (successMessage) {
                        successMessage.style.display = 'none';
                    }
                }, 3000); // Adjust the time in milliseconds (3000 milliseconds = 3 seconds)
            });
        </script>
    <?php } ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h2>Welcome to the Admin Dashboard</h2>
                    </div>
                    <div class="card-body bg-warning-subtle">
                        <div class="d-grid gap-2 mb-4">
                            <a href="Students" class="btn btn-dark">Manage Students</a>
                        </div>
                        <div class="d-grid gap-2 mb-4">
                            <a href="Institutes" class="btn btn-info text-white">Manage Institutes</a>
                        </div>
                        <div class="d-grid gap-2 mb-4">
                            <a href="Branches" class="btn btn-primary">Manage Branches</a>
                        </div>
                        <div class="d-grid gap-2 mb-4">
                            <a href="Subjects" class="btn btn-warning text-white">Manage Subjects</a>
                        </div>
                        <div class="d-grid gap-2 mb-4">
                            <a href="MResults" class="btn btn-success">Manage Results</a>
                        </div>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <a href="/" class="btn btn-outline-dark">Back</a>
                        <a href="../adminlogout" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>