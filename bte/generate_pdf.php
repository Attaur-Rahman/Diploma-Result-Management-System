<?php

// Database connection parameters
$servername = "localhost";  // Database server
$username = "root";    // Database username
$password = "admin";  // Database password
$dbname = "bte";    // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Hardcoded parameters for the student registration number and semester
$regNo = $_GET['RegisterN'];
$sem = $_GET['SemN'];

// Prepare and execute the query to fetch student information
$sql = "
    SELECT 
        students.register_number,
        students.student_name,
        students.father_name,
        branches.branch_name,
        semesters.semester_number,
        institutes.institute_id,
        institutes.institute_name
    FROM results
    JOIN students ON results.register_number = students.register_number
    JOIN branches ON students.branch_id = branches.branch_id
    JOIN semesters ON results.semester_id = semesters.semester_id
    JOIN institutes ON students.institute_id = institutes.institute_id
    WHERE students.register_number = ? AND semesters.semester_number = ?;
";

// Prepare the SQL statement with placeholders
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $regNo, $sem);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Check if student data was found
if (!$student) {
    die("No data found for the given register number and semester.");
}

// Prepare and execute the query to fetch data for the results table
$sqlTableData = "
    SELECT 
        subjects.subject_name,
        subjects.subject_code,
        results.exam_marks,
        results.ia_marks
    FROM results
    JOIN subjects ON results.subject_code = subjects.subject_code
    WHERE results.register_number = ? AND results.semester_id = (
        SELECT semester_id FROM semesters WHERE semester_number = ?
    );
";
$stmtTableData = $conn->prepare($sqlTableData);
$stmtTableData->bind_param("ss", $regNo, $sem);
$stmtTableData->execute();
$data = $stmtTableData->get_result()->fetch_all(MYSQLI_ASSOC);

// Close the prepared statement
$stmt->close();
$stmtTableData->close();

// Close the database connection
$conn->close();

require("TCPDF/tcpdf.php");

// Set the time zone for date and time
date_default_timezone_set('Asia/Kolkata');

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

// Disable header and footer for the PDF
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

// Define page dimensions
$pageWidth = $pdf->getPageWidth();
$pageHeight = $pdf->getPageHeight();

// Set font for the date and time
$pdf->SetFont('times', '', 10);

// Get current date and time
$currentDateTime = date('d/m/Y h:i:s A');

// Set Y position to place the date and time near the top
$pdf->SetY(2); // Adjust the value as needed to position it properly
$pdf->SetX(4); // Adjust the value as needed to position it properly

// Add date and time to the PDF
$pdf->Cell(0, 10, 'Date: ' . $currentDateTime, 0, 1, 'L');

// Define border thickness and color
$borderThickness = 0.1; // Thickness of the border lines
$borderColor = array(0, 0, 0); // RGB color values for the border (black in this case)

// Set border color
$pdf->SetDrawColorArray($borderColor);
$pdf->SetLineWidth($borderThickness);

// Draw the borders using lines
$pdf->Line(5, 10, $pageWidth - 5, 10); // Top border
$pdf->Line(5, 10, 5, $pageHeight - 5); // Left border
$pdf->Line($pageWidth - 5, 10, $pageWidth - 5, $pageHeight - 5); // Right border
$pdf->Line(5, $pageHeight - 5, $pageWidth - 5, $pageHeight - 5); // Bottom border

// Add an image to the PDF
$imageFilePath = './Govt-Logo.png';  // Change this to your image file path
$pdf->Image($imageFilePath, 0, 11, 0, 20, 'PNG', '', '', true, 150, 'C', false, false, 0, false, false, false);
$pdf->Ln(21);

// Add titles and headers to the PDF
$pdf->setFont('Times', '', 12);
$pdf->Cell(0, 0, 'GOVERNTMENT OF KARNATAKA', 0, 1, 'C', 0, '', false);

$pdf->setFont('Times', 'B', 15);
$pdf->Cell(0, 10, 'BOARD OF TECHNICAL EXAMINATIONS, DTE BANGALORE', 0, 1, 'C', 0, '', false);

