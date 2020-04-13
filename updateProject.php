projNameCheckHistory
]<?php
include('verify.php');
include('siteFuncs.php');
include('calculateGADate.php');
//if(!isSQAuser($username))
//{
  //  header("location: ProjectDetails.php");
//}
$link=sqlConnect();
$projectname = mysqli_real_escape_string($link,($_POST['projectname']));
$projecttype = mysqli_real_escape_string($link,$_POST['projecttype']);
$class = mysqli_real_escape_string($link,$_POST['class']);
$complexity = mysqli_real_escape_string($link,$_POST['complexityID']);
$details = mysqli_real_escape_string($link,$_POST['details']);
$handoff = mysqli_real_escape_string($link,convertToSQLDate($_POST['handoff']));
$actualComplete = mysqli_real_escape_string($link,convertToSQLDate($_POST['actCmp']));
$actualITLComplete = mysqli_real_escape_string($link,convertToSQLDate($_POST['actITLCmp']));
$projectid = mysqli_real_escape_string($link,$_POST['projectid']);
//$targetHealth = mysqli_real_escape_string($link,$_POST['health']);
$note = mysqli_real_escape_string($link,$_POST['projectnote']);
$user = mysqli_real_escape_string($link,$_POST['user']);
$studio = mysqli_real_escape_string($link,$_POST['studioname']);
$somethingChanged = "false";
//Checks if the user entered a note and updates the DB
if($note == ""){
        $note = "N/A";
        //addUserNote($note,$user,$projectid,$link);
        //projNameCheckNote($username, $projectid, $link, $projectname);
        //typeCheckNote($username, $projectid, $link, $projecttype);
        //marketCheckNote($username, $projectid, $link, $class);
        //complexityCheckNote($username, $projectid, $link, $complexity);
        //detailsCheckNote($username, $projectid, $link, $details);
        //handoffCheckNote($username, $projectid, $link, $handoff);
        //actualSqaCmpCheckNote($username, $projectid, $link, $actualComplete);
        //actualItlCmpCheckNote($username, $projectid, $link, $actualITLComplete);
        //targetHealthCheckNote($username, $projectid, $link, $targetHealth);
}
else{
	//Dont change the note value
        //Update ProjectNote table with user's note
        //addUserNote($note,$user,$projectid,$link);
}

//Save any project history changes
projNameCheckHistory($username, $projectid, $link, "Project Name", $projectname, $note);
projTypeCheckHistory($username, $projectid, $link, "Project Type", $projecttype, $note);
projClassCheckHistory($username, $projectid, $link, "Class", $class, $note);
projComplexCheckHistory($username, $projectid, $link, "Complexity", $complexity, $note);
projDetailCheckHistory($username, $projectid, $link, "Details", $details, $note);
projHandoffCheckHistory($username, $projectid, $link, "Handoff", $handoff, $note);
projActSQACmpCheckHistory($username, $projectid, $link, "ActualSQAComplete", $actualComplete, $note);
projActITLCmpCheckHistory($username, $projectid, $link, "ActualITLComplete", $actualITLComplete, $note);
studioCheckHistory($username, $projectid, $link, "Studio", $studio, $note);
//projTargetCheckHistory($username, $projectid, $link, "Target", $targetHealth);
//writeToLog("somethingChanged = ".$somethingChanged);
//Determine GA target health value
//ok = 1, caution = 2, danger = 3
//writeToLog("Target Health = ".$targetHealth);
/*if($targetHealth == "ok")
{
	$th = 1;
}
else if($targetHealth == "caution")
{
	$th = 2;
}
else if($targetHealth == "danger")
{
	$th = 3;
}
else
{
	$th = 1;
}*/
//writeToLog("TH = ".$th);

//Get the estimated start date: get start date default value from GADuration table
//Handoff and/or complexity may have been edited so we need to update the start date.
$sql = "SELECT sqaStart FROM GADuration where complexityID=$complexity";
if($results=mysqli_query($link,$sql))
{
        $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
        $start=$row['sqaStart'];//Days to add to handoff date
}

//Get the current 'Actual SQA complete' date so it can be compared to determine if the value has been modified.
$sql = "SELECT actualSQAComplete FROM Projects where ProjectID=$projectid";
if($results=mysqli_query($link,$sql))
{
        $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
        $previous=$row['actualSQAComplete'];//Days to add to handoff date
}
//Get actual start date and pass it into the REPLACE query, because any column not passed through will be nulled out when using replace.
//REPLACE deletes a row if it already exists with projectID, and creates a new row.
$sql = "SELECT actualStartDate FROM Projects where ProjectID=$projectid";
if($results=mysqli_query($link,$sql))
{
       $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
       $actualStart=$row['actualStartDate'];//Days to add to handoff date
}

