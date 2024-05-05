<?php
require_once('tcpdf/tcpdf.php');

include('tracking_db.php');
session_start();

if (!isset($_SESSION['prof_name']) || !isset($_SESSION['faculty_id'])) {
    header('Location: /coda/landing/Register/SignIn/signin.php');
    exit();
}

$faculty_id = $_SESSION['faculty_id'];

$sql = "SELECT s.*, r.room_name, c.course_code 
        FROM schedules s 
        INNER JOIN rooms r ON s.room_id = r.room_id 
        INNER JOIN courses c ON s.course_id = c.course_id
        WHERE faculty_id = '$faculty_id'
        ORDER BY sched_id ASC";

$result = mysqli_query($conn, $sql);

// Create a new PDF instance
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Professor Schedule');
$pdf->SetSubject('Professor Schedule');
$pdf->SetKeywords('Schedule, Professor, PDF');

// Set font
$pdf->SetFont('helvetica', '', 10);

// Add a page
$pdf->AddPage();

// Set some content
$html = '
    <h1 style="text-align:center;">Schedules</h1>
    <table border="1">
        <thead>
            <tr>
                <th style="text-align:center;">Course, Year & Block</th>
                <th style="text-align:center;">Start Time</th>
                <th style="text-align:center;">End Time</th>
                <th style="text-align:center;">Day of Week</th>
                <th style="text-align:center;">Subject</th>
                <th style="text-align:center;">From Month</th>
                <th style="text-align:center;">To Month</th>
                <th style="text-align:center;">Year</th>
                <th style="text-align:center;">Room Name</th>
            </tr>
        </thead>
        <tbody>';

// Add schedule data to the table
while ($row = mysqli_fetch_assoc($result)) {
    $start_time = date("h:i A", strtotime($row['start_time']));
    $end_time = date("h:i A", strtotime($row['end_time']));
    $course_display = $row['course_code'] . ' - ' . $row['yr_and_block'];

    $html .= '
        <tr>
            <td style="text-align:center;">' . $course_display . '</td>
            <td style="text-align:center;">' . $start_time . '</td>
            <td style="text-align:center;">' . $end_time . '</td>
            <td style="text-align:center;">' . $row['day_of_week'] . '</td>
            <td style="text-align:center;">' . $row['subject'] . '</td>
            <td style="text-align:center;">' . $row['from_month'] . '</td>
            <td style="text-align:center;">' . $row['to_month'] . '</td>
            <td style="text-align:center;">' . $row['year'] . '</td>
            <td style="text-align:center;">' . $row['room_name'] . '</td>
        </tr>';
}

$html .= '
        </tbody>
    </table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('professor_schedule.pdf', 'D');
?>
