<?php
// Include the database connection file
include "connectdb.php";

// Check if a student with a specific register number is to be deleted
if (isset($_GET['deletestudentwithregno'])) {
    // Get the register number from the URL parameter
    $register_number = $_GET['deletestudentwithregno'];

    // SQL query to retrieve student details
    $sql = "SELECT * FROM students WHERE register_number = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $register_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // Check if the student exists
    if ($row) {
        // Confirmation dialog for deletion
        echo "<script>
                if (confirm('Are you sure you want to delete the student with register number " . $register_number . "?')) {
                    document.location.href = '?delete_student=" . $register_number . "';
                } else {
                    document.location.href = '../Students.php';
                }
              </script>";
    } else {
        echo "No student found with register number " . $register_number;
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
} elseif (isset($_GET['deleteinstitutewithid'])) {
    // Check if an institute with a specific ID is to be deleted
    $institute_id = $_GET['deleteinstitutewithid'];

    // SQL query to retrieve institute details
    $sql = "SELECT * FROM institutes WHERE institute_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $institute_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // Check if the institute exists
    if ($row) {
        // Confirmation dialog for deletion
        echo "<script>
                if (confirm('Are you sure you want to delete the institute with ID " . $institute_id . "?')) {
                    document.location.href = '?delete_institute=" . $institute_id . "';
                } else {
                    document.location.href = '../Institutes.php';
                }
              </script>";
    } else {
        echo "No institute found with ID " . $institute_id;
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
} elseif (isset($_GET['deletebranchwithid'])) {
    // Check if a branch with a specific ID is to be deleted
    $branch_id = $_GET['deletebranchwithid'];

    // SQL query to retrieve branch details
    $sql = "SELECT * FROM branches WHERE branch_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $branch_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // Check if the branch exists
    if ($row) {
        // Confirmation dialog for deletion
        echo "<script>
                if (confirm('Are you sure you want to delete the branch with ID " . $branch_id . "?')) {
                    document.location.href = '?delete_branch=" . $branch_id . "';
                } else {
                    document.location.href = '../Branches.php';
                }
              </script>";
    } else {
        echo "No branch found with ID " . $branch_id;
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
} elseif (isset($_GET['deletesubjectwithcode'])) {
    // Check if a subject with a specific code is to be deleted
    $subject_code = $_GET['deletesubjectwithcode'];

    // SQL query to retrieve subject details
    $sql = "SELECT * FROM subjects WHERE subject_code = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $subject_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // Check if the subject exists
    if ($row) {
        // Confirmation dialog for deletion
        echo "<script>
                if (confirm('Are you sure you want to delete the subject with code " . $subject_code . "?')) {
                    document.location.href = '?delete_subject=" . $subject_code . "';
                } else {
                    document.location.href = '../Subjects.php';
                }
              </script>";
    } else {
        echo "No subject found with Code " . $subject_code;
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
} elseif (isset($_GET['deleteresultwithregno']) && isset($_GET['deleteresultwithscode'])) {
    // Retrieve register number and subject code from URL parameters
    $register_number = $_GET['deleteresultwithregno'];
    $subject_code = $_GET['deleteresultwithscode'];

    // SQL query to check if the result exists
    $sql = "SELECT * FROM results WHERE register_number = ? AND subject_code = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $register_number, $subject_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // Check if the result exists
    if ($row) {
        // Confirmation dialog for deletion
        echo "<script>
            if (confirm('Are you sure you want to delete the result with register number " . $register_number . " and subject code " . $subject_code . "?')) {
                document.location.href = '?delete_rno=" . $register_number . "&delete_subcode=" . $subject_code . "';
            } else {
                // If canceled, redirect back to results page
                document.location.href = '../MResults.php'; 
            }
        </script>";
    } else {
        // No student found with the provided register number and subject code
        echo "No student found with register number " . $register_number . " and subject code " . $subject_code;
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
} elseif (isset($_GET['delete_student'])) {
    // Delete a student and associated records
    $register_number = $_GET['delete_student'];

    // First, delete associated records from the results table
    $delete_results_sql = "DELETE FROM results WHERE register_number = ?";
    $stmt = $conn->prepare($delete_results_sql);
    $stmt->bind_param("s", $register_number);
    $stmt->execute();

    // Then, delete the student record
    $delete_student_sql = "DELETE FROM students WHERE register_number = ?";
    $stmt = $conn->prepare($delete_student_sql);
    $stmt->bind_param("s", $register_number);
    $stmt->execute();

    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        header("Location: ../Students.php?deleted=1");
    } else {
        echo "No records deleted.";
    }
} elseif (isset($_GET['delete_institute'])) {
    // Delete an institute and associated records
    $institute_id = $_GET['delete_institute'];

    // First, delete associated records from the students table
    $delete_students_sql = "DELETE FROM students WHERE institute_id = ?";
    $stmt = $conn->prepare($delete_students_sql);
    $stmt->bind_param("s", $institute_id);
    $stmt->execute();

    // Then, delete the institute record
    $delete_institute_sql = "DELETE FROM institutes WHERE institute_id = ?";
    $stmt = $conn->prepare($delete_institute_sql);
    $stmt->bind_param("s", $institute_id);
    $stmt->execute();

    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        header("Location: ../Institutes.php?deleted=1");
    } else {
        echo "No records deleted.";
    }
} elseif (isset($_GET['delete_branch'])) {
    // Delete a branch and associated records
    $branch_id = $_GET['delete_branch'];

    // First, delete associated records from the students table
    $delete_students_sql = "DELETE FROM students WHERE branch_id = ?";
    $stmt = $conn->prepare($delete_students_sql);
    $stmt->bind_param("s", $branch_id);
    $stmt->execute();

    // Then, delete the branch record
    $delete_branch_sql = "DELETE FROM branches WHERE branch_id = ?";
    $stmt = $conn->prepare($delete_branch_sql);
    $stmt->bind_param("s", $branch_id);
    $stmt->execute();

    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        header("Location: ../Branches.php?deleted=1");
    } else {
        echo "No records deleted.";
    }
} elseif (isset($_GET['delete_subject'])) {
    // Delete a subject and associated records
    $subject_code = $_GET['delete_subject'];

    // First, delete associated records from the results table
    $delete_results_sql = "DELETE FROM results WHERE subject_code = ?";
    $stmt = $conn->prepare($delete_results_sql);
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();

    // Then, delete the subject record
    $delete_subject_sql = "DELETE FROM subjects WHERE subject_code = ?";
    $stmt = $conn->prepare($delete_subject_sql);
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();

    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        header("Location: ../Subjects.php?deleted=1");
    } else {
        echo "No records deleted.";
    }
} elseif (isset($_GET['delete_rno']) && isset($_GET['delete_subcode'])) {
    // Retrieve register number and subject code from URL parameters
    $register_number = $_GET['delete_rno'];
    $subject_code = $_GET['delete_subcode'];

    // Prepare and execute the DELETE query
    $delete_result_sql = "DELETE FROM results WHERE register_number = ? AND subject_code = ?";
    $stmt = $conn->prepare($delete_result_sql);
    $stmt->bind_param("ss", $register_number, $subject_code);
    $stmt->execute();

    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        // Redirect back to the results page with a query parameter indicating deletion success
        header("Location: ../MResults.php?deleted=1");
        exit(); // Ensure no further output is sent after the header redirection
    } else {
        echo "No records deleted.";
    }

    // Close the statement
    $stmt->close();
}