//Get the estimated sqa completion days
$sql = "SELECT sqaComplete FROM GADuration where complexityID=$complexity";
if($results=mysqli_query($link,$sql))
{
        $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
        $sqaComp=$row['sqaComplete'];//Days to add to estimated sqa completion date
}

//Get ITL complete days
$sql = "SELECT itlComplete FROM GADuration where complexityID=$complexity";
if($results=mysqli_query($link,$sql))
{
        $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
        $itlComp=$row['itlComplete'];//Days to add to estimated itl completion date
}

//Calculate actual itl complete, because actual sqa complete could have been modified, which would push the GA Date (aka actual itl complete) back.
//ONLY update actual itl complete IF actual sqa complete has changed.
//writeTOLog("String compare result: ".strcmp($previous,$actualComplete));
if(strcmp($previous,$actualComplete)!=0)
{
        $actualITLComplete = calculateActualITLComplete($actualComplete,$itlComp);
}
else
{
        //actual sqa complete wasn't changed
       //writeToLog(" Actual sqa complete date hasnt changed. ");
}

//Get the project's complexity from GaDuration table
$sql = "SELECT complexityID FROM Projects where ProjectID=$projectid";
if($results=mysqli_query($link,$sql))
{
        $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
        $currValue=$row['complexityID'];
}

//Get estimated start date and save it to $startdate, in case it doesnt get updated below.
$sql = "SELECT EstimatedStartDate FROM Projects where ProjectID=$projectid";
if($results=mysqli_query($link,$sql))
{
        $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
        $startdate=$row['EstimatedStartDate'];
}

//Get estimated sqa complete date, in case it doesnt get updated below.
$sql = "SELECT estSQAComplete FROM Projects where ProjectID=$projectid";
if($results=mysqli_query($link,$sql))
{
        $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
        $estComplete=$row['estSQAComplete'];
}

//Check if revisions exist or not for the project
if(oneRevisionExists($projectid,$link) || multipleRevisionsExist($projectid,$link))
{
	if($complexity == $currValue)//complexity wasn't changed
	{
		$startdate = calculateEstimatedStart($handoff,$start);
		$estComplete=calculateEstimatedCompletion($actualStart,$sqaComp);
	}
	else
	{
		//Don't update estimated sqa start or est sqa complete if complexity was changed
	}

	//Older projects that have bogus dates or no dates at all
	if($startdate == "1970-01-08" || !isset($startdate) || !isset($estComplete))//defaults to this date or could be null/not set to a value
	{
        //get start date of earliest revision for that project id
       	$sd = getFirstRevStDate($projectid,$link);
        //set actual start date to be this date
		$actualStart=$sd;

		//Additional updates
		$estComplete = calculateEstimatedCompletion($actualStart,$sqaComp);//recalc est sqa complete
		$actualComplete = $estComplete;//actual sqa complete will be the same as est sqa complete
		$estITL=calculateEstimatedITLComplete($actualComplete,$itlComp);
		$actualITLComplete = $estITL;//actual itl complete same as estimate
	}
	else
	{
        //writeToLog(" Start date did not equal 1970-01-08 ");
	}

	$estITL=calculateEstimatedITLComplete($actualComplete,$itlComp);
}
else//No revisions for the project yet
{
	//use the start date, not actual start date, because no revisions exist yet.
	$startdate = calculateEstimatedStart($handoff,$start);//recalc est start date
        $actualStart = $startdate;//actual start date will be the same as estimated start date
        $estComplete = calculateEstimatedCompletion($startdate,$sqaComp);//recalc est sqa complete
        $actualComplete = $estComplete;//actual sqa complete will be the same as est sqa complete
        $estITL = calculateEstimatedITLComplete($estComplete,$itlComp);//recalc est itl comp
        $actualITLComplete = $estITL;//actual itl complete same as estimate

	//TODO:Check if complexity has been changed by user
	//if($complexity != $currValue)
	//{
	//	$startdate = calculateEstimatedStart($handoff,$start);//recalc est start date
	//	$actualStart = $startdate;//actual start date will be the same as estimated start date
	//	$estComplete = calculateEstimatedCompletion($startdate,$sqaComp);//recalc est sqa complete
	//	$actualComplete = $estComplete;//actual sqa complete will be the same as est sqa complete 
	//	$estITL = calculateEstimatedITLComplete($estComplete,$itlComp);//recalc est itl comp
	//	$actualITLComplete = $estITL;//actual itl complete same as estimate
	//}
	//else
	//{
		//writeToLog(" Complexity has not been changed by the user! ");
	//}
}
//writeToLog("Actual start after checks ==== ".$actualStart);
//Check if some dates have changed at this point, for history tracking.
projEstSQAStartHistory($username, $projectid, $link, "EstimatedSQAStart", $startdate, $note);
projActStartHistory($username, $projectid, $link, "ActualStart", $actualStart, $note);
projEstSQACmpHistory($username, $projectid, $link, "EstimatedSQAComplete", $estComplete, $note);
projEstITLCmpHistory($username, $projectid, $link, "EstimatedITLComplete", $estITL, $note);
if($note != ""){//dont write to db if note field is empty
	projNoteHistory($username, $projectid, $link, "USER NOTE", $note, $somethingChanged);
}
else{
	//do nothing
}
$somethingChanged = "false";//reset variable
$th = 1;//not actually using this at the moment, but set it to something to avoid a null error.
$sql = "REPLACE INTO Projects (ProjectID,ProjectName,ProjectType,Class,AdditionalProjectDetails,HandoffDate,EstimatedStartDate,complexityID,actualStartDate,estSQAComplete,actualSQAComplete,estITLComplete,actualITLComplete,gaTargetHealth,studio)
        VALUES('$projectid','$projectname','$projecttype','$class','$details','$handoff','$startdate','$complexity','$actualStart','$estComplete','$actualComplete','$estITL','$actualITLComplete','$th','$studio')";
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

		//multipleRevisionsExist($projectid, $link);

		header("location: Projects.php");
		mysqli_close($link);
	//}
}

