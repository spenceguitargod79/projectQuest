<?php
include('verify.php');
include('siteFuncs.php');
include('calculateGADate.php');
$link=sqlConnect();

//PURPOSE: Add a user entered note to the database

//Notes related stuff
$pid = mysqli_real_escape_string($link,$_POST['projectid']);
$note = mysqli_real_escape_string($link,$_POST['projectnote']);
$user = mysqli_real_escape_string($link,$_POST['user']);

$date = date("Y-m-d");//will return ex: 2018-08-02
$time = date("h:i:sa");// will return ex: 05:22:29pm
$dateTime = $date." ".$time;
//writeToLog("Date = ".$date." ");
//writeToLog("Time = ".$time." ");
//writeToLog("DateTime = ".$dateTime." ");

$sql = "INSERT INTO ProjectNotes (projectID,username,note,timestamp)
        VALUES('$pid','$user','$note','$dateTime')";
if(!mysqli_query($link,$sql))
{
	echo("Error description: " . mysqli_error($link));
	mysqli_close($link);
}
else
{
	//$sql = "DELETE from eps where name='".$name."' and currentTestID!='".$id."'";
	//if(!mysqli_query($link,$sql))
	//{
	//	echo("Error description: " . mysqli_error($link));
	//	mysqli_close($link);
	//}
	//else
	//{
		//logger($name,"Configuration Changed By: ".$username);
		//logger($name,"Name:".$name);			
	//	logger($name,"Class:".$class);			
	//	logger($name,"IP Address:".$ipaddress);			
	//	logger($name,"Cabinet Type:".$cabinetType);			
	//	logger($name,"Game Title:".$gameTitle);			
	//	logger($name,"Legal Config:".$LC);			
	//	logger($name,"Player Version:".$playerVersion);			
	//	logger($name,"Player Revision:".$playerRevision);			
	//	logger($name,"Var Install Date:".$varInstallDate);			
	//	logger($name,"Bill Acceptor:".$billAcceptor);			
	//	logger($name,"Printer:".$printer);			
	//	logger($name,"Mother Board:".$motherboardRev);
	//	logger($name,"Video Card:".$videoCard);
	//	logger($name,"Camera ID:".$camid);
	//	logger($name,"User:".$epsUser);
	//	logger($name,"User Email:".$userEmail);
		//emailGroup("EPS ".$name." Edited/Added by ".$username);

		header("location: Projects.php");
		mysqli_close($link);
	//}
}
?>
