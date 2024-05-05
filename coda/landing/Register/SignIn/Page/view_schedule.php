<?php

date_default_timezone_set('Asia/Manila');

include('tracking_db.php');
session_start();

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
    if(isset($_POST["sched_id"]) && isset($_POST["course_id"]) && isset($_POST["yr"]) && isset($_POST["block"]) && isset($_POST["start_time"]) && isset($_POST["end_time"]) && isset($_POST["day_of_week"]) && isset($_POST["subject"]) && isset($_POST["from_month"]) && isset($_POST["to_month"]) && isset($_POST["room_id"]) && isset($_POST["year"])) {
        $sched_id = $_POST["sched_id"];
        $course_id = $_POST["course_id"];
        $yr = $_POST["yr"];
        $block = $_POST["block"];
        $start_time = $_POST["start_time"];
        $end_time = $_POST["end_time"];
        $day_of_week = $_POST["day_of_week"];
        $subject = $_POST["subject"];
        $from_month = $_POST["from_month"];
        $to_month = $_POST["to_month"];
        $room_id = $_POST["room_id"];
        $year = $_POST["year"];

        $yr_and_block = $yr . $block;

        $update_sql = "UPDATE schedules SET course_id = '$course_id',
                        start_time = '$start_time', end_time = '$end_time', day_of_week = '$day_of_week',
                        subject = '$subject', from_month = '$from_month', to_month = '$to_month', room_id = '$room_id',  yr_and_block = '$yr_and_block',
                        year = '$year' 
                        WHERE sched_id = '$sched_id'";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Your existing form processing code...
        
            if (mysqli_query($conn, $update_sql)) {
                // Set a flag to indicate successful update
                $success = true;
                $success_message = "Record updated successfully";
            } else {
                $success = false;
                $error_message = "Error updating record: " . mysqli_error($conn);
            }
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
    $current_day = $current_day_ph; // Use Philippine day instead
    $sql = "SELECT s.*, r.room_name, c.course_code 
            FROM schedules s 
            INNER JOIN rooms r ON s.room_id = r.room_id 
            INNER JOIN courses c ON s.course_id = c.course_id
            WHERE faculty_id = '$faculty_id' AND day_of_week = '$current_day'";
} else {
    // Default SQL query to show all schedules
    $sql = "SELECT s.*, r.room_name, c.course_code 
            FROM schedules s 
            INNER JOIN rooms r ON s.room_id = r.room_id 
            INNER JOIN courses c ON s.course_id = c.course_id
            WHERE faculty_id = '$faculty_id'
            ORDER BY sched_id ASC";
}

// Handle course selection filter
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['course_select'])) {
    $selected_course_id = $_POST['course_select'];
    if (!empty($selected_course_id)) {
        $sql = "SELECT s.*, r.room_name, c.course_code 
                FROM schedules s 
                INNER JOIN rooms r ON s.room_id = r.room_id 
                INNER JOIN courses c ON s.course_id = c.course_id
                WHERE faculty_id = '$faculty_id' AND s.course_id = '$selected_course_id'";
    }
}

$image_path_query = "SELECT image FROM faculties WHERE faculty_id = $faculty_id";
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
    <link rel="stylesheet" href="css/view_sched.css" />
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


<?php if (!isset($_GET['today']) || (isset($_GET['today']) && mysqli_num_rows($result) > 0)) : ?>
    <div class="faculty-container">
    <h2>My Schedules</h2>
    
    <div class="button-container">
        <div class="select-course-container">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <select id="course_select" name="course_select">
                    <option value="">Select Course</option>
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
                </select>
                <button class="view" type="submit">Select</button>
            </form>
        </div>

        <div class="today-all-buttons">
            <button class="today-btn" onclick="location.href='view_schedule.php?today=true';">Today</button>
            <button class="all-btn" onclick="location.href='view_schedule.php';">All</button>
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
                <th>Actions</th> 
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
                    echo "<td>
        <button class='edit-btn' onclick=\"openPopup(" . $row['sched_id'] . ")\">Edit</button>
        <button class='delete-btn' onclick=\"confirmDelete(" . $row['sched_id'] . ")\">Delete</button>
    </td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No schedules added yet.</td></tr>"; 
            }

            ?>
            </tbody>
        </table>
    </div>
    <div class="button-download">
    <a href="generate_prof_schedule_pdf.php" class="buttones">Download Schedule List</a>
</div>

    <?php else : ?>
<div class="faculty-container">
    <h1>No Schedules for Today</h1>
    <button class="all-btn" onclick="location.href='view_schedule.php';">All Schedules</button>
</div>
<?php endif; ?>

    <div id="success_message" class="success-message" <?php if(isset($success) && $success) echo 'style="display:block"'; ?>>
        <?php if(isset($success_message)) echo $success_message; ?>
    </div>

    <div id="editPopup" class="popup">
        <div class="popup-content">
        
            <h2>Edit Schedule</h2>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

            <input type="hidden" id="sched_id" name="sched_id"> 

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

            <label for="yr_block">Yr and Block:</label>
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
                for ($year = $current_year; $year <= $current_year + 5; $year++) {
                    echo "<option value='$year'>$year</option>";
                }
                ?>
            </select><br><br>

            <label for="room">Room:</label>
            <select id="room" name="room_id" required>
                <?php echo $room_options; ?>
            </select><br><br>

            <button type="submit">Submit</button>
            <button type="button" onclick="closePopup()">Close</button>
        </form>
        </div>
    </div>


    <script>
        function openPopup(sched_id) {

            var popup = document.getElementById('editPopup');
            popup.style.display = 'block';

            document.getElementById('sched_id').value = sched_id;

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var schedule = JSON.parse(xhr.responseText);

                    document.getElementById('course').value = schedule.course_id;
                    document.getElementById('yr').value = schedule.yr_and_block.charAt(0); // Extract year from year_and_block
                    document.getElementById('block').value = schedule.yr_and_block.charAt(1); // Extract block from year_and_block
                    document.getElementById('start_time').value = schedule.start_time;
                    document.getElementById('end_time').value = schedule.end_time;
                    document.getElementById('day_of_week').value = schedule.day_of_week;
                    document.getElementById('subject').value = schedule.subject;
                    document.getElementById('from_month').value = schedule.from_month;
                    document.getElementById('to_month').value = schedule.to_month;
                    document.getElementById('year').value = schedule.year;
                    document.getElementById('room').value = schedule.room_id;
                }
            };
            xhr.open("GET", "get_schedule_details.php?sched_id=" + sched_id, true);
            xhr.send();
        }

        function closePopup() {

            var popup = document.getElementById('editPopup');
            popup.style.display = 'none';
        }
    </script>

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