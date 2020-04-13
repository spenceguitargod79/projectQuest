<?php
include('verify.php');
include('siteFuncs.php');
include('calculateGADate.php');
//if(!isSQAuser($username))
//{
  //  header("location: ProjectDetails.php");
//}
$link=sqlConnect();

$taskid = mysqli_real_escape_string($link,$_POST['taskid']);
$projectid = mysqli_real_escape_string($link,$_POST['projectid']);
$revision = mysqli_real_escape_string($link,$_POST['revision']);
$owner = mysqli_real_escape_string($link,$_POST['owner']);
$start = mysqli_real_escape_string($link,convertToSQLDate($_POST['startdate']));
$end = mysqli_real_escape_string($link,convertToSQLDate($_POST['enddate']));
$status = mysqli_real_escape_string($link,$_POST['status']);
$notes = mysqli_real_escape_string($link,$_POST['notes']);
$demoComplete = mysqli_real_escape_string($link,$_POST['demoCmp']);

$demoChecked = mysqli_real_escape_string($link,$_POST['checkValue']);
$demoCheckedDB = mysqli_real_escape_string($link,$_POST['checkBoxVal']);
if($demoChecked != "Y"){
	//writeToLog(" CHECK box is un-checked");
	$demoChecked = "N";//if the checkbox is left unchecked then give it a value of 'N'
}
else{
	//writeToLog(" checkbox is checked");
}
writeToLog(" demo checked value is: ".$demoChecked);
$dateTime = getCurrentDateTime();
writeToLog(" Current datTime is: : ".$dateTime);
//$link=sqlConnect();

//Tracking project detail change history
ownerCheck($link,$projectid,$taskid,$revision,$username,"owner",$owner);
revCheck($link,$projectid,$taskid,$revision,$username,"revision",$revision);
stDateCheck($link,$projectid,$taskid,$revision,$username,"start date",$start);
endDateCheck($link,$projectid,$taskid,$revision,$username,"end date",$end);
statusCheck($link,$projectid,$taskid,$revision,$username,"status",$status);
notesCheck($link,$projectid,$taskid,$revision,$username,"note",$notes);

//If returns "change", change rev end date to today's date.
//If returns "changeITL", change rev end date AND sqa release date to today's date
//If returns "no change", do nothing.
$statusChanged = statusChangedTo($link,$projectid,$taskid,$status);
writeToLog("Status changed to : ".$statusChanged." ");

if($statusChanged == "change"){
	//addDetailHistoryEntryDates($link,$projectid,$owner,"AutomateDates",$end,getCurrentDate(),"Status change:auto changing revision end date to today");
	$end = getCurrentDate();//set rev end date to today

}
else if($statusChanged == "changeITL"){
	//addDetailHistoryEntryDates($link,$projectid,$owner,"AutomateDates",$end,getCurrentDate(),"Status to ITL change:auto changing revision end date to today");
	//addProjectHistoryEntryDates($link,$projectid,$owner,"AutomateDates","N/A",getCurrentDate(),"Status to ITL change:auto changing sqa release date to today");
	$end = getCurrentDate();//set rev end date to today's date
	//Also update sqa release date to today's date, for the project because its now headed to GLI/BMM.
	updateSQAReleaseDate($link,$projectid);
}
else{
	//do nothing
}

$sql = "REPLACE INTO ProjectDetails (ProjectID,Revision,Owner,StartDate,EndDate,Status,Notes,TaskID,demoIsChecked,demoRequestTime,demoComplete)
        VALUES('$projectid','$revision','$owner','$start','$end','$status','$notes','$taskid','$demoChecked','$dateTime','$demoComplete')";
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
		header("location: ProjectList.php");
		mysqli_close($link);
	//}
}


//FUNCTIONS---------------------------------------------------------------------

//Determine if the owner has been changed and add a db entry if so.
function ownerCheck($link,$pid,$revid,$rev,$user,$changeType,$owner){
	$sql = "SELECT * FROM ProjectDetails where ProjectID=$pid and TaskID=$revid";
	if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Check if project name has changed and if it has, add an entry to the ProjectDetailHistory table
		$o = $row['Owner'];
                if($owner != $o){
			$date=date("Y-m-d");
			$time=date("h:i:s");
                        //writeToLog("Owner has changed");
                        $sql = "INSERT INTO ProjectDetailHistory (projectID,revID,revision,user,dateOfChange,timeOfChange,changeType,ownerOld,ownerNew)
                                VALUES('$pid','$revid','$rev','$user','$date','$time','$changeType','$o','$owner')";
                        if(!mysqli_query($link,$sql))
                        {
                                echo("Error description: " . mysqli_error($link));
                                mysqli_close($link);
                        }
                        else
                        {
                                header("location: ProjectList.php");
                                //mysqli_close($link);
                        }

                }
                else{
                        //writeToLog("Owner has NOT changed");
                }
        }
}

