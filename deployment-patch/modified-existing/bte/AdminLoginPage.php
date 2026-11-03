<?php
// Start session
session_start();

// Check if user is already logged in
if (isset($_SESSION['loggedin'])) {
    // Redirect to login page
    header("Location: ./AdminDashboard/Dashboard?logged=1");
    exit; // Stop further execution
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="LoginStyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .errormsg {
            background: #F2DEDE;
            color: #A24442;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
        }
    </style>
    <title>BTE Admin Login</title>
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("Upass");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }
    </script>
</head>

<body class="bg-dark">
    <div class="Auth-form-container d-flex align-items-center justify-content-center">
        <form class="Auth-form" action="adminlogin.php" method="POST">
            <div class="Auth-form-content">
                <h3 class="Auth-form-title">Admin Login</h3>
                <div class="form-group mt-3">
                    <?php if (isset($_GET['error'])) { ?>
                        <p class="errormsg"><?php echo $_GET['error']; ?></p>
                    <?php } ?>
                    <?php if (isset($_GET['loggedout']) && $_GET['loggedout'] == 1) { ?>
                        <p class="alert alert-danger fade show" id="successMessage"><strong>Logged Out! </strong> Successfully!</p>
                        <?php echo "<script>history.replaceState({}, '', window.location.href.split('?')[0]);</script>"; ?>
                        <script>
                            // Hide the success message after 5 seconds
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
                    <label>Email address</label>
                    <input type="email" class="form-control mt-1" placeholder="Enter email" name="Uemail" required autocomplete="off" />
                </div>
                <div class="form-group mt-3 position-relative">
                    <label for="Upass">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" placeholder="Enter password" name="Upass" id="Upass" required autocomplete="off" />
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()" aria-label="Toggle password visibility">
                            <i class="bi bi-eye" id="showPasswordIcon"></i>
                        </button>
                    </div>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <input type="submit" class="btn btn-success mt-2" value="Login" />
                </div>
                <div class="mt-3 d-flex align-items-center justify-content-center">
                    <span class="">Go to <a href="/">HomePage</a></span>
                </div>
            </div>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>

</html>