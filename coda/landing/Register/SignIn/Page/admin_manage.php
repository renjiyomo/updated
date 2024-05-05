<?php
include('tracking_db.php');
session_start();

if (!isset($_SESSION['admin_name']) || !isset($_SESSION['user_id'])) {
  header('Location: /coda/landing/Register/SignIn/signin.php');
  exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);

if ($result) {
  $user_row = mysqli_fetch_assoc($result);
  $user_name = $user_row['names'];
  $user_email = $user_row['email'];
  $user_password = $user_row['password'];
} else {
    $user_name = "User";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Handle form submission
  $new_name = $_POST['new_name'];
  $new_email = $_POST['new_email'];
  $new_password = $_POST['new_password'];

  // Check if a new image file has been uploaded
  if(isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
    $new_image_name = $_FILES['new_image']['name'];
    $new_image_tmp = $_FILES['new_image']['tmp_name'];
    $new_image_path = "imahe/profile/" . $new_image_name;
    
    // Move uploaded file to desired directory
    move_uploaded_file($new_image_tmp, $new_image_path);
  }

  // Check if any changes were made
  $name_changed = ($new_name != $user_name);
  $email_changed = ($new_email != $user_email);
  $password_changed = ($new_password != $user_password);

  // Update the database with new values
  if ($name_changed || $email_changed || $password_changed || isset($new_image_name)) {
    $update_query = "UPDATE users SET ";
    if ($name_changed) {
      $update_query .= "names = '$new_name', ";
    }
    if ($email_changed) {
      $update_query .= "email = '$new_email', ";
    }
    if ($password_changed) {
      $update_query .= "password = '$new_password', ";
    }
    if (isset($new_image_name)) {
      $update_query .= "image = '$new_image_name', ";
    }
    // Remove trailing comma and execute update query
    $update_query = rtrim($update_query, ", ");
    $update_query .= " WHERE user_id = $user_id";

    $update_result = mysqli_query($conn, $update_query);

    if ($update_result) {
      // Redirect to profile page or display success message
      header('Location: admin_manage.php');
      exit();
    } else {
      // Handle update error
    }
  }
}

$image_path_query = "SELECT image FROM users WHERE user_id = $user_id";
$image_path_result = mysqli_query($conn, $image_path_query);

if ($image_path_result && mysqli_num_rows($image_path_result) > 0) {
  $image_row = mysqli_fetch_assoc($image_path_result);
  $profile_image = $image_row['image'];
} else {
  $profile_image = "default.jpg";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/admin_manage.css" />
  <link rel="stylesheet" href="css/all.min.css" />
  <link rel="stylesheet" href="css/fontawesome.min.css" />
  <script src="js/jquery-3.4.1.min.js"></script>
  <title>Manage Account</title>
  
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
                <a class="profile" href="#"><span class="picon"><i class="fas fa-user-alt"></i></span><?php echo $user_name; ?></a>
              </li>
              <li><a class="manage" href="user_manage.php"><span class="picon"><i class="fa-solid fa-gear"></i></span>Manage Account</a></li>
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
      <div class="edit">
        <input type="file" id="new_image" name="new_image" accept="image/*" style="display: none;">
      
        <label for="new_name">Name:</label>
        <input type="text" id="new_name" name="new_name" required value="<?php echo $user_name; ?>"><br><br>

        <label for="new_email">Email:</label>
        <input type="email" id="new_email" name="new_email" required value="<?php echo $user_email; ?>"><br><br>

        <label for="new_password">Password:</label>
        <input type="password" id="new_password" name="new_password" required value="<?php echo $user_password; ?>"><br><br>

        <div class="button-container">
          <button type="submit">Save Changes</button>
          <a href="adminpage.php"><button type="button">Cancel</button></a>
        </div>
      </div>
    </div>
    </form>
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