function projNameCheckNote($user, $pid, $link, $pn)
{
	$sql = "SELECT * FROM Projects where ProjectID=$pid";
	$date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:sa");// will return ex: 05:22:29pm
        $dateTime = $date." ".$time;

	//adding notes
	if($results=mysqli_query($link,$sql))
	{
		$row=mysqli_fetch_array($results,MYSQLI_ASSOC);
		//Check if project name has changed and if it has, add a note
		if($pn != $row['ProjectName']){
			//writeToLog("Project name has changed");
			//create a note and update project notes table
			$note = "Project name has been changed from <b>".$row['ProjectName']."</b> to <b>".$pn."</b>";
			$sql = "INSERT INTO ProjectNotes (projectID,username,note,timestamp)
        			VALUES('$pid','$user','$note','$dateTime')";
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
		else{
			//writeToLog("Project name has NOT changed");
		}
	}
}

function typeCheckNote($user, $pid, $link, $tp)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:sa");// will return ex: 05:22:29pm
        $dateTime = $date." ".$time;

        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Check if project name has changed and if it has, add a note
                if($tp != $row['ProjectType']){
                        //writeToLog("Project type has changed");
                        //create a note
                        $note = "Project type has been changed from <b>".$row['ProjectType']."</b> to <b>".$tp."</b>";
                        $sql = "INSERT INTO ProjectNotes (projectID,username,note,timestamp)
                                VALUES('$pid','$user','$note','$dateTime')";
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
                else{
                       // writeToLog("Project type has NOT changed");
                }
        }
}

function marketCheckNote($user, $pid, $link, $mk)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:sa");// will return ex: 05:22:29pm
        $dateTime = $date." ".$time;

        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Check if project name has changed and if it has, add a note
                if($mk != $row['Class']){
                        //writeToLog("Project class has changed");
                        //create a note
                        $note = "Project class/market has been changed from <b>".$row['Class']."</b> to <b>".$mk."</b>";
                        $sql = "INSERT INTO ProjectNotes (projectID,username,note,timestamp)
                                VALUES('$pid','$user','$note','$dateTime')";
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
                else{
                       // writeToLog("Project class has NOT changed");
                }
        }
}

