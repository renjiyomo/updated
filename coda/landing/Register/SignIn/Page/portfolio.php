<?php
session_start();
include('tracking_db.php');

if (!isset($_SESSION['user_name']) || !isset($_SESSION['user_id'])) {
  header('Location: /coda/landing/Register/SignIn/signin.php');
  exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT names FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);

if ($result) {
  $user_row = mysqli_fetch_assoc($result);
  $user_name = $user_row['names'];
} else {
  $user_name = "User";
}

$image_path_query = "SELECT image FROM users WHERE user_id = $user_id";
$image_path_result = mysqli_query($conn, $image_path_query);

if ($image_path_result && mysqli_num_rows($image_path_result) > 0) {
  $image_row = mysqli_fetch_assoc($image_path_result);
  $profile_image = $image_row['image'];
} else {
  $profile_image = "default.jpg";
}

if (!isset($_GET['professor_id'])) {
  header('Location: studentpage.php');
  exit();
}

$faculty_id = $_GET['professor_id']; 

$query = "SELECT * FROM faculties WHERE faculty_id = $faculty_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
  $faculty_row = mysqli_fetch_assoc($result);
  $faculty_name = $faculty_row['names'];
  $faculty_coordinator = $faculty_row['coordinator'];
  $faculty_contact = $faculty_row['contact_no'];
  $faculty_email = $faculty_row['email'];
  $faculty_address = $faculty_row['address'];
  $faculty_image = $faculty_row['image'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Portfolio</title>
  <link rel="stylesheet" href="css/portfolio.css">
  <link rel="stylesheet" href="css/all.min.css">
  <link rel="stylesheet" href="css/fontawesome.min.css">
  <script src="js/jquery-3.4.1.min.js"></script>
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
});
  </script>
</head>
<body>
  <nav>  
    <div class="wrapper">
      <nav class="navbar">
        <div class="navbar_left">
          <div class="nav__logo">
            <a href="#"><img class="fast" src="imahe/FAST.png" alt="logo" /></a>
          </div>
        </div>
        <div class="navbar_center_text">
          <a href="StudentPage.php">Home</a>
        </div>
        <div class="navbar_right">
          <div class="notifications"></div>
          <div class="profile">
            <div class="icon_wrap">
              <img src="imahe/profile/<?php echo $profile_image; ?>" alt="profile_pic">
            </div>
            <div class="profile_dd">
              <ul class="profile_ul">
                <li class="profile_li">
                  <a class="profile" ><span class="picon"><i class="fas fa-user-alt"></i></span><?php echo $user_name; ?></a>
                </li>
                <li><a class="manage" href="user_manage.php"><span class="picon"><i class="fas fa-cog"></i></span>Manage Account</a></li>
                <li><a class="logout" href="logout.php"><span class="picon"><i class="fas fa-sign-out-alt"></i></span>Logout</a></li>
              </ul>
            </div>
          </div>
        </div>
      </nav>
    </div>
  </nav>
  
  <div class="container">
    <div class="profile-card"> 
      <div class="profile-pic">
        <img src="imahe/profile/<?php echo $faculty_image; ?>" alt="user avatar">
      </div>
      <div class="profile-details">
        <div class="intro">
          <h2><?php echo $faculty_name; ?></h2>
          <h4><?php echo $faculty_coordinator; ?></h4>
        </div>
        <div class="contact-info">
          <div class="row">
            <div class="icon">
              <i class="fa fa-phone" style="color:var(--light-green)"></i>
            </div>
            <div class="content">
              <span>Contact No.</span>
              <h5><?php echo $faculty_contact; ?></h5>
            </div>
          </div>
          <div class="row">
            <div class="icon">
              <i class="fa fa-envelope-open" style="color:var(--light-green)"></i>
            </div>
            <div class="content">
              <span>Email</span>
              <h5><?php echo $faculty_email; ?></h5>
            </div>
          </div>
          <div class="row">
            <div class="icon">
              <i class="fa fa-map-marker" style="color:var(--light-purple)"></i>
            </div>
            <div class="content">
              <span>Address</span>
              <h5><?php echo $faculty_address; ?></h5>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="contents">
      <h1>Schedules</h1>
      <?php
      $schedule_query = "SELECT s.*, r.room_name, c.course_code, CONCAT(c.course_code, ' - ', s.yr_and_block) AS course_and_block FROM schedules s 
                         INNER JOIN rooms r ON s.room_id = r.room_id 
                         INNER JOIN courses c ON s.course_id = c.course_id 
                         WHERE s.faculty_id = $faculty_id";
     
      $schedule_result = mysqli_query($conn, $schedule_query);
     
      if ($schedule_result && mysqli_num_rows($schedule_result) > 0) {
      ?>
      <button class="all-button" onclick="showAllSchedules()">ALL</button>
      <button class="today-button" onclick="showTodaySchedules()">Today</button>
      <div class="about">
        <table id="schedulesTable">
          <tr>
            <th>Course - Yr&Block</th>
            <th>Time</th>
            <th>Day of Week</th>
            <th>Subject</th>
            <th>Room Name</th>
          </tr>
          <?php
          while ($row = mysqli_fetch_assoc($schedule_result)) {
            $start_time = date("h:i A", strtotime($row['start_time']));
            $end_time = date("h:i A", strtotime($row['end_time']));
         
            echo '<tr>';
            echo '<td>' . $row['course_and_block'] . '</td>';
            echo '<td>' . $start_time . ' - ' . $end_time . '</td>';
            echo '<td>' . $row['day_of_week'] . '</td>';
            echo '<td>' . $row['subject'] . '</td>';
            echo '<td>' . $row['room_name'] . '</td>'; 
            echo '</tr>';
          }
          ?>
        </table>
      </div>
      <?php
      } else {
        echo '<p>No schedules found.</p>';
      }
      ?>
    </div>   
  </div>
  <script>
   function showTodaySchedules() {
  var today = new Date().getDay(); // Get the current day (0 for Sunday, 1 for Monday, ..., 6 for Saturday)
  var table = document.getElementById("schedulesTable");
  var rows = table.getElementsByTagName("tr");
  for (var i = 1; i < rows.length; i++) {
    var row = rows[i];
    var dayOfWeekCell = row.cells[2];
    var dayOfWeek = dayOfWeekCell.textContent.trim();
    var dayAbbreviation = getDayAbbreviation(today); // Get the abbreviation for the current day
    
    // Check if the row belongs to the current page
    if (row.style.display !== "none") {
      if (dayOfWeek === dayAbbreviation) { // Compare with the abbreviation of the current day
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    }
  }
}
    function getDayAbbreviation(dayIndex) {
      var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
      return days[dayIndex];
    }
  </script>

<script>
    function showAllSchedules() {
      var table = document.getElementById("schedulesTable");
      var rows = table.getElementsByTagName("tr");
      for (var i = 1; i < rows.length; i++) {
        var row = rows[i];
        if (row.style.display === "none") {
          row.style.display = "";
        }
      }
    }
  </script>
</body>
</html>

<?php
} // Close the if ($result && mysqli_num_rows($result) > 0) block
?>