function revCheck($link,$pid,$revid,$rev,$user,$changeType,$revision){
        $sql = "SELECT * FROM ProjectDetails where ProjectID=$pid and TaskID=$revid";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Check if project revision is changed and if so, add an entry to the ProjectDetailHistory table
                $r = $row['Revision'];
                if($revision != $r){
                        $date=date("Y-m-d");
                        $time=date("h:i:s");
                        //writeToLog("Revision has changed");
                        $sql = "INSERT INTO ProjectDetailHistory (projectID,revID,revision,user,dateOfChange,timeOfChange,changeType,revOld,revNew)
                                VALUES('$pid','$revid','$rev','$user','$date','$time','$changeType','$r','$revision')";
                        if(!mysqli_query($link,$sql))
                        {
                                echo("Error description: " . mysqli_error($link));
                                mysqli_close($link);
                        }
                        else
                        {
                                header("location: ProjectList.php");
                                //mysqli_close($link);
                        }

                }
                else{
                        //writeToLog("Revision has NOT changed");
                }
        }
}

function stDateCheck($link,$pid,$revid,$rev,$user,$changeType,$stDate){
        $sql = "SELECT * FROM ProjectDetails where ProjectID=$pid and TaskID=$revid";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Check if project revision is changed and if so, add an entry to the ProjectDetailHistory table
                $st = $row['StartDate'];
		//convert date format
		$stDate = convertDateFormat($stDate,"Y-m-d");
                if($stDate != $st){
                        $date=date("Y-m-d");
                        $time=date("h:i:s");
                        //writeToLog("Start Date has changed");
                        $sql = "INSERT INTO ProjectDetailHistory (projectID,revID,revision,user,dateOfChange,timeOfChange,changeType,stDateOld,stDateNew)
                                VALUES('$pid','$revid','$rev','$user','$date','$time','$changeType','$st','$stDate')";
                        if(!mysqli_query($link,$sql))
                        {
                                echo("Error description: " . mysqli_error($link));
                                mysqli_close($link);
                        }
                        else
                        {
                                header("location: ProjectList.php");
                                //mysqli_close($link);
                        }

                }
                else{
                        //writeToLog("Start Date has NOT changed");
                }
        }
}

function endDateCheck($link,$pid,$revid,$rev,$user,$changeType,$endDate){
        $sql = "SELECT * FROM ProjectDetails where ProjectID=$pid and TaskID=$revid";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Check if project revision is changed and if so, add an entry to the ProjectDetailHistory table
                $end = $row['EndDate'];
                //convert date format
                $endDate = convertDateFormat($endDate,"Y-m-d");
                if($endDate != $end){
                        $date=date("Y-m-d");
                        $time=date("h:i:s");
                        //writeToLog("End Date has changed");
                        $sql = "INSERT INTO ProjectDetailHistory (projectID,revID,revision,user,dateOfChange,timeOfChange,changeType,endDateOld,endDateNew)
                                VALUES('$pid','$revid','$rev','$user','$date','$time','$changeType','$end','$endDate')";
                        if(!mysqli_query($link,$sql))
                        {
                                echo("Error description: " . mysqli_error($link));
                                mysqli_close($link);
                        }
                        else
                        {
                                header("location: ProjectList.php");
                                //mysqli_close($link);
                        }

                }
                else{
                        //writeToLog("Start Date has NOT changed");
                }
        }
}

//If status changed to rejected, itl reject, field reject, ITL, on Holed, or Obsolete, set it's revision end date to todays date.
function statusChangedTo($link,$pid,$revid,$status){
	$sql = "SELECT * FROM ProjectDetails where ProjectID=$pid and TaskID=$revid";
        if($results=mysqli_query($link,$sql))
        {
		$row=mysqli_fetch_array($results,MYSQLI_ASSOC);
		$stat = $row['Status'];
		if($status != $stat){//status changed
			writeToLog(" STATUS CHANGED YOU FOOL! ");
			if($status == "On-Hold" || $status == "Rejected" || $status == "ITL Reject" || $status == "Field Reject" || $status == "Obsoleted"){
				writeToLog(" CHANGE A ");
				return "change";
			}
			else if($status == "ITL"){
				writeToLog(" CHANGE B ");
				return "changeITL";
			}
			else{
				writeToLog(" CHANGE HELL NO ");
				return "no change";
			}
		}
		else{
			return "no change";
		}
	}
        else
	{
        	//writeToLog("Status has NOT changed");
        }
}

