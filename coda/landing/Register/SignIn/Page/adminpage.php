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
  <link rel="stylesheet" href="css/admin.css" />
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
              <li><a class="manage" href="admin_manage.php"><span class="picon"><i class="fa-solid fa-gear"></i></span>Manage Account</a></li>
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
  <div class="header__image">
    <img src="imahe/torch.png" alt="header" />
  </div>
</header>

<div class="container" id="prof">
    <div class="row">
        <div class="tree">
            <h1>CSD FACULTY</h1>
            <ul>
                <li>
                    <div class="professor">
                    <div class="dropdown">
                        <a class="prof-mscs">
                          <img src="imahe/profile/prof12.png">
                          <span>
                            <?php
                               
                                $prof_query = "SELECT names FROM faculties WHERE faculty_id = 1"; 
                                $prof_result = mysqli_query($conn, $prof_query);
                                if ($prof_result && mysqli_num_rows($prof_result) > 0) {
                                    $prof_row = mysqli_fetch_assoc($prof_result);
                                    echo $prof_row['names'];
                                } else {
                                    echo "Professor 1";
                                }
                            ?>
                          </span>
                        </a>
                 
                            <div class="dropdown-menu">
                                <ul>
                                <?php
                                  date_default_timezone_set('Asia/Manila');

                                    $currentDayOfWeek = date("l");
                                    echo "Current Day of Week: " . $currentDayOfWeek;

                                    $sched_query = "SELECT s.subject, s.start_time, s.end_time, r.room_name 
                                                  FROM schedules s 
                                                  JOIN rooms r ON s.room_id = r.room_id
                                                  WHERE s.faculty_id = 1 AND s.day_of_week = '$currentDayOfWeek'";
                                    $sched_result = mysqli_query($conn, $sched_query);

                                    if ($sched_result && mysqli_num_rows($sched_result) > 0) {
                                        while ($sched_row = mysqli_fetch_assoc($sched_result)) {
                                            $start_time = date("h:ia", strtotime($sched_row['start_time']));
                                            $end_time = date("h:ia", strtotime($sched_row['end_time']));
                                    
                                            // Output each schedule as a row in the dropdown menu
                                            echo "<li><div class='schedule-row'>";
                                            echo "<span class='schedule-info'>" . $sched_row['subject'] . "</span>";
                                            echo "<span class='schedule-info'>" . $start_time . " - " . $end_time . "</span>";
                                            echo "<span class='schedule-info'>" . $sched_row['room_name'] . "</span>";
                                            echo "</div></li>";
                                        }
                                    } else {
                                      echo "<li><a class='no-schedule'>No schedule found for today</a></li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                </ul>
                <ul>
                <li>
                    <div class="professor">
                    <div class="dropdown">
                        <a class="prof-math" >
                          <img src="imahe/profile/prof8.png">
                          <span>
                            <?php
                                
                                $prof_query2 = "SELECT names FROM faculties WHERE faculty_id = 2";
                                $prof_result2 = mysqli_query($conn, $prof_query2);
                                if ($prof_result2 && mysqli_num_rows($prof_result2) > 0) {
                                    $prof_row2 = mysqli_fetch_assoc($prof_result2);
                                    echo $prof_row2['names'];
                                } else {
                                    echo "Professor 2";
                                }
                            ?>
                          </span>
                        </a>
                 
                            <div class="dropdown-menu">
                                <ul>
                                <?php
                                 date_default_timezone_set('Asia/Manila');
                                 
                                  $currentDayOfWeek = date("l");
                                  echo "Current Day of Week: " . $currentDayOfWeek;

                                  $sched_query = "SELECT s.subject, s.start_time, s.end_time, r.room_name 
                                                FROM schedules s 
                                                JOIN rooms r ON s.room_id = r.room_id
                                                WHERE s.faculty_id = 2 AND s.day_of_week = '$currentDayOfWeek'";
                                if ($sched_result && mysqli_num_rows($sched_result) > 0) {
                                  while ($sched_row = mysqli_fetch_assoc($sched_result)) {
                                      $start_time = date("h:ia", strtotime($sched_row['start_time']));
                                      $end_time = date("h:ia", strtotime($sched_row['end_time']));
                              
                                      // Output each schedule as a row in the dropdown menu
                                      echo "<li><div class='schedule-row'>";
                                      echo "<span class='schedule-info'>" . $sched_row['subject'] . "</span>";
                                      echo "<span class='schedule-info'>" . $start_time . " - " . $end_time . "</span>";
                                      echo "<span class='schedule-info'>" . $sched_row['room_name'] . "</span>";
                                      echo "</div></li>";
                                  }
                              } else {
                                echo "<li><a class='no-schedule'>No schedule found for today</a></li>";
                              }
                              ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="professor">
                    <div class="dropdown">
                    <a class="prof-mscs" >
                          <img src="imahe/profile/prof5.png">
                          <span>
                            <?php
 
                                $prof_query2 = "SELECT names FROM faculties WHERE faculty_id = 3"; 
                                $prof_result2 = mysqli_query($conn, $prof_query2);
                                if ($prof_result2 && mysqli_num_rows($prof_result2) > 0) {
                                    $prof_row2 = mysqli_fetch_assoc($prof_result2);
                                    echo $prof_row2['names'];
                                } else {
                                    echo "Professor 3";
                                }
                            ?>
                          </span>
                        </a>
                 
                            <div class="dropdown-menu">
                                <ul>
                                <?php
                                   date_default_timezone_set('Asia/Manila');

                                    $currentDayOfWeek = date("l");
                                    echo "Current Day of Week: " . $currentDayOfWeek;

                                    $sched_query = "SELECT s.subject, s.start_time, s.end_time, r.room_name 
                                                  FROM schedules s 
                                                  JOIN rooms r ON s.room_id = r.room_id
                                                  WHERE s.faculty_id = 3 AND s.day_of_week = '$currentDayOfWeek'";
                                    $sched_result = mysqli_query($conn, $sched_query);

                                    if ($sched_result && mysqli_num_rows($sched_result) > 0) {
                                        while ($sched_row = mysqli_fetch_assoc($sched_result)) {
                                            $start_time = date("h:ia", strtotime($sched_row['start_time']));
                                            $end_time = date("h:ia", strtotime($sched_row['end_time']));
                                    
                                            // Output each schedule as a row in the dropdown menu
                                            echo "<li><div class='schedule-row'>";
                                            echo "<span class='schedule-info'>" . $sched_row['subject'] . "</span>";
                                            echo "<span class='schedule-info'>" . $start_time . " - " . $end_time . "</span>";
                                            echo "<span class='schedule-info'>" . $sched_row['room_name'] . "</span>";
                                            echo "</div></li>";
                                        }
                                    } else {
                                      echo "<li><a class='no-schedule'>No schedule found for today</a></li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="professor">
                    <div class="dropdown">
                    <a class="prof-mscs" >
                          <img src="imahe/profile/prof10.png">
                          <span>
                            <?php
                                $prof_query2 = "SELECT names FROM faculties WHERE faculty_id = 4";
                                $prof_result2 = mysqli_query($conn, $prof_query2);
                                if ($prof_result2 && mysqli_num_rows($prof_result2) > 0) {
                                    $prof_row2 = mysqli_fetch_assoc($prof_result2);
                                    echo $prof_row2['names'];
                                } else {
                                    echo "Professor 4";
                                }
                            ?>
                          </span>
                        </a>
                 
                            <div class="dropdown-menu">
                                <ul>
                                <?php
                                  date_default_timezone_set('Asia/Manila');

                                  $currentDayOfWeek = date("l");
                                  echo "Current Day of Week: " . $currentDayOfWeek;

                                  $sched_query = "SELECT s.subject, s.start_time, s.end_time, r.room_name 
                                                FROM schedules s 
                                                JOIN rooms r ON s.room_id = r.room_id
                                                WHERE s.faculty_id = 4 AND s.day_of_week = '$currentDayOfWeek'";
                                  $sched_result = mysqli_query($conn, $sched_query);

                                  if ($sched_result && mysqli_num_rows($sched_result) > 0) {
                                    while ($sched_row = mysqli_fetch_assoc($sched_result)) {
                                        $start_time = date("h:ia", strtotime($sched_row['start_time']));
                                        $end_time = date("h:ia", strtotime($sched_row['end_time']));
                                
                                        // Output each schedule as a row in the dropdown menu
                                        echo "<li><div class='schedule-row'>";
                                        echo "<span class='schedule-info'>" . $sched_row['subject'] . "</span>";
                                        echo "<span class='schedule-info'>" . $start_time . " - " . $end_time . "</span>";
                                        echo "<span class='schedule-info'>" . $sched_row['room_name'] . "</span>";
                                        echo "</div></li>";
                                    }
                                } else {
                                  echo "<li><a class='no-schedule'>No schedule found for today</a></li>";
                                }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="professor">
                    <div class="dropdown">
                    <a class="prof-mscs">
                          <img src="imahe/profile/prof9.png">
                          <span>
                            <?php
                                $prof_query2 = "SELECT names FROM faculties WHERE faculty_id = 5";
                                $prof_result2 = mysqli_query($conn, $prof_query2);
                                if ($prof_result2 && mysqli_num_rows($prof_result2) > 0) {
                                    $prof_row2 = mysqli_fetch_assoc($prof_result2);
                                    echo $prof_row2['names'];
                                } else {
                                    echo "Professor 5";
                                }
                            ?>
                          </span>
                        </a>
                 
                            <div class="dropdown-menu">
                                <ul>
                                <?php
                                  date_default_timezone_set('Asia/Manila');

                                    $currentDayOfWeek = date("l");
                                    echo "Day of Week: " . $currentDayOfWeek;

                                    $sched_query = "SELECT s.subject, s.start_time, s.end_time, r.room_name 
                                                  FROM schedules s 
                                                  JOIN rooms r ON s.room_id = r.room_id
                                                  WHERE s.faculty_id = 5 AND s.day_of_week = '$currentDayOfWeek'";

                                    $sched_result = mysqli_query($conn, $sched_query);

                                    if ($sched_result && mysqli_num_rows($sched_result) > 0) {
                                        while ($sched_row = mysqli_fetch_assoc($sched_result)) {
                                            $start_time = date("h:ia", strtotime($sched_row['start_time']));
                                            $end_time = date("h:ia", strtotime($sched_row['end_time']));
                                    
                                            // Output each schedule as a row in the dropdown menu
                                            echo "<li><div class='schedule-row'>";
                                            echo "<span class='schedule-info'>" . $sched_row['subject'] . "</span>";
                                            echo "<span class='schedule-info'>" . $start_time . " - " . $end_time . "</span>";
                                            echo "<span class='schedule-info'>" . $sched_row['room_name'] . "</span>";
                                            echo "</div></li>";
                                        }
                                    } else {
                                      echo "<li><a class='no-schedule'>No schedule found for today</a></li>";
                                    }
                                    ?>

                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                </ul>
                <li>
                    <div class="professor">
                    <div class="dropdown">
                    <a class="prof-mscs" >
                          <img src="imahe/profile/default.jpg">
                          <span>
                            <?php
                                $prof_query2 = "SELECT names FROM faculties WHERE faculty_id = 6";
                                $prof_result2 = mysqli_query($conn, $prof_query2);
                                if ($prof_result2 && mysqli_num_rows($prof_result2) > 0) {
                                    $prof_row2 = mysqli_fetch_assoc($prof_result2);
                                    echo $prof_row2['names'];
                                } else {
                                    echo "Professor 6";
                                }
                            ?>
                          </span>
                        </a>
                 
                            <div class="dropdown-menu">
                                <ul>
                                <?php
                                  date_default_timezone_set('Asia/Manila');

                                  $currentDayOfWeek = date("l");
                                  echo "Current Day of Week: " . $currentDayOfWeek;

                                  $sched_query = "SELECT s.subject, s.start_time, s.end_time, r.room_name 
                                                FROM schedules s 
                                                JOIN rooms r ON s.room_id = r.room_id
                                                WHERE s.faculty_id = 6 AND s.day_of_week = '$currentDayOfWeek'";
                                  $sched_result = mysqli_query($conn, $sched_query);

                                  if ($sched_result && mysqli_num_rows($sched_result) > 0) {
                                    while ($sched_row = mysqli_fetch_assoc($sched_result)) {
                                        $start_time = date("h:ia", strtotime($sched_row['start_time']));
                                        $end_time = date("h:ia", strtotime($sched_row['end_time']));
                                
                                        // Output each schedule as a row in the dropdown menu
                                        echo "<li><div class='schedule-row'>";
                                        echo "<span class='schedule-info'>" . $sched_row['subject'] . "</span>";
                                        echo "<span class='schedule-info'>" . $start_time . " - " . $end_time . "</span>";
                                        echo "<span class='schedule-info'>" . $sched_row['room_name'] . "</span>";
                                        echo "</div></li>";
                                    }
                                } else {
                                  echo "<li><a class='no-schedule'>No schedule found for today</a></li>";
                                }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="professor">
                    <div class="dropdown">
                    <a class="prof-mscs" >
                          <img src="imahe/profile/default.jpg">
                          <span>
                            <?php
                                $prof_query2 = "SELECT names FROM faculties WHERE faculty_id = 7";
                                $prof_result2 = mysqli_query($conn, $prof_query2);
                                if ($prof_result2 && mysqli_num_rows($prof_result2) > 0) {
                                    $prof_row2 = mysqli_fetch_assoc($prof_result2);
                                    echo $prof_row2['names'];
                                } else {
                                    echo "Professor 7";
                                }
                            ?>
                          </span>
                        </a>
                 
                            <div class="dropdown-menu">
                                <ul>
                                <?php
                                  date_default_timezone_set('Asia/Manila');

                                  $currentDayOfWeek = date("l");
                                  echo "Current Day of Week: " . $currentDayOfWeek;

                                  $sched_query = "SELECT s.subject, s.start_time, s.end_time, r.room_name 
                                                FROM schedules s 
                                                JOIN rooms r ON s.room_id = r.room_id
                                                WHERE s.faculty_id = 7 AND s.day_of_week = '$currentDayOfWeek'";
                                  $sched_result = mysqli_query($conn, $sched_query);
                                  if ($sched_result && mysqli_num_rows($sched_result) > 0) {
                                    while ($sched_row = mysqli_fetch_assoc($sched_result)) {
                                        $start_time = date("h:ia", strtotime($sched_row['start_time']));
                                        $end_time = date("h:ia", strtotime($sched_row['end_time']));
                                
                                        // Output each schedule as a row in the dropdown menu
                                        echo "<li><div class='schedule-row'>";
                                        echo "<span class='schedule-info'>" . $sched_row['subject'] . "</span>";
                                        echo "<span class='schedule-info'>" . $start_time . " - " . $end_time . "</span>";
                                        echo "<span class='schedule-info'>" . $sched_row['room_name'] . "</span>";
                                        echo "</div></li>";
                                    }
                                } else {
                                  echo "<li><a class='no-schedule'>No schedule found for today</a></li>";
                                }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="professor">
                    <div class="dropdown">
                    <a class="prof-mscs" >
                          <img src="imahe/profile/default.jpg">
                          <span>
                            <?php
                                $prof_query2 = "SELECT names FROM faculties WHERE faculty_id = 8";
                                $prof_result2 = mysqli_query($conn, $prof_query2);
                                if ($prof_result2 && mysqli_num_rows($prof_result2) > 0) {
                                    $prof_row2 = mysqli_fetch_assoc($prof_result2);
                                    echo $prof_row2['names'];
                                } else {
                                    echo "Professor 8";
                                }
                            ?>
                          </span>
                        </a>
                 
                            <div class="dropdown-menu">
                                <ul>
                                <?php
                                  date_default_timezone_set('Asia/Manila');

                                  $currentDayOfWeek = date("l");
                                  echo "Current Day of Week: " . $currentDayOfWeek;

                                  $sched_query = "SELECT s.subject, s.start_time, s.end_time, r.room_name 
                                                FROM schedules s 
                                                JOIN rooms r ON s.room_id = r.room_id
                                                WHERE s.faculty_id = 8 AND s.day_of_week = '$currentDayOfWeek'";
                                  $sched_result = mysqli_query($conn, $sched_query);

                                  if ($sched_result && mysqli_num_rows($sched_result) > 0) {
                                    while ($sched_row = mysqli_fetch_assoc($sched_result)) {
                                        $start_time = date("h:ia", strtotime($sched_row['start_time']));
                                        $end_time = date("h:ia", strtotime($sched_row['end_time']));
                                
                                        // Output each schedule as a row in the dropdown menu
                                        echo "<li><div class='schedule-row'>";
                                        echo "<span class='schedule-info'>" . $sched_row['subject'] . "</span>";
                                        echo "<span class='schedule-info'>" . $start_time . " - " . $end_time . "</span>";
                                        echo "<span class='schedule-info'>" . $sched_row['room_name'] . "</span>";
                                        echo "</div></li>";
                                    }
                                } else {
                                  echo "<li><a class='no-schedule'>No schedule found for today</a></li>";
                                }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="professor">
                    <div class="dropdown">
                    <a class="prof-mscs" >
                          <img src="imahe/profile/default.jpg">
                          <span>
                            <?php
                                $prof_query2 = "SELECT names FROM faculties WHERE faculty_id = 9";
                                $prof_result2 = mysqli_query($conn, $prof_query2);
                                if ($prof_result2 && mysqli_num_rows($prof_result2) > 0) {
                                    $prof_row2 = mysqli_fetch_assoc($prof_result2);
                                    echo $prof_row2['names'];
                                } else {
                                    echo "Professor 9";
                                }
                            ?>
                          </span>
                        </a>
                 
                            <div class="dropdown-menu">
                                <ul>
                                <?php
                                  date_default_timezone_set('Asia/Manila');

                                  $currentDayOfWeek = date("l");
                                  echo "Current Day of Week: " . $currentDayOfWeek;

                                  $sched_query = "SELECT s.subject, s.start_time, s.end_time, r.room_name 
                                                FROM schedules s 
                                                JOIN rooms r ON s.room_id = r.room_id
                                                WHERE s.faculty_id = 9 AND s.day_of_week = '$currentDayOfWeek'";
                                  $sched_result = mysqli_query($conn, $sched_query);

                                  if ($sched_result && mysqli_num_rows($sched_result) > 0) {
                                    while ($sched_row = mysqli_fetch_assoc($sched_result)) {
                                        $start_time = date("h:ia", strtotime($sched_row['start_time']));
                                        $end_time = date("h:ia", strtotime($sched_row['end_time']));
                                
                                        // Output each schedule as a row in the dropdown menu
                                        echo "<li><div class='schedule-row'>";
                                        echo "<span class='schedule-info'>" . $sched_row['subject'] . "</span>";
                                        echo "<span class='schedule-info'>" . $start_time . " - " . $end_time . "</span>";
                                        echo "<span class='schedule-info'>" . $sched_row['room_name'] . "</span>";
                                        echo "</div></li>";
                                    }
                                } else {
                                  echo "<li><a class='no-schedule'>No schedule found for today</a></li>";
                                }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
              </ul>
                <ul>
                  <li>
                    <div class="professor">
                    <div class="dropdown">
                    <a class="prof-mscs" >
                          <img src="imahe/profile/default.jpg">
                          <span>
                            <?php
                                $prof_query2 = "SELECT names FROM faculties WHERE faculty_id = 10";
                                $prof_result2 = mysqli_query($conn, $prof_query2);
                                if ($prof_result2 && mysqli_num_rows($prof_result2) > 0) {
                                    $prof_row2 = mysqli_fetch_assoc($prof_result2);
                                    echo $prof_row2['names'];
                                } else {
                                    echo "Professor 10";
                                }
                            ?>
                          </span>
                        </a>
                 
                            <div class="dropdown-menu">
                                <ul>
                                <?php
                                  date_default_timezone_set('Asia/Manila');

                                  $currentDayOfWeek = date("l");
                                  echo "Current Day of Week: " . $currentDayOfWeek;

                                  $sched_query = "SELECT s.subject, s.start_time, s.end_time, r.room_name 
                                                FROM schedules s 
                                                JOIN rooms r ON s.room_id = r.room_id
                                                WHERE s.faculty_id = 10 AND s.day_of_week = '$currentDayOfWeek'";
                                  $sched_result = mysqli_query($conn, $sched_query);

                                  if ($sched_result && mysqli_num_rows($sched_result) > 0) {
                                    while ($sched_row = mysqli_fetch_assoc($sched_result)) {
                                        $start_time = date("h:ia", strtotime($sched_row['start_time']));
                                        $end_time = date("h:ia", strtotime($sched_row['end_time']));
                                
                                        // Output each schedule as a row in the dropdown menu
                                        echo "<li><div class='schedule-row'>";
                                        echo "<span class='schedule-info'>" . $sched_row['subject'] . "</span>";
                                        echo "<span class='schedule-info'>" . $start_time . " - " . $end_time . "</span>";
                                        echo "<span class='schedule-info'>" . $sched_row['room_name'] . "</span>";
                                        echo "</div></li>";
                                    }
                                } else {
                                  echo "<li><a class='no-schedule'>No schedule found for today</a></li>";
                                }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="professor">
                    <div class="dropdown">
                    <a class="prof-mscs" >
                          <img src="imahe/profile/default.jpg">
                          <span>
                            <?php
                                $prof_query2 = "SELECT names FROM faculties WHERE faculty_id = 11";
                                $prof_result2 = mysqli_query($conn, $prof_query2);
                                if ($prof_result2 && mysqli_num_rows($prof_result2) > 0) {
                                    $prof_row2 = mysqli_fetch_assoc($prof_result2);
                                    echo $prof_row2['names'];
                                } else {
                                    echo "Professor 11";
                                }
                            ?>
                          </span>
                        </a>
                 
                            <div class="dropdown-menu">
                                <ul>
                                <?php
                                  date_default_timezone_set('Asia/Manila');

                                  $currentDayOfWeek = date("l");
                                  echo "Current Day of Week: " . $currentDayOfWeek;

                                  $sched_query = "SELECT s.subject, s.start_time, s.end_time, r.room_name 
                                                FROM schedules s 
                                                JOIN rooms r ON s.room_id = r.room_id
                                                WHERE s.faculty_id = 11 AND s.day_of_week = '$currentDayOfWeek'";
                                  $sched_result = mysqli_query($conn, $sched_query);

                                  if ($sched_result && mysqli_num_rows($sched_result) > 0) {
                                    while ($sched_row = mysqli_fetch_assoc($sched_result)) {
                                        $start_time = date("h:ia", strtotime($sched_row['start_time']));
                                        $end_time = date("h:ia", strtotime($sched_row['end_time']));
                                
                                        // Output each schedule as a row in the dropdown menu
                                        echo "<li><div class='schedule-row'>";
                                        echo "<span class='schedule-info'>" . $sched_row['subject'] . "</span>";
                                        echo "<span class='schedule-info'>" . $start_time . " - " . $end_time . "</span>";
                                        echo "<span class='schedule-info'>" . $sched_row['room_name'] . "</span>";
                                        echo "</div></li>";
                                    }
                                } else {
                                  echo "<li><a class='no-schedule'>No schedule found for today</a></li>";
                                }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="professor">
                    <div class="dropdown">
                    <a class="prof-mscs">
                          <img src="imahe/profile/default.jpg">
                          <span>
                            <?php
                                $prof_query2 = "SELECT names FROM faculties WHERE faculty_id = 12";
                                $prof_result2 = mysqli_query($conn, $prof_query2);
                                if ($prof_result2 && mysqli_num_rows($prof_result2) > 0) {
                                    $prof_row2 = mysqli_fetch_assoc($prof_result2);
                                    echo $prof_row2['names'];
                                } else {
                                    echo "Professor 12";
                                }
                            ?>
                          </span>
                        </a>
                 
                            <div class="dropdown-menu">
                                <ul>
                                <?php
                                  date_default_timezone_set('Asia/Manila');

                                  $currentDayOfWeek = date("l");
                                  echo "Current Day of Week: " . $currentDayOfWeek;

                                  $sched_query = "SELECT s.subject, s.start_time, s.end_time, r.room_name 
                                                FROM schedules s 
                                                JOIN rooms r ON s.room_id = r.room_id
                                                WHERE s.faculty_id = 12 AND s.day_of_week = '$currentDayOfWeek'";
                                  $sched_result = mysqli_query($conn, $sched_query);

                                  if ($sched_result && mysqli_num_rows($sched_result) > 0) {
                                    while ($sched_row = mysqli_fetch_assoc($sched_result)) {
                                        $start_time = date("h:ia", strtotime($sched_row['start_time']));
                                        $end_time = date("h:ia", strtotime($sched_row['end_time']));
                                
                                        // Output each schedule as a row in the dropdown menu
                                        echo "<li><div class='schedule-row'>";
                                        echo "<span class='schedule-info'>" . $sched_row['subject'] . "</span>";
                                        echo "<span class='schedule-info'>" . $start_time . " - " . $end_time . "</span>";
                                        echo "<span class='schedule-info'>" . $sched_row['room_name'] . "</span>";
                                        echo "</div></li>";
                                    }
                                } else {
                                  echo "<li><a class='no-schedule'>No schedule found for today</a></li>";
                                }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                                  
                <li>
                    <div class="professor">
                    <div class="dropdown">
                    <a class="prof-mscs" >
                          <img src="imahe/profile/default.jpg">
                          <span>
                            <?php
                                $prof_query2 = "SELECT names FROM faculties WHERE faculty_id = 13";
                                $prof_result2 = mysqli_query($conn, $prof_query2);
                                if ($prof_result2 && mysqli_num_rows($prof_result2) > 0) {
                                    $prof_row2 = mysqli_fetch_assoc($prof_result2);
                                    echo $prof_row2['names'];
                                } else {
                                    echo "Professor 13";
                                }
                            ?>
                          </span>
                        </a>
                 
                            <div class="dropdown-menu">
                                <ul>
                                <?php
                                  date_default_timezone_set('Asia/Manila');

                                  $currentDayOfWeek = date("l");
                                  echo "Current Day of Week: " . $currentDayOfWeek;

                                  $sched_query = "SELECT s.subject, s.start_time, s.end_time, r.room_name 
                                                FROM schedules s 
                                                JOIN rooms r ON s.room_id = r.room_id
                                                WHERE s.faculty_id = 13 AND s.day_of_week = '$currentDayOfWeek'";
                                  $sched_result = mysqli_query($conn, $sched_query);

                                  if ($sched_result && mysqli_num_rows($sched_result) > 0) {
                                    while ($sched_row = mysqli_fetch_assoc($sched_result)) {
                                        $start_time = date("h:ia", strtotime($sched_row['start_time']));
                                        $end_time = date("h:ia", strtotime($sched_row['end_time']));
                                
                                        // Output each schedule as a row in the dropdown menu
                                        echo "<li><div class='schedule-row'>";
                                        echo "<span class='schedule-info'>" . $sched_row['subject'] . "</span>";
                                        echo "<span class='schedule-info'>" . $start_time . " - " . $end_time . "</span>";
                                        echo "<span class='schedule-info'>" . $sched_row['room_name'] . "</span>";
                                        echo "</div></li>";
                                    }
                                } else {
                                  echo "<li><a class='no-schedule'>No schedule found for today</a></li>";
                                }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                              </div>

                              <div class="status" id="status">
                <h1>Faculty Status</h1>

<table border="1">
<thead>
    <tr>
      <th>Faculty Name</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
  <?php

include('tracking_db.php');

$query = "SELECT names, status FROM faculties";


$result = mysqli_query($conn, $query);


if ($result && mysqli_num_rows($result) > 0) {

  while ($row = mysqli_fetch_assoc($result)) {

    switch ($row['status']) {
      case 'ou':
        $status = 'OUT';
        $color = 'red'; 
        break;
      case 'in':
        $status = 'IN';
        $color = 'green'; 
        break;
      case 'oc':
        $status = 'ON CLASS';
        $color = 'blue'; 
        break;
      default:
        $status = 'Unknown';
        $color = 'black'; 
        break;
    }

    echo "<tr>";
    echo "<td>" . $row['names'] . "</td>";
    echo "<td style='color: $color;'>" . $status . "</td>";
    echo "</tr>";
  }
} else {

  echo "<tr><td colspan='2'>No faculty data available</td></tr>";
}

mysqli_close($conn);
?>

  </tbody>
</table>
                
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
    $(document).ready(function(){
        $(".prof-mscs, .prof-math").hover(function(){
            $(this).siblings(".dropdown-menu").toggle();
        });
    });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function() {

    var professorsLink = document.querySelector('a[href="#prof"]');
    var statusLink = document.querySelector('a[href="#status"]');

    professorsLink.addEventListener("click", function(event) {
      event.preventDefault(); 
      var element = document.getElementById("prof");
      element.scrollIntoView({ behavior: "smooth" }); 
    });

    statusLink.addEventListener("click", function(event) {
      event.preventDefault(); 
      var element = document.getElementById("status");
      element.scrollIntoView({ behavior: "smooth" }); 
    });
  });
</script>

<script>
$(document).ready(function() {

  $('#searchInput').on('keyup click', function() {
    var query = $(this).val();
    searchSuggestions(query);
  });

  $(document).on('click', function(event) {
    if (!$(event.target).closest('#searchInput').length) {
      $('#searchSuggestions').empty();
    }
  });
});

function searchSuggestions(query) {
  $.ajax({
    url: 'StudentPage.php',
    method: 'POST',
    data: { query: query },
    success: function(response) {
      $('#searchSuggestions').html(response);
    }
  });
}

</script>

<script>
  document.addEventListener("DOMContentLoaded", function() {

    var professorsLink = document.querySelector('a[href="#prof"]');
    var statusLink = document.querySelector('a[href="#status"]');

    professorsLink.addEventListener("click", function(event) {
      event.preventDefault(); 
      var element = document.getElementById("prof");
      element.scrollIntoView({ behavior: "smooth" }); 
    });

    statusLink.addEventListener("click", function(event) {
      event.preventDefault(); 
      var element = document.getElementById("status");
      element.scrollIntoView({ behavior: "smooth" }); 
    });
  });
</script>

</body>
</html>