$pdf->setFont('Times', '', 15);
$pdf->Cell(0, 0, 'Provisional Marks Card', 0, 1, 'C', 0, '', false);
$pdf->Ln(1);

// Define dimensions for the outer border around student information
$borderX = 10;
$borderY = 56.5;
$borderWidth = 190;
$borderHeight = 60; // Adjust height as needed for your content

// Draw the outer border for student information
$pdf->SetLineWidth(0.1);
$pdf->Rect($borderX, $borderY, $borderWidth, $borderHeight); // x, y, width, height

// Define starting Y position for the content within the border
$yStart = $borderY;

// Define row height and column widths for the student information table
$rowHeight = 8;
$col1Width = 60; // Width of the label column
$col2Width = $borderWidth - $col1Width; // Width of the data column

// Add student information to the PDF
$pdf->setFont('freemono', 'B', 15);
$pdf->Cell(0, 10, 'STUDENT INFORMATION', 0, 1, 'C', 0, '', false);

$pdf->setFont('Times', 'B', 13);
$pdf->Cell(42, 8, 'Register Number   : ', 0, 0, 'L', 0, '', false);
$pdf->setFont('opensans', '', 12);
$pdf->Cell(0, 8, $student['register_number'], 0, 1, 'L', 0, '', false);

$pdf->setFont('Times', 'B', 13);
$pdf->Cell(42, 8, 'Student Name        : ', 0, 0, 'L', 0, '', false);
$pdf->setFont('opensans', '', 12);
$pdf->Cell(0, 8, $student['student_name'], 0, 1, 'L', 0, '', false);

$pdf->setFont('Times', 'B', 13);
$pdf->Cell(42, 8, 'Father Name          : ', 0, 0, 'L', 0, '', false);
$pdf->setFont('opensans', '', 12);
$pdf->Cell(0, 8, $student['father_name'], 0, 1, 'L', 0, '', false);

$pdf->setFont('Times', 'B', 13);
$pdf->Cell(42, 8, 'Branch                    : ', 0, 0, 'L', 0, '', false);
$pdf->setFont('opensans', '', 12);
$pdf->Cell(0, 8, $student['branch_name'], 0, 1, 'L', 0, '', false);

$pdf->setFont('Times', 'B', 13);
$pdf->Cell(42, 8, 'Semester                 : ', 0, 0, 'L', 0, '', false);
$pdf->setFont('opensans', '', 12);
$pdf->Cell(0, 8, $student['semester_number'], 0, 1, 'L', 0, '', false);

$pdf->setFont('Times', 'B', 13);
$pdf->Cell(42, 8, 'Institute                  : ', 0, 0, 'L', 0, '', false);
$pdf->setFont('opensans', '', 12);
$pdf->Cell(0, 8, $student['institute_id'] . ' – ' . $student['institute_name'], 0, 1, 'L', 0, '', false);

$pdf->Ln(5);