function statusCheck($link,$pid,$revid,$rev,$user,$changeType,$status){
	$sql = "SELECT * FROM ProjectDetails where ProjectID=$pid and TaskID=$revid";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Check if project status is changed and if so, add an entry to the ProjectDetailHistory table
                $stat = $row['Status'];
                if($status != $stat){//status changed

			//Check if the new status is 'On-Hold'
			if($status == "On-Hold"){
				writeToLog("New status is On-Hold");
				//Add 7 days to SQA release date, and ITL
				addDaysToSQARelease($link,$pid,"7",$user);
				addDaysToITLRelease($link,$pid,"7",$user);
			}
			else{
				writeToLog("New status is NOT On-Hold");
			}


                        $date=date("Y-m-d");
                        $time=date("h:i:s");
                        //writeToLog("Status has changed");
                        $sql = "INSERT INTO ProjectDetailHistory (projectID,revID,revision,user,dateOfChange,timeOfChange,changeType,statusOld,statusNew)
                                VALUES('$pid','$revid','$rev','$user','$date','$time','$changeType','$stat','$status')";
                        if(!mysqli_query($link,$sql))
                        {
                                echo("Error description: " . mysqli_error($link));
                                mysqli_close($link);
                        }
                        else
                        {
                                header("location: ProjectList.php");
                                //mysqli_close($link);
                        }

                }
                else{
                        //writeToLog("Status has NOT changed");
                }
        }
}

function addDaysToSQARelease($link,$pid,$days,$user){
	$sql = "SELECT * FROM Projects where ProjectID=$pid";
	if($results=mysqli_query($link,$sql))
        {
		$row=mysqli_fetch_array($results,MYSQLI_ASSOC);
		//Get the current SQA release date from Projects table for this PID
		$sqaRel = $row['actualSQAComplete'];
		if($sqaRel != "")//some older projects may have blank dates, so skip those.
		{
			writeToLog("original sqa release = ".$sqaRel);
                	//add $days days to sqa release
                	$result = date("Y-m-d", strtotime($sqaRel."+".$days." days"));
                	writeToLog(" NEW sqa release = ".$result);
                	//update the db
                	$sql2 = "UPDATE Projects SET actualSQAComplete = '$result' WHERE ProjectID = $pid";

                	if(!mysqli_query($link,$sql2))
                	{
                        	echo("Error description: " . mysqli_error($link));
                        	mysqli_close($link);
                	}
                	else
                	{
               	        	//ADD HISTORY ENTRY
                        	//addHistoryEntry($link,$pid,$user,"SQA Release est + $days days",$sqaRel,$result,"On-Hold Automation(SQA REL)");
				addHistoryEntry($link,$pid,"auto","actSQARel",$sqaRel,$result,"on-hold,sqacomp+7");
                	}

		}
		else{
			writeToLog(" original est sqa release is null ");
			addHistoryEntry($link,$pid,"auto","actSQARel","NA","NA","INVALID DATE EXISTS:BYPASSING LOGIC");
		}
	}
}

function addDaysToITLRelease($link,$pid,$days,$user){
        $sql = "SELECT * FROM Projects where ProjectID=$pid";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Get the current ITL Complete date from Projects table for this PID
                $itlcmp = $row['actualITLComplete'];
                if($itlcmp != "")//some older projects may have blank dates, so skip those.
                {
                        writeToLog("original ITL Complete date = ".$itlcmp);
                        //add $days days to ITL Complete date
                        $result = date("Y-m-d", strtotime($itlcmp."+".$days." days"));
                        writeToLog(" NEW ITL Complete date = ".$result);
                        //update the db
                        $sql2 = "UPDATE Projects SET actualITLComplete = '$result' WHERE ProjectID = $pid";

                        if(!mysqli_query($link,$sql2))
                        {
                                echo("Error description: " . mysqli_error($link));
                                mysqli_close($link);
                        }
                        else
                        {
                                //ADD HISTORY ENTRY
                                addHistoryEntry($link,$pid,"auto","actITLRel",$itlcmp,$result,"on-hold,itlcomp+7");
                        }

                }
                else{
                        writeToLog(" original ITL release date is null ");
                        addHistoryEntry($link,$pid,"auto","actITLRel","NA","NA","INVALID DATE EXISTS:BYPASSING LOGIC");
                }
        }
}

