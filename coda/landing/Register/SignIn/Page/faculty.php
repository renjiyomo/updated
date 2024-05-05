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

// Handle faculty deletion
if (isset($_GET['delete_id'])) {
  $faculty_id = $_GET['delete_id'];
  $delete_query = "DELETE FROM faculties WHERE faculty_id = $faculty_id";
  $delete_result = mysqli_query($conn, $delete_query);
  if ($delete_result) {
    // Redirect to the same page to refresh the faculty list after deletion
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
  } else {
    echo "Error deleting faculty: " . mysqli_error($conn);
  }
}

// Handle form submission to add faculty
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $pass = $_POST['password'];
  $coordinator = $_POST['coordinator'];

  $insert_query = "INSERT INTO faculties (names, email, password, coordinator) VALUES ('$name', '$email', '$pass', '$coordinator')";
  $insert_result = mysqli_query($conn, $insert_query);
  if ($insert_result) {
    // Redirect to the same page to refresh the faculty list after insertion
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
  } else {
    echo "Error adding faculty: " . mysqli_error($conn);
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
  <link rel="stylesheet" href="css/faculty.css" />
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
                <li><a class="logout" href="logout.php"><span class="picon"><i class="fas fa-sign-out-alt"></i></span>Logout</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </nav>
  
<body>

<div class="popup" id="addFacultyPopup">
  
    <h3>Add Faculty</h3>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
  <label for="name">Name:</label>
  <input type="text" id="name" name="name" required><br><br>

  <label for="email">Email:</label>
  <input type="email" id="email" name="email" required><br><br>

  <label for="password">Password:</label>
  <input type="password" id="password" name="password" required><br><br>

  <label for="coordinator">Coordinator:</label>
  <input type="coordinator" id="coordinator" name="coordinator"><br><br>

  <button type="submit">Add</button>
  <button type="button" class="cancel-btn">Cancel</button>
</form>
  </div>

<div class="faculty-container">
    <h2>Faculty List</h2>
    <button class="add-faculty-btn">Add Faculty</button>
    <div class="table-header">
      <?php
      $query = "SELECT * FROM faculties";
      $result = mysqli_query($conn, $query);

      if (mysqli_num_rows($result) > 0) {
        echo '<table class="faculty-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Name</th>';
        echo '<th>Email</th>';
        echo '<th>Contact No.</th>';
        echo '<th>Coordinator</th>';
        echo '<th>Address</th>';
        echo '<th>Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        while ($row = mysqli_fetch_assoc($result)) {
          echo '<tr>';
          echo '<td>' . $row['faculty_id'] . '</td>';
          echo '<td>' . $row['names'] . '</td>';
          echo '<td>' . $row['email'] . '</td>';
          echo '<td>' . $row['contact_no'] . '</td>';
          echo '<td>' . $row['coordinator'] . '</td>';
          echo '<td>' . $row['address'] . '</td>';
          echo '<td><button class="delete-btn" onclick="confirmDelete(' . $row['faculty_id'] . ')">Delete</button></td>';
          echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
      } else {
        echo '<p>No faculties found.</p>';
      }
      ?>
    </div>
  </div>
  <div class="button-container">
    <a href="generate_faculty_pdf.php" class="button">Download Faculty List</a>
</div>


  <script>
    function confirmDelete(faculty_id) {
      if (confirm("Are you sure you want to delete this faculty?")) {
        window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>?delete_id=" + faculty_id;
      }
    }
  </script>

  <script>
    $(document).ready(function(){
      // Function to show the pop-up form
      $(".add-faculty-btn").click(function(){
        $("#addFacultyPopup").show();
      });

      // Function to close the pop-up form when cancel button is clicked
      $(".cancel-btn").click(function(){
        $("#addFacultyPopup").hide();
      });
    });
  </script>

</body>
</html>
