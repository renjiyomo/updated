<?php
session_start();
include('tracking_db.php');

if (!isset($_SESSION['prof_name']) || !isset($_SESSION['faculty_id'])) {
    header('Location: /coda/landing/Register/SignIn/signin.php');
    exit();
}

$faculty_id = $_SESSION['faculty_id'];
$query = "SELECT names, status FROM faculties WHERE faculty_id = $faculty_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user_row = mysqli_fetch_assoc($result);
    $user_name = $user_row['names'];
    $status = $user_row['status'];
} else {
    $user_name = "User";
    $status = "out"; // Set default status if not found
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status'])) {
    $status = $_POST['status'];
    if ($status === 'in_class') {
        $status = 'oc'; // Set status to 'On Class'
    }
    $sql = "UPDATE faculties SET status = '$status' WHERE faculty_id = $faculty_id";
    if ($conn->query($sql) === TRUE) {
        // Update successful
        $_SESSION['status'] = $status; // Update session variable with new status
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['action'] == 'add_sched') {
        $start_time = $_POST["start_time"];
        $end_time = $_POST["end_time"];
        $day_of_week = $_POST["day_of_week"];
        $subject = $_POST["subject"];
        $from_month = $_POST["from_month"];
        $to_month = $_POST["to_month"];
        $year = $_POST["year"]; 
        $room_id = $_POST["room_id"];
        $course_id = $_POST["course_id"]; 
        $yr = $_POST["yr"]; 
        $block = $_POST["block"]; 

        $yr_and_block = $yr . $block;

        $sql = "INSERT INTO schedules (faculty_id, course_id, start_time, end_time, day_of_week, subject, from_month, to_month, year, room_id, yr_and_block)
                VALUES ('$faculty_id', '$course_id', '$start_time', '$end_time', '$day_of_week', '$subject', '$from_month', '$to_month', '$year', '$room_id', '$yr_and_block')";

        if (mysqli_query($conn, $sql)) {
            // Set a flag to indicate successful insertion
            $success = true;
            $success_message = "New record created successfully";
        } else {
            $success = false;
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
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

// Array of month names
$months = array(
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
);

// Array of days of the week
$days_of_week = array(
    "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
);

$image_path_query = "SELECT image FROM faculties WHERE faculty_id = $faculty_id";
$image_path_result = mysqli_query($conn, $image_path_query);

if ($image_path_result && mysqli_num_rows($image_path_result) > 0) {
    $image_row = mysqli_fetch_assoc($image_path_result);
    $profile_image = $image_row['image'];
} else {
    // Default image path if not found in the database
    $profile_image = "default.jpg";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/add_sched.css" />
  <link rel="stylesheet" href="css/all.min.css" />
  <link rel="stylesheet" href="css/fontawesome.min.css" />
  <script src="js/jquery-3.4.1.min.js"></script>
  <title>CSD Faculty</title>
  <script>
   	$(document).ready(function(){
    $(".profile .icon_wrap").click(function(){
        $(this).parent().toggleClass("active");
    });

    $(".notifications .icon_wrap").click(function(){
        $(this).parent().toggleClass("active");
        $(".profile").removeClass("active");
    });


    $(document).on('click', function(event) {
        if (!$(event.target).closest('.profile').length) {
            $(".profile").removeClass("active");
        }

    });

    $(".close").click(function(){
        $(".popup").hide();
    });

      // Script to handle button clicks
      $(".status-button").click(function() {
        $(".status-button").removeClass("active"); // Remove active class from all buttons
        $(this).addClass("active"); // Add active class to the clicked button
        var status = $(this).val();
        $.post("<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>", {status: status}, function(data) {
          // Handle any response if needed
        });
      });

      // Set the initial active button based on PHP status variable
      var status = "<?php echo $status; ?>";
      $(".status-button[value='" + status + "']").addClass("active");
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
        <a href="profpage.php">Home</a>
        <a href="add_sched.php">Add Schedule</a>
        <a href="view_schedule.php">View Schedule</a>
      </div>

      <div class="navbar_right">
      <div class="status-text">Status</div>

<div class="status-buttons">
  <button class="status-button" value="in">In</button>
  <button class="status-button" value="ou">Out</button>
  <button class="status-button" value="oc">On Class</button>
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
              <li><a class="manage" href="manage_account.php"><span class="picon"><i class="fa-solid fa-gear"></i></span>Manage Account</a></li>
              <li><a class="logout" href="logout.php"><span class="picon"><i class="fas fa-sign-out-alt"></i></span>Logout</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </nav>
</nav>

<div class="form-container">
    <h2>Add Schedule</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="hidden" name="action" value="add_sched"> <!-- Hidden input field for action -->

        <label for="course">Course:</label>
        <select id="course" name="course_id" required>
            <?php
            $sql_courses = "SELECT * FROM courses";
            $result_courses = mysqli_query($conn, $sql_courses);
            if (mysqli_num_rows($result_courses) > 0) {
                while ($row = mysqli_fetch_assoc($result_courses)) {
                    $course_id = $row['course_id'];
                    $course_code = $row['course_code'];
                    echo "<option value='$course_id'>$course_code</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="yr_block">Year and Block:</label>
        <div class="yr-block-select">
            <select id="yr" name="yr" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
            <select id="block" name="block" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
            </select>
        </div>

        <label for="start_time">Start Time:</label>
        <input type="time" id="start_time" name="start_time" required><br><br>

        <label for="end_time">End Time:</label>
        <input type="time" id="end_time" name="end_time" required><br><br>

        <label for="day_of_week">Day of Week:</label>
        <select id="day_of_week" name="day_of_week" required>
            <?php
            foreach ($days_of_week as $day) {
                echo "<option value='$day'>$day</option>";
            }
            ?>
        </select><br><br>
  
        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" required><br><br>

        <label for="from_month">From Month:</label>
        <select id="from_month" name="from_month" required>
            <?php
            foreach ($months as $month) {
                echo "<option value='$month'>$month</option>";
            }
            ?>
        </select><br><br>

        <label for="to_month">To Month:</label>
        <select id="to_month" name="to_month" required>
            <?php
            foreach ($months as $month) {
                echo "<option value='$month'>$month</option>";
            }
            ?>
        </select><br><br>
        
        <label for="year">Year:</label>
        <select id="year" name="year" required>
            <?php
            $current_year = date('Y');
            for ($i = $current_year; $i <= $current_year + 5; $i++) {
                echo "<option value='$i'>$i</option>";
            }
            ?>
        </select><br><br>

        <label for="room">Room:</label>
        <select id="room" name="room_id" required>
            <?php echo $room_options; ?>
        </select><br><br>

        <button type="submit">Submit</button>
    </form>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    var statusButtons = document.querySelector(".status-buttons");
    var statusText = document.querySelector(".status-text");

    statusButtons.style.display = "none";

    function toggleStatusButtons() {
      if (statusButtons.style.display === "none") {
        statusButtons.style.display = "block";
        statusText.innerHTML = "<i class='fas fa-times'></i>"; 
      } else {
        statusButtons.style.display = "none";
        statusText.textContent = "Status"; 
      }
    }

    statusText.addEventListener("click", function(event) {
      event.stopPropagation(); 
      toggleStatusButtons();
    });

    document.addEventListener("click", function(event) {
      if (!statusText.contains(event.target) && !statusButtons.contains(event.target)) {
        statusButtons.style.display = "none";
        statusText.textContent = "Status"; 
      }
    });

    var statusButtonList = document.querySelectorAll(".status-button");
    statusButtonList.forEach(function(button) {
      button.addEventListener("click", function() {
        var status = this.value;
        console.log("Selected status:", status);

      });
    });
  });
</script>
</body>
</html>