function updateSQAReleaseDate($link,$pid){
	$today = getCurrentDate();
	$sql = "SELECT * FROM Projects where ProjectID=$pid";
	if($results=mysqli_query($link,$sql))
        {
		$row=mysqli_fetch_array($results,MYSQLI_ASSOC);
		$sql2 = "UPDATE Projects SET actualSQAComplete = '$today' WHERE ProjectID = $pid";
                if(!mysqli_query($link,$sql2))
                {
                	echo("Error description: " . mysqli_error($link));
                        mysqli_close($link);
                }
                else
                {
                	//ADD HISTORY ENTRY
                        //addHistoryEntry($link,$pid,"auto","actITLRel",$itlcmp,$result,"on-hold,itlcomp+7");
                }
	}
}

//Add history entry for SQA complete and itl complete when project status changes to 'On-Hold'
function addHistoryEntry($link,$pid,$user,$changeType,$dateFrom,$dateTo,$note){
        $date=date("Y-m-d");
        $time=date("h:i:s");
	if($note == "on-hold,sqacomp+7"){
		 $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,actSqaCmpOld,actSQACmpNew,note)
                	VALUES('$pid','$user','$date','$time','$changeType','$dateFrom','$dateTo','$note')";
	}
        elseif($note == "on-hold,itlcomp+7"){
                 $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,actItlCmpOld,actItlCmpNew,note)
                        VALUES('$pid','$user','$date','$time','$changeType','$dateFrom','$dateTo','$note')";
        }
        elseif($note == "INVALID DATE EXISTS:BYPASSING LOGIC"){
                 $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note)
                        VALUES('$pid','$user','$date','$time','$changeType','$note')";
        }
	else{
		writeToLog("On hold automation ERROR in updateProjectDetails");
	}

	if(!mysqli_query($link,$sql))
        {
        	echo("Error description: " . mysqli_error($link));
                mysqli_close($link);
        }
        else
        {
        	//header("location: ProjectList.php");
                //mysqli_close($link);
        }
}

//Add history entry when revison end date and sqa release date is auto updated to todays date, when status changes to ITL.
function addProjectHistoryEntryDates($link,$pid,$user,$changeType,$dateFrom,$dateTo,$note){
        $date=date("Y-m-d");
        $time=date("h:i:s");
        $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,actSqaCmpOld,actSqaCmpNew,note)
        	VALUES('$pid','$user','$date','$time','$changeType','$dateFrom','$dateTo','$note')";

        if(!mysqli_query($link,$sql))
        {
                echo("addProjectHistoryEntryDates - Error description: " . mysqli_error($link));
                mysqli_close($link);
        }
        else
        {
                //header("location: ProjectList.php");
                //mysqli_close($link);
        }
}

//Add history entry when revison end date is auto updated to todays date, when status changes to certain values.
function addDetailHistoryEntryDates($link,$pid,$user,$changeType,$dateFrom,$dateTo,$note){
        $date=date("Y-m-d");
        $time=date("h:i:s");
        $sql = "INSERT INTO ProjectDetailHistory (projectID,user,dateOfChange,timeOfChange,changeType,endDateOld,endDateNew,note)
        	VALUES('$pid','$user','$date','$time','$changeType','$dateFrom','$dateTo','$note')";

        if(!mysqli_query($link,$sql))
        {
                echo("addDetailHistoryEntryDates - Error description: " . mysqli_error($link));
                mysqli_close($link);
        }
        else
        {
                //header("location: ProjectList.php");
                //mysqli_close($link);
        }
}

function notesCheck($link,$pid,$revid,$rev,$user,$changeType,$note){
        $sql = "SELECT * FROM ProjectDetails where ProjectID=$pid and TaskID=$revid";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Check if project revision is changed and if so, add an entry to the ProjectDetailHistory table
                $nt = $row['Notes'];
                if($note != $nt){
                        $date=date("Y-m-d");
                        $time=date("h:i:s");
                        //writeToLog("End Date has changed");
                        $sql = "INSERT INTO ProjectDetailHistory (projectID,revID,revision,user,dateOfChange,timeOfChange,changeType,notesOld,notesNew)
                                VALUES('$pid','$revid','$rev','$user','$date','$time','$changeType','$nt','$note')";
                        if(!mysqli_query($link,$sql))
                        {
                                echo("Error description: " . mysqli_error($link));
                                mysqli_close($link);
                        }
                        else
                        {
                                header("location: ProjectList.php");
                                //mysqli_close($link);
                        }

                }
                else{
                        //writeToLog("Start Date has NOT changed");
                }
        }
}

?>
