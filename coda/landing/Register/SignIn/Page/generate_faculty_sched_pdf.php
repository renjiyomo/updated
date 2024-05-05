<?php
require_once('tcpdf/tcpdf.php');

include('tracking_db.php');

// Get the list of faculties
$sql_faculties = "SELECT * FROM faculties";
$result_faculties = mysqli_query($conn, $sql_faculties);

// Create a new PDF instance
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Schedule List');
$pdf->SetSubject('Schedule List');
$pdf->SetKeywords('Schedule, List, PDF');

// Set font
$pdf->SetFont('helvetica', '', 10);

// Add a page
$pdf->AddPage();

// Loop through each faculty
while ($faculty = mysqli_fetch_assoc($result_faculties)) {
    $faculty_id = $faculty['faculty_id'];
    $faculty_name = $faculty['names'];

    // Query schedules for the current faculty
    $sql_schedule = "SELECT s.*, r.room_name, c.course_code 
                    FROM schedules s 
                    INNER JOIN rooms r ON s.room_id = r.room_id 
                    INNER JOIN courses c ON s.course_id = c.course_id
                    WHERE faculty_id = '$faculty_id'
                    ORDER BY sched_id ASC";

    $result_schedule = mysqli_query($conn, $sql_schedule);

    // Add faculty name as heading
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Faculty: ' . $faculty_name, 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);

    // Check if there are schedules for the current faculty
    if (mysqli_num_rows($result_schedule) > 0) {
        // Generate table header
        $html = '<table border="1" style="margin: 0 auto; width: 100%; text-align: center;">
                    <thead>
                        <tr>
                            <th>Course, Yr & Block</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Day of Week</th>
                            <th>Subject</th>
                            <th>From Month</th>
                            <th>To Month</th>
                            <th>Year</th>
                            <th>Room Name</th>
                        </tr>
                    </thead>
                    <tbody>';

        // Add schedule data to the table
        while ($row = mysqli_fetch_assoc($result_schedule)) {
            $start_time = date("h:i A", strtotime($row['start_time']));
            $end_time = date("h:i A", strtotime($row['end_time']));
            $course_display = $row['course_code'] . ' - ' . $row['yr_and_block'];

            $html .= '<tr>
                        <td>' . $course_display . '</td>
                        <td>' . $start_time . '</td>
                        <td>' . $end_time . '</td>
                        <td>' . $row['day_of_week'] . '</td>
                        <td>' . $row['subject'] . '</td>
                        <td>' . $row['from_month'] . '</td>
                        <td>' . $row['to_month'] . '</td>
                        <td>' . $row['year'] . '</td>
                        <td>' . $row['room_name'] . '</td>
                    </tr>';
        }

        $html .= '</tbody></table>';

        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
    } else {
        // If no schedules found for the faculty
        $pdf->Cell(0, 10, 'No schedules found for ' . $faculty_name, 0, 1, 'C');
    }

    // Add space between tables
    $pdf->Ln(10);
}

// Close and output PDF document
$pdf->Output('schedule_list.pdf', 'D');
?>