// Define the criteria for each semester and subject
$criteria = [
    // Criteria for semester 1
    1 => [
        "20SC01T" => ["exam" => 20, "ia" => 20],
        "20CS11T" => ["exam" => 20, "ia" => 20],
        "20ME11T" => ["exam" => 20, "ia" => 20],
        "20EC11T" => ["exam" => 20, "ia" => 20],
        "20CE11T" => ["exam" => 20, "ia" => 20],
        "20EE11T" => ["exam" => 20, "ia" => 20],
        "20EC01P" => ["exam" => 16, "ia" => 24],
        "20CS01P" => ["exam" => 16, "ia" => 24],
        "20EG01P" => ["exam" => 16, "ia" => 24],
        "20ME12P" => ["exam" => 16, "ia" => 24],
        "20SC02P" => ["exam" => 16, "ia" => 24],
        "20ME02P" => ["exam" => 16, "ia" => 24],
        "20EE01P" => ["exam" => 16, "ia" => 24],
        "20AU01T" => ["ia" => 20],
    ],
    // Criteria for semester 2
    2 => [
        "20PM01T" => ["exam" => 20, "ia" => 20],
        "20SC01T" => ["exam" => 20, "ia" => 20],
        "20SC02P" => ["exam" => 16, "ia" => 24],
        "20EG01P" => ["exam" => 16, "ia" => 24],
        "20ME02P" => ["exam" => 16, "ia" => 24],
        "20CS21P" => ["exam" => 16, "ia" => 24],
        "20EE01P" => ["exam" => 16, "ia" => 24],
        "20CS01P" => ["exam" => 16, "ia" => 24],
        "20ME21P" => ["exam" => 16, "ia" => 24],
        "20EC21P" => ["exam" => 16, "ia" => 24],
        "20CE21P" => ["exam" => 16, "ia" => 24],
        "20CE22P" => ["exam" => 16, "ia" => 24],
        "20EE21P" => ["exam" => 16, "ia" => 24],
        "20KA21T" => ["ia" => 20],
    ],
    // Criteria for semester 3
    3 => [
        "20CS31P" => ["exam" => 16, "ia" => 24],
        "20CS32P" => ["exam" => 16, "ia" => 24],
        "20CS33P" => ["exam" => 16, "ia" => 24],
        "20CS34P" => ["exam" => 16, "ia" => 24],
        "20ME31P" => ["exam" => 16, "ia" => 24],
        "20ME32P" => ["exam" => 16, "ia" => 24],
        "20ME33P" => ["exam" => 16, "ia" => 24],
        "20ME34P" => ["exam" => 16, "ia" => 24],
        "20EC31P" => ["exam" => 16, "ia" => 24],
        "20EC32P" => ["exam" => 16, "ia" => 24],
        "20EC33P" => ["exam" => 16, "ia" => 24],
        "20EC34P" => ["exam" => 16, "ia" => 24],
        "20CE31P" => ["exam" => 16, "ia" => 24],
        "20CE32P" => ["exam" => 16, "ia" => 24],
        "20CE33P" => ["exam" => 16, "ia" => 24],
        "20CE34P" => ["exam" => 16, "ia" => 24],
        "20EE31P" => ["exam" => 16, "ia" => 24],
        "20EE32P" => ["exam" => 16, "ia" => 24],
        "20EE33P" => ["exam" => 16, "ia" => 24],
        "20EE34P" => ["exam" => 16, "ia" => 24],
        "20KA31T" => ["ia" => 20],
    ],
    // Criteria for semester 4
    4 => [
        "20CS41P" => ["exam" => 16, "ia" => 24],
        "20CS42P" => ["exam" => 16, "ia" => 24],
        "20CS43P" => ["exam" => 16, "ia" => 24],
        "20CS44P" => ["exam" => 16, "ia" => 24],
        "20ME41P" => ["exam" => 16, "ia" => 24],
        "20ME42P" => ["exam" => 16, "ia" => 24],
        "20ME43P" => ["exam" => 16, "ia" => 24],
        "20ME44P" => ["exam" => 16, "ia" => 24],
        "20EC41P" => ["exam" => 16, "ia" => 24],
        "20EC42P" => ["exam" => 16, "ia" => 24],
        "20EC43P" => ["exam" => 16, "ia" => 24],
        "20EC44P" => ["exam" => 16, "ia" => 24],
        "20CE41P" => ["exam" => 16, "ia" => 24],
        "20CE42P" => ["exam" => 16, "ia" => 24],
        "20CE43P" => ["exam" => 16, "ia" => 24],
        "20CE44P" => ["exam" => 16, "ia" => 24],
        "20EE41P" => ["exam" => 16, "ia" => 24],
        "20EE42P" => ["exam" => 16, "ia" => 24],
        "20EE43P" => ["exam" => 16, "ia" => 24],
        "20EE44P" => ["exam" => 16, "ia" => 24],
        "20CS45T" => ["ia" => 20],
        "20ME45T" => ["ia" => 20],
        "20EC45T" => ["ia" => 20],
        "20CE45T" => ["ia" => 20],
        "20EE45T" => ["ia" => 20],
    ],
    // Criteria for semester 5
    5 => [
        "20CS51I" => ["exam" => 64, "ia" => 96],
        "20CS52I" => ["exam" => 64, "ia" => 96],
        "20CS53I" => ["exam" => 64, "ia" => 96],
        "20CS54I" => ["exam" => 64, "ia" => 96],
        "20ME51I" => ["exam" => 64, "ia" => 96],
        "20ME52I" => ["exam" => 64, "ia" => 96],
        "20ME53I" => ["exam" => 64, "ia" => 96],
        "20ME54I" => ["exam" => 64, "ia" => 96],
        "20EC51I" => ["exam" => 64, "ia" => 96],
        "20EC52I" => ["exam" => 64, "ia" => 96],
        "20EC53I" => ["exam" => 64, "ia" => 96],
        "20EC54I" => ["exam" => 64, "ia" => 96],
        "20CE51I" => ["exam" => 64, "ia" => 96],
        "20CE52I" => ["exam" => 64, "ia" => 96],
        "20CE53I" => ["exam" => 64, "ia" => 96],
        "20CE54I" => ["exam" => 64, "ia" => 96],
        "20EE51I" => ["exam" => 64, "ia" => 96],
        "20EE52I" => ["exam" => 64, "ia" => 96],
        "20EE53I" => ["exam" => 64, "ia" => 96],
        "20EE54I" => ["exam" => 64, "ia" => 96],
        "20SC51T" => ["exam" => 20, "ia" => 20],
        "20SC52T" => ["exam" => 20, "ia" => 20],
        "20RM53T" => ["exam" => 20, "ia" => 20],
        "20TW54P" => ["exam" => 16, "ia" => 24],
        "20ET51I" => ["exam" => 64, "ia" => 96],
    ],
    // Criteria for semester 6
    6 => [
        "20CS61S" => ["exam" => 64, "ia" => 96],
        "20CS61R" => ["exam" => 64, "ia" => 96],
        "20CS61E" => ["exam" => 64, "ia" => 96],
        "20ME61S" => ["exam" => 64, "ia" => 96],
        "20ME61R" => ["exam" => 64, "ia" => 96],
        "20ME61E" => ["exam" => 64, "ia" => 96],
        "20EC61S" => ["exam" => 64, "ia" => 96],
        "20EC61R" => ["exam" => 64, "ia" => 96],
        "20EC61E" => ["exam" => 64, "ia" => 96],
        "20CE61S" => ["exam" => 64, "ia" => 96],
        "20CE61R" => ["exam" => 64, "ia" => 96],
        "20CE61E" => ["exam" => 64, "ia" => 96],
        "20EE61S" => ["exam" => 64, "ia" => 96],
        "20EE61R" => ["exam" => 64, "ia" => 96],
        "20EE61E" => ["exam" => 64, "ia" => 96],
    ],
];