function complexityCheckNote($user, $pid, $link, $cmp)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:sa");// will return ex: 05:22:29pm
        $dateTime = $date." ".$time;

        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Check if project name has changed and if it has, add a note
                if($cmp != $row['complexityID']){
			//If 1 or more revisions exist for this project,add additional info to note.
			if(oneRevisionExists($pid,$link) || multipleRevisionsExist($pid,$link)){
				$note = "Project complexity has been changed from <b>".$row['complexityID']."</b> to <b>".$cmp."</b>. Dates will not be updated because revisions exist.";
			}
			else{
				$note = "Project complexity has been changed from <b>".$row['complexityID']."</b> to <b>".$cmp."</b>. Dates will update based off the new complexity.";
			}
                        //writeToLog("Project complexity has changed");
                        $sql = "INSERT INTO ProjectNotes (projectID,username,note,timestamp)
                                VALUES('$pid','$user','$note','$dateTime')";
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
                else{
                       // writeToLog("Project complexity has NOT changed");
                }
        }
}

function detailsCheckNote($user, $pid, $link, $det)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:sa");// will return ex: 05:22:29pm
        $dateTime = $date." ".$time;

        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Check if project name has changed and if it has, add a note
                if($det != $row['AdditionalProjectDetails']){
                        //writeToLog("Project complexity has changed");
                        //create a note
                        $note = "Project details has been changed from <b>".$row['AdditionalProjectDetails']."</b> to <b>".$det."</b>";
                        $sql = "INSERT INTO ProjectNotes (projectID,username,note,timestamp)
                                VALUES('$pid','$user','$note','$dateTime')";
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
                else{
                       // writeToLog("Project complexity has NOT changed");
                }
        }
}

function handoffCheckNote($user, $pid, $link, $ho)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:sa");// will return ex: 05:22:29pm
        $dateTime = $date." ".$time;

        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Check if project name has changed and if it has, add a note
                if($ho != $row['HandoffDate']){
                        //writeToLog("Project complexity has changed");
                        //create a note
                        $note = "Project handoff date has been changed from <b>".$row['HandoffDate']."</b> to <b>".$ho."</b>";
                        $sql = "INSERT INTO ProjectNotes (projectID,username,note,timestamp)
                                VALUES('$pid','$user','$note','$dateTime')";
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
                else{
                       // writeToLog("Project complexity has NOT changed");
                }
        }
}

function actualSqaCmpCheckNote($user, $pid, $link, $actCmp)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:sa");// will return ex: 05:22:29pm
        $dateTime = $date." ".$time;

        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Check if project name has changed and if it has, add a note
                if($actCmp != $row['actualSQAComplete']){
			//If zero or more revisions exist for this project,add additional info to note.
                        if(oneRevisionExists($pid,$link) || multipleRevisionsExist($pid,$link)){
				$note = "Actual SQA complete date has been changed from <b>".$row['actualSQAComplete']."</b> to <b>".$actCmp."</b>";
                        }
                        else{
				$note = "Actual SQA complete date change attempted from <b>".$row['actualSQAComplete']."</b> to <b>".$actCmp."</b>. Date will not update- project still in queue.";
                        }

                        //writeToLog("Project complexity has changed");
                        $sql = "INSERT INTO ProjectNotes (projectID,username,note,timestamp)
                                VALUES('$pid','$user','$note','$dateTime')";
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
                else{
                       // writeToLog("Project complexity has NOT changed");
                }
        }
}

function actualItlCmpCheckNote($user, $pid, $link, $actCmp)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:sa");// will return ex: 05:22:29pm
        $dateTime = $date." ".$time;

        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Check if project name has changed and if it has, add a note
                if($actCmp != $row['actualITLComplete']){
                        //If zero revisions exist for this project,add additional info to note.
                        if(oneRevisionExists($pid,$link) || multipleRevisionsExist($pid,$link)){
				$note = "Actual ITL complete date has been changed from <b>".$row['actualITLComplete']."</b> to <b>".$actCmp."</b>";
                        }
                        else{
                                $note = "Actual ITL complete date change attempted from <b>".$row['actualITLComplete']."</b> to <b>".$actCmp."</b>. Date will not update- project still in queue.";
                        }

                        //writeToLog("Project complexity has changed");
                        $sql = "INSERT INTO ProjectNotes (projectID,username,note,timestamp)
                                VALUES('$pid','$user','$note','$dateTime')";
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
                else{
                       // writeToLog("Project complexity has NOT changed");
                }
        }
}

