<?php
require_once('tcpdf/tcpdf.php');

include('tracking_db.php');
session_start();

if (!isset($_SESSION['admin_name']) || !isset($_SESSION['user_id'])) {
  header('Location: /coda/landing/Register/SignIn/signin.php');
  exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT names FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user_row = mysqli_fetch_assoc($result);
    $user_name = $user_row['names'];
} else {
    $user_name = "User";
}

// Query to select only non-admin users
$query = "SELECT user_id, names, email FROM users WHERE user_type = 'u'";
$result = mysqli_query($conn, $query);

$user_list = array();
if ($result && mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $user_list[] = $row;
  }
}

// Create a new PDF instance
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('User List');
$pdf->SetSubject('User List');
$pdf->SetKeywords('User, List, PDF');

// Set font
$pdf->SetFont('helvetica', '', 10);

// Add a page
$pdf->AddPage();

// Set some content
$html = '
    <h1 style="text-align: center;">User List</h1>
    <table border="1" style="margin: 0 auto; text-align: center;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>';

// Add user data to the table
foreach ($user_list as $user) {
    $html .= '
        <tr>
            <td>' . $user['user_id'] . '</td>
            <td>' . $user['names'] . '</td>
            <td>' . $user['email'] . '</td>
        </tr>';
}

$html .= '
        </tbody>
    </table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('user_list.pdf', 'D');
?>
