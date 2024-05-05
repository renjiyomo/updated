<?php
session_start();
session_destroy();

header('Location: /coda/landing/Register/SignIn/signin.php');
exit();
?>
