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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CSD Faculty</title>
  <link rel="stylesheet" href="css/profdashboard.css">
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
        <a href="profdashboard.php">Dashboard</a>
        <a href="add_sched.php">Add Schedule</a>
        <a href="view_schedule.php">View Schedule</a>
      </div>

      <div class="navbar_right">
        <div class="notifications"></div>
        <button class="status-button" value="in">In</button>
        <button class="status-button" value="ou">Out</button>
        <button class="status-button" value="oc">On Class</button>
        <div class="profile">
          <div class="icon_wrap">
            <img src="imahe/profile/icon.png" alt="profile_pic">
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

<div class="dashboard">
  <h1>Welcome, <?php echo $user_name; ?></h1>
</div>

</body>
</html>
