<?php
require_once('tcpdf/tcpdf.php');

include('tracking_db.php');

// Fetching courses data
$query_courses = "SELECT * FROM courses";
$result_courses = mysqli_query($conn, $query_courses);

$course_list = array();
if ($result_courses && mysqli_num_rows($result_courses) > 0) {
  while ($row = mysqli_fetch_assoc($result_courses)) {
    $course_list[] = $row;
  }
}

// Create a new PDF instance
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Course List');
$pdf->SetSubject('Course List');
$pdf->SetKeywords('Course, List, PDF');

// Set font
$pdf->SetFont('helvetica', '', 10);

// Add a page
$pdf->AddPage();

// Set some content
$html = '
    <h1 style="text-align: center;">Course List</h1>
    <table border="1" style="margin: 0 auto; width: 100%; text-align: center;">
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Name</th>
            </tr>
        </thead>
        <tbody>';

// Add course data to the table
foreach ($course_list as $course) {
    $html .= '
        <tr>
            <td style="line-height: 20px; vertical-align: middle; text-align: center;">' . $course['course_code'] . '</td>
            <td style="line-height: 20px; vertical-align: middle; text-align: center;">' . $course['course_name'] . '</td>
        </tr>';
}

$html .= '
        </tbody>
    </table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('course_list.pdf', 'D');
?>
