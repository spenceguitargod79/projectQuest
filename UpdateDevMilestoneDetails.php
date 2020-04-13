<?php
include('verify.php');
include('siteFuncs.php');
//if(!isSQAuser($username))
//{
  //  header("location: ProjectDetails.php");
//}
$link=sqlConnect();

$projectid = mysqli_real_escape_string($link,$_POST['projid']);
$milestoneid = mysqli_real_escape_string($link,$_POST['milestoneid']);
$name = mysqli_real_escape_string($link,$_POST['name']);
$desc = mysqli_real_escape_string($link,$_POST['description']);
$owner = mysqli_real_escape_string($link,$_POST['assignee']);
$start = mysqli_real_escape_string($link,convertToSQLDate($_POST['startdate']));
$end = mysqli_real_escape_string($link,convertToSQLDate($_POST['enddate']));
$priority = mysqli_real_escape_string($link,$_POST['priority']);
$ver = mysqli_real_escape_string($link,$_POST['version']);
$status = mysqli_real_escape_string($link,$_POST['status']);
$notes = mysqli_real_escape_string($link,$_POST['notes']);

$link=sqlConnect();

$sql = "REPLACE INTO DevMilestones (projectID,milestoneID,name,description,assignee,startDate,deployedDate,priority,version,status,notes)
        VALUES('$projectid','$milestoneid','$name','$desc','$owner','$start','$end','$priority','$ver','$status','$notes')";
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
		header("location: DevProjectsList.php");
		mysqli_close($link);
	//}
}

?>
