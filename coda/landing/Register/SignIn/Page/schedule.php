<?php
date_default_timezone_set('Asia/Manila');
session_start();

if (isset($_POST['faculty_select'])) {
    $_SESSION['selected_faculty_id'] = $_POST['faculty_select'];
}

include('tracking_db.php');

if (!isset($_SESSION['admin_name']) || !isset($_SESSION['user_id'])) {
    header('Location: /coda/landing/Register/SignIn/signin.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$selected_faculty_id = isset($_SESSION['selected_faculty_id']) ? $_SESSION['selected_faculty_id'] : '';

$query = "SELECT names FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);

if ($result) {
    $user_row = mysqli_fetch_assoc($result);
    $user_name = $user_row['names'];
} else {
    $user_name = "User";
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM schedules WHERE sched_id = '$delete_id'";
    if (mysqli_query($conn, $delete_sql)) {
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["sched_id"]) && isset($_POST["course_id"]) && isset($_POST["yr"]) && isset($_POST["block"]) && isset($_POST["start_time"]) && isset($_POST["end_time"]) && isset($_POST["day_of_week"]) && isset($_POST["subject"]) && isset($_POST["from_month"]) && isset($_POST["to_month"]) && isset($_POST["room_id"]) && isset($_POST["year"])) {

        if (mysqli_query($conn, $update_sql)) {
            $success = true;
            $success_message = "Record updated successfully";
        } else {
            $success = false;
            $error_message = "Error updating record: " . mysqli_error($conn);
        }
    }
}

$sql_rooms = "SELECT * FROM rooms";
$result_rooms = mysqli_query($conn, $sql_rooms);
$room_options = "";
if (mysqli_num_rows($result_rooms) > 0) {
    while ($row = mysqli_fetch_assoc($result_rooms)) {
        $room_id = $row['room_id'];
        $room_name = $row['room_name'];
        $room_options .= "<option value='$room_id'>$room_name</option>";
    }
}

$months = array(
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
);

$days_of_week = array(
    "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
);

$current_day_ph = date("l");

if (isset($_GET['today'])) {
    $current_day = date("l"); 
    if (!empty($selected_faculty_id)) {
        $sql = "SELECT s.*, r.room_name, c.course_code 
                FROM schedules s 
                INNER JOIN rooms r ON s.room_id = r.room_id 
                INNER JOIN courses c ON s.course_id = c.course_id
                WHERE faculty_id = '$selected_faculty_id' AND day_of_week = '$current_day'";
    } else {
        $sql = "SELECT s.*, r.room_name, c.course_code 
                FROM schedules s 
                INNER JOIN rooms r ON s.room_id = r.room_id 
                INNER JOIN courses c ON s.course_id = c.course_id
                WHERE day_of_week = '$current_day'";
    }
} else {
    $sql = "SELECT s.*, r.room_name, c.course_code 
            FROM schedules s 
            INNER JOIN rooms r ON s.room_id = r.room_id 
            INNER JOIN courses c ON s.course_id = c.course_id
            ORDER BY sched_id ASC";
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['faculty_select'])) {
    $selected_faculty_id = $_POST['faculty_select'];
    $_SESSION['selected_faculty_id'] = $selected_faculty_id;
    if (!empty($selected_faculty_id)) {
        $sql = "SELECT s.*, r.room_name, c.course_code 
                FROM schedules s 
                INNER JOIN rooms r ON s.room_id = r.room_id 
                INNER JOIN courses c ON s.course_id = c.course_id
                WHERE faculty_id = '$selected_faculty_id'";
    }
}

if (isset($_GET['today'])) {
    unset($_SESSION['selected_faculty_id']); 
} else {

}   

$image_path_query = "SELECT image FROM users WHERE user_id = $user_id";
$image_path_result = mysqli_query($conn, $image_path_query);

if ($image_path_result && mysqli_num_rows($image_path_result) > 0) {
  $image_row = mysqli_fetch_assoc($image_path_result);
  $profile_image = $image_row['image'];
} else {
  // Default image path if not found in the database
  $profile_image = "default.jpg";
}


$result = mysqli_query($conn, $sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/schedule.css" />
  <link rel="stylesheet" href="css/all.min.css" />
  <link rel="stylesheet" href="css/fontawesome.min.css" />
  <script src="js/jquery-3.4.1.min.js"></script>
  <title>CSD Faculty</title>
  <script>
    $(document).ready(function(){
        // Function to toggle the profile dropdown
        $(".profile .icon_wrap").click(function(){
            $(this).parent().toggleClass("active");
            $(".notifications").removeClass("active");
        });

        // Function to close the profile dropdown when clicked outside
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.profile').length) {
                $(".profile").removeClass("active");
            }
        });

        // Close button click event
        $(".close").click(function(){
            $(".popup").hide();
        });

        // Automatically submit the form when faculty is selected
        $("#faculty_select").change(function(){
            $(this).closest('form').submit();
        });
    });
