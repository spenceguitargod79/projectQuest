<?php
include('verify.php');
include('siteFuncs.php');
include('calculateGADate.php');
//if(!isSQAuser($username))
//{
 //   header("location: ProjectDetails.php");
//}
$link=sqlConnect();

$projectID = mysqli_real_escape_string($link,$_POST['ProjectID']);
$revision = mysqli_real_escape_string($link,$_POST['revision']);
$owner = mysqli_real_escape_string($link,$_POST['owner']);
$start = mysqli_real_escape_string($link,$_POST['StartDate']);
$start = convertDateFormat($start, "Y/m/d");//convert start date to save in best format for table
$end = mysqli_real_escape_string($link,convertToSQLDate($_POST['end']));
$status = mysqli_real_escape_string($link,$_POST['status']);
$notes = mysqli_real_escape_string($link,$_POST['notes']);

$link=sqlConnect();

//Used to pass into a function below
$r = isDuplicateRevision($link,$revision,$projectID);

//If no revisions exist for this project yet, copy start date to actual start date and save to db
if(oneRevisionExists($projectID,$link) || multipleRevisionsExist($projectID,$link))
{
	//writeToLog("One or more revisions exist for project id ".$projectID.", so actual start date will not be overridden at this time.");
}
else
{
	//Set 'actual start date' from Projects table with the value of 'start date' from ProjectDetails table because this should be the very 1st revision of the project.
	$sql = "UPDATE Projects SET actualStartDate='$start' WHERE ProjectID='$projectID'";
	//writeToLog("actual start date should be equal to start date. Start date = ".$start." Actual start date = ".$actualStart);
	if(mysqli_query($link,$sql))
	{
		writeToLog("UPDATING PROJECTS TABLE Actual start date WITH 1st REVISION Start date");
	}
	else
	{
		writeToLog("ERR executing query");
		echo("Error description: " . mysqli_error($link));
                //mysqli_close($link);
	}
	//Update estimated sqa complete date to be equal to 'Actual sqa start' + 'sqa complete' from GA duration table (based of the 1st revision added).
	//Get this projects complexity id, from Projects table.
	$sql = "SELECT complexityID FROM Projects WHERE ProjectID='$projectID'";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $complexity=$row['complexityID'];//This projects complexity id
        }
	//Get sqa complete days
	$sql = "SELECT sqaComplete FROM GADuration where complexityID=$complexity";
	if($results=mysqli_query($link,$sql))
	{
        	$row=mysqli_fetch_array($results,MYSQLI_ASSOC);
        	$sqaComp=$row['sqaComplete'];//Days to add to estimated sqa completion date
	}
	//Calculate the adjusted SQA complete date
	$estSQACompleteAdjusted=calculateEstimatedCompletion($start,$sqaComp);
	$sql = "UPDATE Projects SET estSQAComplete='$estSQACompleteAdjusted' WHERE ProjectID='$projectID'";
        if(mysqli_query($link,$sql))
        {
                writeToLog("UPDATING PROJECTS TABLE estimated sqa complete date WITH 1st REVISION Start date + sqa complete from GA duration table.");
        }
        else
        {
                writeToLog("ERR executing query");
                echo("Error description: " . mysqli_error($link));
                //mysqli_close($link);
        }
	//Update Actual SQA Complete based off new sqa start date of the 1st project revision
	$actualSQACompAdjusted = calculateActualCompletion($start,$sqaComp);
        $sql = "UPDATE Projects SET actualSQAComplete='$actualSQACompAdjusted' WHERE ProjectID='$projectID'";
        if(mysqli_query($link,$sql))
        {
                writeToLog("UPDATING PROJECTS TABLE actual sqa complete date WITH 1st REVISION Start date + sqa complete from GA duration table.");
        }
        else
        {
                writeToLog("ERR executing query");
                echo("Error description: " . mysqli_error($link));
                //mysqli_close($link);
        }
	//Get ITL complete days
        $sql = "SELECT itlComplete FROM GADuration where complexityID=$complexity";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $itlComp=$row['itlComplete'];//Days to add to estimated itl completion date
        }
	//Update estimated ITL complete
	$estITLCompAdjusted = calculateEstimatedITLComplete($estSQACompleteAdjusted,$itlComp);
        $sql = "UPDATE Projects SET estITLComplete='$estITLCompAdjusted' WHERE ProjectID='$projectID'";
        if(mysqli_query($link,$sql))
        {
                writeToLog("UPDATING PROJECTS TABLE estimated itl complete date WITH 1st REVISION Start date + sqa complete from GA duration table.");
        }
        else
        {
                writeToLog("ERR executing query");
                echo("Error description: " . mysqli_error($link));
                //mysqli_close($link);
        }
        //Update actual ITL complete
        $actualITLCompAdjusted = calculateActualITLComplete($actualSQACompAdjusted,$itlComp);
        $sql = "UPDATE Projects SET actualITLComplete='$actualITLCompAdjusted' WHERE ProjectID='$projectID'";
        if(mysqli_query($link,$sql))
        {
                writeToLog("UPDATING PROJECTS TABLE actual itl complete date WITH 1st REVISION Start date + sqa complete from GA duration table.");
        }
        else
        {
                writeToLog("ERR executing query");
                echo("Error description: " . mysqli_error($link));
                //mysqli_close($link);
        }
}
//===============================================================================================

