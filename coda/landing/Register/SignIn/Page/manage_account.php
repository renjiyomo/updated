<?php
include('tracking_db.php');
session_start();

if (!isset($_SESSION['prof_name']) || !isset($_SESSION['faculty_id'])) {
  header('Location: /coda/landing/Register/SignIn/signin.php');
  exit();
}

$faculty_id = $_SESSION['faculty_id'];
$query = "SELECT * FROM faculties WHERE faculty_id = $faculty_id";
$result = mysqli_query($conn, $query);

if ($result) {
  $user_row = mysqli_fetch_assoc($result);
  $user_name = $user_row['names'];
  $contact_no = $user_row['contact_no'];
  $email = $user_row['email'];
  $pass = $user_row['password'];
  $coordinator = $user_row['coordinator'];
  $address = $user_row['address'];
} else {
  // Handle error
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Handle form submission
  $new_name = $_POST['new_name'];
  $new_contact_no = $_POST['new_contact_no'];
  $new_email = $_POST['new_email'];
  $new_password = $_POST['new_password'];
  $new_coordinator = $_POST['new_coordinator']; 
  $new_address = $_POST['new_address'];

  // Check if a new image is uploaded
  if (!empty($_FILES['new_image']['name'])) {
    $new_image = $_FILES['new_image']['name'];
    $temp_image = $_FILES['new_image']['tmp_name'];
    $image_path = "imahe/profile/"; // Path where images are stored

    // Move uploaded file to the desired location
    move_uploaded_file($temp_image, $image_path . $new_image);
  } else {
    // Keep the existing image if no new image is uploaded
    $new_image = $user_row['image'];
  }

  // Update the database with new values including the image filename
  $update_query = "UPDATE faculties SET names = '$new_name', contact_no = '$new_contact_no', email = '$new_email', password = '$new_password', coordinator = '$new_coordinator', address = '$new_address', image = '$new_image' WHERE faculty_id = $faculty_id";
  $update_result = mysqli_query($conn, $update_query);
  
  if ($update_result) {
    // Redirect to profile page or display success message
    header('Location: manage_account.php');
    exit();
  } else {
    // Handle update error
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/manage_account.css" />
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
        <a href="profpage.php">Home</a>
        <a href="add_sched.php">Add Schedule</a>
        <a href="view_schedule.php">View Schedule</a>
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
              <li><a class="manage" href="manage_account.php"><span class="picon"><i class="fa-solid fa-gear"></i></span>Manage Account</a></li>
              <li><a class="logout" href="logout.php"><span class="picon"><i class="fas fa-sign-out-alt"></i></span>Logout</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </nav>
</nav>

<body>
  <h2>Manage Account</h2>
<div class="itloog__container">
  <form method="POST" action="" enctype="multipart/form-data">
    <div class="image-upload">
      <label for="new_image">
        <img id="preview_image" class="user-pic" src="imahe/profile/<?php echo $profile_image; ?>" alt="profile_pic">
        <span class="edit-text">Edit Profile</span>
      </label>
      <input type="file" id="new_image" name="new_image" accept="image/*" style="display: none;">
    </div>

  <div class="edit">
      <label for="new_name">Name:</label>
      <input type="text" id="new_name" name="new_name" required value="<?php echo $user_name; ?>"><br><br>

      <label for="new_contact_no">Contact Number:</label>
      <input type="text" id="new_contact_no" name="new_contact_no" value="<?php echo $contact_no; ?>"><br><br>

      <label for="new_email">Email:</label>
      <input type="email" id="new_email" name="new_email" required value="<?php echo $email; ?>"><br><br>

      <label for="new_password">Password:</label>
      <input type="password" id="new_password" name="new_password" value="<?php echo $pass; ?>"><br><br>

      <label for="new_coordinator">Coordinator:</label>
      <input type="coordinator" id="new_coordinator" name="new_coordinator" value="<?php echo $coordinator; ?>"><br><br>

      <label for="new_address">Address:</label>
      <input type="address" id="new_address" name="new_address" value="<?php echo $address; ?>"><br><br>

      <div class="button-container">
      <button type="submit">Save Changes</button>
      <a href="profpage.php"><button type="button">Cancel</button></a>
    </form>
  </div>
  </div>

  <script>
  // Function to display selected image
  function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function() {
      var preview = document.getElementById('preview_image');
      preview.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
  }

  // Add onchange event listener to the file input
  document.getElementById('new_image').addEventListener('change', previewImage);
</script>

</body>

</html>