function targetHealthCheckNote($user, $pid, $link, $th)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:sa");// will return ex: 05:22:29pm
        $dateTime = $date." ".$time;

	//convert target health value
        if($th == "ok"){
                $th = 1;
        }
        else if($th == "caution"){
                $th = 2;
        }
        else if($th == "danger"){
                $th = 3;
        }
        else{
                //do nothing
        }

        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
		$dbVal = $row['gaTargetHealth'];
                //Check if project name has changed and if it has, add a note
                if($th != $dbVal){
                        //writeToLog("Project complexity has changed");
			//handle if value is null
			if(!isset($dbVal)){
				$dbVal = "NULL";
			}
			//Create a note
                        $note = "Target health value has been changed from <b>".$dbVal."</b> to <b>".$th."</b>";
                        $sql = "INSERT INTO ProjectNotes (projectID,username,note,timestamp)
                                VALUES('$pid','$user','$note','$dateTime')";
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
                else{
                       // writeToLog("Project complexity has NOT changed");
                }
        }
}

function projNameCheckHistory($user, $pid, $link, $changeType, $pn, $note)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectHistory table if a project name change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
		//writeToLog("New project name = ".$pn);
                $oldPN = $row['ProjectName'];
		//writeToLog("OLD project name = ".$oldPN);
                if($pn != $oldPN){
			$oldPN = addslashes($oldPN);
			//writeToLog("OLD project name after add slashes = ".$oldPN);
			global $somethingChanged;
			$somethingChanged = "true";
                        //writeToLog("Project name has changed");
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note,projNameOld,projNameNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$note','$oldPN','$pn')";
                        if(!mysqli_query($link,$sql))
                        {
                                echo("projNameCheckHistory() Error description: " . mysqli_error($link));
                                mysqli_close($link);
                        }
                        else
                        {
                                header("location: Projects.php");
                                //mysqli_close($link);
                        }

                }
                else{
                        //writeToLog("Project name has NOT changed");
		}
	}
}

function studioCheckHistory($user, $pid, $link, $changeType, $st, $note)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

       //Insert into the ProjectHistory table if a project type change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $oldST = $row['studio'];
                if($st != $oldST){
                        global $somethingChanged;//gives access to variable declared at the top of this file
                        $somethingChanged = "true";
                        //writeToLog("Project type has changed");
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note,studioOld,studioNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$note','$oldST','$st')";
                        if(!mysqli_query($link,$sql))
                        {
                                echo("Studio Error description: " . mysqli_error($link));
                                mysqli_close($link);
                        }
                        else
                        {
                                header("location: Projects.php");
                                //mysqli_close($link);
                        }

                }
                else{
                        //writeToLog("Project type has NOT changed");
                }
        }
}

function projTypeCheckHistory($user, $pid, $link, $changeType, $tp, $note)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectHistory table if a project type change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $oldPT = $row['ProjectType'];
                if($tp != $oldPT){
			global $somethingChanged;//gives access to variable declared at the top of this file
			$somethingChanged = "true";
                        //writeToLog("Project type has changed");
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note,projTypeOld,projTypeNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$note','$oldPT','$tp')";
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
                else{
                        //writeToLog("Project type has NOT changed");
                }
        }
}

function projClassCheckHistory($user, $pid, $link, $changeType, $cs, $note)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectHistory table if a project type change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $oldCS = $row['Class'];
                if($cs != $oldCS){
			global $somethingChanged;
			$somethingChanged = "true";
                        //writeToLog("Project type has changed");
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note,classOld,classNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$note','$oldCS','$cs')";
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
                else{
                        //writeToLog("Project type has NOT changed");
                }
        }
}

function projComplexCheckHistory($user, $pid, $link, $changeType, $cmp, $note)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectHistory table if a project complexity change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $oldCMP = $row['complexityID'];
                if($cmp != $oldCMP){
			global $somethingChanged;
			$somethingChanged = "true";
                        //writeToLog("Project complexity has changed");
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note,complexityOld,complexityNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$note','$oldCMP','$cmp')";
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
                else{
                        //writeToLog("Project complexity has NOT changed");
                }
        }
}

function projDetailCheckHistory($user, $pid, $link, $changeType, $dt, $note)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectHistory table if a project complexity change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $oldDT = $row['AdditionalProjectDetails'];
                if($dt != $oldDT){
			global $somethingChanged;
			$somethingChanged = "true";
                        //writeToLog("Project complexity has changed");
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note,detailsOld,detailsNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$note','$oldDT','$dt')";
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
                else{
                        //writeToLog("Project complexity has NOT changed");
                }
        }
}

function projHandoffCheckHistory($user, $pid, $link, $changeType, $ho, $note)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectHistory table if handoff date change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $oldHO = $row['HandoffDate'];
                if($ho != $oldHO){
			global $somethingChanged;
			$somethingChanged = "true";
                        //writeToLog("Project handoff date has changed");
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note,handoffOld,handoffNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$note','$oldHO','$ho')";
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
                else{
                        //writeToLog("Project handoff date has NOT changed");
                }
        }
}