//Add a history entry for this new project, only if the user entered a unique revision for this project.
//Also, only update DB if its a unique revision.
if($r != "")
{
	//Navigate to the error page, because it was a dup revision
	header("Location: DupRevisionError.php?name=".$projectID);//pass project id to next page
}
else{
	//Add a history entry for the new revision and update DB
	newRevisionHistory($username,$projectID,$revision,$link,"NewRevision",$r);

	$sql = "INSERT INTO ProjectDetails (ProjectID,Revision,Owner,StartDate,EndDate,Status,Notes)
        VALUES('$projectID','$revision','$owner','$start','$end','$status','$notes')";

	if(!mysqli_query($link,$sql))
	{
        	echo("Error description: " . mysqli_error($link));
        	mysqli_close($link);
	}
	else
	{
                //If the revision the user tried to add is a duplicate for this project, navigate to a page that shows an error. Pass needed values to that page as well.
                if($r != ""){
                        header("Location: DupRevisionError.php?name=".$projectID);
                }
                else{
                        header("location: ProjectList.php");//if it was a unique revision, then carry on.
                }

                mysqli_close($link);
	}

}

//FUNCTIONS------------------------------------
function newRevisionHistory($user, $pID, $rev ,$link, $changeType, $isDupRev)
{
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectDetailHistory table if a new project was created
        $sql = "INSERT INTO ProjectDetailHistory (projectID,revID,user,dateOfChange,timeOfChange,changeType,revision)
                VALUES('$pID','N/A','$user','$date','$time','$changeType','$rev')";
        if(!mysqli_query($link,$sql))
        {
                echo("Error description: " . mysqli_error($link));
                mysqli_close($link);
        }
        else
        {
		//If the revision the user tried to add is a duplicate for this project, navigate to a page that shows an error. Pass needed values to that page as well.
		if($isDupRev != ""){
        		header("Location: DupRevisionError.php?name=".$pID);//pass project id to next page
		}
		else{
        		header("location: ProjectList.php");//if it was a unique revision, then carry on.
		}
                //mysqli_close($link);
        }
}

//Check if the revision passed in is a duplicate within the project
function isDuplicateRevision($link,$revision,$pid){
	$sql = "select Revision, COUNT(Revision) as RevisionDup from ProjectDetails where Revision = '$revision' and ProjectID = '$pid' GROUP BY Revision";
	if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
		$endResult=$row["RevisionDup"];
		writeToLog("<br>Row/RevisionDup  = ".$endResult);
		if($endResult != ""){
			writeToLog("---- There was a duplicate revision!-----");
		}
		else{
			writeToLog("---- There aint do dang duplicate yall!-----");
		}
                return $endResult;
        }
        else
        {
		writeToLog("<br>isDuplicateRevision ERROR");
        }
}

?>
