<?php
include('tracking_db.php');
session_start();

if (!isset($_SESSION['admin_name']) || !isset($_SESSION['user_id'])) {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/admin.css" />
  <link rel="stylesheet" href="css/all.min.css" />
  <link rel="stylesheet" href="css/fontawesome.min.css" />
  <script src="js/jquery-3.4.1.min.js"></script>
  <title>CSD Faculty</title>
  <script>
	$(document).ready(function(){
		$(".profile .icon_wrap").click(function(){
		  $(this).parent().toggleClass("active");
		  $(".notifications").removeClass("active");
		});

	
		$(".close").click(function(){
		  $(".popup").hide();
		});
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
            <img src="imahe/profile/icon.png" alt="profile_pic">
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

<header class="itloog__container header__container" id="homealone">
  <div class="header__content">
    <h1><span class="welcome-text"><?php echo "Welcome back " ?><br><h1 class="dp"><?php echo $_SESSION['admin_name'] . "!" ?></span></h1></h1>
  </div>
</header>


<header class="itloog__container header__search" id="homealone">
  <div class="header__content">
  <div class="header__image">
    <img src="imahe/torch.png" alt="header" />
  </div>
</header>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    // Find the "Professors" link
    var professorsLink = document.querySelector('a[href="#prof"]');

    // Add click event listener
    professorsLink.addEventListener("click", function(event) {
      event.preventDefault(); 
      var element = document.getElementById("prof");
      element.scrollIntoView({ behavior: "smooth" }); 
    });
  });
</script>

</body>
</html>
