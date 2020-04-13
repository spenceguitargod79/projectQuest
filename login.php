<!DOCTYPE html>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="Stylesheet" href="styles.css" type="text/css" />

<title>SQA Project Tracking</title>

</head>

<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginheight="0" marginwidth="0" bgcolor="#FFFFFF">

<table border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#123456" height="100">
  <tr>
    <td width="100%">	
      <center><img src="images/ProjectTrackerRunner2.png" height="169" width="650"/></td></center>
  </tr>
</table>
<?php
  include('siteFuncs.php');
  session_start();
  if($_SERVER["REQUEST_METHOD"]=="POST")
  {
	 $username_upper=$_POST['email'];
	 $username=strtolower($username_upper);
     $password=$_POST['password'];
	if(checkCreds($username,$password))
	{
			$_SESSION['login_user']=$username;
			header("location: ProjectList.php");
	}
    else
    {
      displayForm(1);
    }
  }
  else
  {
    displayForm(0);
  }
  //Display login form
  function displayForm($error)
  {
    echo "<table width=100% height=50%>";
    echo "<tr>";
    echo "<td style=\"text-align: center; vertical-align: middle;\">";
    echo "<form action=\"\" method=\"post\">";
	echo "<br><br>";
	//echo "<img src=\"medusa.gif\">";
    echo "<br><br>";
	 echo "<label>Username:</label>";
    echo "<input type=\"text\" name=\"email\"/><br>";
    echo "<label>Password:</label>";
    echo "<input type=\"password\" name=\"password\"/><br>";
    echo "<input type=\"submit\" value=\"Login\"/>";
    echo "</form>";
    if($error)
    {
	echo "<b><font color=\"red\">Invalid Email/Password or Unapproved Guest</font></b>";
	echo "<b><font color=\"red\"><br>Use your windows credentials to login please</font></b>";
      //echo $first;
    }
   $imageOne = chooseRandImage();
   $imageTwo = chooseRandImage();
   $imageThree = chooseRandImage();
   //setCredits();
   //displayCredits();
   //addCredits(getCredits(),1000);
   //$imageTwo = $imageOne;
   //$imageThree = $imageOne;

    echo "<br>";
    echo '<img src="'.$imageOne.'" width="250" height="250" />';
    //echo '<img src="'.chooseRandImage().'" width="250" height="250" />';
    echo '<img src="'.$imageTwo.'" width="250" height="250" />';
    echo '<img src="'.$imageThree.'" width="250" height="250" />';
    echo "</td>";
    echo "</tr>";

    echo "<td>";
    $displayResult=imageMatch($imageOne,$imageTwo,$imageThree);
    //echo $displayResult;
    echo "</td>";

    echo "</table>";

    if($displayResult == "0"){
         //echo '<center><img src="images/creepyChewy.gif" width="250" height="250" /></center>';
    }
    else if($displayResult == "2"){
	echo '<center><img src="images/winner-1.gif" width="250" height="250" /></center>';
    }
    else if($displayResult == "3"){
        echo '<center><img src="images/chewyCool.gif" width="250" height="250" /></center>';
    }
  }
function checkCreds($username,$password)
{
	$ldaphost = "atmssv-addc14.ags.local";//LDAP server
	$user=$username;
	$username.="@playags.com";

// Connecting to LDAP

	if(empty($password))
	{
		return false;
	}
	$ldapconn = ldap_connect($ldaphost)
        	or die("Could not connect to $ldaphost");
		  
	if($ldapconn)
	{
		if($bind=@ldap_bind($ldapconn,$username,$password))
		{
			return true;
			/*if(isAdmin($user) || isSQAuser($user))
			{
				if($password=="test4success")
				{
					return true;
				}
				else
				{
					return false;
				}
					
			}
			else if(isGuest($user))
			{
				if($password=="justl00king"){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}*/
		}
		else
		{
			//echo "ERROR CONNECTING TO LDAP!";
			return false;
		}
	}
}
//Pick a random image and return its location/name
function chooseRandImage(){

	$images = array("images/Cherries.png", "images/bufflo5.png", "images/bufflo3.png", "images/SoHot.png", "images/jewels.png",
			"images/yingYang.png", "images/emFairyGirl.png","images/ladySilk.png", "images/aztec-logo.png", "images/chili-scatter.png",
			"images/aztec-bonus.png", "images/chili-wild.png", "images/fierce-factor.png", "images/hot-sauce.png", "images/power-xtreme.png", "images/scarlett.png");

	//generate a random number
	$randomNum = rand(0,count($images)-1);

	return $images[$randomNum];
}

function chooseRandGif(){
	//$gifs = array("images/badA");
}

function imageMatch($a, $b, $c){
 	if($a == $b && $a == $c && $b == $c){
                //echo "ALL IMAGES MATCH!";
		return "3";
        }
	else if($a == $b){
		//echo "Image 1 and 2 match!";
		return "2";
	}
	else if($a == $c){
		//echo "Image 1 and 3 match!";
		return "2";
	}
	else if($b == $c){
		//echo "Image 2 and 3 match!";
		return "2";
	}
	else{
		//echo "There aren't any matches, sorry :^(";
		return "0";
	}
}

function setCredits(){
	$GLOBALS['creds'] = 10000;
}

function getCredits(){
	return $GLOBALS['creds'];
}

function addCredits($credits,$amt){
        $GLOBALS['creds'] = $credits + $amt;
}

function subtractCredits($credits,$amt){
	$GLOBALS['creds'] = $credits - $amt;
}

function noCreditCheck(){
	if($GLOBALS['creds'] <= 0){
		return true;
	}
	else{
		return false;
	}
}

function displayCredits(){
	echo "CREDITS: ".$GLOBALS['creds'];
}

?>
</body>
