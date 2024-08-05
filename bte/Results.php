<?php

// Set headers for CORS (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Origin: *"); // Adjust this to your specific domain if needed
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Cross-Origin-Resource-Policy: same-site"); // Use 'cross-origin' if needed

// Database connection parameters
$host = "localhost";
$username = "";
$password = "";
$database = "";

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    // Terminate script execution and display error message if connection fails
    die("Connection failed: " . $conn->connect_error);
}

// Get data from the frontend (ensure data sanitization to prevent SQL injection)
$regNo = $_GET['RegNo'];
$sem = $_GET['Sem'];

// Prepare and execute the query (use prepared statements to prevent SQL injection)
$sql = "
    SELECT 
        students.register_number,
        students.student_name,
        students.father_name,
        branches.branch_name,
        semesters.semester_number,
        institutes.institute_id,
        institutes.institute_name,
        subjects.subject_name,
        subjects.subject_code,
        results.exam_marks,
        results.ia_marks
    FROM results
    JOIN students ON results.register_number = students.register_number
    JOIN branches ON students.branch_id = branches.branch_id
    JOIN semesters ON results.semester_id = semesters.semester_id
    JOIN institutes ON students.institute_id = institutes.institute_id
    JOIN subjects ON results.subject_code = subjects.subject_code
    WHERE students.register_number = ? AND semesters.semester_number = ?;
";

// Prepare the SQL statement with placeholders
$stmt = $conn->prepare($sql);

// Bind parameters to the prepared statement
$stmt->bind_param("ss", $regNo, $sem);

// Execute the prepared statement
$stmt->execute();

// Get the result set from the executed statement
$result = $stmt->get_result();

// Fetch and return the data as JSON
if ($result->num_rows > 0) {
    // Fetch all rows into an associative array
    $data = $result->fetch_all(MYSQLI_ASSOC);
    // Encode the data array as JSON and output it
    echo json_encode($data);
} else {
    // If no rows are returned, output an empty JSON array
    echo json_encode([]);
}

// Close the prepared statement
$stmt->close();

// Close the database connection
$conn->close();