function projActSQACmpCheckHistory($user, $pid, $link, $changeType, $actCmp, $note)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectHistory table if actual sqa complete date change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $oldCMP = $row['actualSQAComplete'];
                if($actCmp != $oldCMP){
			global $somethingChanged;
			$somethingChanged = "true";
                        //writeToLog("Project handoff date has changed");
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note,actSqaCmpOld,actSqaCmpNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$note','$oldCMP','$actCmp')";
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
                else{
                        //writeToLog("actual sqa complete date has NOT changed");
                }
        }
}

function projActITLCmpCheckHistory($user, $pid, $link, $changeType, $actCmp, $note)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectHistory table if actual itl complete date change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $oldCMP = $row['actualITLComplete'];
                if($actCmp != $oldCMP){
			global $somethingChanged;
			$somethingChanged = "true";
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note,actItlCmpOld,actItlCmpNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$note','$oldCMP','$actCmp')";
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
                else{
                        //writeToLog("actual itl complete date has NOT changed");
                }
        }
}

function projEstSQAStartHistory($user, $pid, $link, $changeType, $estSt, $note)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectHistory table if actual itl complete date change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $oldEST = $row['EstimatedStartDate'];
                if($estSt != $oldEST){
			global $somethingChanged;
			$somethingChanged = "true";
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note,estSqaStDateOld,estSqaStDateNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$note','$oldEST','$estSt')";
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
                else{
                        //writeToLog("actual itl complete date has NOT changed");
                }
        }
}

function projEstSQACmpHistory($user, $pid, $link, $changeType, $estCmp, $note)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectHistory table if estimated sqa complete date change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $oldEST = $row['estSQAComplete'];
                if($estCmp != $oldEST){
			global $somethingChanged;
			$somethingChanged = "true";
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note,estSqaCmpOld,estSqaCmpNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$note','$oldEST','$estCmp')";
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
                else{
                        //writeToLog("actual itl complete date has NOT changed");
                }
        }
}

function projEstITLCmpHistory($user, $pid, $link, $changeType, $estCmp, $note)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectHistory table if estimated itl complete date change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $oldEST = $row['estITLComplete'];
                if($estCmp != $oldEST){
			global $somethingChanged;
			$somethingChanged = "true";
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note,estItlCmpOld,estItlCmpNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$note','$oldEST','$estCmp')";
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
                else{
                        //writeToLog("actual itl complete date has NOT changed");
                }
        }
}

function projActStartHistory($user, $pid, $link, $changeType, $actSt, $note)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //Insert into the ProjectHistory table if an actual sqa start date change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $oldST = $row['actualStartDate'];
                if($actSt != $oldST){
			global $somethingChanged;
			$somethingChanged = "true";
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note,actSqaStDateOld,actSqaStDateNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$note','$oldST','$actSt')";
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
                else{
                        //writeToLog("actual itl complete date has NOT changed");
                }
        }
}

function projTargetCheckHistory($user, $pid, $link, $changeType, $tgt)
{
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

        //convert target health value
        if($tgt == "ok"){
                $tgt = 1;
        }
        else if($tgt == "caution"){
                $tgt = 2;
        }
        else if($tgt == "danger"){
                $tgt = 3;
        }
        else{
                //do nothing
        }

        //Insert into the ProjectHistory table if actual itl complete date change was made
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $oldTGT = $row['gaTargetHealth'];
                if($tgt != $oldTGT){
                        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,onTargetOld,onTargetNew)
                                VALUES('$pid','$user','$date','$time','$changeType','$oldTGT','$tgt')";
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
                else{
                        //writeToLog("actual itl complete date has NOT changed");
                }
        }
}

function addUserNote($note,$user,$pid,$link){
	$date = date("Y-m-d");//will return ex: 2018-08-02
	$time = date("h:i:sa");// will return ex: 05:22:29pm
	$dateTime = $date." ".$time;

	$sql = "INSERT INTO ProjectHistory (projectID,username,note,timestamp)
        	VALUES('$pid','$user','$note','$dateTime')";
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

function projNoteHistory($user, $pid, $link, $changeType, $note, $somethingChanged){
        $date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29
	global $somethingChanged;//grants access to the global variable
	if($somethingChanged == "false"){
		//update db
		$sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note)
                        VALUES('$pid','$user','$date','$time','$changeType','$note')";
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
	else{
		//some data changed so don't add an entry just for user note
	}
}
?>