</script>
</head>
<body>

<nav>  
  <div class="wrapper">
    <nav class="navbar">
      <div class="navbar_left">
        <div class="nav__logo">
          <a href="#"><img src="imahe/FAST.png" alt="logo" /></a>
        </div>
      </div>
      <div class="navbar_center_text">
        <a href="adminpage.php">Home</a>
        <a href="total_user.php">Users</a>
        <a href="courses.php">Course List</a>
        <a href="faculty.php">Faculty List</a>
        <a href="schedule.php">Schedule</a>
      </div>

      <div class="navbar_right">
        <div class="notifications">

        </div>
         <div class="profile">
          <div class="icon_wrap">
          <img src="imahe/profile/<?php echo $profile_image; ?>" alt="profile_pic">
          </div>

          <div class="profile_dd">
            <ul class="profile_ul">
              <li class="profile_li">
                <a class="profile" href="#"><span class="picon"><i class="fas fa-user-alt"></i></span><?php echo $user_name; ?></a>
              </li>
              <li><a class="logout" href="logout.php"><span class="picon"><i class="fas fa-sign-out-alt"></i></span>Logout</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </nav>
</nav>

<?php if (!isset($_GET['today']) || (isset($_GET['today']) && mysqli_num_rows($result) > 0)) : ?>
    <div class="faculty-container">
    <h2>Faculty Schedules</h2>
    
    <div class="button-container">
        <div class="select-course-container">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <select id="faculty_select" name="faculty_select">
                <option value="">Select Professor</option>
    <?php
    $sql_faculties = "SELECT * FROM faculties";
    $result_faculties = mysqli_query($conn, $sql_faculties);
    if (mysqli_num_rows($result_faculties) > 0) {
        while ($row = mysqli_fetch_assoc($result_faculties)) {
            $faculty_id = $row['faculty_id'];
            $faculty_name = $row['names'];
            $selected = ($faculty_id == $selected_faculty_id) ? "selected" : "";
            echo "<option value='$faculty_id' $selected>$faculty_name</option>";
        }
    }
    ?>
</select>
            </form>
        </div>

        <div class="today-all-buttons">
            <button class="today-btn" onclick="location.href='schedule.php?today=true';">Today</button>
            <button class="all-btn" onclick="location.href='schedule.php';">All</button>
        </div>
    </div>
    <div class="faculties">
    <table class="faculty-table">
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
            <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $start_time = date("h:i A", strtotime($row['start_time']));
                    $end_time = date("h:i A", strtotime($row['end_time']));
                    $course_display = $row['course_code'] . ' - ' . $row['yr_and_block'];
                    echo "<tr>";
                    echo "<td>" . $course_display . "</td>"; 
                    echo "<td>" . $start_time . "</td>";
                    echo "<td>" . $end_time . "</td>";
                    echo "<td>" . $row['day_of_week'] . "</td>";
                    echo "<td>" . $row['subject'] . "</td>";
                    echo "<td>" . $row['from_month'] . "</td>";
                    echo "<td>" . $row['to_month'] . "</td>";
                    echo "<td>" . $row['year'] . "</td>";
                    echo "<td>" . $row['room_name'] . "</td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No schedules added yet.</td></tr>"; 
            }

            ?>
            </tbody>
        </table>
    </div>

    <?php else : ?>
<div class="faculty-container">
    <h1>No Schedules for Today</h1>
    <button class="all-btn" onclick="location.href='schedule.php';">All Schedules</button>
</div>
<?php endif; ?>

    <div id="success_message" class="success-message" <?php if(isset($success) && $success) echo 'style="display:block"'; ?>>
        <?php if(isset($success_message)) echo $success_message; ?>
    </div>

    <div class="button-download">
    <a href="generate_faculty_sched_pdf.php" class="buttones">Download Schedule List</a>
</div>




    <script>
        function confirmDelete(sched_id) {
            if (confirm("Are you sure you want to delete this schedule?")) {
                window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>?delete_id=" + sched_id;
            }
        }
    </script>

    <script>
        // Function to hide the success message after a few seconds
        $(document).ready(function(){
            setTimeout(function(){
                $("#success_message").fadeOut("slow");
            }, 500); // 1 seconds
        });
    </script>
    
</body>
</html>