// Function to determine result based on criteria
function getResult($result, $criteria)
{
    // Extract semester criteria based on the semester number
    $semesterNumber = isset($result['semester_number']) ? (int)$result['semester_number'] : null;
    $semesterCriteria = $semesterNumber !== null && isset($criteria[$semesterNumber]) ? $criteria[$semesterNumber] : null;

    // Check if the criteria for the subject exists
    if (!$semesterCriteria || !isset($semesterCriteria[$result['subject_code']])) {
        return "";
    }

    $subjectCriteria = $semesterCriteria[$result['subject_code']];
    $examMarks = isset($result['exam_marks']) ? (int)$result['exam_marks'] : 0;
    $iaMarks = isset($result['ia_marks']) ? (int)$result['ia_marks'] : 0;

    $exam = isset($subjectCriteria['exam']) ? (int)$subjectCriteria['exam'] : null;
    $ia = isset($subjectCriteria['ia']) ? (int)$subjectCriteria['ia'] : null;

    if (($exam !== null && $examMarks < $exam) || ($ia !== null && $iaMarks < $ia)) {
        return "Fail";
    } else {
        return "Pass";
    }
}

// Create HTML table with inline CSS
$html = '
<style>
.t-c {
    text-align: center;
}
thead th {
    font-weight: bold;
}
</style>
<table border="0.1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr style="text-align: center; vertical-align: middle; background-color: gray; color: white;">
            <th style="width: 10%;">Sl.No.</th>
            <th style="width: 38.1%;">Subject Name</th>
            <th style="width: 10%;">Exam Marks</th>
            <th style="width: 10%;">IA Marks</th>
            <th style="width: 10%;">Total Marks</th>
            <th style="width: 12%;">Subject Code</th>
            <th style="width: 10%;">Result</th>
        </tr>
    </thead>
    <tbody>';

