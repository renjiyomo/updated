<?php
require_once('tcpdf/tcpdf.php');

include('tracking_db.php');

// Query to select faculties
$query = "SELECT * FROM faculties";
$result = mysqli_query($conn, $query);

$faculty_list = array();
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $faculty_list[] = $row;
    }
}

// Create a new PDF instance
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Faculty List');
$pdf->SetSubject('Faculty List');
$pdf->SetKeywords('Faculty, List, PDF');

// Set font
$pdf->SetFont('helvetica', '', 10);

// Add a page
$pdf->AddPage();

// Set some content
$html = '
    <h1 style="text-align: center;">Faculty List</h1>
    <table border="1" style="margin: 0 auto; text-align: center;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact No.</th>
                <th>Coordinator</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>';

// Add faculty data to the table
foreach ($faculty_list as $faculty) {
    $html .= '
        <tr>
            <td>' . $faculty['faculty_id'] . '</td>
            <td>' . $faculty['names'] . '</td>
            <td>' . $faculty['email'] . '</td>
            <td>' . $faculty['contact_no'] . '</td>
            <td>' . $faculty['coordinator'] . '</td>
            <td>' . $faculty['address'] . '</td>
        </tr>';
}

$html .= '
        </tbody>
    </table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('faculty_list.pdf', 'D');
?>
