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


// Fetch the image path from the database based on the logged-in faculty
$image_path_query = "SELECT image FROM faculties WHERE faculty_id = $faculty_id";
$image_path_result = mysqli_query($conn, $image_path_query);

if ($image_path_result && mysqli_num_rows($image_path_result) > 0) {
    $image_row = mysqli_fetch_assoc($image_path_result);
    $profile_image = $image_row['image'];
} else {
    // Default image path if not found in the database
    $profile_image = "default.jpg";
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/prof.css">
  <link rel="stylesheet" href="css/all.min.css">
  <link rel="stylesheet" href="css/fontawesome.min.css">
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

      $(".status-button").click(function() {
        $(".status-button").removeClass("active"); 
        $(this).addClass("active");
        var status = $(this).val();
        $.post("<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>", {status: status}, function(data) {

        });
      });

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

<header class="itloog__container header__container" id="homealone">
  <div class="header__content">
    <h1><span class="welcome-text"><?php echo "Welcome back," ?><br><h1 class="dp"><?php echo $user_name . "!" ?></span></h1></h1>
  </div>
</header>
<header class="itloog__container header__search" id="homealone">
  <div class="header__content">
    
  </div>
 
</header>

<div id="objectives" class="department-objectives">
<h1>Department's Objectives</h1>
        <div class="box">
            <div class="obj">
                

                <h3>THE COMPUTER STUDIES DEPARTMENT IS TRUSTED TO CARRY OUT THE VISION AND MISSION <span class="bitnga"> OF BICOL UNIVERSITY. 
                   AS SUCH, THE DEPARTMENT IS COMMITTED TO:</span></h3>
        
                <p>1. PROVIDE HIGH-QUALITY IT EDUCATION IN DIFFERENT PROGRAMS OFFERED BY 
                  THE DEPARTMENT THROUGH BALANCED ACADEMIC AND NON-ACADEMIC ACTINITIES 
                  THAT WILL HELP IN THE HOLISTIC DEVELOPMENT OF THE FACULTY AND STUDENTS.</p>
        
                <p>2. ENCOURAGE FACULTY AND STUDENTS TO RESEARCH FOR THE ADVANCEMENT OF KNOWLEDGE.</p>
        
                <p>3. DEVELOP EXTENSION PROGRAMS RELEVANT TO THE UPLIFTMENT OF THE COMMUNITY'S
                   LIVING STANDARDS.</p>
        
                <p>4. PURSUE CONTINUOUS GROWTH AND QUALITY ASSURANCE OF DIFFERENT ACADEMIC PROGRAMS 
                  THROUGH ACCREDITATION.</p>
        
                <p>5. ESTABLISH LINKAGES WITH OTHER ACADEMIC INSTITUTIONS AND THE INDUSTRY.</p>

            </div>
        </div>
    </div>


<script>
  document.addEventListener("DOMContentLoaded", function() {
    var professorsLink = document.querySelector('a[href="#prof"]');

    professorsLink.addEventListener("click", function(event) {
      event.preventDefault(); 
      var element = document.getElementById("prof");
      element.scrollIntoView({ behavior: "smooth" }); 
    });
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

</html>