// Add rows dynamically
foreach ($data as $index => $result) {
    // Determine the exam marks, showing '--' if null or 0
    $examMarks = ($result['exam_marks'] !== null && (int)$result['exam_marks'] !== 0) ? $result['exam_marks'] : '--';
    $totalMarks = ($examMarks !== '--' ? (int)$examMarks : 0) + (int)$result['ia_marks'];

    $resultss = [
        'semester_number' => $student['semester_number'],
        'subject_code' => $result['subject_code'],
        'exam_marks' => $examMarks,
        'ia_marks' => $result['ia_marks']
    ];

    // Call getResult function to determine the result
    $resultStatus = getResult($resultss, $criteria);

    // Set the result style based on the result status
    $resultStyle = $resultStatus === 'Fail' ? 'color: red;' : 'color: green;';

    $html .= '
        <tr style="text-align: center;">
            <td style="width: 10%;">' . ($index + 1) . '</td>
            <td style="text-align: left; width: 38.1%;">' . htmlspecialchars($result['subject_name']) . '</td>
            <td style="width: 10%;">' . htmlspecialchars($examMarks) . '</td>
            <td style="width: 10%;">' . htmlspecialchars($result['ia_marks']) . '</td>
            <td style="width: 10%;">' . $totalMarks . '</td>
            <td style="width: 12%;">' . htmlspecialchars($result['subject_code']) . '</td>
            <td style="width: 10%; ' . $resultStyle . '">' . getResult($resultss, $criteria) . '</td>
        </tr>';
}

$html .= '
    </tbody>
</table>';

// Output the HTML as a table in TCPDF
$pdf->writeHTML($html, true, false, true, false, '');

// Define dimensions for the outer border
$borderX = 10;
$borderY = 201;
$borderWidth = 190;
$borderHeight = 39; // Adjust height as needed for your content

// Draw the outer border
$pdf->SetLineWidth(0.1);
$pdf->Rect($borderX, $borderY, $borderWidth, $borderHeight); // x, y, width, height

// Define starting Y position for the content within the border
$yStart = $borderY;

// Define row height
$rowHeight = 8;

// Define column widths
$col1Width = 60; // Width of the label column
$col2Width = $borderWidth - $col1Width; // Width of the data column

// Add "RESULTS" header to the PDF
$pdf->SetY(201); // Set Y position for the header (adjust as needed)
$pdf->SetFont('freemono', 'B', 15);
$pdf->Cell(0, 10, 'RESULTS', 0, 1, 'C', 0, '', false);
$pdf->Ln(2);

// Add "Total Exam Marks" label
$pdf->SetFont('Times', 'B', 13);
$pdf->Cell(46, 8, 'Total Exam Marks    : ', 0, 0, 'L', 0, '', false);

// Calculate total exam marks
$totalExamMarks = array_reduce($data, function ($sum, $result) {
    return $sum + ($result['exam_marks'] !== null ? intval($result['exam_marks']) : 0);
}, 0);

// Add total exam marks value (adjust the cell width as needed)
$pdf->SetFont('opensans', '', 12);
$pdf->Cell(46, 8, $totalExamMarks, 0, 0, 'L', 0, '', false);

// Add "IA Marks" label
$pdf->SetFont('Times', 'B', 13);
$pdf->Cell(46, 8, 'Total IA Marks          : ', 0, 0, 'L', 0, '', false);

// Calculate total ia marks
$totalIaMarks = array_reduce($data, function ($sum, $result) {
    return $sum + ($result['ia_marks'] !== null ? intval($result['ia_marks']) : 0);
}, 0);

