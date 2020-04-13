<?php
include('verify.php');
include('siteFuncs.php');
include('calculateGADate.php');
//if(!isSQAuser($username) || !isAdmin($username))
//{
//    header("location: ProjectDetails.php");
//}
$link=sqlConnect();
$name = mysqli_real_escape_string($link,$_POST['name']);
$type = mysqli_real_escape_string($link,$_POST['type']);
$class = mysqli_real_escape_string($link,$_POST['class']);
$complexity = mysqli_real_escape_string($link,$_POST['complexityID']);
$handoff = mysqli_real_escape_string($link,convertToSQLDate($_POST['handoff']));
$details = mysqli_real_escape_string($link,$_POST['details']);
$studioName = mysqli_real_escape_string($link,$_POST['studioname']);
//Get the estimated start date: get start date default value from GADuration table
$sql = "SELECT sqaStart FROM GADuration where complexityID=$complexity";
if($results=mysqli_query($link,$sql))
{
	$row=mysqli_fetch_array($results,MYSQLI_ASSOC);
	$start=$row['sqaStart'];//Days to add to handoff date
}
$end = calculateEstimatedStart($handoff,$start);

//Set actual start date to the same value as estimated start date.
//This will later be updated when the 1st revision of the project is added.
$actualStart=$end;

//Get the estimated completion days from GADuration table
$sql = "SELECT sqaComplete FROM GADuration where complexityID=$complexity";
if($results=mysqli_query($link,$sql))
{
        $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
        $sqaComp=$row['sqaComplete'];//Days to add to estimated sqa completion date
}
//Calculate estimated sqa completion date
$estComplete=calculateEstimatedCompletion($end,$sqaComp);

//Calculate estimated ITL complete date
$sql = "SELECT itlComplete FROM GADuration where complexityID=$complexity";
if($results=mysqli_query($link,$sql))
{
        $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
        $itlComp=$row['itlComplete'];//Days to add to estimated itl completion date
}
$estITL=calculateEstimatedITLComplete($estComplete,$itlComp);

//Actual ITL complete date - will be the same as estimated itl date initially
$actITL=$estITL;

//Add a history entry for this new project
newProjectHistory($username,$name,$link,"NewProject");

//Actual SQA complete will take estimated sqa completes' value when the project is created
$sql = "INSERT INTO Projects (ProjectName,ProjectType,Class,HandoffDate,AdditionalProjectDetails,EstimatedStartDate,complexityID,actualStartDate,estSQAComplete,actualSQAComplete,estITLComplete,actualITLComplete,studio)
        VALUES('$name','$type','$class','$handoff','$details','$end','$complexity','$actualStart','$estComplete','$estComplete','$estITL','$actITL','$studioName')";
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

		header("location: ProjectQueue.php");
		mysqli_close($link);
	//}
}

//FUNCTIONS------------------------------------
function newProjectHistory($user, $pName, $link, $changeType)
{
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectHistory table if a new project was created
        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,projNameNew)
                VALUES('N/A','$user','$date','$time','$changeType','$pName')";
        if(!mysqli_query($link,$sql))
        {
        	echo("Error description: " . mysqli_error($link));
               	mysqli_close($link);
        }
        else
        {
        	header("location: Projects.php");
                                //mysqli_close($link);
        }
}
?>
