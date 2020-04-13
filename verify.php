<?php

session_start();
  $username=$_SESSION['login_user'];
  //$password=$_SESSION['login_pass'];
  //$email=$_SESSION['login_email']."@cadillacjack.com";
  if(!isset($username))
  {
    header("location: login.php");
  }
?>