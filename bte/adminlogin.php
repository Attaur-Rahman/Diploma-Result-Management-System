<?php

session_start();

// Database connection parameters
$host = "localhost";
$db_user = "";
$db_pass = "";
$db_name = "";

$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);

if (mysqli_connect_error()) {
    exit("Failed to connect the database : " . mysqli_connect_error());
}

if ($stmt = $conn->prepare("SELECT Id, Password FROM adminlogin WHERE Email = ?")) {
    $stmt->bind_param('s', $_POST['Uemail']);
    $stmt->execute();

    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($Id, $hashedPassword);
        $stmt->fetch();

        if (password_verify($_POST['Upass'], $hashedPassword)) {
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['Uemail'];
            $_SESSION['id'] = $Id;
            header('Location: ./AdminDashboard/Dashboard.php?loggedin=1');
            exit();
        } else {
            // echo "Incorrect Password!";
            // header('refresh:2;url=AdminLoginPage.php');
            header("Location: AdminLoginPage.php?error=Invalid Password!");
            exit();
        }
    } else {
        // echo "Incorrect Username!";
        // header('refresh:2;url=AdminLoginPage.php');
        header("Location: AdminLoginPage.php?error=Invalid Email!");
        exit();
    }
    $stmt->close();
} else {
    echo "Error in the prepared statement";
}

mysqli_close($conn);
