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

$image_path_query = "SELECT image FROM users WHERE user_id = $user_id";
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
  <link rel="stylesheet" href="css/user.css" />
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

  <div class="container">
    <h2>User List</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($user_list as $user): ?>
          <tr>
            <td><?php echo $user['user_id']; ?></td>
            <td><?php echo $user['names']; ?></td>
            <td><?php echo $user['email']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="button-container">
  <a href="generate_pdf.php" class="button">Download User List</a>
</div>


</body>
</html>