// Add total IA marks value
$pdf->SetFont('opensans', '', 12);
$pdf->Cell(0, 8, $totalIaMarks, 0, 1, 'L', 0, '', false);

// Add "Grand Total" label
$pdf->SetFont('Times', 'B', 13);
$pdf->Cell(46, 8, 'Grand Total               : ', 0, 0, 'L', 0, '', false);

// Initialize grand total
$grandTotal = 0;
// Loop through data to calculate total marks and accumulate grand total
foreach ($data as $result) {
    $examMarks = $result['exam_marks'] !== '--' ? (int)$result['exam_marks'] : 0;
    $iaMarks = isset($result['ia_marks']) && $result['ia_marks'] !== null ? (int)$result['ia_marks'] : 0;
    $totalMarks = $examMarks + $iaMarks;
    $grandTotal += $totalMarks;
}

// Add total Grand Total value
$pdf->SetFont('opensans', '', 12);
$pdf->Cell(46, 8, $grandTotal, 0, 0, 'L', 0, '', false);

// Helper function to calculate the percentage
function calculatePercentage($semester, $grandTotal)
{
    switch ($semester) {
        case 1:
            $totalMarks = 450;
            break;
        case 2:
            $totalMarks = 550;
            break;
        case 3:
            $totalMarks = 450;
            break;
        case 4:
            $totalMarks = 450;
            break;
        case 5:
            $totalMarks = 400;
            break;
        case 6:
            $totalMarks = 400;
            break;
        default:
            return null; // Handle invalid semester
    }
    $percentage = ($grandTotal / $totalMarks) * 100;
    return number_format($percentage, 2); // Convert to string with 2 decimal points
}
$semV = $student['semester_number'];
$percentageV = calculatePercentage($semV, $grandTotal);

// Determine the result for each subject
foreach ($data as $result) {
    if (getResult($resultss, $criteria) === "Fail") {
        $overallResult = "Failed";
        break;
    } else {
        $overallResult = "Passed";
    }
}

// Add "Percentage" label
$pdf->SetFont('Times', 'B', 13);
$pdf->Cell(46, 8, 'Percentage                  : ', 0, 0, 'L', 0, '', false);

if ($overallResult === "Passed") {
    $pdf->SetFont('opensans', '', 12);
    $pdf->Cell(0, 8, $percentageV . '%', 0, 1, 'L', 0, '', false);
} else {
    $pdf->SetFont('opensans', '', 12);
    $pdf->Cell(9, 8, '--', 0, 1, 'C', 0, '', false);
}

// Add "Overall Result" label
$pdf->SetFont('Times', 'B', 13);
$pdf->Cell(46, 8, 'Overall Result            : ', 0, 0, 'L', 0, '', false);

// Determine color based on overall result
if ($overallResult === "Passed") {
    $pdf->SetTextColor(0, 128, 0); // Green
} else {
    $pdf->SetTextColor(255, 0, 0); // Red
}

// Add total Overall Result value
$pdf->SetFont('opensans', 'B', 12);
$pdf->Cell(0, 8, $overallResult, 0, 1, 'L', 0, '', false);

// Reset text color to default (black) for subsequent cells
$pdf->SetTextColor(0, 0, 0);

$pdf->SetY(266.9); // Set Y position for the header (adjust as needed)
$pdf->SetFont('times', 'B', 15);

// First cell for 'HOD'
$pdf->SetX(10); // Set X position
$pdf->Cell(80, 10, 'HOD', 0, 0, 'C', 0, '', false);

// Second cell for 'HOD' (shifted right, adjust width as needed)
$pdf->SetX(110); // Set X position
$pdf->Cell(80, 10, 'PRINCIPAL', 0, 0, 'C', 0, '', false);


// $pdf->Output("PMC-" . $student['register_number'] . "(" . $student['semester_number'] . ")", 'D');
$pdf->Output("Provisional_Marksheet_" . $student['register_number'] . "_Sem" . $student['semester_number'] . ".pdf", 'D');
