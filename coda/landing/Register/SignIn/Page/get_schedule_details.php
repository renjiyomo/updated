<?php
include('tracking_db.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['sched_id'])) {
    $sched_id = $_GET['sched_id'];
    $sql = "SELECT * FROM schedules WHERE sched_id = '$sched_id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $schedule = mysqli_fetch_assoc($result);
        echo json_encode($schedule);
    } else {
        echo json_encode(array('error' => 'Schedule not found'));
    }
} else {
    echo json_encode(array('error' => 'Invalid request'));
}
?>
